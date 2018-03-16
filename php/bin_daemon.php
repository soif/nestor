#!/usr/bin/php
<?php
require_once(dirname(__FILE__).'/controllers/daemon.php');
$command=new NestorController_daemon();
$command->run();
?>