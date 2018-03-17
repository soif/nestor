<?php
class NestorCore {
	private	$nestor_version	="0.5.0a";

	private $path_root;				// path to the main nestor folder
	protected $path_php;				// path to php folder
	protected $path_bin;				// path to binaries
//	protected $path_data;				// path to data (state, db, etc...)
//	protected $path_lib;				// path to the needed libraries or scripts
	protected $path_etc;				// path to configs files

	protected $conf;					// configuration array
	protected $invocation;				// how are we launched: site|command
	protected $error_event	=false;		// array ('msg'=>'text','fatal'=>1)
	protected $log_commands	=false;		// array of commands
	protected $debug_mode	=false;		// allow to log commands

	// ----------------------------------------------------------------------------------------------------------------
	public function __construct(){
		$this->_setPaths();
		$this->conf= $this->_getConfig();
	}

	// ----------------------------------------------------------------------------------------------------------------
	public function GetNestorVersion(){
		return $this->nestor_version;
	}

	// ----------------------------------------------------------------------------------------------------------------
	protected function _AssignProperties($cfg){
		foreach($this->properties as $k){
			isset($cfg[$k]) and $this->$k =$cfg[$k];
		}
	}


	// ----------------------------------------------------------------------------------------------------------------
	protected function SetError($msg,$is_fatal=1){	//0 = not blocking | 1 = fatal
		$this->error_event=array('msg'=>$msg, 'fatal'=>$is_fatal);
		$this->DebugLog("ERROR ($is_fatal) $msg");
	}

	// ----------------------------------------------------------------------------------------------------------------
	protected function GetError(){
		if(isset($this->error_event['fatal']) and $this->error_event['fatal']){
			return $this->error_event['msg'];
		}
	}

	// ----------------------------------------------------------------------------------------------------------------
	protected function LogCommand($str){
		if($this->debug_mode and trim($str)){
			$this->log_commands[]=$str;
		}		
	}

	// ----------------------------------------------------------------------------------------------------------------
	protected function DebugLog($str,$do_ret=true){
		if($this->debug_mode){
			echo "$str ";
			if ($do_ret) echo "\n";
		}		
	}


	// ----------------------------------------------------------------------------------------------------------------
	private function _setPaths(){
		$this->path_root	=dirname(dirname(__FILE__)).'/';
		$this->path_php		=dirname(__FILE__).'/';
		$this->path_bin		=$this->path_root.'bin/';
//		$this->path_data	=$this->path_root.'var/data/';
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