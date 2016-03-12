<?php

namespace CvoTechnologies\Gearman\Panel;

use CvoTechnologies\Gearman\DebugJob;
use DebugKit\DebugPanel;

class JobPanel extends DebugPanel
{

    public $plugin = 'CvoTechnologies/Gearman';

    /**
     * {@inheritDoc}
     */
    public function summary()
    {
        return count(DebugJob::$jobs);
    }

    /**
     * {@inheritDoc}
     */
    public function data()
    {
        return [
            'jobs' => DebugJob::$jobs,
        ];
    }
}
