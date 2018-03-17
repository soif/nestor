<?php
require_once(dirname(dirname(__FILE__)).'/controller.php');

class NestorController_daemon extends NestorController{
	protected $invocation 		='daemon';

	private $last_update_time		=array();	
	private $last_update_ms			=array();
	private $last_clock_colon		=array();

/*
	// ----------------------------------------------------------------------------------------------------------------
	public function __construct($cfg){
		parent::__construct();
	}
*/
	// ---------------------------------------------------------------------------------------
	public function run(){
		$this->_ParseCommandLine();
		set_time_limit(0);
		isset($this->conf['prefs']['sleep'] ) and $sleep=$this->conf['prefs']['sleep']  or $sleep=1;
		$i=0;
		while(true){
			$this->DebugLog($i++, 0);
			$this->_ProcessSources();
			$this->_UpdateDisplays();
			usleep($sleep * 1000 * 1000);
		}
	}

	// ---------------------------------------------------------------------------------------
	public function run_clock(){
		set_time_limit(0);
		while(true){
			$this->_UpdateDisplays(true);
			usleep(100 * 1000);
		}
	}


	// ---------------------------------------------------------------------------------------
	private function _ProcessSources(){
		if(isset($this->conf['sources'])){
			foreach($this->conf['sources'] as $src_name => $cfg){
				isset($cfg['method']) and $method=$cfg['method'] or  $method='';
				$this->ProcessObject('source', $src_name , $method, $cfg['param']);
			}
		}
	}
	// ---------------------------------------------------------------------------------------
	private function _UpdateDisplays($do_clok=false){
		if(isset($this->conf['modules'])){
			foreach($this->conf['modules'] as $module => $cfg){
				if(isset($cfg['display']['is_clock']) and $cfg['display']['is_clock']){
					if($do_clok){
						$this->_UpdateClock($module, $cfg);
					}
				}
				else{
					$this->_UpdateDisplay($module, $cfg);					
				}
			}
		}
	}

	// ---------------------------------------------------------------------------------------
	private function _UpdateDisplay($module_name, $cfg){
		$this->DebugLog("Updating $module_name : ",0);

		$source	=$cfg['display']['source']; //required
		$method	=$cfg['display']['method']; //required
		isset($cfg['display']['period'])	and $period	=$cfg['display']['period']	or 	$period=60;

		isset($this->last_update_time[$module_name]) or $this->last_update_time[$module_name]=0;	//no warning
		$force=0;
		if($this->last_update_time[$module_name] < time() - $period ){
			$force=1;
		}
			$this->DebugLog("Get $source (force=$force):",0);
			if($o=$this->GetObject('source',$source)){
				$new_data=$o->GetFreshdata($force);
				$this->DebugLog("Data=$new_data",0);
				if(strlen($new_data)){
					$this->ProcessObject('module', $module_name, $method, $new_data);
					$this->_MarkLastClock($module_name);
					$o->SetDisplayed();
					$this->DebugLog("WRITING to Display",0);
				}				
			}
		$this->DebugLog("");
	}

	// ---------------------------------------------------------------------------------------
	private function _UpdateClockDisplays(){
		return $this->_UpdateDisplays(true);
	}

	// ---------------------------------------------------------------------------------------
	private function _UpdateClock($module_name, $cfg){
		isset($cfg['display']['blink'])		and $blink	=$cfg['display']['blink']	or 	$blink=0;
		isset($cfg['display']['method'])	and $method	=$cfg['display']['method']	or 	$method='clock';

		$colon=1;
		if($blink and $this->last_update_ms[$module_name] >= 500 ){
			$colon=0;
		}

		if( ($blink and $this->last_clock_colon[$module_name] != $colon) or (!$blink and $this->last_update_time[$module_name] != time())){
			$this->ProcessObject('module', $module_name, $method, $colon);
			$this->last_clock_colon[$module_name] =$colon;
		}
		$this->_MarkLastClock($module_name);
	}

	// ---------------------------------------------------------------------------------------
	private function _MarkLastClock($module_name){
		$this->last_update_time[$module_name]	=time();
		$this->last_update_ms[$module_name]		=round( (microtime(true) - $this->last_update_time[$module_name]) * 1000, 1) ;
	}


}
?>