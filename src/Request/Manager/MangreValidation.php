<?php namespace Request\Manager;

abstract class MangreValidation extends MangreBase {

  protected $guard , $validates = true, $errorType = false;

  public function __construct(StorageGuard $guard){
    $this->validates && $this->makeValidation($guard);
  }

  public function check($input, $id = ""){
    $this->guard->check($input, $id);
  }

  private function makeValidation($guard){
    $this->guard = $guard;

    property_exists($this, "validator") ?
      $this->guard->setValidator($this->validator     , false, $this->errorType):
      $this->guard->setValidator($this->selectedModel , true,  $this->errorType);
  }

  public function isValidating() {
    return $this->validates;
  }
}
