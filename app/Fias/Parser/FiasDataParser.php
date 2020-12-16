<?php

namespace App\Fias\Parser;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Class Parser
 *
 * @package App\Fias\Parser
 */
abstract class FiasDataParser
{
    protected $_reader;
    protected $_files;
    protected $_storage;
    protected $_maxInsertCount = 1700;
    protected $_tablePk = [
        'fias_actual_status'           => 'actstatid',
        'fias_address_object_type'     => 'kod_t_st',
        'fias_center_status'           => 'centerstid',
        'fias_current_status'          => 'curentstid',
        'fias_estate_status'           => 'eststatid',
        'fias_flat_type'               => 'fltypeid',
        'fias_house'                   => 'houseid',
        'fias_house_interval'          => 'houseintid',
        'fias_house_state_status'      => 'housestid',
        'fias_interval_status'         => 'intvstatid',
        'fias_landmark'                => 'landid',
        'fias_normative_document'      => 'normdocid',
        'fias_normative_document_type' => 'ndtypeid',
        'fias_object'                  => 'aoid',
        'fias_operation_status'        => 'operstatid',
        'fias_room'                    => 'roomid',
        'fias_room_type'               => 'rmtypeid',
        'fias_stead'                   => 'steadid',
        'fias_structure_status'        => 'strstatid',
    ];

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        Log::notice('Create Parser instance');
        $this->_storage = Storage::disk('fias');
    }

    /**
     * Создает поток для чтения файла
     *
     * @param $file
     */
    abstract protected function _open($file);

    /**
     * Закрывает поток для чтения файла
     */
    abstract protected function _close();

    /**
     * Парсер
     *
     * @param $fileType
     * @param $dataType
     *
     * @return void
     */
    abstract public function parse($fileType, $dataType);

    /**
     * Список файлов с схемами
     *
     * @param $fileType
     * @param $dataType
     */
    protected function _files($fileType, $dataType)
    {
        Log::notice('Get list of data files');
        $this->_files = $this->_storage->files("/data/{$fileType}_{$dataType}");
    }

    /**
     * Вставка строк в таблицу
     *
     * @param $tableName
     * @param $queryValues
     * @param $queryCount
     * @param bool $delete
     * @param bool $update
     */
    protected function _queryValues($tableName, &$queryValues, &$queryCount, bool $delete = false, bool $update = false)
    {
        /*foreach ($this->_tablePk as $tableName => $uniqueKey) {
//            $queryStr = "ALTER TABLE {$tableName} DROP INDEX {$tableName}_uniqueKey;";
//            DB::statement($queryStr);

            $queryStr = "CREATE UNIQUE INDEX IF NOT EXISTS {$tableName}_uniqueKey ON {$tableName} ({$uniqueKey});";
//            $queryStr = "ALTER TABLE $tableName ADD UNIQUE INDEX {$tableName}_uniqueKey ($uniqueKey)";
//            $queryStr = "CREATE INDEX {$tableName}_idx_id ON $tableName($uniqueKey)";
            DB::statement($queryStr);
        }*/

        if (!$delete) {
            $logActionName = $update ? 'update' : 'insert';
            echo "[" . date('Y-m-d H:i:s') . "] Trying to {$logActionName} {$queryCount} values in {$tableName}...";
            $keys = array_keys($queryValues[0]);
            $values = [];

            foreach ($queryValues as $rowValues) {
                $rowValuesArr = [];

                foreach ($keys as $key) {
                    $rowValuesArr[] = "?";
                }

                $values[] = "(" . implode(",", $rowValuesArr) . ")";
            }

            $keysStr = "`" . implode("`,`", $keys) . "`";
            $valuesStr = implode(',', $values);
            $queryStr = "INSERT IGNORE INTO `$tableName` ({$keysStr}) VALUES {$valuesStr}";

            if ($update) {
                $onDuplicate = [];

                foreach ($keys as $key) {
                    if ($this->_tablePk[$tableName] !== $key) {
                        $onDuplicate[] = "`$key` = VALUES(`$key`)";
                    }
                }

                $onDuplicateStr = implode(',', $onDuplicate);
                $queryStr .= " ON DUPLICATE KEY UPDATE {$onDuplicateStr}";
            }

            DB::insert($queryStr, Arr::flatten($queryValues));
        }
        else {
            echo "[" . date('Y-m-d H:i:s') . "] Trying to delete values from `{$tableName}` ";
            $primaryKey = $this->_tablePk[$tableName];
            $values = [];

            foreach ($queryValues as $rowValues) {
                $values[] = "'{$rowValues[$primaryKey]}'";
            }

            $valuesStr = implode(',', $values);
            echo " ({$valuesStr})...";
            $queryStr = "DELETE FROM `$tableName` WHERE `{$primaryKey}` IN ($valuesStr)";

            DB::delete($queryStr);
        }

        echo " Complete\n";

        // Сброс значений
        $queryCount = 0;
        $queryValues = [];
    }
}
