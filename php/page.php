<?php
class NestorPage{

	protected $page_url;
	protected $page_title;
	protected $show_title=true;

	protected $url_static	='/static';
	protected $url_root		='';
	

	protected $menu_names=array(
		'/'			=> 'Home',
		'/about'	=> 'About',
	);

	protected $menu_icons=array(
		'/'			=> 'home',
		'/about'	=> 'info-circle',
	);

	protected $menu_adm=array(
		'/api'						=> 'API',
		'/gpio'						=> 'GPIO Pins State',
		'/info'						=> 'PhpInfo',
	);
	



	// ----------------------------------------------------------------------------------------------------------------
	function __construct($page=''){
		$cur=$page;
		$cur=str_replace('_','/',$page);
		if($cur=='index'){$cur='';}
		$this->page_url="/$cur";

		$this->url_root		="{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}";
		$this->url_static	=$this->url_root.$this->url_static;
	}


	// ----------------------------------------------------------------------------------------------------------------
	function GetHeader(){
		$static= & $this->url_static;
		
		//menus
		foreach($this->menu_names as $url => $title){
			$class='';$src="";
			if($url == $this->page_url){$class="active";$src=" <span class='sr-only'>(current)</span>";}
			$html_menu .="\t\t\t\t<li class='nav-item $class'><a class='nav-link' href='$url'><i class='fa fa-{$this->menu_icons[$url]}'></i> $title$src</a></li>\n";
		}
		foreach($this->menu_adm as $url => $title){
			$html_menu_adm .="\t\t\t\t\t\t<a class='dropdown-item' href='$url'>$title</a>\n";
		}
		//title
		if(! $this->page_title){
			$this->page_title=$this->menu_names[$this->page_url] or $this->page_title=$this->menu_adm[$this->page_url];
			$auto_title=true;
		}
		$this->page_title and $this->show_title and $html_title="<h1 class='text-center'>{$this->page_title}</h1>";
		$page_title = $this->page_title;
		$auto_title and $page_title="Nestor - $page_title";

		return <<<EOF
<!doctype html>
<html lang="en">
  <head>
    <!-- Required for Bootstrap -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="{$static}/bootstrap/css/bootstrap_nestor.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{$static}/fa/css/fontawesome-all.min.css" crossorigin="anonymous">

	<!-- IOS -->
	<meta name="apple-mobile-web-app-title" content="Nestor" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	<link rel="apple-touch-icon" href="{$static}/img/icon57.png" />
	<!-- Icon -->
	<link rel="SHORTCUT ICON" href="{$static}/img/icon32.gif" />
	<link rel="icon" type="image/gif" href="{$static}/img/icon32.gif" />

    <title>{$page_title}</title>
  </head>
  <body>
	<div class="container">

		<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
		  <a class="navbar-brand" href="/"><img src="{$static}/img/logo.png" width="30" height="30" alt=""></a>
	  
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		  </button>

		  <div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
			  
			$html_menu

	<!--
			  <li class="nav-item">
				<a class="nav-link disabled" href="#">Disabled</a>
			  </li>
	-->
			</ul>
			<div class="my-2 my-lg-0">
				<ul class="navbar-nav mr-auto">
				  <li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					  Admin
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
$html_menu_adm
					  <div class="dropdown-divider"></div>
					  <a class="dropdown-item" href="https://github.com/soif/" target="_blank">Soif Github</a>
					</div>
				  </li>
				</ul>
			</div>

		  </div>
		</nav>
$html_title
	<!-- ####### Page ################################################################################################## -->

EOF;
	}

	// ----------------------------------------------------------------------------------------------------------------
	function GetFooter(){
		$static= & $this->url_static;

		return <<<EOF

	<!-- ####### FOOTER ################################################################################################## -->
	<div class='nestor_footer text-center'>
		<small><a href="http://github.com/soif/nestor/"><i class='fab fa-github'></i> Nestor at Github</a></small>
	</div>
	</div><!-- end main container -->

    <script src="{$static}/js/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="{$static}/js/popper.min.js" crossorigin="anonymous"></script>
    <script src="{$static}/bootstrap/js/bootstrap.min.js" crossorigin="anonymous"></script>
  </body>
</html>
EOF;

	}

	// ----------------------------------------------------------------------------------------------------------------
	function Display($content){
		echo $this->GetHeader();
		echo $content;
		echo $this->GetFooter();
	}
}
?>