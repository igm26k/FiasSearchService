<?php

namespace App\Console\Commands;

use App\Fias\Fias;
use App\Fias\Xml;
use Exception;
use Illuminate\Console\Command;

/**
 * Class FiasParseData
 *
 * @package App\Console\Commands
 */
class FiasParseData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fias:parseData {--fileType=} {--dataType=} {--update=?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'XML data parser';

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
        $options = $this->options();
        $options['update'] = empty($options['update']) ? false : (bool)$options['update'];
        $fias = new Fias($options['fileType'], $options['dataType']);
        $fias->parseData($options['update']);
    }
}
