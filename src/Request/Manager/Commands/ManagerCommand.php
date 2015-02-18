<?php namespace Request\Manager\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;

class ManagerCommand extends Command {

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'manager:make';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Creates a new Manager';

  /**
   * Create a new command instance.
   *
   * @return void
   */

  protected $generator;

  public function __construct(ManagerGenerator $generator)
  {
    $this->generator = $generator;
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function fire() {
    $name       = $this->argument('name');
    $ns         = $this->option('ns');
    $fill       = $this->option('fill');
    $model      = $this->option('model');
    $validates  = $this->option('validates');
    $validator  = $this->option('validator');

    $toReplace = compact('ns','fill','model','validates', 'validator');

    $this->generator->replace($toReplace)->save($ns, $name);


    $this->info("{$name}Manager Created");

  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return array(
      ['name', InputArgument::REQUIRED, 'The name of the Manager']
    );
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return array(
      ['ns'       , null, InputOption::VALUE_OPTIONAL, 'Set Other namespace' , Config::get("manager::mSpace")],
      ['fill'     , null, InputOption::VALUE_OPTIONAL, 'Set fillable fields' , ""],
      ['model'    , null, InputOption::VALUE_OPTIONAL, 'Set the model' , "User"],
      ['validates', null, InputOption::VALUE_OPTIONAL, 'Set if this should validate' , "false"],
      ['validator', null, InputOption::VALUE_OPTIONAL, 'Set what validator to use' , "\\SomeNamespace\\NewValidator"],
    );
  }

}
