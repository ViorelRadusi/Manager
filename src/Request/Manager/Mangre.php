<?php namespace Request\Manager;

use Request\Manager\Interfaces\ManagerInterface, App;

abstract class Mangre implements ManagerInterface {

  protected $instance, $selectedModel, $guard, $fillable, $validates = true;

  public function __construct(StorageGuard $guard) {

    $this->selectedModel = $this->setModel();
    property_exists($this, "model") && $this->selectedModel = $this->model;

    $this->instance      = App::make($this->selectedModel);

    $this->fillable      = $this->instance->getFillable();
    property_exists($this, "fill")  && $this->fillable = $this->fill;

    $this->validates && $this->makeValidation($guard);

    method_exists($this, "afterConstruct") && $this->afterConstruct();
  }

  public function find($id, $relationships = []) {
    return $this->instance->with($relationships)->find($id);
  }

  public function all($order = 'id', $get = null, $paginate = false) {
    $collection = $this->instance->orderBy($order);
    return ($paginate) ? $collection->paginate($paginate, $get) : $collection->get($get);
  }

  public function create($input) {
    $this->guard && $this->guard->check($input);
    return  $this->instance->create($this->getData($input));
  }

  public function update($input, $id) {
    $this->guard && $this->guard->check($input, $id);

    $entry = $this->find($id);
    $entry->update($this->getData($input, $id));
    return $entry;
  }

  public function delete($id) {
    $entry = $this->find($id);
    $entry->delete();
    return $entry;
  }

  public function count() {
    return $this->instance->count();
  }

  private function setModel(){
    $split = explode('\\',get_called_class());
    $subclass = "\\" . end($split);
    return str_replace("Manager", "",$subclass);
  }

  private function makeValidation($guard){
    $this->guard = $guard;

    property_exists($this, "validator") ?
      $this->guard->setValidator($this->validator, false) :
      $this->guard->setValidator($this->selectedModel    , true);
  }

  private function init($input , $id = null) {
    foreach($input as $key => $val) $this->$key = $val;

    if(property_exists($this, 'transforms'))
      foreach($this->transforms as $key => $val)
        is_string($key)
          ? $this->$key = $this->callback($id, $key, $input , $val)
          : $this->initField($id, $val, $input);
  }

  private function initField($id, $field, $input) {
    return $this->$field = (isset($input[$field]) && !empty($input[$field]) )
      ? $input[$field]
      : (($id) ? $this->find($id)->$field : null);
  }

  private function callback($id, $prop, $input, $fn) {
   
    $val = $this->initField($id, $prop, $input);

    if(strpos("@", $fn)){

      list($class, $method) = explode("@", $fn);

      return ($input[$prop]) ? $this->$prop = $class::$method($val) : $val;
    }

    if(strpos("#", $fn)){

      list($class, $method) = explode("#", $fn);

      return ($input[$prop]) ? $this->$prop = App::make($class)->$method($val) : $val;
    }

  
  }

  private function getData($input, $id = null) {
    $this->init($input, $id);
    foreach($this->fillable as $prop)
      $accepted[$prop] = isset($this->$prop) ? $this->$prop : null;

    return $accepted;
  }

}
