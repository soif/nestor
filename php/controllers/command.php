<?php
require_once(dirname(dirname(__FILE__)).'/controller.php');

class NestorController_command extends NestorController{
	protected $invocation 		='command';

	// command lines arguments
	private $args				= array();	// command line arguments
	private $bin				= '';		// binary name of the invoked command
	private $object				= '';		//  object to instance
	private $method				= '';		//  method to invoke
	private $param				= '';		//  param to pass to method
	
/*
	// ----------------------------------------------------------------------------------------------------------------
	public function __construct($cfg){
		parent::__construct();
	}
*/

	// ---------------------------------------------------------------------------------------
	public function run(){
		$this->_ParseCommandLine();
		$this->SwitchAction();
		exit(0);
	}

	// ---------------------------------------------------------------------------------------
	public function Action_default(){
		echo "Invalid Command! \n";
		$this->Command_usage();
		echo "* Use '{$this->bin} help' to list all options\n";
		echo "\n";
	}


	// ---------------------------------------------------------------------------------------
	public function Action_module(){
		$obj= $this->ProcessObject('module', $this->object, $this->method, $this->param);
		if($this->flag_debug and $obj->log_commands){
			echo "# Command DEBUG:\n";
			echo implode("\n",$obj->log_commands);
			echo "\n";
		}
	}

	// ---------------------------------------------------------------------------------------
	public function Action_source(){
		$cfg = $this->conf['sources'][$this->object];
		isset($cfg['method']) and $method=$cfg['method'] or  $method='';
		$obj=$this->ProcessObject('source', $this->object, $method, $cfg['param']);		
		$new_data=$obj->GetFreshdata();
		echo $new_data."\n";
	}

	// ---------------------------------------------------------------------------------------
	private	$actions_desc=array(
		'module'		=> "perform an action from the selected module (ie display text)",
		'source'			=> "fetch data  from the selected data source ",
		'list'			=> "list modules defined in the configuration file",
		'help'			=> "Show full help"
	);
	private	$actions_usage=array(
		'module'		=> "module	MODULE METHOD PARAM [options]",
		'source'		=> "source	SOURCE [options]",
		'list'			=> "list modules",
		'help'			=> "help"
	);

	// ---------------------------------------------------------------------------------------
	public function Command_usage(){
		echo "* Usage             : {$this->bin} ACTION [PARAMS...] [options]\n";
		echo "\n";
		echo "* Valid Actions : \n";
		foreach($this->actions_desc as $k => $v){
			echo "  - ".str_pad($k,15)." : $v\n";
		}
		echo "\n";
	}

	// ---------------------------------------------------------------------------------------
	public function Action_help(){
		$bin= $this->bin;
		echo "Nestor v";
		echo $this->GetNestorVersion();
		echo "\n\n";
		$this->Command_usage();
		echo "* Actions Usage: \n";
		foreach($this->actions_usage as $k => $usage){
			echo "  - ".str_pad($k,15)." : $bin $usage\n";
		}
		echo <<<EOF

* OPTIONS :
	-d  : Debug (show commands used)

EOF;
	}

	// ---------------------------------------------------------------------------------------
	public function Action_list($types=''){
		isset($types) or $types=$this->object.'s';
		$type=preg_replace('#s$#','',$types);
		if(isset($this->conf[$types])){
			echo "Available $types are: \n";
			foreach($this->conf[$types] as $conf_name => $arr){
				$parent='';
				isset($arr['type']) and $parent=": [{$arr['type']}] $type";
				echo "  - $conf_name $parent\n";
			}
		}
		else{
			$this->_dieError ("Unknown List type '$types'");
		}
		echo "\n";
	}




	// ############################################################################################
	// #### PROTECTED #############################################################################
	// ############################################################################################

	// ---------------------------------------------------------------------------------------
	protected function _dieError($mess,$list=''){
		echo "\n";
		echo "\033[31mFATAL ERROR: $mess !!!";
		echo "\033[0m\n";
		if($list){
			echo "\n";
			$this->Action_list($list);
		}
		else{
			echo "\n";
		}
		exit(1);
	}



	// ############################################################################################
	// #### PRIVATE ###############################################################################
	// ############################################################################################
}
?>