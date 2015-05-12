<?php namespace Request\Manager\Exceptions;

class ArgumentNotArrayException extends \Exception{

  public function __construct($name, $args) {
    $this->message = "Arguments passed to `{$name}Manager` \$bindManager in not an array ... got `$args`";
  }

}
