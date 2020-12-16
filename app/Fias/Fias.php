<?php

namespace App\Fias;

use App\Fias\Parser\FiasDataParserFactory;
use Exception;
use Illuminate\Support\Facades\Storage;

/**
 * Class Fias
 *
 * @package App\Fias
 */
class Fias
{
    private $_soap;
    private $_downloader;
    private $_archiver;
    private $_fileType;
    private $_dataType;

    /**
     * Fias constructor.
     *
     * @param string $fileType
     * @param string $dataType
     */
    public function __construct($fileType = 'xml', $dataType = 'complete')
    {
        $this->_soap = new FiasSoap();
        $this->_downloader = new FiasDataDownloader();
        $this->_archiver = new FiasArchiver();
        $this->_fileType = strtolower($fileType);
        $this->_dataType = strtolower($dataType);
    }

    /**
     * Загрузка архива с портала ФИАС и дальнейшее разархивирование
     */
    public function downloadAndUnrar()
    {
        $fileType = $this->_fileType;
        $dataType = $this->_dataType;
        echo "Trying to get url of archive from Fias SOAP... ";
        $soapKey = 'Fias' . ucfirst($dataType) . ucfirst($fileType) . 'Url';
        $this->_soap->get('last');
        $url = $this->_soap->getValue($soapKey)[0];
        echo "Successfully received url: {$url}\n";
        $relativeDirectoryPath = "/data/{$fileType}_{$dataType}";
        $path = Storage::disk('fias')->path('') . $relativeDirectoryPath;

        if (!file_exists($path)) {
            Storage::disk('fias')->makeDirectory($relativeDirectoryPath);
        }
        elseif ($dataType === 'delta') {
            echo "Clean directory {$path}\n";
            Storage::disk('fias')->deleteDirectory($relativeDirectoryPath);
            Storage::disk('fias')->makeDirectory($relativeDirectoryPath);
        }

        $path = "{$path}/fias_{$fileType}.rar";
        echo "Download archive from {$url} to {$path}\n";
        $this->_downloader->download($url, $path);
        echo "Unrar archive...\n";
        $this->_archiver->unrar($path);
    }

    /**
     * Парсер файлов
     *
     * @param bool $update
     * @throws Exception
     */
    public function parseData(bool $update = false)
    {
        $parser = FiasDataParserFactory::create($this->_fileType);

        if ($parser) {
            $parser->parse($this->_fileType, $this->_dataType, $update);
        }
    }
}
