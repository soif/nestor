<?php
require_once(dirname(__FILE__).'/core.php');

class NestorModule extends NestorCore {
	// global properties
	protected $board;					// on which board are we running ? opi|raspi
	protected $module;					// name of the module

	protected $pin_map;					// the current pin map
	protected $path_main_bin;			// full path to the main binary

	// properties set by EACH the module
	protected $main_bin;				// main binary to use from the library folder)
	protected $properties	=array();	// list of all properties that can be set by $cfg
	protected $board_maps;				// map to use per board			


/* 
example OrangePicPc+
root@opi2# gpio readall
 +-----+-----+----------+------+---+-Orange Pi+---+---+------+---------+-----+--+
 | BCM | wPi |   Name   | Mode | V | Physical | V | Mode | Name     | wPi | BCM |
 +-----+-----+----------+------+---+----++----+---+------+----------+-----+-----+
 |     |     |     3.3v |      |   |  1 || 2  |   |      | 5v       |     |     |
 |  12 |   8 |    SDA.0 | ALT3 | 0 |  3 || 4  |   |      | 5V       |     |     |
 |  11 |   9 |    SCL.0 | ALT3 | 0 |  5 || 6  |   |      | 0v       |     |     |
 |   6 |   7 |   GPIO.7 | ALT3 | 0 |  7 || 8  | 0 | ALT3 | TxD3     | 15  | 13  |
 |     |     |       0v |      |   |  9 || 10 | 0 | ALT3 | RxD3     | 16  | 14  |
 |   1 |   0 |     RxD2 | ALT3 | 0 | 11 || 12 | 0 | ALT3 | GPIO.1   | 1   | 110 |
 |   0 |   2 |     TxD2 | ALT3 | 0 | 13 || 14 |   |      | 0v       |     |     |
 |   3 |   3 |     CTS2 | ALT3 | 0 | 15 || 16 | 1 | OUT  | GPIO.4   | 4   | 68  |
 |     |     |     3.3v |      |   | 17 || 18 | 1 | OUT  | GPIO.5   | 5   | 71  |
 |  64 |  12 |     MOSI | ALT4 | 0 | 19 || 20 |   |      | 0v       |     |     |
 |  65 |  13 |     MISO | ALT4 | 0 | 21 || 22 | 0 | ALT3 | RTS2     | 6   | 2   |
 |  66 |  14 |     SCLK | ALT4 | 0 | 23 || 24 | 0 | ALT4 | CE0      | 10  | 67  |
 |     |     |       0v |      |   | 25 || 26 | 0 | ALT3 | GPIO.11  | 11  | 21  |
 |  19 |  30 |    SDA.1 | ALT3 | 0 | 27 || 28 | 0 | ALT3 | SCL.1    | 31  | 18  |
 |   7 |  21 |  GPIO.21 | ALT3 | 0 | 29 || 30 |   |      | 0v       |     |     |
 |   8 |  22 |  GPIO.22 | ALT3 | 0 | 31 || 32 | 0 | ALT3 | RTS1     | 26  | 200 |
 |   9 |  23 |  GPIO.23 | ALT3 | 0 | 33 || 34 |   |      | 0v       |     |     |
 |  10 |  24 |  GPIO.24 | ALT3 | 0 | 35 || 36 | 0 | ALT3 | CTS1     | 27  | 201 |
 |  20 |  25 |  GPIO.25 | ALT3 | 0 | 37 || 38 | 0 | ALT3 | TxD1     | 28  | 198 |
 |     |     |       0v |      |   | 39 || 40 | 0 | OUT  | RxD1     | 29  | 199 |
 +-----+-----+----------+------+---+----++----+---+------+----------+-----+-----+
 | BCM | wPi |   Name   | Mode | V | Physical | V | Mode | Name     | wPi | BCM |
 +-----+-----+----------+------+---+-Orange Pi+---+------+----------+-----+-----+

What a pain in the ass to have to handle things like that !!!!!

*/
	protected $pins_maps	=	array(
		// LIB	=> mapping
			// pin	=> BCM (or whatever the lib use as pin input)
		'pyA20'	=>array(		//orangepi, LED TM1637, pyA20
			// odd pins -----
			3		=> 12,
			5		=> 11,
			7		=> 6,

			11		=> 1,
			13		=> 0,
			15		=> 3,

			19		=> 64,
			21		=> 65,
			23		=> 66,

			27		=> 19,
			29		=> 7,
			31		=> 8,
			33		=> 9,
			35		=> 10,
			37		=> 20,

			// even pins -----
			8		=> 13,
			10		=> 14,
			12		=> 110,

			16		=> 68,
			18		=> 71,

			22		=> 2,
			24		=> 67,
			26		=> 21,
			28		=> 18,
			
			32		=> 200,
			
			36		=> 201,
			38		=> 198,
			40		=> 199,
		),

		'raspi_gpio_lib?'	=>array(
			
		),
	);
	

	// ----------------------------------------------------------------------------------------------------------------
	public function __construct($cfg){
		parent::__construct();

		$this->path_main_bin=$this->path_bin.$this->main_bin;
		$this->_AssignProperties($cfg);
		$this->_GuessBoard();
		$this->_EnsureWeHaveaWHTFmappingForThePins();
	}

/*
	// ----------------------------------------------------------------------------------------------------------------
	public function Process(){
	}
*/

	// ----------------------------------------------------------------------------------------------------------------
	//public function Display(){
	//	die("NOT IMPLEMENTED");
	//}

	// ----------------------------------------------------------------------------------------------------------------
	public function ListProperties(){
		foreach($this->properties as $k){
			$out[$k]=$this->$k;
		}
		return $out;
	}



	// ################################################################################################################
	// ### PROTECTED ##################################################################################################
	// ################################################################################################################

	// ----------------------------------------------------------------------------------------------------------------
	protected function _AssignProperties($cfg){
		foreach($this->properties as $k){
			isset($cfg[$k]) and $this->$k =$cfg[$k];
		}
	}

	// ----------------------------------------------------------------------------------------------------------------
	protected function _ErrorMissingProperties(){
		if($missing = $this->_CheckMissingProperties()){
			$this->SetError("The following required properties are missing: ".implode(', ',$missing));
			return true;
		}
	}


	// ----------------------------------------------------------------------------------------------------------------
	protected function _SetModulename(){
		$parent =strtolower(get_parent_class());
		$name	=strtolower(get_class());
		$name	=str_replace($parent.'_', '', $name);
		$this->module=$name;
	}


	// ----------------------------------------------------------------------------------------------------------------
	protected  function _EnsureWeHaveaWHTFmappingForThePins(){
		$this->pin_map		=$this->pins_maps[$this->board_maps[$this->board]];
		if(!$this->pin_map){
			$this->SetError("I miss a pin map for the board '{$this->board}' when using module '{$this->module} ");
		}
	}

	// ----------------------------------------------------------------------------------------------------------------
	protected  function _exec($command){
		$this->LogCommand($command);
		return ! shell_exec($command);
	}





	// ################################################################################################################
	// ### PRIVATE ####################################################################################################
	// ################################################################################################################

	// ----------------------------------------------------------------------------------------------------------------
	private function _CheckMissingProperties(){
		$not_set=array();
		foreach($this->properties as $k){
			if(!isset($this->$k)){
				$not_set[]=$k;
			}
		}
		if(count($not_set)){
			return $not_set;
		}
	}


	
	// ----------------------------------------------------------------------------------------------------------------
	private function _GuessBoard(){
		$cpuinfo=shell_exec('cat /proc/cpuinfo');
		if(preg_match('#Allwinner sun8i#i',$cpuinfo)){
			$this->board='opi';
		}
		elseif(preg_match('# BCM2708#i',$cpuinfo)){
			$this->board='raspi';
		}
		$this->_EnsureWeHaveaWHTFmappingForThePins();
	}

}
?>
