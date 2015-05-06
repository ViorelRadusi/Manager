<?php namespace Request\Manager;

class BindedManager {

  protected $name, $args, $relation, $manager;

  public function __construct($name, $relation, $args) {
    $this->name     = $name;
    $this->relation = $relation;
    $this->args     = $args;
    $this->manager  = (new ManagerCreator)->make($this->name);
  }

  public function check() {
    return $this->manager->check($this->args);
  }

  public function create($entry) {
    return $entry->{$this->relation}()->create($this->args);
  }

  public function update($entry) {
    return $entry->{$this->relation}()->update($this->args);
  }

}
