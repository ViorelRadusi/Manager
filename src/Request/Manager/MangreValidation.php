<?php namespace Request\Manager;

abstract class MangreValidation extends MangreBase {

  protected $guard , $validates = true;

  public function __construct(StorageGuard $guard){
    $this->validates && $this->makeValidation($guard);
  }

  public function check($input){
    $this->guard->check($input);
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
