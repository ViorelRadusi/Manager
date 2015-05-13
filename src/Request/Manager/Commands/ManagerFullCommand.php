<?php namespace Request\Manager\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;

class ManagerFullCommand extends Command {

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'manager:for';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Creates a new Manager and a Validator for the that Manager';

  /**
   * Create a new command instance.
   *
   * @return void
   */

  protected $generator;

  public function __construct() {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function fire() {
    $names      = $this->argument('name');
    $ns         = $this->option('ns');
    $v_ns       = $this->option('v_ns');
    $fill       = $this->option('fill');
    $model      = $this->option('model');
    $validates  = $this->option('validates');
    $validator  = $this->option('validator');
    $doc        = $this->option('doc');
    $plain      = $this->option('plain');
    $rules      = $this->option('rules');


    $this->call("manager:make",['name' => $names,
      '--ns'        =>  $ns,
      '--fill'      =>  $fill,
      '--model'     =>  $model,
      '--validates' =>  $validates,
      '--doc'       =>  $doc,
      '--plain'     =>  $plain,
    ]);

    $this->call("manager:validator",['name' => $names,
      '--v_ns'   =>  $v_ns,
      '--rules'  =>  $rules,
    ]);


  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments() {
    return [
      ['name', InputArgument::REQUIRED, 'The name of the Manager']
    ];
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions() {
    return [
      ['ns'         , null  , InputOption::VALUE_OPTIONAL, 'Set manager   namespace' , Config::get("manager::mSpace")],
      ['v_ns'       , null  , InputOption::VALUE_OPTIONAL, 'Set validator namespace' , Config::get("manager::vSpace")],
      ['fill'       , null  , InputOption::VALUE_OPTIONAL, 'Set fillable fields' , ""],
      ['model'      , null  , InputOption::VALUE_OPTIONAL, 'Set the model' , "User"],
      ['doc'        , null  , InputOption::VALUE_NONE    , 'Outputs the comments to override defaults'],
      ['plain'      , null  , InputOption::VALUE_NONE    , 'Outputs  an empty manager' ],
      ['validates'  , null  , InputOption::VALUE_OPTIONAL, 'Set if this should validate' , "false"],
      ['validator'  , null  , InputOption::VALUE_OPTIONAL, 'Set what validator to use' , "\\\\SomeNamespace\\\\NewValidator"],
      ['rules'      , null  , InputOption::VALUE_OPTIONAL, 'Add the rules' , ""],
    ];
  }

}
