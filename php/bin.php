#!/usr/bin/php
<?php
require_once(dirname(__FILE__).'/controllers/command.php');
$command=new NestorController_command();
$command->run();
?>