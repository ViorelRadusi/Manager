<?php namespace Request\Manager;

use Request\Manager\Interfaces\ManagerInterface, App, Route;

abstract class Mangre implements ManagerInterface {

  protected $root = null, $chain = null, $instance, $selectedModel, $guard, $fillable, $validates = true, $transforms = [], $transformArgs = [];

  public function __construct(StorageGuard $guard){
    method_exists($this, "beforeConstruct") && $this->beforeConstruct();

    $this->selectedModel = $this->setModel();
    property_exists($this, "model") && $this->selectedModel = $this->model;

    $this->instance      = App::make($this->selectedModel);

    $this->fillable      = $this->instance->getFillable();
    property_exists($this, "fill")  && $this->fillable = $this->fill;

    $this->validates && $this->makeValidation($guard);

    $this->transformArgs = (object) $this->transformArgs;

    method_exists($this, "afterConstruct") && $this->afterConstruct();
  }

  public function setRoot(array $rootInfo){

    $params = Route::current()->parameters();

    $class = array_shift($rootInfo);
    $this->instance = App::make($class);

    $this->chain = $rootInfo;
    foreach($rootInfo as $order => $chain)
      $this->instance = ($order % 2 == 0)
          ?  $this->instance->find($params[$chain])
          :  $this->instance->$chain();

    $this->root = App::make($class)->find($params[$this->chain[0]]);

    return $this;
  }

  public function chain($find){
    $params = Route::current()->parameters();
    $segment = $this->root;
    foreach($this->chain as $order => $chain){
      $this->segment = ($order % 2 == 0)
          ?  $segment->find($params[$chain])
          :  $segment->$chain();
      if($chain == $find && $order % 2 == 1) return $segment;
    }

    return null;
  }

  public function getRoot(){
    return $this->root;
  }

  public function getInstance(){
    return $this->instance;
  }

  public function find($id, $relationships = []) {
    return $this->_findBranch($relationships)->find($id);
  }

  public function with(array $relationships){
    $this->instance = $this->instance->with($relationships);
    return $this;
  }

  public function onlyTrashed(){
    $this->instance = $this->instance->onlyTrashed();
    return $this;
  }

  public function withTrashed(){
    $this->instance = $this->instance->withTrashed();
    return $this;
  }

  public function all($order = null, $get = ['*'], $paginate = false) {

    $get = ($this->root) ? ['*'] : $get;

    if($order)
      $this->instance = is_array($order) ?
        $this->instance->orderBy($order[0], $order[1]) :
        $this->instance->orderBy($order);

    return ($paginate) ? $this->instance->paginate($paginate, $get) : $this->instance->get($get);
  }

  public function create(array $input) {
    method_exists($this, "beforeCreate") && $this->beforeCreate($input);

    $this->guard && $this->guard->check($input);
    $created = $this->instance->create($this->getData($input));

    method_exists($this, "afterCreate") && $this->afterCreate($input, $created);
    return $created;
  }

  public function update(array $input, $id) {

    $this->guard && $this->guard->check($input, $id);

    $entry = $this->find($id);
    method_exists($this, "beforeUpdate") && $this->beforeUpdate($input, $entry);

    $entry->update($this->getData($input, $id));

    method_exists($this, "afterUpdate") && $this->afterUpdate($input, $entry);
    return $entry;
  }

  public function delete($id) {
    $entry = $this->find($id);

    method_exists($this, "beforeDelete") && $this->beforeDelete($entry);

    $entry->delete();

    method_exists($this, "afterDelete") && $this->afterDelete($entry);
    return $entry;
  }

  public function count() {
    return $this->instance->count();
  }

  public function first() {
    return $this->instance->first();
  }

  public function findWithTrash($id, $relationships = []){
    return $this->_findBranch($relationships)->withTrashed()->find($id);

  }

  public function findInTrash($id, $relationships = []){
    return $this->_findBranch($relationships)->onlyTrashed()->find($id);
  }

  public function restore($id){
    $entry = $this->findInTrash($id);
    $entry->restore();
    return $entry;
  }

  public function deletePermanent(){
    $this->onlyTrashed()->all()->each(function($entry){
        $entry->forceDelete();
    });
  }

  private function setModel(){
    $split = explode('\\',get_called_class());
    $subclass = "\\" . end($split);
    return str_replace("Manager", "",$subclass);
  }

  private function makeValidation($guard){
    $this->guard = $guard;

    property_exists($this, "validator") ?
      $this->guard->setValidator($this->validator     , false):
      $this->guard->setValidator($this->selectedModel , true);
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

  private function _findBranch($relationships = []){
      return ($this->root)
         ?  $this->instance->get()
         :  $this->instance->with($relationships);
  }

  private function getData($input, $id = null) {
    $this->init($input, $id);
    foreach($this->fillable as $prop)
      $accepted[$prop] = isset($this->$prop) ? $this->$prop : null;

    return $accepted;
  }

}
