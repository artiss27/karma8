<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $_REQUEST['file'] ?? 'Test Cases' ?> Karma8</title>
    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <?php
    if (!empty($_REQUEST['file'])) {
        echo '<h1>' . $_REQUEST['file'] . '</h1><br>';
        require '../src/Cron/' . $_REQUEST['file'] . '.php';
    } else {
    ?>
    <h1>PHP Developer. Test Cases</h1>
    <br>
    <h4>Задача:</h4>
    <ul>
        <li>Вы разрабатываете сервис для рассылки уведомлений об истекающих подписках.</li>
        <li>Примерно за три дня до истечения срока подписки, нужно отправить письмо пользователю с текстом "{username},
            your subscription is expiring soon".
        </li>
    </ul>
    <br>
    <h4>Ограничения:</h4>
    <ul>
        <li>Необходимо регулярно отправлять емейлы об истечении срока подписки, но только на те емейлы, на которые
            письмо точно дойдёт.
        </li>
        <li>Можно использовать cron</li>
        <li>Можно создать необходимые таблицы в DB илиизменить существующие.</li>
        <li>Не нужно использовать ООП</li>
    </ul>
    <br>
    <div class="alert alert-info" role="alert">
        <h3>Решение:</h3>
        <div class="alert alert-warning" role="alert">
            <ul>
                <li>Исходя из ограничений, весь код написан без ООП.</li>
                <li>Все тестовые данные имеют рандомный характер.</li>
                <li>Для теста по умолчанию генерируется 10000 юзеров, при желании можно изменить количество в <b>.env -> CNT_USERS</b>.</li>
                <li>Для перегенерации данных нужно запустить в консоли <b>make faker-migrations</b>.</li>
                <li>Логи:
                    <ul>
                        <li><b>/logs/crons.txt</b> - Мониторинг работы кронов (время старта и выполнения)</li>
                        <li><b>/logs/multithread.txt</b> - Мониторинг работы многопоточности (время старта и завершения потоков)</li>
                        <li><b>/logs/send_email.txt</b> - Результат работы крона send-email</li>
                        <li><b>/logs/check_email.txt</b> - Результат работы крона check-email</li>
                    </ul>
                </li>
                <li>Так как если поставить Cron то это было бы не удобно тестировать ниже я приведу ссылки для запуска
                    напрямую.</li>
                <li>Скрипт крона обработки ошибок отправки я не делал, ничего уникального там не нет.</li>
            </ul>
        </div>
        <ul>
            <li>
                <br>
                <p>Раз в день запускаем Cron <b>subscription-expires.php</b> (пусть будет на время 00:01) он собирает всех пользователей у которых
                    подходит срок подписки к концу (данные собираются в разрезе 1 дня) и записываем его в таблицу BD для отправки писем (примитивные очереди).
                    Отправка писем отложенная и имеет дату и время старта отправки (для предварительной валидации
                    так как функция валидации занимает слишком много времени)</p>
                <p>Тут же проверяем если у пользователя почта подтверждена, то дополнительной проверки email не требуется,
                    иначе отправляем email на проверку сторонним сервисом (делаем запись в BD).</p>
                <p>Я формирую задачи на отправку писем и проверки почты за 3 дня до окончания срока подписки,
                    т.е. это как раз старт рассылки по расписанию (исключительно для удобства тестирования). Для рабочего скрипта
                    нужно будет установить время за несколько дней чтобы успеть пройти валидацию (переменная <b>$DAYS_BEFORE</b>).</p>
                <a href="/?file=subscription-expires" target="_blank" class="btn btn-primary">Запустить subscription-expires</a>
            </li>
            <li>
                <br>
                <p>Раз в день после старта предыдущего крона (пусть будет на время 00:30)
                    запускаем Cron <b>check-email.php</b> который запускает воркер и отправляет письма на валидацию
                    сторонним сервисом.</p>
                <p>Все проверки выполняются в несколько потоков для ускорения процесса проверки. (Для теста
                    я установил 5 потоков).</p>
                <a href="/?file=check-email" target="_blank" class="btn btn-primary">Запустить check-email</a>
            </li>
            <li>
                <br>
                <p>Раз в 5 минут (думаю это оптимальное время) запускаем Cron <b>send-email.php</b> который запускает
                    воркер и отправляет письма у которых указанный срок отправки истек и которые еще не отправлены.</p>
                <p>Все письма отправляются в несколько потоков для ускорения процесса отправки. (Для теста
                    я установил 5 потоков).</p>
                <a href="/?file=send-email" target="_blank" class="btn btn-primary">Запустить send-email</a>
            </li>
        </ul>
    </div>
    <?php
    }
    ?>
</div>

</body>
</html>


<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
//
//require_once('../src/Cron/subscription-expires.php');