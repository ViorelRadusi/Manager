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
<<<<<<< HEAD

  private function init($input , $id = null) {
=======
  private function init($input , $id = null) {

>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
    foreach($this->transforms as $key => $val)
      is_string($key)
        ? $this->$key = $this->callback($id, $key, $input , $val)
        : $this->initField($id, $val, $input);

<<<<<<< HEAD
    $fullAssocTransform = [];
    foreach($this->transforms as $key => $val) {
      if(is_int($key)) $fullAssocTransform[$val] = $val;
      else $fullAssocTransform[$key] = $val;
    }
    foreach(array_except($input, $fullAssocTransform) as $key => $val) $this->$key = $val;
=======
    foreach(array_except($input, array_keys($this->transforms)) as $key => $val) $this->$key = $val;
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
  }

  private function initField($id, $field, $input) {
    if(property_exists($this, $field)) return $this->$field;

<<<<<<< HEAD

     $this->$field = (array_key_exists($field, $input) && !empty($input[$field]))
=======
    return $this->$field = (array_key_exists($field, $input) && !empty($input[$field]))
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
      ? $input[$field]
      : (($id) ? $this->find($id)->$field : null);
  }

  private function callback($id, $prop, $input, $fn) {
    $val = $this->initField($id, $prop, $input);

<<<<<<< HEAD
    $t = get_object_vars($this->transformArgs);
    if(strpos($fn , "@")){
      list($class, $method) = explode("@", $fn);
      if(empty($t))
=======
    if(strpos($fn , "@")){
      list($class, $method) = explode("@", $fn);
      if(empty(get_object_vars($this->transformArgs)))
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
        return (array_key_exists($prop, $input) && $input[$prop]) ? $this->$prop = $class::$method($val) : $val;
      else
        return (array_key_exists($prop, $input) && $input[$prop]) ? $this->$prop = $class::$method($val, $this->transformArgs) : $val;
    }

    if(strpos($fn , "#")){
      list($class, $method) = explode("#", $fn);
<<<<<<< HEAD
      if(empty($t))
=======
      if(empty(get_object_vars($this->transformArgs)))
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
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
