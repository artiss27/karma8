<?php

$pid     = $pid = getmypid();
$connect = time() . $pid ?: rand(1, 100000);

while (true) {

    try {
        Core::DB($connect)->beginTransaction();

        $stmt = \Core::DB($connect)->prepare('SELECT * FROM `emails` WHERE `checked` = 0 LIMIT 1 FOR UPDATE SKIP LOCKED');
        $stmt->execute();

        if (!$task = $stmt->fetch()) {
            Core::CloseDB($connect);

            if ($pid) posix_kill($pid, SIGTERM);
            exit();
        }
        $stmt = Core::DB($connect)->prepare('UPDATE `emails` SET `checked` = -1 WHERE email = ?')->execute([$task['email']]);

        Core::DB($connect)->commit();
    } catch (Exception $e) {
        Core::DB($connect)->rollBack();
        continue;
    }

    if ($result = Email::checkEmail($task['email'], $connect)) {
        $stmt = Core::DB($connect)->prepare('
            SELECT *, FROM_UNIXTIME(`validts` - (3 * 24 * 60 * 60)) as `start_emailing`
            FROM `users` WHERE `email` = ? LIMIT 1
        ');
        $stmt->execute([$task['email']]);

        if (!$user = $stmt->fetch()) continue;

        Email::addEmailToSend($user['email'], $user['username'], $user['start_emailing'], 'expires', $connect);
    }

    $stmt = Core::DB($connect)->prepare('UPDATE `emails` SET `checked` = 1, `valid` = ? WHERE email = ?')->execute([$result, $task['email']]);
}