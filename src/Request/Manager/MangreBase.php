<?php namespace Request\Manager;

use App,
  Request\Manager\Exceptions\EmptyFillableException,
  Request\Manager\Exceptions\TransformerException;

abstract class MangreBase {

  protected $transforms = [], $optional = [], $sanitizeSkip = [], $defaults = [], $instance;

  public $fillabel = null;

  public function __construct() {
    $this->instance      = App::make($this->selectedModel);
    $this->fillable      = $this->instance->getFillable();
    property_exists($this, "fill")  && $this->fillable = $this->fill;
    if(empty($this->fillable)) throw new EmptyFillableException();
  }

  public function getData($input, $id = null) {
    $this->init($input, $id);
    return array_reduce($this->fillable , function($accepted, $prop){
      $accepted[$prop] = isset($this->$prop)
        ? $this->$prop
        : ( (array_key_exists($prop, $this->defaults)) ?   $this->defaults[$prop] : null);
      return $accepted;
     });
  }

  public function getInstance() {
    return $this->instance;
  }

  private function init($input, $id = null) {

    foreach($this->optional as $key => $val) $this->$val = $this->initField($id, $val, $input);

    foreach(array_except($input, $this->optional) as $key => $val) {
      if(is_array($val) && property_exists($this, $key))  $this->$key = array_merge($val, $this->$key);
      else                                                $this->$key = (isset($this->$key)) ? $this->$key :  $val;
    }

    if(!empty($this->transforms))
    foreach($this->transforms as $prop => $fn) {
      if(!is_string($prop)) throw new TransformerException();

      $this->$prop = $this->callback($id, $prop, $input, $fn);
    }

  }

  private function initField($id, $field, $input) {
    if(property_exists($this, $field)) return $this->$field;

    return (array_key_exists($field, $input) && !empty($input[$field]))
      ? $input[$field]
      : (($id) ? $this->find($id)->$field : null);
  }

  private function callback($id, $prop, $input, $fn) {

    $val = $this->initField($id, $prop, $input);

    if(!is_array($fn)) $fn = [$fn];
    $functions = array_shift($fn);
    $functions = explode("|", $functions);
    $function = array_shift($functions);
    array_unshift($fn, $val);

    if(strpos($function, "@") === false && strpos($function, "#") === false){
      $val = ((array_key_exists($prop, $input) && $input[$prop]) || !in_array($prop, $this->optional)) ? call_user_func_array($function,  $fn) : $val;
      return ($functions) ?  $this->multiTransform($val, $functions) : $val;
    }

    if(strpos($function, "@") !== false) list($class, $method) = explode("@", $function);
    if(strpos($function, "#") !== false) list($class, $method) = explode("#", $function);

    if($class === '') $class = get_class($this);

    $val = ((array_key_exists($prop, $input) && $input[$prop]) ||  !in_array($prop, $this->optional))
        ? call_user_func_array([ (strpos($function, "#") !== false)  ? App::make($class) : $class , $method],  $fn)
        :  $val;
    return ($functions) ?  $this->multiTransform($val, $functions) : $val;
  }

  private function multiTransform($value, $transformers) {
    return array_reduce($transformers , function($value, $transformer) {
      $value = call_user_func($transformer, $value);
      return $value;
    }, $value);
  }

  protected function sanitize($input) {
     return  array_map(function($entry) use ($input) {
      if(is_array($entry)) return  $this->sanitize($entry);
      $key = array_search($entry, $input);
      return (in_array($key, $this->sanitizeSkip)) ? $entry  : e($entry);
    }, $input);
  }

  public function arr($obj) {
    return json_decode(json_encode($obj), true);
  }

  public function obj($arr) {
    return json_decode(json_encode($arr));
  }

}
