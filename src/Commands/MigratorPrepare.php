<?php namespace AwkwardIdeas\Migrator\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use AwkwardIdeas\Migrator\Migrator;

class MigratorPrepare extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrator:prepare {--from=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //Create Migration Files
        if ($this->option('from')) {
            $from = $this->option('from');
        } else {
            $from="";
        }
        $this->comment("Migrating from $from.");
        $this->comment(PHP_EOL.Migrator::PrepareMigrations($from).PHP_EOL);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('from', null, InputOption::VALUE_OPTIONAL, "Database to Migrate from","")
        );
    }
}
