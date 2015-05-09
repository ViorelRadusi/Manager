<?php namespace Request\Manager;

class BindedManagerParser {

  public static function parse($name, array $data) {
    switch(count($data)) {
    case 0:
      $relation = camel_case($name);
      $args     = camel_case($name);
      $position = null;
    break;
    case 1:
      list($relation) = $data;
      $args           = camel_case($name);
      $position       = null;
    break;
    case 2:
      list($relation, $args) = $data;
      $position       = null;
    break;
    default:
      list($relation, $args, $position) = $data;
    }

    return [$relation, $args, $position];

  }
}
