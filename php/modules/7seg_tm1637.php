<?php
class NestorModule_7seg_tm1637 extends NestorModule{

	// list of all properties that can be set by $cfg
	protected $pin_clk		;
	protected $pin_dio		;
	protected $segments		=4;
	protected $has_dots		=true;
	protected $has_colon	=true;
	
	protected $properties	=array('pin_clk', 'pin_dio','segments', 'has_dots','has_colon');	

	protected $main_bin		="7seg_tm1637";	// main binary to use from the library folder)

	protected $board_maps	=array(
		'opi'	=> 'pyA20',
		'raspi'	=> '?'
	);

/*
	// ----------------------------------------------------------------------------------------------------------------
	public function __construct($cfg){
		parent::__construct();
	}
*/

	// ----------------------------------------------------------------------------------------------------------------
	public function Display($txt){
		$txt	=$this->_CleanString($txt);
		$len	=$this->segments;
		$len   +=substr_count($txt,'.');
		$txt	=substr($txt,0,$len);
		$txt	=str_pad($txt,	$len,	' ',	STR_PAD_LEFT);
		$txt	='"'.$txt.'"';
		return $this->_SendCommand('display',$txt);
	}

	// ----------------------------------------------------------------------------------------------------------------
	public function Brightness($value){ //1 to 7
		$value= intval($value);
		if($value < 0){$value=0;}
		if($value > 7){$value=7;}
		return $this->_SendCommand('brightness',$value);
	}

	// ----------------------------------------------------------------------------------------------------------------
	public function Clock($show_dot=1){
		$h	=date('G');
		$mm	=date('i');
		$date=$h;
		$show_dot and $date.=".";
		$date .=$mm;
		$this->Display($date);
	}

	// ----------------------------------------------------------------------------------------------------------------
	public function Temperature($value){
		$value=number_format( floatval($value), 1, '.', '');
		$this->Display($value);
	}

	// ----------------------------------------------------------------------------------------------------------------
	private function _SendCommand($action, $value){
		if($this->_ErrorMissingProperties()){
			return false;
		}
		$clk=$this->pin_map[$this->pin_clk];
		$dio=$this->pin_map[$this->pin_dio];
		$command="{$this->path_main_bin} $clk $dio $action $value";
		return $this->_exec($command);
	}

	// ----------------------------------------------------------------------------------------------------------------
	private function _CleanString($str, $replace_bad_by=''){
		$out ='';
		$this->has_colon	and $str =str_replace(':', '.', $str);
		$arr=str_split($str);
		if(is_array($arr)){
			foreach($arr as $letter){
				$out .=$this->_CleanChar($letter,$replace_bad_by);
			}
		}
		// dot can NOT be alone
		$rep_dot	=$replace_bad_by or $rep_dot=" ";
		$rep_dot 	.='.';
		preg_replace("#^\.#", $rep_dot, $str);
		preg_replace("#\.\.#", $rep_dot.$rep_dot, $str);
		return $out;
	}

	// ----------------------------------------------------------------------------------------------------------------
	private function _CleanChar($char,$replace){
		$o	= ord($char);
		$ok = false;
		if($o == 32){$ok=true;}	//space
		if($o == 42){$ok=true;}	//star/deg
		if(preg_match('#[a-z0-9\.-]#i',$char)){$ok=true;} //letter, num
		if($ok){
			return $char;
		}
		return $replace;
	}
}
?>