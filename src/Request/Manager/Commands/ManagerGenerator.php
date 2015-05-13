<?php namespace Request\Manager\Commands;

class ManagerGenerator{

  public $tpl;

  public function printDoc($doc, $plain) {
    $type = "Compact";

    if($doc)
      $type = "Expand";
    elseif($plain)
      $type = "Plain";

    $this->tpl = file_get_contents(__DIR__ .  "/../Stubs/StubManager{$type}.txt");
    return $this;
  }

  public function replace(array $replacements) {
    foreach($replacements as $what => $with){
      $this->tpl = str_replace( "{{".strtoupper($what) . "}}", $with, $this->tpl);
    }
    return $this;
  }

  public function save($where, $name) {
    $path = app_path() . "/" . str_replace("\\", "/" , $where) . "/";
    file_put_contents($path . $name . "Manager.php", $this->tpl);
    $this->tpl = (file_get_contents(__DIR__ . "/../Stubs/StubManagerExpand.txt"));
  }

}
