<?php
require_once(dirname(dirname(__FILE__)).'/controller.php');

class NestorController_daemon extends NestorController{
	protected $invocation 		='daemon';

	private $last_time		=0;
	private $last_ms		=0;
	private $last_state		=0;

/*
	// ----------------------------------------------------------------------------------------------------------------
	public function __construct($cfg){
		parent::__construct();
	}
*/
	// ---------------------------------------------------------------------------------------
	public function run(){
		set_time_limit(0);
		isset($this->conf['prefs']['sleep'] ) and $sleep=$this->conf['prefs']['sleep']  or $sleep=1;
		while(true){
			$this->_MarkTime();
			$this->_ProcessSources();
			usleep($sleep * 1000 * 1000);
		}
	}

	// ---------------------------------------------------------------------------------------
	public function run_clock(){
		set_time_limit(0);
		while(true){
			$this->_MarkTime();
			$this->_UpdateClock();
			usleep(100 * 1000);
		}
	}

	// ---------------------------------------------------------------------------------------
	private function _UpdateClock(){
		$state=1;
		$blink=$this->conf['prefs']['clock_blink'];
		if($blink and $this->last_ms >= 500 ){
			$state=0;
		}
		$this->object	="clock";
		$this->method	="clock";
		$this->param	=$state;
		if( ($blink and $this->last_state != $state) or (!$blink and $this->last_time != time())){
			$this->ProcessObject('module');
			$this->last_state =$state;
		}
	}

	// ---------------------------------------------------------------------------------------
	private function _MarkTime(){
		$this->last_time	=time();
		$this->last_ms	=round( (microtime(true) - $this->last_time) * 1000, 1) ;
	}

	// ---------------------------------------------------------------------------------------
	private function _ProcessSources(){
	}



}
?>