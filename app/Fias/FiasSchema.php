<?php

namespace App\Fias;

use DOMDocument;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FiasSchema
{
    /**
     * @var array
     */
    private $_schemas = [];

    /**
     * @var array
     */
    private $_files;

    /**
     * @var DOMDocument
     */
    private $_doc;

    /**
     * FiasSchema constructor.
     */
    private function __construct()
    {
        $this->_doc = new DOMDocument();
    }

    /**
     * Список файлов с схемами
     *
     * @throws Exception
     */
    private function _files()
    {
        $this->_files = Storage::disk('fias')->files('/schemes');

        if (empty($this->_files)) {
            throw new Exception('Empty files array');
        }
    }

    /**
     * Парсер
     */
    private function _parse()
    {
        foreach ($this->_files as $file) {
            $file = storage_path('app/fias') . "/$file";
            $this->_doc->load($file);
            $xml = $this->_doc->getElementsByTagNameNS('http://www.w3.org/2001/XMLSchema', '*');
            $attributes = [];
            unset($schemaName, $schemaDescription);
            $schemaNameCounter = false;
            $schemaDescriptionCounter = false;

            foreach ($xml as $id => $item) {
                if ($item->tagName === 'xs:element' && !isset($schemaName)) {
                    if ($schemaNameCounter) {
                        $schemaName = Str::snake($item->attributes[0]->value);
                    }
                    else {
                        $schemaNameCounter = true;
                    }
                }

                if ($item->tagName === 'xs:documentation' && !isset($schemaDescription)) {
                    if ($schemaDescriptionCounter) {
                        $schemaDescription = $item->nodeValue;
                    }
                    else {
                        $schemaDescriptionCounter = true;
                    }
                }

                if ($item->tagName === 'xs:attribute') {
                    foreach ($item->attributes as $attribute) {
                        $attributeValue = str_replace('xs:', '', $attribute->value);

                        if ($attribute->name === 'name') {
                            $id = strtolower($attributeValue);
                        }

                        $attributes[$id][$attribute->name] = $attributeValue;
                    }

                    foreach ($item->childNodes as $childNode) {
                        if (!isset($childNode->tagName)) {
                            continue;
                        }

                        switch ($childNode->tagName) {
                            case 'xs:simpleType':
                                foreach ($childNode->childNodes as $childChildNode) {
                                    if (!isset($childChildNode->tagName)) {
                                        continue;
                                    }

                                    if ($childChildNode->tagName === 'xs:restriction') {
                                        $valueType = $childChildNode->attributes[0];
                                        $valueType = str_replace('xs:', '', $valueType->value);
                                        $attributes[$id]['type'] = $valueType;
                                        $attributes[$id]['typeOptions'] = [];

                                        foreach ($childChildNode->childNodes as $option) {
                                            if ($option->nodeType === 1) {
                                                $tagName = str_replace('xs:', '', $option->tagName);
                                                $attributes[$id]['typeOptions'][$tagName] = $option->attributes[0]->value;
                                            }
                                        }
                                    }
                                }
                                break;
                            case 'xs:annotation':
                                foreach ($childNode->childNodes as $childChildNode) {
                                    if (!isset($childChildNode->tagName)) {
                                        continue;
                                    }

                                    if ($childChildNode->tagName === 'xs:documentation') {
                                        $attributes[$id]['comment'] = $childChildNode->nodeValue;
                                    }
                                }
                                break;
                        }
                    }
                }
            }

            if (!empty($schemaName)) {
                if (!empty($this->_schemas[$schemaName])) {
                    foreach ($this->_schemas[$schemaName]['fields'] as $fieldName => $field) {
                        if (!isset($attributes[$fieldName])) {
                            $attributes[$fieldName] = $field;
                        }
                    }

                    foreach ($attributes as $fieldName => $field) {
                        if (!isset($this->_schemas[$schemaName]['fields'][$fieldName])) {
                            $attributes[$fieldName] = $field;
                        }
                    }

                    $schemaDescription = !empty($this->_schemas[$schemaName]['description'])
                        ? $this->_schemas[$schemaName]['description']
                        : !empty($schemaDescription)
                            ? $schemaDescription
                            : '';
                }

                $this->_schemas[$schemaName] = [
                    'description' => !empty($schemaDescription) ? $schemaDescription : '',
                    'fields'      => $attributes,
                ];
            }
        }
    }

    /**
     * Парсинг схемы и создание таблиц ФИАС в базе данных приложения
     *
     * @throws Exception
     */
    public static function parseXsdAndCreateDb()
    {
        $instance = new self();
        $instance->_files();
        $instance->_parse();

        foreach ($instance->_schemas as $tableName => $schema) {
            $tableName = "fias_$tableName";
            $tableDescription = $schema['description'];

            if (Schema::hasTable($tableName)) {
                continue;
            }

            Schema::create($tableName, function (Blueprint $table) use ($schema) {
                // Опции таблицы
                $table->engine = 'InnoDB';
                $table->charset = 'utf8mb4';
                $table->collation = 'utf8mb4_unicode_ci';

                // Столбцы
                $table->bigIncrements('id')->comment('ID');

                foreach ($schema['fields'] as $columnName => $column) {
                    switch ($column['type']) {
                        case 'integer':
                        case 'int':
                            $table->integer($columnName)->nullable()->comment($column['comment']);
                            break;

                        case 'string':
                            $length = (isset($column['typeOptions']['maxLength'])
                                ? $column['typeOptions']['maxLength']
                                : (isset($column['typeOptions']['length'])
                                    ? $column['typeOptions']['length']
                                    : 'typeText'));

                            if ($length === 'typeText') {
                                $table->text($columnName)->nullable()->default('')->comment($column['comment']);
                            }
                            else {
                                $table->string($columnName,
                                    $length)->nullable()->default('')->comment($column['comment']);
                            }

                            break;

                        case 'date':
                            $table->date($columnName)->nullable()->comment($column['comment']);
                            break;

                        case 'byte':
                            $table->boolean($columnName)->nullable()->comment($column['comment']);
                            break;
                    }
                }
            });

            DB::statement("ALTER TABLE `$tableName` COMMENT '$tableDescription'");
        }
    }
}
