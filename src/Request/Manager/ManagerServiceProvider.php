<?php namespace Request\Manager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class ManagerServiceProvider extends ServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->app->bind('manager', 'Request\Manager\ManagerCreator');
    $this->bindCommands();

  }

  public function boot()
  {

    $this->package('request/manager');



    AliasLoader::getInstance()
      ->alias("Manager", "Request\Manager\Facades\Manager");
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

  private function bindCommands(){

    $this->app->bind('managerCommand'   ,"\Request\Manager\Commands\ManagerCommand");
    $this->app->bind('managerValidator' ,"\Request\Manager\Commands\ValidatorCommand");
    $this->app->bind('managerFull'      ,"\Request\Manager\Commands\ManagerFullCommand");

    $this->commands(['managerCommand', 'managerValidator', 'managerFull']);
  }

}
