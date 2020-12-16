<?php

namespace App\Fias;

use Illuminate\Support\Facades\Log;
use SoapClient;
use SoapFault;

/**
 * Class FiasSoap
 *
 * @package app\components
 */
class FiasSoap
{
    /**
     * @var SoapClient
     */
    private $_soapClient;

    /**
     * @var object
     */
    private $_fileInfo;

    /**
     * @var array
     */
    private $_getMethod = [
        'last' => '_getLastDownloadFileInfo',
        'all'  => '_getAllDownloadFileInfo',
    ];

    /**
     * Инициализация клиента SOAP
     */
    private function _soapClientInit(): void
    {
        $wsdlPath = env('FIAS_WSDL_PATH');

        Log::notice('SoapClient initialization...');
        Log::notice('Trying to get WSDL from ' . $wsdlPath . '...');

        try {
            $this->_soapClient = new SoapClient($wsdlPath);
        }
        catch (SoapFault $exception) {
            Log::error($exception->getMessage());
            Log::error($exception->getTraceAsString());
        }
    }

    /**
     * Записывает в $fileInfo информацию о последней версии файлов, доступных для скачивания
     */
    private function _getLastDownloadFileInfo(): void
    {
        $lastDownloadFileInfo = $this->_soapClient->GetLastDownloadFileInfo();
        $this->_fileInfo = $lastDownloadFileInfo->GetLastDownloadFileInfoResult;
    }

    /**
     * Записывает в $fileInfo нформацию о всех версиях файлов, доступных для скачивания
     */
    private function _getAllDownloadFileInfo(): void
    {
        $lastDownloadFileInfo = $this->_soapClient->GetAllDownloadFileInfo();
        $this->_fileInfo = $lastDownloadFileInfo->GetAllDownloadFileInfoResult;
    }

    /**
     * FiasDataDownloader constructor.
     */
    public function __construct()
    {
        $this->_soapClientInit();
    }

    /**
     * @param string $getMethod
     */
    public function get(string $getMethod): void
    {
        Log::notice('Trying to get download link...');

        if (isset($this->_getMethod[$getMethod])) {
            $method = $this->_getMethod[$getMethod];

            Log::notice('Calling method "' . $method . '"...');

            $this->$method();
        }

        Log::error('Called method "' . $getMethod . '" not found');
    }

    /**
     * Возвращает массив значений по указанному ключу или все, если ключ не указан
     *
     * @param string $key
     *
     * @return array
     */
    public function getValue(string $key = ''): array
    {
        Log::notice('Trying to get info by key ' . $key . '...');

        $values = [];

        if (
            isset($this->_fileInfo->DownloadFileInfo)
            && is_array($this->_fileInfo->DownloadFileInfo)
            && count($this->_fileInfo->DownloadFileInfo) > 0
        ) {
            foreach ($this->_fileInfo->DownloadFileInfo as $item) {
                if (!empty($key)) {
                    $values[] = $item->$key;
                }
                else {
                    $values[] = $item;
                }
            }
        }
        else {
            if (!empty($key)) {
                $values[] = $this->_fileInfo->$key;
            }
            else {
                $values[] = $this->_fileInfo;
            }
        }

        return $values;
    }
}
