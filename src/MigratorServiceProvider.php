<?php namespace AwkwardIdeas\Migrator;

use Illuminate\Support\ServiceProvider;
use AwkwardIdeas\MyPDO\MyPDOServiceProvider;

class MigratorServiceProvider extends ServiceProvider
{

	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$configPath = __DIR__ . '/../config/migrator.php';
		
		$this->publishes([$configPath => $this->getConfigPath()], 'config');
	}

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
		
		$this->mergeConfigFrom(__DIR__ . '/../config/migrator.php', 'migrator');

        $this->app->singleton('command.migrator.clean', function($app){
            return $app['AwkwardIdeas\Migrator\Commands\MigratorClean'];
        });

        $this->app->singleton('command.migrator.migrate', function($app){
            return $app['AwkwardIdeas\Migrator\Commands\MigratorMigrate'];
        });

        $this->app->singleton('command.migrator.prepare', function($app){
            return $app['AwkwardIdeas\Migrator\Commands\MigratorPrepare'];
        });

        $this->app->singleton('command.migrator.purge', function($app){
            return $app['AwkwardIdeas\Migrator\Commands\MigratorPurge'];
        });

        $this->app->singleton('command.migrator.truncate', function($app){
            return $app['AwkwardIdeas\Migrator\Commands\MigratorTruncate'];
        });

        $this->commands(
            'command.migrator.clean',
            'command.migrator.migrate',
            'command.migrator.prepare',
            'command.migrator.purge',
            'command.migrator.truncate'
        );
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
	
	private function getConfigPath()
    {
        return config_path('migrator.php');
    }
}
