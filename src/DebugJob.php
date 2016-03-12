<?php

namespace CvoTechnologies\Gearman;

class DebugJob
{
    public static $jobs = [];

    public static function add($name, $workload, $background, $priority)
    {
        self::$jobs[] = compact(['name', 'workload', 'background', 'priority']);
    }
}
