<?php namespace Request\Manager\Facades;

use Illuminate\Support\Facades\Facade;

class Manager extends Facade{

  protected static function getFacadeAccessor() {
    return 'manager';
  }
}
