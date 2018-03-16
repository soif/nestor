<?php
require_once(dirname(dirname(__FILE__)).'/controller.php');

class NestorController_command extends NestorController{
	protected $invocation 		='command';

	// command lines arguments
	private $args				= array();	// command line arguments
	private $bin				= '';		// binary name of the invoked command
	
	// command lines flags
	private $flag_debug			= false;

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
		$obj= $this->ProcessObject('module');
		if($this->flag_debug and $obj->log_commands){
			echo "# Command DEBUG:\n";
			echo implode("\n",$obj->log_commands);
			echo "\n";
		}
	}


	// ---------------------------------------------------------------------------------------
	private	$actions_desc=array(
		'module'		=> "perform an action from the selected module (ie display text, get data...)",
		'list'			=> "list modules defined in the configuration file",
		'help'			=> "Show full help"
	);
	private	$actions_usage=array(
		'module'		=> "module	MODULE METHOD PARAM [options]",
		'list'			=> "list modules",
		'help'			=> "help"
	);

	// ---------------------------------------------------------------------------------------
	public function Command_usage(){
		echo "* Usage             : {$this->bin} ACTION [PARMS...] [options]\n";
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
	public function Action_list(){
		$type=$this->object;
		switch ($type) {
			case 'modules':
				echo "Available Modules are: \n";
				foreach($this->conf['modules'] as $conf => $arr){
					echo "  - $conf : {$arr['module']} module\n";
				}
				break;
			default:
				$this->_dieError ("Unknown List type '$type'");
				break;
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
			$this->command_list($list);
		}
		else{
			echo "\n";
		}
		exit(1);
	}




	// ############################################################################################
	// #### PRIVATE ###############################################################################
	// ############################################################################################

	// -------------------------------------------------------------
	private function _ParseCommandLine(){
		global $argv;
		$this->args		= $this->_ParseArguments($argv);
		$this->bin 		= basename($this->args['commands'][0]);
		isset($this->args['commands'][1])	and	$this->action	= $this->args['commands'][1];
		isset($this->args['commands'][2])	and	$this->object	= $this->args['commands'][2];
		isset($this->args['commands'][3])	and	$this->method	= $this->args['commands'][3];
		isset($this->args['input'][4])		and	$this->param	= $this->args['input'][4]; // allow NEGATIVE numbers

		isset($this->args['flags']['d'])		and $this->flag_debug	= (boolean) $this->args['flags']['d'];		
		
		//$this->arg_from			= $this->args['vars']['from'];

	}

	// -------------------------------------------------------------
	// http://php.net/manual/en/features.commandline.php#78804
	private function _ParseArguments($argv) {
		$_ARG = array();
		foreach ($argv as $arg) {
			if (preg_match('#^-{1,2}([a-zA-Z0-9]*)=?(.*)$#', $arg, $matches)) {
				$key = $matches[1];
				switch ($matches[2]) {
					case '':
					case 'true':
						$arg = true;
						break;
					case 'false':
						$arg = false;
						break;
					default:
						$arg = $matches[2];
				}
		   
				/* make unix like -afd == -a -f -d */			
				if(preg_match("/^-([a-zA-Z0-9]+)/", $matches[0], $match)) {
					$string = $match[1];
					for($i=0; strlen($string) > $i; $i++) {
						$_ARG['flags'][$string[$i]] = true;
					}
				}
				else {
					$_ARG['vars'][$key] = $arg;	  
				}			
			}
			else {
				$_ARG['commands'][] = $arg;
			}		
		}
		$_ARG['commands'][0] = basename($_ARG['commands'][0]);
		$_ARG['input']=$argv;
		return $_ARG;	
	}
}
?>