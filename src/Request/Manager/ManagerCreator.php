<?php namespace Request\Manager;

use Config, App;

class ManagerCreator  {

  public function make($managerClass) {
    $class = Config::get('manager::mSpace') . "\\" . $managerClass . "Manager";
    return App::make($class);
  }

  public function one($managerClass) {
    $class = Config::get('manager::mSpace') . "\\" . $managerClass . "ManagerOne";
    return App::make($class);
  }

}
