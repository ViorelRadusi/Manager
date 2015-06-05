<?php namespace Request\Manager;

use  Request\Manager\Exceptions\ValidatorException,
     Config, App, Redirect, Response, Input;

class StorageGuard {

  protected $validator ;

  public function setValidator($className, $useDefault, $errorResponse) {

    $this->errorResponse = $errorResponse;

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

      $noArrayInput = array_filter(Input::all(), function($in) {

        if(!is_array($in)) return true;

        return !is_int(key($in));
      });

      if($this->errorResponse && $this->errorResponse  === 'json') return Response::json($e->getErrors(), 403);
      if($this->errorResponse && $this->errorResponse  === 'view') return Redirect::back()->withInput($noArrayInput)->withErrors($e->getErrors());

      if(Config::get("manager::errorResponse") === 'view') return Redirect::back()->withInput($noArrayInput)->withErrors($e->getErrors());
      if(Config::get("manager::errorResponse") === 'json') return Response::json($e->getErrors(), 403);
    });

  }

}
