<?php namespace Request\Manager;

class BindedManagerKey {

  private static $instance = null;
  private $listOfKeys = [];

  public static function getInstance() {
      if (null === self::$instance) self::$instance = new static();
      return self::$instance;
  }

  protected function __construct() { }

  private function __clone() { }

  private function __wakeup() { }


  public  function put($key, $value) {
    $index = 0;
    if(array_key_exists($key, $this->listOfKeys)) $key = $key . ++$index;
    $this->listOfKeys[ucfirst($key)] = $value;
  }

  public function get($key) {
    return isset($this->listOfKeys[$key]) ? $this->listOfKeys[$key] : null;
  }

  public function all() {
    return $this->listOfKeys;
  }



}

