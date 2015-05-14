<?php namespace Request\Manager;

use  Request\Manager\Exceptions\ValidatorException,
     Config, App, Redirect, Response;

class StorageGuard {

  protected $validator ;

  public function setValidator($className, $useDefault, $errorType) {

    $this->errorType = $errorType;

    $this->errorResponse();

    $this->validator = ($useDefault)
      ? App::make(Config::get("manager::vSpace") . $className . "Validator")
      : App::make($className);
  }

  public function check($input, $id = ''){
      $this->validator->isValid($input, $id);
  }


  private function errorResponse() {
    App::error(function(ValidatorException $e) {

      if($this->errorType && $this->errorType  === 'json') return Response::json($e->getErrors(), 403);
      if($this->errorType && $this->errorType  === 'view') return Redirect::back()->withInput()->withErrors($e->getErrors());

      if(Config::get("manager::errorResponse") === 'view') return Redirect::back()->withInput()->withErrors($e->getErrors());
      if(Config::get("manager::errorResponse") === 'json') return Response::json($e->getErrors(), 403);
    });

  }

}
