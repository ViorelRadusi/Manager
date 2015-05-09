<?php namespace Request\Manager;

abstract class MangreBinder extends MangreValidation {

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

    throw new EmptyInputException($args);
  }

  private function checkValidaty($bind) {
    if($bind->getManager()->isValidating()) $bind->check();
    $nextBinds = $bind->getManager()->bindManager;
    if($nextBinds) {
      foreach($nextBinds as $key => $bindString){
        $nextBind = $this->extractBind($key, $bindString);
        $nextBind->getManager()->checkValidaty($nextBind);
      }
    }

  }

}
