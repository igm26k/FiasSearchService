<?php

namespace App\Fias\Parser;

/**
 * Class FiasDataParserFactory
 *
 * @package App\Fias\Parser
 */
class FiasDataParserFactory
{
    /**
     * @param $fileType
     *
     * @return FiasDataParserDbf|FiasDataParserXml|false
     */
    public static function create($fileType)
    {
        switch ($fileType) {
            case 'dbf':
                return new FiasDataParserDbf();
                break;
            case 'xml':
                return new FiasDataParserXml();
                break;
            default:
                return false;
                break;
        }
    }
}
