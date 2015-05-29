<?php namespace Request\Manager;

use App, Request\Manager\Exceptions\EmptyFillableException;

abstract class MangreBase {

  protected $transforms = [], $transformArgs = [], $sanitizeSkip = [], $defaults = [], $instance;

  public $fillabel = null;

  public function __construct() {
    $this->instance      = App::make($this->selectedModel);
    $this->fillable      = $this->instance->getFillable();
    property_exists($this, "fill")  && $this->fillable = $this->fill;
    if(empty($this->fillable)) throw new EmptyFillableException();
    $this->transformArgs = (object) $this->transformArgs;
  }

  public function getData($input, $id = null) {
    $this->init($input, $id);
    foreach($this->fillable as $prop) {
      $accepted[$prop] = isset($this->$prop)
        ? $this->$prop
        : ( (array_key_exists($prop, $this->defaults)) ?   $this->defaults[$prop] : null);
    }

    return $accepted;
  }

  public function getInstance() {
    return $this->instance;
  }

  private function init($input , $id = null) {
    foreach($this->transforms as $key => $val)
      is_string($key)
        ? $this->$key = $this->callback($id, $key, $input , $val)
        : $this->initField($id, $val, $input);

    $fullAssocTransform = [];
    foreach($this->transforms as $key => $val) {
      if(is_int($key)) $fullAssocTransform[$val] = $val;
      else $fullAssocTransform[$key] = $val;
    }
    foreach(array_except($input, $fullAssocTransform) as $key => $val) {
      if(is_array($val) && property_exists($this, $key))  $this->$key = array_merge($val, $this->$key);
      else $this->$key = (isset($this->$key)) ? $this->$key :  $val;

    }
  }

  private function initField($id, $field, $input) {
    if(property_exists($this, $field)) return $this->$field;


     $this->$field = (array_key_exists($field, $input) && !empty($input[$field]))
      ? $input[$field]
      : (($id) ? $this->find($id)->$field : null);
  }

  private function callback($id, $prop, $input, $fn) {
    $val = $this->initField($id, $prop, $input);

    $t = get_object_vars($this->transformArgs);
    if(strpos($fn , "@")){
      list($class, $method) = explode("@", $fn);
      if(empty($t))
        return (array_key_exists($prop, $input) && $input[$prop]) ? $this->$prop = $class::$method($val) : $val;
      else
        return (array_key_exists($prop, $input) && $input[$prop]) ? $this->$prop = $class::$method($val, $this->transformArgs) : $val;
    }

    if(strpos($fn , "#")){
      list($class, $method) = explode("#", $fn);
      if(empty($t))
        return (array_key_exists($prop, $input) && $input[$prop]) ? $this->$prop = App::make($class)->$method($val) : $val;
      else{
        return  (array_key_exists($prop, $input) && $input[$prop]) ? $this->$prop = App::make($class)->$method($val, $this->transformArgs) : $val;
      }

    }
  }

  protected function sanitize($input) {
     return  array_map(function($entry) use ($input) {
      if(is_array($entry)) return  $this->sanitize($entry);
      $key = array_search($entry, $input);
      return (in_array($key, $this->sanitizeSkip)) ? $entry  : e($entry);
    }, $input);

  }
}
