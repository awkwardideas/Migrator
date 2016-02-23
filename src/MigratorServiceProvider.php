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

        $this->app['migrator.clean'] = $this->app->share(function () {
            return new Commands\MigratorClean();
        });
        $this->app['migrator.migrate'] = $this->app->share(function () {
            return new Commands\MigratorMigrate();
        });
        $this->app['migrator.prepare'] = $this->app->share(function () {
            return new Commands\MigratorPrepare();
        });
        $this->app['migrator.purge'] = $this->app->share(function () {
            return new Commands\MigratorPurge();
        });
        $this->app['migrator.truncate'] = $this->app->share(function () {
            return new Commands\MigratorTruncate();
        });
        $this->commands(
            'migrator.clean',
            'migrator.migrate',
            'migrator.prepare',
            'migrator.purge',
            'migrator.truncate'
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
