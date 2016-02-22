<?php namespace AwkwardIdeas\Migrator\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use AwkwardIdeas\Migrator\Migrator;

class MigratorClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrator:clean {--force}';

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
        //Clean Migration Files
        if (($this->option('force')=="VALUE_NONE") OR $this->confirm('Are you sure you want to delete all migration files in the migrations folder?')) {
            $this->comment(PHP_EOL . Migrator::CleanMigrationsDirectory().PHP_EOL);
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('force', null, InputOption::VALUE_NONE, "force clean")
        );
    }
}
