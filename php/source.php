<?php
require_once(dirname(__FILE__).'/core.php');

class NestorSource extends NestorCore {

	private   $grab_period		=60;								// period to fetch data from source
	protected $provider;										// the source provider
	protected $provider_cfg		=array();							// provider settings
	protected $properties		=array('provider','grab_period');	// list of all properties that can be set by $cfg

	private $is_fresh			=true;
	private $last_grabbed		=0;
	private $last_displayed;
	private $last_data;


	protected $retry_grab_err	=10;		// period to re-grap the source if last grab has failed
	protected $force_refresh	=3600;		// max time to re-update the display even if data is not fresh
	protected $grab_error		=false;		//grab produced an error?
	protected $url_error_status;			// last url error status 


	// ----------------------------------------------------------------------------------------------------------------
	public function __construct($cfg){
		parent::__construct();
		$this->_AssignProperties($cfg);
		isset($this->conf['providers'][$this->provider]) and $this->provider_cfg=$this->conf['providers'][$this->provider];
	}

	// ----------------------------------------------------------------------------------------------------------------
	public function default($params){
		if($this->_DataIsOld()){					
			$data=$this->Grab($params);
			//there was an error, retry sooner
			if($this->grab_error){
				$this->last_grabbed = time() - $this->grab_period + $this->retry_grab_err ;
				$this->is_fresh		=false;
			}
			else{
				$this->last_grabbed = time();
				if($data != $this->last_data){
					$this->is_fresh		=true;
					$this->last_data	=$data;
				}
			}
		}
	}

	// ----------------------------------------------------------------------------------------------------------------
	public function GetFreshdata($force=false){		
		if($force or $this->is_fresh ){
			return $this->_GetData();
		}
		if( $this->last_displayed < ( time() - $this->force_refresh) ){
			//return $this->_GetData();
		}
	}

	// ----------------------------------------------------------------------------------------------------------------
	public function SetDisplayed(){
		$this->last_displayed	=time();
		$this->is_fresh			=false;
	}


	// ################################################################################################################
	// ### PROTECTED ##################################################################################################
	// ################################################################################################################

	protected function Grab($params){
	}

	// ---------------------------------------------------------------------------------------
	protected function _FetchUrl($url, $auth_login='', $auth_pass='',$timeout=3){
		$this->url_error_status=0;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if($auth_login and $auth_pass){
			curl_setopt($ch, CURLOPT_USERPWD, "$auth_login:$auth_pass");
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Dont verify SSL
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$result	= curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);		
		if(curl_errno($ch) or $status !=200 ){
			$result='';
			$this->url_error_status=$status;
		}
		curl_close($ch);
		return $result;
	}


	// ################################################################################################################
	// ### PRIVATE ####################################################################################################
	// ################################################################################################################

	private function _DataIsOld(){
		if($this->last_grabbed < ( time() - $this->grab_period) ){
			return true;
		}
	}
	
	private function _setData($data){
		$this->last_data=$data;
	}

	private function _GetData(){
		return $this->last_data;
	}



}
?>