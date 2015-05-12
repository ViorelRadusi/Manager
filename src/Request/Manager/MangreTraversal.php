<?php namespace Request\Manager;

use App, Route;

abstract class MangreTraversal extends MangreBinder {

  protected $root = null, $chain = null;

  public function getRoot() {
    return $this->root;
  }

  public function setRoot(array $rootInfo) {
    $params = Route::current()->parameters();

    $class = array_shift($rootInfo);
    $this->instance = App::make($class);

    $this->chain = $rootInfo;
    foreach($rootInfo as $order => $chain)
      $this->instance = ($order % 2 == 0)
          ?  $this->instance->find($params[$chain])
          :  $this->instance->$chain();

    $this->root = App::make($class)->find($params[$this->chain[0]]);
    return $this;
  }

  public function chain($find) {
    $params = Route::current()->parameters();
    $segment = $this->root;
    foreach($this->chain as $order => $chain){
      $this->segment = ($order % 2 == 0)
          ?  $segment->find($params[$chain])
          :  $segment->$chain();
      if($chain == $find && $order % 2 == 1) return $segment;
    }

    return null;
  }

}
