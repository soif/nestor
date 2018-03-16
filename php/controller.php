<?php
require_once(dirname(__FILE__).'/core.php');

class NestorController extends NestorCore{

	protected $action				= '';		//  action to perform
	protected $object				= '';		//  object to instance
	protected $method				= '';		//  method to invoke
	protected $param				= '';		//  param to pass to method

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
	public function Action_list(){
	}
	// ---------------------------------------------------------------------------------------
	public function Action_help(){
	}
	// ---------------------------------------------------------------------------------------
	public function Action_default(){
	}

	// ---------------------------------------------------------------------------------------
	public function ProcessObject($type){
		$types="{$type}s";
		if(!$this->object){
			return $this->_dieError("You must provide a $type", $types);
		}
		if(!$obj=$this->_GetObject($type, $this->object)){
			return $this->_dieError("Can't launch module: '{$this->object}' ", $types);
		}
		$method=$this->method;
		if(method_exists($obj, $method)){
			$result=$obj->$method("{$this->param}");
			
			if($err_msg=$obj->GetError()){
				return $this->_dieError($err_msg);
			}
		}
		else{
			$child=$this->conf[$types][$this->object][$type];
			return $this->_dieError("Cant find a method named '$method' from the [$child] $type named '{$this->object}' ");
		}
		return $obj;
	}

	// ----------------------------------------------------------------------------------------------------------------
	private function _GetObject($type, $obj_key){
		$types="{$type}s";
		if(!isset($this->conf[$types][$obj_key])){
			return false;
		}
		$obj_conf	= $this->conf[$types][$obj_key];
		$obj_name	= $obj_conf[$type];

		$obj_path	= "{$this->path_php}{$types}/{$obj_name}.php";
		if(!file_exists($obj_path)){
			die("Can't find a $type named $obj_name at $obj_path !");
		}

		require_once($this->path_php.$type.'.php');
		require_once($obj_path);
		$class="Nestor{$type}_{$obj_name}";
		return new $class($obj_conf);
	}

	// ---------------------------------------------------------------------------------------
	protected function _dieError($mess,$list=''){
		return false;
	}

}
?>