<?php

namespace App\Fias\Parser;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inok\Dbf\Records;
use Inok\Dbf\Table;

/**
 * Class FiasDataParserDbf
 *
 * @package App\Fias\Parser
 */
class FiasDataParserDbf extends FiasDataParser
{
    private $fileNameTableMatch = [
        'ACTSTAT'  => 'fias_actual_status',
        'SOCRBASE' => 'fias_address_object_type',
        'CENTERST' => 'fias_center_status',
        'CURENTST' => 'fias_current_status',
        'ESTSTAT'  => 'fias_estate_status',
        'FLATTYPE' => 'fias_flat_type',
        'HOUSE'    => 'fias_house',
        'HOUSEINT' => 'fias_house_interval',
        'HSTSTAT'  => 'fias_house_state_status',
        'INTVSTAT' => 'fias_interval_status',
        'LANDMARK' => 'fias_landmark',
        'NORDOC'   => 'fias_normative_document',
        'NDOCTYPE' => 'fias_normative_document_type',
        'ADDROB'   => 'fias_object',
        'OPERSTAT' => 'fias_operation_status',
        'ROOM'     => 'fias_room',
        'ROOMTYPE' => 'fias_room_type',
        'STEAD'    => 'fias_stead',
        'STRSTAT'  => 'fias_structure_status',
    ];

    /**
     * @var Table
     */
    protected $_reader;

    /**
     * Dbf constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Создает поток для чтения файла
     *
     * @param $file
     */
    protected function _open($file)
    {
        Log::notice('Open file ' . $file);

        try {
            $this->_reader = new Table($file);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        /*$this->_reader = dbase_open($file, DBASE_RDONLY);

        if (!$this->_reader) {
            Log::error('Could not open file ' . $file);
        }*/
    }

    /**
     * Закрывает поток для чтения файла
     */
    protected function _close()
    {
        if ($this->_reader) {
//            dbase_close($this->_reader);
            $this->_reader->close();
        }
    }

    /**
     * Парсер
     *
     * @param $fileType
     * @param $dataType
     * @param bool $update
     *
     * @return void
     */
    public function parse($fileType, $dataType, bool $update = false)
    {
        set_time_limit(0);
        $this->_files($fileType, $dataType);

        if (empty($this->_files)) {
            Log::error('Empty files array');

            return;
        }

        $this->_parseIterator($fileType, $dataType, false, $update);
        $this->_parseIterator($fileType, $dataType, true);
    }

    /**
     * @param $fileType
     * @param $dataType
     * @param bool $delete
     * @param bool $update
     *
     * @return bool
     */
    private function _parseIterator($fileType, $dataType, bool $delete = false, bool $update = false)
    {
        foreach ($this->_files as $file) {
            $baseFileName = basename($file);
            preg_match('/^([D]?)([A-Z]+)[\d]{0,4}.DBF$/iu', $baseFileName, $matches);

            if (
                !isset($matches[2])
                || (isset($matches[1], $matches[2]) && $matches[1] === 'D' && !$delete)
            ) {
                continue;
            }

            $tableName = $this->fileNameTableMatch[$matches[2]];
            $this->_open($this->_storage->path('') . $file);

            $tableHeaders = $this->_reader->getHeaders();
            $numberOfRecords = $tableHeaders['records'];

            try {
                $recordsInstance = new Records($this->_reader);
            } catch (Exception $e) {
                Log::error($e->getMessage());

                return false;
            }

            $insertCount = 0;
            $insertValues = [];
            $insertCountTotal = 0;
            Log::notice('Parse file: "' . $file . '"');
            Log::notice('Getting columns of "' . $tableName . '"');
            $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
            unset($columns[0]);
            $maxInsertCount = floor(65000 / count($columns));
            Log::notice('Insert data to "' . $tableName . '"');

            if ($numberOfRecords <= 0) {
                $movePath = $this->_storage->path("data_loaded/{$fileType}_{$dataType}");

                if (!file_exists($movePath)) {
                    Storage::disk('fias')->makeDirectory("/data_loaded/{$fileType}_{$dataType}");
                }

                rename($this->_storage->path($file), "$movePath/{$baseFileName}");

                continue;
            }

            for ($i = 1; $i <= $numberOfRecords; $i++) {
                $values = [];
                $row = $recordsInstance->nextRecord();

                if (empty($row)) {
                    continue;
                }

                unset($row['deleted']);

                foreach ($row as $columnName => $value) {
                    $value = trim($value);
                    $values[strtolower($columnName)] = ($value === '' ? null : $value);
                }

                foreach ($columns as $column) {
                    if (!isset($values[$column])) {
                        $values[$column] = null;
                    }
                }

                if ($insertCount <= $maxInsertCount) {
                    $insertValues[] = $values;
                }

                if ($insertCount >= $maxInsertCount) {
                    $this->_queryValues($tableName, $insertValues, $insertCount, $delete, $update);

                    if ($i === $numberOfRecords) {
                        Log::notice("$insertCountTotal rows inserted");
                        $movePath = $this->_storage->path("data_loaded/{$fileType}_{$dataType}");

                        if (!file_exists($movePath)) {
                            Storage::disk('fias')->makeDirectory("/data_loaded/{$fileType}_{$dataType}");
                        }

                        rename($this->_storage->path($file), "$movePath/{$baseFileName}");
                    }

                    continue;
                }

                $insertCount++;
                $insertCountTotal++;

                if ($i === $numberOfRecords) {
                    $this->_queryValues($tableName, $insertValues, $insertCount, $delete, $update);
                    Log::notice("$insertCountTotal rows inserted");
                    $movePath = $this->_storage->path("data_loaded/{$fileType}_{$dataType}");

                    if (!file_exists($movePath)) {
                        Storage::disk('fias')->makeDirectory("/data_loaded/{$fileType}_{$dataType}");
                    }

                    rename($this->_storage->path($file), "$movePath/{$baseFileName}");

                    continue;
                }
            }
        }
    }
}
