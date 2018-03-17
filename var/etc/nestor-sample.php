<?php

// Preferences ----------------------------------------------
$cfg['prefs']['clock_blink']=true;

// Modules ----------------------------------------------
$cfg['modules']['7seg_in']['type']					='7seg_tm1637';
$cfg['modules']['7seg_in']['pin_clk']				=16;
$cfg['modules']['7seg_in']['pin_dio']				=18;
$cfg['modules']['7seg_in']['segments']				=4;
$cfg['modules']['7seg_in']['has_dots']				=true; 
$cfg['modules']['7seg_in']['display']['source']		='temp_int'; 
$cfg['modules']['7seg_in']['display']['method']		='temperature'; 
$cfg['modules']['7seg_in']['display']['period']		=120; 

$cfg['modules']['7seg_out']['type']					='7seg_tm1637';
$cfg['modules']['7seg_out']['pin_clk']				=8;
$cfg['modules']['7seg_out']['pin_dio']				=10;
$cfg['modules']['7seg_out']['segments']				=4;
$cfg['modules']['7seg_out']['has_dots']				=true; // 0=None, 1=dots, 2=colon
$cfg['modules']['7seg_out']['display']['source']	='temp_ext'; 
$cfg['modules']['7seg_out']['display']['method']	='temperature'; 
$cfg['modules']['7seg_out']['display']['period']	=120; 

$cfg['modules']['clock']['type']					='7seg_tm1637';
$cfg['modules']['clock']['pin_clk']					=36;
$cfg['modules']['clock']['pin_dio']					=38;
$cfg['modules']['clock']['segments']				=4;
$cfg['modules']['clock']['has_dots']				=false; // 0=None, 1=dots, 2=colon
$cfg['modules']['clock']['has_colon']				=true; // 0=None, 1=dots, 2=colon
$cfg['modules']['clock']['display']['is_clock']		=1; 
//$cfg['modules']['clock']['display']['method']		='clock'; 
$cfg['modules']['clock']['display']['blink']		=true; 
	
// Sources to fetch ----------------------------------------------
$cfg['sources']['temp_int']['type']					='domoticz'; 
$cfg['sources']['temp_int']['param']['id']			=159; 
$cfg['sources']['temp_int']['param']['key']			='Temp'; 
$cfg['sources']['temp_int']['grab_period']			=60; 
$cfg['sources']['temp_int']['provider']				='my_domoticz';

$cfg['sources']['temp_ext']['type']					='domoticz'; 
$cfg['sources']['temp_ext']['param']['id']			=166; 
$cfg['sources']['temp_ext']['param']['key']			='Temp'; 
$cfg['sources']['temp_ext']['grab_period']			=60; 
$cfg['sources']['temp_ext']['provider']				='my_domoticz';

// Providers ----------------------------------------------
$cfg['providers']['my_domoticz']['url']				='http://domoticz.lo.lo:8080';
$cfg['providers']['my_mqtt']['ip']					='mqtt.lo.lo';


?>
