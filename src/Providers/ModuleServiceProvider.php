<?php 

namespace Kuato\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     *
     * @return void
     */
    public function boot() {
        $this->app['Kuato\Contracts\ModuleServiceInterface']->boot();
    }

    public function register() {
        $this->app['Kuato\Contracts\ModuleServiceInterface']->register();
    }

}
