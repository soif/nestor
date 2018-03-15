<?php
require_once(dirname(__FILE__).'/core.php');

class NestorSite extends NestorCore{
	protected $invocation ='site';

/*
	// ----------------------------------------------------------------------------------------------------------------
	public function __construct($cfg){
		parent::__construct();
	}
*/


	// ----------------------------------------------------------------------------------------------------------------
	public function Controller(){
		$path_pages	= $this->path_php.'pages/';

		$url		= preg_replace('#\?.*$#','',$_SERVER['REQUEST_URI']);
		$url		= str_replace('/',	'_',	$url);
		$url		= preg_replace('#^_#',	'',	$url);
		$page		= $url or $page="index";	
		$page_file	= $path_pages.$page.'.php';

		// debug @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		//echo "<hr><pre>\n";print_r($_SERVER);echo "\n</pre>\n\n";exit;		
		
		if(!file_exists($page_file)){
			$page=404;
			$page_file=$path_pages.$page.'.php';
		}

		require_once($this->path_php.'page.php');
		require_once($page_file);
		$obj=new NestorPage_page($page);
		$obj->run();
	}
}
?>