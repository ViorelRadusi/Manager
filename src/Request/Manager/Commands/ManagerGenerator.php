<?php namespace Request\Manager\Commands;

class ManagerGenerator{

  public $tpl;

  public function printDoc($doc){
    $doc = ($doc) ? "Expand" : "Compact";
    $this->tpl = file_get_contents(__DIR__ .  "/../Stubs/StubManager{$doc}.txt");
    return $this;
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
    $this->tpl = (file_get_contents(__DIR__ . "/../Stubs/StubManagerExpand.txt"));
  }

}
