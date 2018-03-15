<?php
class NestorPage_page extends NestorPage {

	protected $page_title="About Nestor";

	function run(){
		$content=<<<EOF
<div class="jumbotron text-center">
<h1>I'm Nestor, Always at your service!</h1>
<img src="/static/img/nestor_dring.gif">
</div>
EOF;
		$this->Display($content);
	}
}
?>
