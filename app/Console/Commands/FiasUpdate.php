<?php

namespace App\Console\Commands;

use App\Fias\Fias;
use App\Fias\Xml;
use Exception;
use Illuminate\Console\Command;

/**
 * Class FiasUpdate
 *
 * @package App\Console\Commands
 */
class FiasUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fias:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление базы данных из дельта-выгрузки ФИАС. Приоритетный формат - DBF.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function handle()
    {
        // Опции
        $options = [
            'fileType' => 'dbf',
            'dataType' => 'delta'
        ];

        // Загрузка и разархивирование
        $fias = new Fias($options['fileType'], $options['dataType']);
        $fias->downloadAndUnrar();

        // Парсер и обновление БД
        $fias = new Fias($options['fileType'], $options['dataType']);
        $fias->parseData(true);
    }
}
