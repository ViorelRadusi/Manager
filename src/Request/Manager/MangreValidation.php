<?php namespace Request\Manager;

abstract class MangreValidation extends MangreBase {

  protected $guard, $selectedModel, $validates = true, $errorResponse = false;

  public function __construct(StorageGuard $guard){
    $this->selectedModel = $this->setModel();
    property_exists($this, "model") && $this->selectedModel = $this->model;

    $this->validates && $this->makeValidation($guard);

    parent::__construct();
  }

  public function check($input, $id = ""){
    $this->guard->check($input, $id);
  }

  private function makeValidation($guard){
    $this->guard = $guard;

    property_exists($this, "validator") ?
      $this->guard->setValidator($this->validator     , false, $this->errorResponse):
      $this->guard->setValidator($this->selectedModel , true,  $this->errorResponse);
  }

  public function isValidating() {
    return $this->validates;
  }

  private function setModel() {
    $split = explode('\\',get_called_class());
    $subclass = "\\" . end($split);
    $caller = (substr($subclass, -3) == 'One') ? true : false;
    return ($caller)
      ?  str_replace("ManagerOne", "",$subclass)
      :  str_replace("Manager", "",$subclass);
  }
}


