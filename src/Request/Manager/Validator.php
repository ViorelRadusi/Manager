<?php namespace Request\Manager;

use Request\Manager\Interfaces\ValidatorInterface,
    Request\Manager\Exceptions\ValidatorException,
    Validator as V ;

abstract class Validator implements ValidatorInterface {

  protected static $messages = [];

  protected static $update   = [];

  public function isValid($input, $id = '') {
    $v = V::make($input, array_merge(static::check($id), static::$update), static::$messages);
    if($v->fails()) throw new ValidatorException($v->messages());
  }


}
