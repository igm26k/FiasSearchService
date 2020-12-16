<?php

namespace App\Api;

use App\AddressObjectManager;

/**
 * Class ApiResponseDTO
 *
 * @package App
 */
class ApiResponseDTO
{
    /**
     * @var string
     */
    public $value = '';

    /**
     * @var array
     */
    public $data = [];

    /**
     * ApiResponseDTO constructor.
     *
     * @param AddressObjectManager $addressObjectManager
     */
    public function __construct(AddressObjectManager $addressObjectManager)
    {
        $this->value = $addressObjectManager->fullAddressString;
        $this->data = $addressObjectManager->addressInfo;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if (!empty($this->data['houses'])) {
            $returnArray = [];

            foreach ($this->data['houses'] as $house) {
                $houseInfo = [
                    'postal_code'     => $house['postal_code'],
                    'house_fias_id'   => $house['house_fias_id'],
                    'house_type'      => $house['house_type'],
                    'house_type_full' => $house['house_type_full'],
                    'house'           => $house['house'],
                    'block'           => $house['block'],
                    'block_type'      => $house['block_type'],
                    'block_type_full' => $house['block_type_full'],
                ];

                $returnArray[] = [
                    'value' => "{$this->value}, {$house['house_type']} {$house['house']} {$house['block_type']} {$house['block']}",
                    'data'  => array_merge($this->data, $houseInfo),
                ];
            }

            foreach ($returnArray as $index => $item) {
                unset($returnArray[$index]['data']['houses']);
            }

            return $returnArray;
        }
        else {
            return [
                'value' => $this->value,
                'data'  => $this->data,
            ];
        }
    }

    /**
     * @param $text
     * @param array $description
     * @param bool $json
     *
     * @return array|false|string
     */
    public static function error($text, array $description = [], bool $json = false)
    {
        $return = [
            'data'        => [],
            'error'       => $text,
            'description' => $description,
        ];

        if ($json) {
            $return = json_encode($return);
        }

        return $return;
    }
}
