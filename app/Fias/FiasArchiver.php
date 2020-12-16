<?php

namespace App\Fias;

use Illuminate\Support\Facades\Log;

/**
 * Class FiasArchiver
 *
 * @package App\Fias
 */
class FiasArchiver
{
    /**
     * @param $path
     */
    public function unrar($path)
    {
        $fileName = basename($path);
        $path = str_replace("/$fileName", '', $path);
        $output = [];

        Log::notice('Unrar ' . $path . '...');

        exec(
            "unar -o $path -f -D {$path}/{$fileName}",
            $output
        );

        foreach ($output as $index => $row) {
            Log::notice($row);
            echo "{$row}\n";
        }
    }
}
