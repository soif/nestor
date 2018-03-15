<?php
class NestorCore {
	private	$nestor_version	="0.5.0a";

	protected $path_root;				// path to the main nestor folder
	protected $path_php;				// path to php folder
	protected $path_bin;				// path to binaries
//	protected $path_data;				// path to data (state, db, etc...)
//	protected $path_lib;				// path to the needed libraries or scripts
	protected $path_etc;				// path to configs files

	protected $conf;					// configuration array
	protected $invocation;				// how are we launched: site|command
	protected $error_event	=false;		// array ('msg'=>'text','fatal'=>1)
	protected $log_commands	=false;		// array of commands

	// ----------------------------------------------------------------------------------------------------------------
	public function __construct(){
		$this->_setPaths();
		$this->conf= $this->_getConfig();
	}

	// ----------------------------------------------------------------------------------------------------------------
	public function GetModule($module_key){
		if(!isset($this->conf['modules'][$module_key])){
			return false;
		}
		$module_conf	= $this->conf['modules'][$module_key];
		$module_name	= $module_conf['module'];

		$path_module	= "{$this->path_php}modules/{$module_name}.php";
		if(!file_exists($path_module)){
			die("Cant find a modules named $module_name at $path_module !");
		}

		require_once($this->path_php.'module.php');
		require_once($path_module);
		$class="NestorModule_{$module_name}";
		return new $class($module_conf);
	}

	// ----------------------------------------------------------------------------------------------------------------
	public function GetNestorVersion(){
		return $this->nestor_version;
	}

	// ----------------------------------------------------------------------------------------------------------------
	protected function SetError($msg,$is_fatal=1){	//0 = not blocking | 1 = fatal
		$this->error_event=array('msg'=>$msg, 'fatal'=>$is_fatal);
	}
	// ----------------------------------------------------------------------------------------------------------------
	protected function GetError(){
		if(isset($this->error_event['fatal']) and $this->error_event['fatal']){
			return $this->error_event['msg'];
		}
	}
	// ----------------------------------------------------------------------------------------------------------------
	protected function LogCommand($str){
		if(trim($str)){
			$this->log_commands[]=$str;
		}		
	}


	// ----------------------------------------------------------------------------------------------------------------
	private function _setPaths(){
		$this->path_root	=dirname(dirname(__FILE__)).'/';
		$this->path_php		=dirname(__FILE__).'/';
		$this->path_bin		=$this->path_root.'bin/';
		$this->path_data	=$this->path_root.'var/data/';
		$this->path_etc		=$this->path_root.'var/etc/';
//		$this->path_lib		=$this->path_root.'lib/';		
	}

	// ----------------------------------------------------------------------------------------------------------------
	private function _getConfig($file='nestor'){
		require($this->path_etc.$file.'.php');
		return $cfg;
	}

}
?>