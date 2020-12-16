<?php

namespace App\Http\Controllers\Api;

use App\AddressObjectManager;
use App\Api\ApiRequestDTO;
use App\Api\ApiResponseDTO;
use App\FiasHouse;
use App\FiasObject;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use sngrl\SphinxSearch\SphinxSearch;
use Sphinx\SphinxClient;

/**
 * Class QueryController
 *
 * @package App\Http\Controllers\Api
 */
class QueryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function index(Request $request)
    {
        $startTime = microtime(true);
        Log::notice('---Api Query---');
        Log::notice('Start search in `idx_fias_object` at ' . (microtime(true) - $startTime));
        try {
            $data = ApiRequestDTO::create($request->all());
            Log::notice('Query data: ' . var_export($data, true));
            $nums = implode('|', $data->nums);
            $sphinx = new SphinxSearch();

            // Поиск по адресным элементам fias_object
            $sphinxQueryAddressObjectResult = $sphinx
                ->setRankingMode(SphinxClient::SPH_RANK_SPH04)
                ->search($data->query, 'idx_fias_object')
                ->query();

            if (empty($sphinxQueryAddressObjectResult['matches'])) {
                return ApiResponseDTO::error('Не найдено ни одного совпадения');
            }

            // ID всех найденных адресообразующих элементов
            $objectRawIds = Arr::pluck($sphinxQueryAddressObjectResult['matches'], 'attrs.ids');
            $objectIdsString = implode(',', $objectRawIds);
            $objectIds = explode(',', $objectIdsString);

            // Все значения найденных адресообразующих элементов
            $objects = FiasObject::where('actstatus', 1)
                ->whereIn('id', $objectIds)
                ->get()
                ->keyBy('id')
                ->toArray();

            // Массив найденных адресов разделенных на адресообразующие элементы с ключом aoguid последнего из элементов
            $addresses = [];

            foreach ($objectRawIds as $index => $objectRawId) {
                $ids = explode(',', $objectRawId);
                $value = [];

                foreach ($ids as $idKey => $id) {
                    $value[$id] = $objects[$id];
                }

                $key = end($value)['aoguid'];
                $addresses[$key] = $value;
            }

            Log::notice('Got addresses at ' . (microtime(true) - $startTime));

            // Создание массива всех найденных aoguid в формате crc32
            $lastAoguids = Arr::pluck($sphinxQueryAddressObjectResult['matches'], 'attrs.aoguid');
            $lastAoguids = array_map(
                function ($value) {
                    return crc32($value);
                },
                $lastAoguids
            );

            // Условие для создания поля сортировки
            $cond = [];

            foreach ($lastAoguids as $index => $lastAoguid) {
                $index = $index + 1;
                $cond[] = "IF(aoguid={$lastAoguid},{$index},0)";
            }

            $cond = implode('+', $cond);

            /* Поиск по домам fias_house
             * Используются номера дома, строения и корпуса из поискового запроса
             * Фильтрация по найденным адресным элементам из предыдущего запроса
             */
            $sphinxQueryHouseResult = $sphinx
                ->setSelect("*, {$cond} as sorter")
                ->search($nums, 'idx_fias_house')
                ->setRankingMode(SphinxClient::SPH_RANK_SPH04)
                ->setSortMode(SphinxClient::SPH_SORT_EXTENDED, "sorter ASC, weight() DESC")
                ->filter('aoguid', $lastAoguids)
                ->limit(200)
                ->query();

            // Добавление найденных домов к найденным адресам по aoguid, сохраняя сортировку домов
            if (!empty($sphinxQueryHouseResult['matches'])) {
                $houseIds = array_keys($sphinxQueryHouseResult['matches']);
                $houseIdsString = implode(',', $houseIds);
                $houses = FiasHouse::whereIn('id', $houseIds)
                    ->orderByRaw("FIELD(id,{$houseIdsString})")
                    ->get()
                    ->toArray();

                foreach ($houses as $index => $house) {
                    $addresses[$house['aoguid']]['houses'][] = $house;
                }
            }

            Log::notice('Got houses at ' . (microtime(true) - $startTime));

            $results = [];

            foreach ($addresses as $address) {
                $responseDTO = new ApiResponseDTO(
                    new AddressObjectManager($address)
                );
                $response = $responseDTO->toArray();

                if (isset($response['value'])) {
                    $results[] = $response;
                }
                else {
                    foreach ($response as $item) {
                        $results[] = $item;
                    }
                }
            }

            $results = array_slice($results, 0, $data->count);
            Log::notice('Results at ' . (microtime(true) - $startTime));

            return $results;
        } catch (Exception $e) {
            return ApiResponseDTO::error($e->getMessage(), $e->getTrace());
        }
    }
}
