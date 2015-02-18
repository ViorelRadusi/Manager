<?php namespace Request\Manager;

use Request\Manager\Interfaces\ValidatorInterface,
    Request\Manager\Exceptions\ValidatorException,
    Validator as V ;

abstract class Validator implements ValidatorInterface {

  protected static $messages = [];

  public function checkStore($input) {
    $v = V::make($input, static::store(), static::$messages);
    if($v->fails()) throw new ValidatorException($v->messages());
  }

  public function checkUpdate($input, $id) {
    $v = V::make($input, static::update($id), static::$messages);
    if($v->fails()) throw new ValidatorException($v->messages());
  }

}
