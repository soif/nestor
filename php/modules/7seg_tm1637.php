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
		if($this->_ErrorMissingProperties()){
			return false;
		}
		
		$clk=$this->pin_map[$this->pin_clk];
		$dio=$this->pin_map[$this->pin_dio];
		$len=$this->segments;
		$msg_pad=str_pad($txt,	$len,	' ',	STR_PAD_LEFT);
		$command="{$this->path_main_bin} $clk $dio display \"$msg_pad\"";
		return $this->_exec($command);
	}

	// ----------------------------------------------------------------------------------------------------------------
/*
	public function DisplayNum($val){
		$len=$this->segments;
		if($val < 0){
			$len--;
		}
	}
*/

}
?>
