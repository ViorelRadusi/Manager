<?php namespace Request\Manager;

use  Request\Manager\Exceptions\ArgumentNotArrayException;

class BindedManager {

<<<<<<< HEAD
  public $name, $args, $position = null, $filter, $relation, $manager, $parentEntry;

  public function __construct($name, $relation, $args,$type, $position, $entry = null) {
    if(!is_array($args)) throw new  ArgumentNotArrayException($name, $args);
    $this->list     =  BindedManagerKey::getInstance();
    $this->name     = $name;
    $this->relation = $relation;
    $this->position = $position;
    $this->manager  = (new ManagerCreator)->make($this->name);
    $this->args     = $args;
    $this->filter   = $this->cleanArgs($this->args);
    try                  { $update_id = $entry->{$this->relation}->id; }
    catch(\Exception $e) { $update_id = null; }

    $this->filter   =   $this->manager->getData($this->args, $update_id);
    $this->manager->setBindInput($type, $this->args);
=======
  public $name, $args, $position, $filter, $relation, $manager, $parentEntry;

  public function __construct($name, $relation, $args, $position) {
    if(!is_array($args)) throw new  ArgumentNotArrayException($name, $args);
    $this->name     = $name;
    $this->relation = $relation;
    $this->args     = $args;
    $this->position = $position;
    $this->filter   = $this->cleanArgs($this->args);
    $this->manager  = (new ManagerCreator)->make($this->name);
    $this->manager->getData($this->args);
    $this->manager->setBindInput($this->filterData());
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a

  }

  private function cleanArgs($args) {
   $filter = [];
   if(is_array($args))
     foreach($args as $key => $value) if(!is_array($value))  $filter[$key] =  $value;

   return $filter;
  }

  public function getManager() {
    return $this->manager;
  }

  public function check() {
    return $this->manager->check($this->args);
  }

<<<<<<< HEAD
  public function create($parentEntry) {
    $args = json_decode(json_encode($this->args));

    if(method_exists($this->manager, "beforeBindCreate")) $this->manager->beforeBindCreate($args);

     $entry = $parentEntry->{$this->relation}()->create($this->manager->arr($this->filter));
     $this->list->put($this->relation , $entry->id);
     $this->chainNextManager("Create", $entry);

     if(is_array($this->manager->getBind("Create")))
       foreach($this->manager->getBind("Create") as $bind)
         $bind->create($this->parentEntry);

    if(method_exists($this->manager, "afterBindCreate")) $this->manager->afterBindCreate($args, $entry);
  }


  public function update($entry) {
   $args = json_decode(json_encode($this->args));
   if(is_array($this->position)) {
     $prop = key($this->position);
     $val  = array_shift($this->position);
     $entryRelation = $entry->{$this->relation}->find($val) ;
     $this->filter[$prop] = $entry->id;
   } else {
     $entryRelation = $entry->{$this->relation};
   }

   if(method_exists($this->manager, "beforeBindUpdate")) $this->manager->beforeBindUpdate($args, $entryRelation);


   $entryRelation->update($this->manager->arr($this->filter));

   $this->list->put($this->relation, $entryRelation->id);
   $this->chainNextManager("Update", $entryRelation);

   if(is_array($this->manager->getBind("Update")))
     foreach($this->manager->getBind("Update") as $bind)
       $bind->update($this->parentEntry);

   if(method_exists($this->manager, "afterBindUpdate")) $this->manager->afterBindUpdate($args, $entryRelation);
  }

  private function filterData() {
    $filter = [];
    foreach($this->manager->fillable as $value)
      if(array_key_exists($value, $this->filter))  $filter[$value] =  $this->filter[$value];
    return $filter;
  }

  private function chainNextManager($which, $relation) {
     $this->parentEntry = $this->manager->find($relation->id);

     $bind = $this->manager->getBind($which);

     if(!is_null($bind)) {
       $newBind  = $this->manager->bind($which, $bind, $relation);
       $this->manager->setBind($which, $newBind);
     }
=======
  public function create($entry) {

   $filter = $this->filterData();
   $entry = $entry->{$this->relation}()->create($filter);
   $this->chainNextManager($entry);

   if(is_array($this->manager->getBind()))
     foreach($this->manager->getBind() as $bind)
       $bind->create($this->parentEntry);
  }

  public function update($entry) {
   $entryRelation = ($this->position)? $entry->{$this->relation}->find($this->position) : $entry->{$this->relation};

   $filter = $this->filterData();

   $entry->{$this->relation}()->update($filter);
   $this->chainNextManager($entryRelation);

   if(is_array($this->manager->getBind()))
     foreach($this->manager->getBind() as $bind)
       $bind->update($this->parentEntry);
  }

  private function filterData() {
     $filter = [];
     foreach($this->manager->fillable as $value)
      if(array_key_exists($value, $this->filter))  $filter[$value] =  $this->filter[$value];
     return $filter;
  }

  private function chainNextManager($entry) {
   $this->parentEntry = $this->manager->find($entry->id);

   $bind = $this->manager->getBind();

   if(!is_null($bind)) {
     $newBind  = $this->manager->bind($bind);
     $this->manager->setBind($newBind);
   }
>>>>>>> 2e6caaf1974795681b806c46a1a96e67d3886c2a

  }

}
