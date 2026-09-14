<?php

namespace Modules\Report\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'report:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate reports from command line.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Report module command executed.');
    }
}

