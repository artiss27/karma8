<?php

$cnt_users = getenv('CNT_USERS');

require_once('./src/Core/functions.php');

Core::DB()->exec("TRUNCATE `emails`");
Core::DB()->exec("TRUNCATE `users`");
Core::DB()->exec("TRUNCATE `emails_to_send`");

for ($i = 1; $i <= $cnt_users; $i++) {
    $username  = sprintf('user_%s', $i);
    $email     = sprintf('email_%s@test.com', $i);
    $confirmed = rand(0, 1);

    $currentTimestamp = time();
    $minTimestamp     = strtotime('-1 month', $currentTimestamp);
    $maxTimestamp     = strtotime('+1 month', $currentTimestamp);
    $validts          = rand($minTimestamp, $maxTimestamp);

    $stmt = Core::DB()->prepare('INSERT INTO `users` (`username`, `email`, `validts`, `confirmed`) VALUES (?, ?, ?, ?)');
    $stmt->execute([$username, $email, $validts, $confirmed]);
}
