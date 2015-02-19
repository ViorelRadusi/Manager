<?php namespace Request\Manager\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Config;

class ValidatorCommand extends Command {

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'manager:validator';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Creates a new Validator';

  /**
   * Create a new command instance.
   *
   * @return void
   */

  protected $generator;

  public function __construct(ValidatorGenerator $generator)
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
    $names      = $this->argument('name');
    $ns         = $this->option('ns');
    $rules      = $this->option('rules');
    $namesArray = explode(":", $names);

    foreach($namesArray as $name){
      $name  = ucfirst(trim($name));
      $toReplace = compact('name', 'ns');
      $this->generator->replace($toReplace)->setRules($rules)->save($ns, $name);
      $this->info("{$name}Validator Created");
    }

    $this->info("Finished!");

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
      ['ns'     , null  , InputOption::VALUE_OPTIONAL, 'Set Other namespace' , Config::get("manager::vSpace")],
      ['rules'  , null  , InputOption::VALUE_OPTIONAL, 'Add the rules' , ""],
    );
  }

}
