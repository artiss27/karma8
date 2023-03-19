<?php

$pid     = $pid = getmypid();
$connect = time() . $pid ?: rand(1, 100000);

while (true) {

    try {
        Core::DB($connect)->beginTransaction();

        $stmt = Core::DB($connect)->prepare('
            SELECT * 
            FROM `emails_to_send` 
            WHERE `status` = "pending" AND start_at < NOW()
            LIMIT 1 FOR UPDATE SKIP LOCKED
        ');
        $stmt->execute();

        if (!$task = $stmt->fetch()) {
            Core::CloseDB($connect);

            if ($pid) posix_kill($pid, SIGTERM);
            exit();
        }
        $stmt = Core::DB($connect)->prepare('UPDATE `emails_to_send` SET `status` = "in_progress" WHERE id = ?')->execute([$task['id']]);

        Core::DB($connect)->commit();
    } catch (Exception $e) {
        Core::DB($connect)->rollBack();
        continue;
    }

    $result = Email::sendEmail($task['email'], $task['from'], $task['to'], $task['subject'], $task['body']);

    $stmt = Core::DB($connect)->prepare('UPDATE `emails_to_send` SET `status` = ? WHERE id = ?');
    $stmt->execute([$result, $task['id']]);
}