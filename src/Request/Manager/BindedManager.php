<?php namespace Request\Manager;

use  Request\Manager\Exceptions\ArgumentNotArrayException;

class BindedManager {

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

  }

}
