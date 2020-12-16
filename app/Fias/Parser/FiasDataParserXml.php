<?php

namespace App\Fias\Parser;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use XMLReader;

/**
 * Class Xml
 *
 * @package App\Fias\Parser
 */
class FiasDataParserXml extends FiasDataParser
{
    public function __construct()
    {
        parent::__construct();

        $this->_reader = new XMLReader();
    }

    /**
     * Создает поток для чтения файла
     *
     * @param $file
     */
    protected function _open($file)
    {
        $file = $this->_storage->path($file);
        Log::notice('Open file ' . $file);
        $openFile = $this->_reader->open($file);

        if (!$openFile) {
            Log::error('Could not open file ' . $file);
        }
    }

    /**
     * Закрывает поток для чтения файла
     */
    protected function _close()
    {
        Log::notice('Close file');

        $this->_reader->close();
    }

    /**
     * @param $fileType
     * @param $dataType
     */
    public function parse($fileType, $dataType)
    {
        set_time_limit(0);
        $this->_files($fileType, $dataType);

        if (empty($this->_files)) {
            Log::error('Empty files array');

            return;
        }

        foreach ($this->_files as $file) {
            $this->_open($file);
            $insertCount = 0;
            $insertValues = [];
            $tableName = '';
            $columns = null;
            $tableNameCounter = false;
            $insertCountTotal = 0;

            Log::notice('Parse file: "' . $file . '"');

            while ($this->_reader->read()) {
                $tagName = $this->_reader->name;

                if ($columns === null) {
                    if ($tableNameCounter) {
                        $tableName = 'fias_' . Str::snake($tagName);

                        Log::notice('Getting columns of "' . $tableName . '"');

                        $columns = DB::getSchemaBuilder()->getColumnListing($tableName);

                        Log::notice('Insert data to "' . $tableName . '"');
                    }
                    else {
                        $tableNameCounter = true;
                    }
                }

                if ($columns !== null && $this->_reader->hasAttributes) {
                    $attributeCount = $this->_reader->attributeCount;
                    $values = [];
                    unset($columns[0]);

                    for ($i = 0; $i < $attributeCount; $i++) {
                        $this->_reader->moveToAttributeNo($i);
                        $values[strtolower($this->_reader->name)] = $this->_reader->value;
                    }

                    foreach ($columns as $column) {
                        if (!isset($values[$column])) {
                            $values[$column] = null;
                        }
                    }

                    if ($insertCount <= $this->_maxInsertCount) {
                        $insertValues[] = $values;
                    }

                    if ($insertCount === $this->_maxInsertCount) {
                        $this->_queryValues($tableName, $insertValues, $insertCount);

                        if ($this->_reader->nodeType === XMLReader::END_ELEMENT) {
                            Log::notice('Inserted ' . $insertCountTotal . ' rows');
                        }

                        continue;
                    }

                    $insertCount++;
                    $insertCountTotal++;
                }

                if ($this->_reader->nodeType === XMLReader::END_ELEMENT) {
                    $this->_queryValues($tableName, $insertValues, $insertCount);

                    Log::notice('Inserted ' . $insertCountTotal . ' rows');

                    continue;
                }
            }

            $this->_close();
        }
    }
}
