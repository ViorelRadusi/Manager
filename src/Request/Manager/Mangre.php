<?php namespace Request\Manager;

use Request\Manager\Interfaces\ManagerInterface,
    Request\Manager\Exceptions\EmptyInputException,
    App;

abstract class Mangre extends MangreTraversal implements ManagerInterface {

  protected $instance, $selectedModel;

  public $fillabel = null;

  public function __construct(StorageGuard $guard) {
    method_exists($this, "beforeConstruct") && $this->beforeConstruct();

    $this->selectedModel = $this->setModel();
    property_exists($this, "model") && $this->selectedModel = $this->model;
    $this->listOfKeys = BindedManagerKey::getInstance();

    $this->instance      = App::make($this->selectedModel);

    $this->fillable      = $this->instance->getFillable();
    property_exists($this, "fill")  && $this->fillable = $this->fill;
    if(is_null($this->fillable)) throw \Exception("Empty fill property");

    parent::__construct($guard);

    $this->transformArgs = (object) $this->transformArgs;

    method_exists($this, "afterConstruct") && $this->afterConstruct();
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

    $input = $this->sanitize($input);
    $data  = $this->getData($input);


    method_exists($this, "beforeCreate") && $this->beforeCreate($input);

    $this->setBindInput("Create", $input);
    if(!is_null($this->bindManagerCreate))  $this->bind("Create", $this->bindManagerCreate);

    $this->guard && $this->check($input);
    $entry = $this->instance->create($data);

    if(!is_null($this->bindManagerCreate))
      foreach($this->bindManagerCreate as $manager){
        if($manager  instanceof BindedManager)  $manager->create($entry);
    }

    method_exists($this, "afterCreate") && $this->afterCreate($input, $entry);
    return $entry;
  }


  public function update(array $input, $id) {

    $input = $this->sanitize($input);

    $data = $this->getData($input, $id);


    $entry = in_array("SoftDeletingTrait", class_uses( get_class($this->instance)))
      ? $this->withTrashed()->find($id)
      : $this->find($id) ;

    method_exists($this, "beforeUpdate") && $this->beforeUpdate($input, $entry);

    $this->setBindInput("Update", $input);
    if(!is_null($this->bindManagerUpdate))  $this->bind("Update", $this->bindManagerUpdate, $entry);

    $this->guard && $this->check($input, $id);
    $entry->update($data);



    if(!is_null($this->bindManagerUpdate))
      foreach($this->bindManagerUpdate as $manager) {
        if($manager  instanceof BindedManager)  $manager->update($entry);
    }

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

  public function findWithTrash($id, $relationships = []) {
    return $this->_findBranch($relationships)->withTrashed()->find($id);
  }

  public function findInTrash($id, $relationships = []) {
    return $this->_findBranch($relationships)->onlyTrashed()->find($id);
  }

  public function restore($id) {
    $entry = $this->findInTrash($id);
    $entry->restore();
    return $entry;
  }

  public function deletePermanent() {
    $this->onlyTrashed()->all()->each(function($entry){
        $entry->forceDelete();
    });
  }

  private function setModel() {
    $split = explode('\\',get_called_class());
    $subclass = "\\" . end($split);
    return str_replace("Manager", "",$subclass);
  }

  private function _findBranch($relationships = []){
      return ($this->root)
         ?  $this->instance->get()
         :  $this->instance->with($relationships);
  }

  public function arr($obj) {
    return json_decode(json_encode($obj), true);
  }

}
