<?php
require_once(dirname(dirname(__FILE__)).'/source.php');

class NestorSource_domoticz extends NestorSource{

	// ################################################################################################################
	// ### PROTECTED ##################################################################################################
	// ################################################################################################################

	// --------------------------------------------------------------------------
	protected function grab($p){
		$this->DebugLog("Grabbing Domoticz {$p['id']}", 0);
		if($result=$this->_FetchDevice($p['id'])){
			$this->DebugLog("");
			return $result[$p['key']];
		}
		$this->DebugLog("");
	}

	// --------------------------------------------------------------------------
	protected function _FetchDevice($id){
		$this->grab_error=true;
		if(!$this->provider_cfg['url']){
			$this->SetError("No url defined for the domoticz source");
			return false;
		}
		if(!$id){
			$this->SetError("No id defined as param the domoticz source");
			return false;
		}

		$url=$this->provider_cfg['url'].'/json.htm?type=devices&rid='.$id;

		$data=$this->_FetchUrl($url);
		if($this->url_error_status){
			$this->SetError("Error while fetching url : $url");
			return false;
		}
		$json=@json_decode($data,true);
		if($json['status']=='OK'){
			$this->DebugLog("OK ",0);

			$this->grab_error=false;
			return $json['result'][0];
		}
		$this->DebugLog("JSON Status Error",0);
	}


}
?>