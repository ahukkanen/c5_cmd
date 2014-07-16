<?php 
	include dirname(__FILE__) . '/base.php';
	
	$exitstatus = 0;
	$tool = $argv[1] . '.php';
	
	$env = Environment::get();
	$r = $env->getPath(DIRNAME_TOOLS . '/' . $tool);
	
	echo t("[%s] Starting to process tool", date("Y-m-d H:i:s")) . PHP_EOL;
	if (file_exists($r)) {
		try {
			include($r);
		} catch (Exception $e) {
			echo t("Unexpected error occured: %s", $e->getMessage()) . PHP_EOL;
			echo $e->getTraceAsString();
			$exitstatus = 1;
		}
	} else {
		echo t("Could not find tool with handle: %s", $tool) . PHP_EOL;
		$exitstatus = 1;
	}
	
	echo PHP_EOL;
	exit($exitstatus);
