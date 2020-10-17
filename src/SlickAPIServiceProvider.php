<?php
/**
 * Created by SlickLabs - Wefabric.
 * User: nathanjansen <nathan@wefabric.nl>
 * Date: 26-03-18
 * Time: 11:54
 */

namespace SlickLabs\Laravel\SlickAPI;

use Illuminate\Support\ServiceProvider;
use SlickLabs\Laravel\SlickAPI\Exceptions\InvalidConfiguration;

class SlickAPIServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/slickapi.php' => config_path('slickapi.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/slickapi.php', 'slickapi');

        $this->app->bind(SlickAPIManager::class, function () {
            $config = config('slickapi');
            $this->guardAgainstInvalidConfiguration($config);

            return SlickAPIManagerFactory::create($config['apis']);
        });

        $this->app->alias(SlickAPIManager::class, 'slickapi-manager');
    }

    /**
     * @param array $config
     * @throws \Exception
     */
    public function guardAgainstInvalidConfiguration(array $config = [])
    {
        if (empty($config['apis'])) {
            throw InvalidConfiguration::apisNotSpecified();
        }
    }
}
