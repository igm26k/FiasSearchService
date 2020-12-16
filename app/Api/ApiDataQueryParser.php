<?php

namespace App\Api;

/**
 * Class ApiDataQueryParser
 *
 * @package App
 */
class ApiDataQueryParser
{
    /**
     * @param string $query
     *
     * @return array
     */
    public static function execute(string $query)
    {
        $array = [];

        preg_match('/(к(орп)?(ус)?)[\s]*([\d]+)/iu', $query, $buildMatches);

        if (!empty($buildMatches)) {
            $array[] = $buildMatches[4];
            $query = str_replace($buildMatches[0], '', $query);
        }

        preg_match('/(с(тр)?(оен)?(ие)?)[\s]*([\d]+)/iu', $query, $structureMatches);

        if (!empty($structureMatches)) {
            $array[] = $structureMatches[5];
            $query = str_replace($structureMatches[0], '', $query);
        }

        preg_match('/(д(ом)?)[\s]*([\d]+)/iu', $query, $houseMatches);

        if (!empty($houseMatches)) {
            $array[] = $houseMatches[3];
            $query = str_replace($houseMatches[0], '', $query);
        }
        else {
            $pattern = '/\b([\d]+)\b/iu';
            preg_match($pattern, $query, $houseMatches);

            if (!empty($houseMatches)) {
                $array[] = $houseMatches[1];
                $query = preg_replace($pattern, '', $query);
            }
        }

        return [
            'query' => trim($query),
            'nums' => $array
        ];
    }
}
