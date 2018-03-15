<?php
class NestorPage_page extends NestorPage {

	protected $page_title="Nestor";
	protected $show_title=false;

	function run(){
		$content=<<<EOF
<div class="jumbotron text-center">
	<h1>I'm Nestor, At your service!</h1>
	<img src="{$this->url_static}/img/nestor.gif">
</div>
EOF;
		$this->Display($content);
	}	
}
?>
