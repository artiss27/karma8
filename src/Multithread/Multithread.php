<?php

class Multithread
{
    private const MONITORING_DELAY      = 5;
    private const MAX_PROCESSES_DEFAULT = 5;

    public static function execution(string $worker, int $max_processes = self::MAX_PROCESSES_DEFAULT): void
    {
        $child_processes = [];
        for ($i = 0; $i < $max_processes; $i++) {
            $pid = pcntl_fork();
            if ($pid == -1) {
                addToLog('Error: could not fork', 'multithread');
            } elseif ($pid) {
                $child_processes[] = $pid;
            } else {
                $child_pid = getmypid();
                addToLog("Started child process $child_pid with worker: $worker", 'multithread');
                require dirname(__DIR__) . "/Worker/$worker.php";
                exit();
            }
        }
        self::startMonitoring($child_processes);
    }

    protected static function startMonitoring(array $child_processes): void
    {
        while (true) {
            foreach ($child_processes as $key => $pid) {
                $result = pcntl_waitpid($pid, $status, WNOHANG);

                if ($result == -1 || $result > 0) {
                    unset($child_processes[$key]);

                    if ($result == -1) {
                        addToLog("Error: could not wait for child process $pid", 'multithread');
                    } else {
                        addToLog("Child process: $pid finished successfully", 'multithread');
                    }
                }
            }
            if (empty($child_processes)) break;

            sleep(self::MONITORING_DELAY);
        }
    }
}