<?php
set_time_limit(7200);

$start_time = microtime(true);

require_once('cron-init.php');

addToLog('Started Cron check-email', 'crons');

Multithread::execution('check-email');

addToLog(sprintf('Finished Cron check-email: %s sec', round(microtime(true) - $start_time, 2)), 'crons');

// display for testing
echo '<div class="alert alert-success" role="alert">';
echo 'Cron successfully completed since everything is done in multithreading, you can see the detailed information in the logs </br>';
echo 'Detailed log for this Cron in <b>/logs/check_email.txt</b>';
echo '</div>';
