<?php

function myAuthoload(string $class): void
{
    $document_root = dirname(__DIR__);
    $file          = $document_root . '/' . (strpos($class, '\\') ? $class : $class . '/' . $class) . '.php';
    $file          = str_replace('\\', '/', $file);

    if (file_exists($file)) require_once $file;
}

spl_autoload_register('myAuthoload');

function wtf($data, $stop = true): void
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
    echo (current($trace)['file'] ?? '') . ':' . (current($trace)['line'] ?? '') . ' <pre>' . htmlspecialchars(print_r($data, 1)) . '</pre>';

    if ($stop) exit();
}

function addToLog($data, string $fname = 'log'): void
{
    $data  = (!is_string($data) && !is_numeric($data) ? stripslashes(json_encode($data)) : $data);
    $data  = date('d.m.Y H:i:s') . ': ' . $data . "\r\n";
    file_put_contents(dirname(__DIR__, 2) . '/logs/' . $fname . '.txt', $data, FILE_APPEND);
}
