<?php
namespace CvoTechnologies\Gearman\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use CvoTechnologies\Gearman\JobAwareTrait;
use GearmanJob;
use Psr\Log\LogLevel;

class WorkerShell extends Shell
{
    use JobAwareTrait;

    /**
     * Process jobs by calling main() function in Shell Tasks.
     *
     * @return mixed Instance of Shell Task
     */
    public function main()
    {
        $worker = $this->gearmanWorker();

        $jobs = self::getJobs();

        foreach ($jobs as $job => $options) {
            $worker->addFunction($job, function (GearmanJob $job) use ($options) {
                $task = $this->Tasks->load($options['className']);

                $workload = @unserialize($job->workload());
                if ($workload === false) {
                    $workload = $job->workload();
                }

                try {
                    $response = $task->main($workload, $job);
                } catch (\Exception $exception) {
                    $response = null;

                    $this->log($exception, LogLevel::ERROR);
                }

                if (is_array($response) || is_object($response)) {
                    $response = serialize($response);
                }

                return $response;
            });
        }

        while ($worker->work()) {
            if ($worker->returnCode() != GEARMAN_SUCCESS) {
                echo "return_code: " . $worker->returnCode() . "\n";
                break;
            }
        }
    }

    /**
     * Returns an array with all configured jobs.
     *
     * @throws \Cake\Core\Exception\Exception
     * @return array Hash with jobs as found in Configure
     */
    protected function getJobs()
    {
        $jobs = Configure::read('Gearman.Jobs');

        if (empty($jobs)) {
            $this->abort('Invalid Gearman configuration: you must configure at least one job before starting this worker');
        }

        return $jobs;
    }
}
