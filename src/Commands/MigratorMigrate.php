<?php namespace AwkwardIdeas\Migrator\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use AwkwardIdeas\Migrator\Migrator;

class MigratorMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrator:migrate {--force} {--from} {--purge}';

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
        if ($this->option('purge')) {
            if ($this->option('from')!="") {
                $database = $this->option('from');
            } else {
                $database = $this->ask('What database do you want to truncate?');
            }
            if (($this->option('force')=="VALUE_NONE") OR $this->confirm('Are you sure you want to truncate the database ('.$database .')?')) {
                $this->comment(PHP_EOL . Migrator::TruncateDatabase($database).PHP_EOL);
            }
            if (($this->option('force')=="VALUE_NONE") OR $this->confirm('Are you sure you want to delete all migration files in the migrations folder?')) {
                $this->comment(PHP_EOL . Migrator::CleanMigrationsDirectory().PHP_EOL);
            }
        }

        if ($this->option('from')) {
            $from = $this->option('from');
        } else {
            $from="";
        }
        $this->comment(PHP_EOL.Migrator::PrepareMigrations($from).PHP_EOL);

        $this->call('migrate');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('force', null, InputOption::VALUE_NONE, "no prompts"),
            array('from', null, InputOption::VALUE_OPTIONAL, "Database to Migrate from",""),
            array('purge', null, InputOption::VALUE_NONE, "force clean")
        );
    }
}
