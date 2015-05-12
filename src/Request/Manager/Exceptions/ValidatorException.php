<?php namespace Request\Manager\Exceptions;

class ValidatorException extends \Exception{

  protected $errors;

  public function __construct($errors) {
    $this->errors = $errors;
  }

  public function getErrors() {
    return $this->errors;
  }

}
