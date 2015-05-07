<?php namespace Request\Manager\Exceptions;

class EmptyInputException extends \Exception{

  public function __construct($key) {
    $this->message = "Input with key `$key` does not exist";
  }

}
