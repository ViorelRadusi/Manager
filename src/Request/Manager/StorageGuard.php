<?php namespace Request\Manager;

use  Request\Manager\Exceptions\ValidatorException,
     App, Redirect;

class StorageGuard {

  protected $validatorPath = "\ACME\Admin\Validators\{{CLS_NAME}}Validator";

  protected $validator ;

  public function setValidator($className, $useDefault){

    $this->errorResponse();

    $this->validator = ($useDefault)
      ? App::make(str_replace("{{CLS_NAME}}", $className, $this->validatorPath))
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
