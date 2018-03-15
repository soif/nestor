<?php
class NestorPage_page extends NestorPage {
	
	function run(){
		$content=shell_exec('gpio readall');
		$content=<<<EOF
<pre class='text-center'>
<code>
$content
</code>
</pre>
EOF;
		$this->Display($content);
	}
}

?>

