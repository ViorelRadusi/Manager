<?php namespace Request\Manager\Commands;

class ValidatorGenerator{

  public $tpl;

  public function __construct(){
    $this->tpl = file_get_contents(__DIR__ .  "/../Stubs/StubValidator.txt");
  }

  public function replace(array $replacements){
    foreach($replacements as $what => $with){
      $this->tpl = str_replace( "{{".strtoupper($what) . "}}", $with, $this->tpl);
    }
    return $this;
  }

  public function setRules($rules){

    $rules = str_replace("=", " => \"", $rules);
    $rules = preg_replace('/([a-zA-Z]+)\s/i', '"$1" ', $rules);
    $rules = str_replace(";", "\",\n\t", $rules);
    $rules .= '"';

    $this->tpl = str_replace( "{{RULES}}", $rules, $this->tpl);

    return $this;
  }

  public function save($where, $name){
    $path = app_path() . "/" . str_replace("\\", "/" , $where) . "/";
    file_put_contents($path . $name . "Validator.php", $this->tpl);
  }

}

