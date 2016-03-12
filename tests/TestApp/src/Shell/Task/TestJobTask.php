<?php

namespace TestApp\Shell\Task;

use Cake\Console\Shell;

class TestJobTask extends Shell
{
    public function main($workload)
    {
        $this->out('Woo!');

        return $workload;
    }
}
