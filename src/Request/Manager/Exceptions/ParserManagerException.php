<?php namespace Request\Manager\Exceptions;

class ParserManagerException extends \Exception{

  public function __construct(){
    $this->message = "bindManager need at least the name of the manager to bind to";
  }

}
