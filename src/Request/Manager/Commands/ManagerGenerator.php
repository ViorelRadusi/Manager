<?php namespace Request\Manager\Commands;

class ManagerGenerator{

  public $tpl;

  public function __construct(){
    $this->tpl = (file_get_contents(__DIR__ . "/../Stubs/StubManager.txt"));
  }

  public function replace(array $replacements){
    foreach($replacements as $what => $with){
      $this->tpl = str_replace( "{{".strtoupper($what) . "}}", $with, $this->tpl);
    }
    return $this;
  }

  public function save($where, $name){

    $path = app_path() . "/" . str_replace("\\", "/" , $where) . "/";
    file_put_contents($path . $name . "Manager.php", $this->tpl);

  }

}
