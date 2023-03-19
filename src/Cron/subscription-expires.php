<?php
set_time_limit(7200);

$start_time = microtime(true);

require_once('cron-init.php');

addToLog('Started Cron subscription-expires', 'crons');

$DAYS_BEFORE = 3;

$stmt = Core::DB()->prepare('
    SELECT u.*, FROM_UNIXTIME(u.`validts` - (3 * 24 * 60 * 60)) as `start_emailing`, e.`checked` as `email_checked`, e.`valid` as `email_valid`
    FROM `users` u
    LEFT JOIN `emails` AS e ON e.`email` = u.`email`
    LEFT JOIN `emails_to_send` AS ets ON ets.`status` = "pending" AND ets.`ident` = "expires" AND ets.`email` = u.`email`
    WHERE u.`validts` BETWEEN UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL ' . $DAYS_BEFORE . ' DAY))
          AND UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL ' . $DAYS_BEFORE + 1 . ' DAY) - INTERVAL 1 SECOND)
          AND ets.`id` IS NULL
');
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

foreach ($result as $k => $v) {
    if ($v['confirmed'] || $v['email_checked'] && $v['email_valid']) {
        Email::addEmailToSend($v['email'], $v['username'], $v['start_emailing'], 'expires');
        // display for testing
        echo sprintf('%s - Email: %s has been queued for sending, time start - %s<br>', $k + 1, $v['email'], $v['start_emailing']);
        continue;
    }
    if (!$v['email_checked'] && $v['email_checked'] !== 0) {
        Core::DB()->prepare('INSERT INTO `emails` (`email`) VALUES (?)')->execute([$v['email']]);
        // display for testing
        echo sprintf('%s - Email: %s has been queued for validation<br>', $k + 1, $v['email']);
    }
}

addToLog(sprintf('Finished Cron subscription-expires: %s sec', round(microtime(true) - $start_time, 2)), 'crons');

