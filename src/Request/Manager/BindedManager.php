<?php namespace Request\Manager;


use  Request\Manager\Exceptions\ArgumentNotArrayException;


class BindedManager {

  protected $name, $args, $filter, $relation, $manager, $parentEntry;

  public function __construct($name, $relation, $args) {
    if(!is_array($args)) throw new  ArgumentNotArrayException($name, $args);
    $this->name     = $name;
    $this->relation = $relation;
    $this->args     = $args;
    $this->filter   = $this->cleanArgs($this->args);
    $this->manager  = (new ManagerCreator)->make($this->name);

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
    return $this->getManager()->check($this->args);
  }

  public function create($entry) {

   $filter = $this->filterData();
   $entry = $entry->{$this->relation}()->create($filter);
   if($this->chainNextManager($entry)) return $this->getManager()->getBind()->create($this->parentEntry);
  }

  public function update($entry) {
   $entryRelation = $entry->{$this->relation};

   $filter = $this->filterData();
   $entry->{$this->relation}()->update($filter);
   if($this->chainNextManager($entryRelation))  return $this->getManager()->getBind()->update($this->parentEntry);
  }

  private function filterData() {
     $filter = [];
     foreach($this->getManager()->fillable as $value)
      if(array_key_exists($value, $this->filter))  $filter[$value] =  $this->filter[$value];
     return $filter;
  }

  private function chainNextManager($entry) {
   $this->getManager()->getData($this->args);
   $this->parentEntry = $this->getManager()->find($entry->id);

   $bind = $this->getManager()->getBind();
   if(!is_null($bind)) {
     $newBind  = $this->getManager()->bind($bind);
     $this->getManager()->setBind($newBind);
   }
   return ($this->getManager()->getBind() instanceof BindedManager);
  }

}
