<?php namespace Request\Manager\Interfaces;

  interface ManagerInterface {
    public function all();
    public function find($id);
    public function create(array $input);
    public function update(array $input, $id);
    public function delete($id);
    public function count();
  }
