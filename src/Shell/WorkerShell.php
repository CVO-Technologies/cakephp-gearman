<?php
namespace CvoTechnologies\Gearman\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use CvoTechnologies\Gearman\JobAwareTrait;
use GearmanJob;
use GearmanWorker;
use Psr\Log\LogLevel;

class WorkerShell extends Shell
{
    use JobAwareTrait;

    public function main()
    {
        $worker = $this->gearmanWorker();

        $jobs = Configure::read('Jobs');

        foreach ($jobs as $job => $options) {
            $worker->addFunction($job, function(GearmanJob $job) use ($options) {
                $task = $this->Tasks->load($options['className']);

                $workload = @unserialize($job->workload());
                if ($workload === false) {
                    $workload = $job->workload();
                }

                try {
                    $response = $task->main($workload, $job);
                }
                catch (\Exception $exception) {
                    $response = null;

                    $this->log($exception, LogLevel::ERROR);
                }

                if (is_array($response) || is_object($response)) {
                    $response = serialize($response);
                }

                return $response;
            });
        }

        while($worker->work()) {
            if ($worker->returnCode() != GEARMAN_SUCCESS) {
                echo "return_code: " . $worker->returnCode() . "\n";
                break;
            }
        }
    }
}
