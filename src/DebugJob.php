<?php

namespace CvoTechnologies\Gearman;

class DebugJob
{
    public static $jobs = [];

    /**
     * Log a job that's executed.
     *
     * @param string $name Name of the job
     * @param mixed $workload Workload provided to the job
     * @param bool $background Whether the job runs in the background
     * @param int $priority Priority from the Gearman class
     * @return void
     */
    public static function add($name, $workload, $background, $priority)
    {
        self::$jobs[] = compact(['name', 'workload', 'background', 'priority']);
    }
}
