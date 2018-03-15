<?php
class NestorPage_page extends NestorPage {
	function run(){

		$content=<<<EOF
<div class="container text-center">
	<h1 class="display-1">404</h1>
	<img src="{$this->url_static}/img/nestor_404.gif">
</div>
EOF;

		header("HTTP/1.0 404 Not Found");
		$this->Display($content,'NONE');
	}	
}
?>
