<?php

class Email
{
    public static function addEmailToSend(string $email, string $to, string $start_emailing, string $ident, string $connect = 'default'): void
    {
        $from    = 'test_from@test.com';
        $subject = 'Subscription expires';
        $body    = "$to, your subscription is expiring soon";

        $stmt = Core::DB($connect)->prepare('INSERT INTO `emails_to_send` (`email`, `from`, `to`, `subject`, `body`, `ident`, `start_at`) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$email, $from, $to, $subject, $body, $ident, $start_emailing]);
    }

    public static function checkEmail(string $email, string $connect): int
    {
//    sleep(rand(1, 60));
        sleep(rand(1, 10));
        $result = rand(0, 1);

        addToLog(sprintf('Email: %s has been verifed, result - %s', $email, $result), 'check_email');

        return $result;
    }

    public static function sendEmail(string $email, string $from, string $to, string $subj, string $body): string
    {
        sleep(rand(1, 10));
        $result = rand(0, 1) ? 'sent' : 'error';

        addToLog(sprintf('Email: %s has been sent, result - %s', $email, $result), 'send_email');

        return $result;
    }
}