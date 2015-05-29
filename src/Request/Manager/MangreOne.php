<?php namespace Request\Manager;

abstract class MangreOne extends MangreValidation {

  protected $key = "key" ,  $value = "value" , $group = 'group', $description = 'description';

  public function __construct(StorageGuard $guard) {
    method_exists($this, "beforeConstruct") && $this->beforeConstruct();
    parent::__construct($guard);
    method_exists($this, "afterConstruct") && $this->afterConstruct();
  }

  public function set($key, $value) {
    if(!in_array($key, ['key', 'value', 'group', 'description'])) throw new \Exception('Invalid Key');
    $this->$key = $value;
    return $this;
  }

  public function get($key) {
    return $this->instance->where($this->key, $key)->first()->{$this->value};
  }

  public function all($order = null, $get = ['*'], $paginate = false) {
    if($order)
      $this->instance = is_array($order) ?
        $this->instance->orderBy($order[0], $order[1]) :
        $this->instance->orderBy($order);
    return ($paginate) ? $this->instance->paginate($paginate, $get) : $this->instance->get($get);
  }

  public function find($key) {
    return $this->instance->where($this->key, $key)->first();
  }

  public function findGroup($group){
    return $this->instance->where($this->group, $group)->get();
  }

  public function create(array $array, $key) {
    $data = array_merge([$this->$key => $key], $this->prepare($array) );
    $entry = $this->instance->create($data);
    return $entry;
  }

  public function update(array $array, $key) {
    $entry = $this->find($key);
    $entry->update($this->prepare($array));
    return $entry;
  }

  public function delete() {
    $entry = $this->instance->where($this->key, $key);
    $entry->delete();
    return $entry;
  }

  private function prepare($array) {
     return  [
        $this->value       => $array[$this->value],
        $this->group       => $array[$this->group],
        $this->description => $array[$this->description]
      ];
  }

}


