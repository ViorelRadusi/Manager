<?php namespace Request\Manager;

use Request\Manager\Interfaces\ValidatorInterface,
    Request\Manager\Exceptions\ValidatorException,
    Validator as V ;

abstract class Validator implements ValidatorInterface {

  protected static $messages = [];

  public function isValid($input, $id) {
    $v = V::make($input, static::check($id), static::$messages);
    if($v->fails()) throw new ValidatorException($v->messages());
  }


}
