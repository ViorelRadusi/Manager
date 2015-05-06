<?php namespace Request\Manager;

use Request\Manager\Exceptions\ParserManagerException;

class BindedManagerParser {

  public static function parse(array $data) {
    switch(count($data)) {
    case 0:
      throw new ParserManagerException;
    case 1:
      list($manager) = $data;
      $relation = camel_case($manager);
      $args     = camel_case($manager);
      break;
    case 2:
      list($manager, $relation) = $data;
      $args = camel_case($manager);
      break;
    default:
      list($manager, $relation, $args) = $data;
    }
    $manager = ucfirst(strtolower($manager));

    return [$manager, $relation, $args];

  }
}
