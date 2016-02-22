<?php namespace AwkwardIdeas\Migrator\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use AwkwardIdeas\Migrator\Migrator;

class MigratorPurge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrator:purge {--force} {--database}';

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
        //Truncate Database and Clean Migration Files
        if ($this->option('database')) {
            $database = $this->option('database');
        } else {
            $database = $this->ask('What database do you want to truncate?');
        }
        if (($this->option('force')=="VALUE_NONE") OR $this->confirm('Are you sure you want to truncate the database ('.$database .')? [yes|no]')) {
            $this->comment(PHP_EOL . Migrator::TruncateDatabase($database).PHP_EOL);
        }
        if (($this->option('force')=="VALUE_NONE") OR $this->confirm('Are you sure you want to delete all migration files in the migrations folder? [yes|no]')) {
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
            array('force', null, InputOption::VALUE_NONE, "force clean"),
            array('database', null, InputOption::VALUE_OPTIONAL, "Database to truncate","")
        );
    }
}
