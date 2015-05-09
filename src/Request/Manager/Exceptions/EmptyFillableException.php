<?php namespace Request\Manager\Exceptions;

class EmptyFillableException extends \Exception{

  public function __construct() {
    $this->message = "Empty fill property! Set the models fillable propery or the Manager fill property";
  }

}
