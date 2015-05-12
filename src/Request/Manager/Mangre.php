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
<<<<<<< HEAD
    $this->listOfKeys = BindedManagerKey::getInstance();
=======
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a

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

<<<<<<< HEAD
    $this->setBindInput("Create", $input);
    if(!is_null($this->bindManagerCreate))  $this->bind("Create", $this->bindManagerCreate);
=======
    $this->setBindInput($input);
    if(!is_null($this->bindManager))  $this->bind($this->bindManager);
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a

    $this->guard && $this->check($input);
    $entry = $this->instance->create($data);

<<<<<<< HEAD
    if(!is_null($this->bindManagerCreate))
      foreach($this->bindManagerCreate as $manager){
        if($manager  instanceof BindedManager)  $manager->create($entry);
=======
    foreach($this->bindManager as $manager){
      if($manager  instanceof BindedManager)  $manager->create($entry);
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
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

<<<<<<< HEAD
    $this->setBindInput("Update", $input);
    if(!is_null($this->bindManagerUpdate))  $this->bind("Update", $this->bindManagerUpdate, $entry);
=======
    $this->setBindInput($input);
    if(!is_null($this->bindManager))  $this->bind($this->bindManager);
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a

    $this->guard && $this->check($input, $id);
    $entry->update($data);



<<<<<<< HEAD
    if(!is_null($this->bindManagerUpdate))
      foreach($this->bindManagerUpdate as $manager) {
        if($manager  instanceof BindedManager)  $manager->update($entry);
=======
    foreach($this->bindManager as $manager){
      if($manager  instanceof BindedManager)  $manager->update($entry);
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
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

<<<<<<< HEAD
  public function findWithTrash($id, $relationships = []) {
    return $this->_findBranch($relationships)->withTrashed()->find($id);
  }

  public function findInTrash($id, $relationships = []) {
    return $this->_findBranch($relationships)->onlyTrashed()->find($id);
  }

  public function restore($id) {
=======
  public function findWithTrash($id, $relationships = []){
    return $this->_findBranch($relationships)->withTrashed()->find($id);
  }

  public function findInTrash($id, $relationships = []){
    return $this->_findBranch($relationships)->onlyTrashed()->find($id);
  }

  public function restore($id){
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
    $entry = $this->findInTrash($id);
    $entry->restore();
    return $entry;
  }

<<<<<<< HEAD
  public function deletePermanent() {
=======
  public function deletePermanent(){
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
    $this->onlyTrashed()->all()->each(function($entry){
        $entry->forceDelete();
    });
  }

<<<<<<< HEAD
  private function setModel() {
=======
  private function setModel(){
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
    $split = explode('\\',get_called_class());
    $subclass = "\\" . end($split);
    return str_replace("Manager", "",$subclass);
  }

  private function _findBranch($relationships = []){
      return ($this->root)
         ?  $this->instance->get()
         :  $this->instance->with($relationships);
  }

<<<<<<< HEAD
  public function arr($obj) {
    return json_decode(json_encode($obj), true);
  }

=======
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a
}
