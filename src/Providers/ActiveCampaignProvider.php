<?php

namespace Kingscode\ActiveCampaignApi\Providers;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Support\ServiceProvider;

class ActiveCampaignProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/activecampaign.php' => config_path('activecampaign.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../config/logging.php', 'logging.channels'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ActiveCampaign',  function($app) {
            $config = $app['config']['activecampaign'];
            return new \ActiveCampaign($config['url'], $config['key']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [\ActiveCampaign::class];
    }
}
