<?php
require_once(dirname(__FILE__).'/core.php');

class NestorController extends NestorCore{

	protected $action				= '';		//  action to perform

	protected $use_objects_cache	=true;
	protected $objets_cache			=array();

	// command lines flags
	protected $flag_debug			= false;

/*
	// ----------------------------------------------------------------------------------------------------------------
	public function __construct($cfg){
		parent::__construct();
	}
*/

	// ---------------------------------------------------------------------------------------
	public function SwitchAction(){
		switch ($this->action) {
			case 'module':
				$this->Action_module();
				break;
			case 'source':
				$this->Action_source();
				break;
			case 'list':
				$this->Action_list();
				break;
			case 'help':
				$this->Action_help();
				break;
			default:
				$this->Action_default();
				break;
		}
	}
	// ---------------------------------------------------------------------------------------
	public function Action_module(){
	}
	// ---------------------------------------------------------------------------------------
	public function Action_source(){
	}
	// ---------------------------------------------------------------------------------------
	public function Action_list(){
	}
	// ---------------------------------------------------------------------------------------
	public function Action_help(){
	}
	// ---------------------------------------------------------------------------------------
	public function Action_default(){
	}

	// ---------------------------------------------------------------------------------------
	public function ProcessObject($type,$object,$method,$param=null){
		$types="{$type}s";
		if(!$object){
			return $this->_dieError("You must provide a $type", $types);
		}
		if(!$o=$this->GetObject($type, $object)){
			return $this->_dieError("Can't launch $type : '{$object}' ", $types);
		}
		$method or $method='default'; //default method
		if(method_exists($o, $method)){
			$result=$o->$method($param);
			
			if($err_msg=$o->GetError()){
				return $this->_dieError($err_msg);
			}
		}
		else{
			$child=$this->conf[$types][$object][$type];
			return $this->_dieError("Cant find a method named '$method' from the [$child] $type named '{$object}' ");
		}
		return $o;
	}

	// ----------------------------------------------------------------------------------------------------------------
	public function GetObject($type, $obj_key){
		if($this->use_objects_cache){
			if(isset($this->objects_cache[$type][$obj_key])){
				return $this->objects_cache[$type][$obj_key];
			}
		}

		$types="{$type}s";
		if(!isset($this->conf[$types][$obj_key])){
			return false;
		}
		
		$obj_conf	= $this->conf[$types][$obj_key];
		//isset($obj_conf[$type]) and $obj_name	= $obj_conf[$type] or $obj_name= $obj_key;
		$obj_name	= $obj_conf['type'];
		
		$obj_path	= "{$this->path_php}{$types}/{$obj_name}.php";
		if(!file_exists($obj_path)){
			die("Can't find a $type named $obj_name at $obj_path !");
		}

		require_once($this->path_php.$type.'.php');
		require_once($obj_path);
		$class="Nestor{$type}_{$obj_name}";
		$obj = new $class($obj_conf);
		$obj->debug_mode=$this->debug_mode;
		if($this->use_objects_cache){
			$this->objects_cache[$type][$obj_key]=$obj;
		}
		return $obj;
	}

	// ---------------------------------------------------------------------------------------
	protected function _dieError($mess,$list=''){
		return false;
	}


	// ############################################################################################
	// #### PROTECTED #############################################################################
	// ############################################################################################

	// -------------------------------------------------------------
	protected function _ParseCommandLine(){
		global $argv;
		$this->args		= $this->_ParseArguments($argv);
		$this->bin 		= basename($this->args['commands'][0]);
		isset($this->args['commands'][1])	and	$this->action	= $this->args['commands'][1];
		isset($this->args['commands'][2])	and	$this->object	= $this->args['commands'][2];
		isset($this->args['commands'][3])	and	$this->method	= $this->args['commands'][3];
		isset($this->args['input'][4])		and	$this->param	= $this->args['input'][4]; // allow NEGATIVE numbers

		isset($this->args['flags']['d'])		and $this->flag_debug	= (boolean) $this->args['flags']['d'];
		
		if($this->flag_debug){
			$this->debug_mode=true;
		}
		
		//$this->arg_from			= $this->args['vars']['from'];
	}

	// -------------------------------------------------------------
	// http://php.net/manual/en/features.commandline.php#78804
	protected function _ParseArguments($argv) {
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