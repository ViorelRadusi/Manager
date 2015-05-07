<?php namespace Request\Manager\Interfaces;

interface ValidatorInterface {
  public function isValid($input, $id);
}
