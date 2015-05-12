<?php namespace Request\Manager;

abstract class MangreValidation extends MangreBase {

  protected $guard , $validates = true;

  public function __construct(StorageGuard $guard){
    $this->validates && $this->makeValidation($guard);
  }

<<<<<<< HEAD
  public function check($input, $id = ""){
    $this->guard->check($input, $id);
=======
  public function check($input){
    $this->guard->check($input);
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
  }

  private function makeValidation($guard){
    $this->guard = $guard;

    property_exists($this, "validator") ?
      $this->guard->setValidator($this->validator     , false):
      $this->guard->setValidator($this->selectedModel , true);
  }

  public function isValidating() {
    return $this->validates;
  }
}
