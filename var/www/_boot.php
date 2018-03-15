<?php
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

$boot_lib_path=dirname(dirname(dirname(__FILE__))).'/php/';
require_once($boot_lib_path.'site.php');
$nestor_site=new NestorSite();
?>
