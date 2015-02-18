<?php namespace Request\Manager;

use  Request\Manager\Exceptions\ValidatorException,
     Config, App, Redirect;

class StorageGuard {

  protected $validator ;

  public function setValidator($className, $useDefault){

    $this->errorResponse();

    $this->validator = ($useDefault)
      ? App::make(Config::get("manager::vSpace") . $className . "Validator")
      : App::make($className);
  }

  public function checkStore($input){
      $this->validator->checkStore($input);
  }

  public function checkUpdate($input, $id){
      $this->validator->checkUpdate($input, $id);
  }

  private function errorResponse() {
    App::error(function(ValidatorException $e) {
        return Redirect::back()->withInput()->withErrors($e->getErrors());
    });

  }

}
