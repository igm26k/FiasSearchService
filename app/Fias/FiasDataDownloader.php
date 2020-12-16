<?php

namespace App\Fias;

use Illuminate\Support\Facades\Log;

/**
 * Class FiasDataDownloader
 *
 * @package App\Fias
 */
class FiasDataDownloader
{
    /**
     * @param $url
     * @param $path
     */
    public function download($url, $path)
    {
        Log::notice('Downloading from ' . $url . ' to ' . $path . '...');

        set_time_limit(0);

        $fp = fopen($path, 'w+');
        $ch = curl_init($url);
        $options = [
            CURLOPT_TIMEOUT        => 18000,
            CURLOPT_FILE           => $fp,
            CURLOPT_FOLLOWLOCATION => true,
        ];

        curl_setopt_array($ch, $options);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }
}
