<?php
// Modules
$cfg['modules']['temp_int']['module']	='7seg_tm1637';
$cfg['modules']['temp_int']['pin_clk']	=16;
$cfg['modules']['temp_int']['pin_dio']	=18;
$cfg['modules']['temp_int']['segments']	=4;
$cfg['modules']['temp_int']['has_dots']	=true; 

$cfg['modules']['temp_ext']['module']	='7seg_tm1637';
$cfg['modules']['temp_ext']['pin_clk']	=16;
$cfg['modules']['temp_ext']['pin_dio']	=18;
$cfg['modules']['temp_ext']['segments']	=4;
$cfg['modules']['temp_ext']['has_dots']	=true; // 0=None, 1=dots, 2=colon

$cfg['modules']['clock']['module']		='7seg_tm1637';
$cfg['modules']['clock']['pin_clk']		=36;
$cfg['modules']['clock']['pin_dio']		=38;
$cfg['modules']['clock']['segments']	=4;
$cfg['modules']['clock']['has_dots']	=true; // 0=None, 1=dots, 2=colon
$cfg['modules']['clock']['has_colon']	=true; // 0=None, 1=dots, 2=colon
	


?>
