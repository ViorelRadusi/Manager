<?php namespace Request\Manager;

abstract class MangreBinder extends MangreValidation {

<<<<<<< HEAD
  protected $bindManagerCreate = null;
  protected $bindManagerUpdate = null;

  public function getInfo($key) {
    $key = ucfirst($key);
    return $this->listOfKeys->get($key) ?: null;
  }

  public function putInfo($key, $id) {
    $this->listOfKeys->put($key , $id);
  }

  public function getInfos() {
    return $this->listOfKeys->all();
  }

  public function getBind($which) {
    $bindManagerArray = "bindManager{$which}";
    return $this->$bindManagerArray;
  }

  public function setBind($which, $bind) {

    $bindManagerArray = "bindManager{$which}";
    return $this->$bindManagerArray = $bind;
  }

  public function setBindInput($which, $input) {
    $binder = "bindManager{$which}";
    $input = json_decode(json_encode($input));
    method_exists($this, $binder) && $this->{$binder} = $this->{$binder}($input);
  }

  public function bind($which, array $data, $entry = null) {
    $bindedArray = "bindManager{$which}";
    foreach($data as $manager => $bind) {
      $extract = $this->extractBind($which, $manager, $bind, $entry);
      $this->{$bindedArray}[$manager] = $extract;
      $extract->getManager()->checkValidaty($which, $extract);
    }
    return $this->$bindedArray;
  }

  private function extractBind($which, $manager, array $data, $entry = null) {
    list($relation, $args, $position) = BindedManagerParser::parse($manager, $data);
    if(is_array($args))                return new BindedManager($manager, $relation, json_decode(json_encode($args), true), $which, $position, $entry);
    if(property_exists($this, $args))  return new BindedManager($manager, $relation, $this->$args, $which, $position, $entry);
=======
  protected $bindManager = null;

  public function getBind(){
    return $this->bindManager;
  }

  public function setBind($bind){
    return $this->bindManager = $bind;
  }

  public function setBindInput($input) {
    method_exists($this, "bindManager") && $this->bindManager = $this->bindManager($input);
  }

  public function bind(array $data) {
    foreach($data as $manager => $bind) {
      $extract = $this->extractBind($manager, $bind);
      $this->bindManager[$manager] = $extract;
      $extract->getManager()->checkValidaty($extract);
    }
    return $this->bindManager;
  }

  private function extractBind($manager, array $data) {
    list($relation, $args, $position) = BindedManagerParser::parse($manager, $data);
    if(is_array($args))                return new BindedManager($manager, $relation, $args, $position);
    if(property_exists($this, $args))  return new BindedManager($manager, $relation, $this->$args, $position);
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a

    throw new EmptyInputException($args);
  }

<<<<<<< HEAD
  private function checkValidaty($which, $bind) {
    $bindedArray = "bindManager{$which}";
    if($bind->getManager()->isValidating()) $bind->check();
    $nextBinds = $bind->getManager()->$bindedArray;
    if($nextBinds) {
      foreach($nextBinds as $key => $bindString){
        $nextBind = $this->extractBind($which, $key, $bindString);
        $nextBind->getManager()->checkValidaty($which, $nextBind);
=======
  private function checkValidaty($bind) {
    if($bind->getManager()->isValidating()) $bind->check();
    $nextBinds = $bind->getManager()->bindManager;
    if($nextBinds) {
      foreach($nextBinds as $key => $bindString){
        $nextBind = $this->extractBind($key, $bindString);
        $nextBind->getManager()->checkValidaty($nextBind);
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
      }
    }

  }

}
