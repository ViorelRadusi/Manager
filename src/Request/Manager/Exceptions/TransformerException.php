<?php namespace Request\Manager\Exceptions;

class TransformerException extends \Exception{

  public function __construct() {
    $this->message = "`\$transforms` propertie must be an  associative array";
  }

}
