<?php namespace Request\Manager;
use App;


abstract class MangreBase {

  protected $transforms = [], $transformArgs = [];

  public function getData($input, $id = null) {
    $this->init($input, $id);
    foreach($this->fillable as $prop)
      $accepted[$prop] = isset($this->$prop) ? $this->$prop : null;

    return $accepted;
  }
  private function init($input , $id = null) {

    foreach($this->transforms as $key => $val)
      is_string($key)
        ? $this->$key = $this->callback($id, $key, $input , $val)
        : $this->initField($id, $val, $input);

    foreach(array_except($input, array_keys($this->transforms)) as $key => $val) $this->$key = $val;
  }

  private function initField($id, $field, $input) {
    if(property_exists($this, $field)) return $this->$field;

    return $this->$field = (array_key_exists($field, $input) && !empty($input[$field]))
      ? $input[$field]
      : (($id) ? $this->find($id)->$field : null);
  }

  private function callback($id, $prop, $input, $fn) {
    $val = $this->initField($id, $prop, $input);

    if(strpos($fn , "@")){
      list($class, $method) = explode("@", $fn);
      if(empty(get_object_vars($this->transformArgs)))
        return (array_key_exists($prop, $input) && $input[$prop]) ? $this->$prop = $class::$method($val) : $val;
      else
        return (array_key_exists($prop, $input) && $input[$prop]) ? $this->$prop = $class::$method($val, $this->transformArgs) : $val;
    }

    if(strpos($fn , "#")){
      list($class, $method) = explode("#", $fn);
      if(empty(get_object_vars($this->transformArgs)))
        return (array_key_exists($prop, $input) && $input[$prop]) ? $this->$prop = App::make($class)->$method($val) : $val;
      else{
        return  (array_key_exists($prop, $input) && $input[$prop]) ? $this->$prop = App::make($class)->$method($val, $this->transformArgs) : $val;
      }

    }
  }

  protected function sanitize($input) {
     return  array_map(function($entry){
      if(is_array($entry)) return  $this->sanitize($entry);
      return e($entry);
    }, $input);

  }
}
