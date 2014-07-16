<?php 
	include dirname(__FILE__) . '/base.php';
	
	$exitstatus = 0;
	$jHandle = $argv[1];
	$foundWithID = false;
	if (is_numeric($jHandle)) {
		$job = Job::getByID($jHandle);
		$foundWithID = true;
	} else {
		$job = Job::getByHandle($jHandle);
	};
	
	echo t("[%s] Starting to process job", date("Y-m-d H:i:s")) . PHP_EOL;
	try {
		if (is_object($job)) {
			echo t("Running job: %s", $job->getJobName()) . PHP_EOL;
			$msg = $job->executeJob();
			echo $msg . PHP_EOL;
		} else {
			if ($foundWithID) {
				echo t("Could not find job with ID: %s", $jHandle) . PHP_EOL;
			} else {
				echo t("Could not find job with handle: %s", $jHandle) . PHP_EOL;
			}
			$exitstatus = 1;
		}
	} catch (Exception $e) {
		echo "Unexpected error occured: " . $e->getMessage() . PHP_EOL;
		echo $e->getTraceAsString();
		$exitstatus = 1;
	}
	
	echo PHP_EOL;
	exit($exitstatus);
