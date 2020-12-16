<?php

namespace App\Console\Commands;

use App\Fias\Fias;
use Illuminate\Console\Command;

/**
 * Class FiasDownloadData
 *
 * @package App\Console\Commands
 */
class FiasDownloadData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fias:downloadData {--fileType=} {--dataType=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Data downloader";

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
     */
    public function handle()
    {
        $options = $this->options();
        $fias = new Fias($options['fileType'], $options['dataType']);
        $fias->downloadAndUnrar();
    }
}
