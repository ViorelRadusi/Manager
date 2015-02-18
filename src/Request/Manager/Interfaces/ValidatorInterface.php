<?php namespace Request\Manager\Interfaces;

interface ValidatorInterface{
  public function checkStore($input);
  public function checkUpdate($id, $input);
}
