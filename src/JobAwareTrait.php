<?php
namespace CvoTechnologies\Gearman;

use Cake\Core\Configure;
use GearmanClient;
use GearmanWorker;

trait JobAwareTrait
{

    /**
     * Setup a GearmanClient object configured
     * with the servers from the configuration.
     * 
     * @return GearmanClient
     */
    public function gearmanClient() {
        $client = new GearmanClient();

        $servers = Configure::read('Gearman.Servers');

        $client->addServers(implode(',', $servers));

        return $client;
    }

    /**
     * Setup a GearmanWorker object configured
     * with the servers from the configuration.
     * 
     * @return GearmanWorker
     */
    public function gearmanWorker() {
        $client = new GearmanWorker();

        $servers = Configure::read('Gearman.Servers');

        $client->addServers(implode(',', $servers));

        return $client;
    }

    /**
     * Execute a job by sending it to the
     * Gearman server using the GearmanClient
     * 
     * Example for a background job with normal priority:
     * $this->execute('sleep', ['seconds' => 60]);
     * 
     * Example for a background job with HIGH priority:
     * $this->execute('sleep', ['seconds' => 60], true, Gearman::PRIORITY_HIGH);
     *
     * Example for a normal job with HIGH priority:
     * $this->execute('sleep', ['seconds' => 60], false, Gearman::PRIORITY_HIGH);
     *      * 
     * @param string $name Name of the job to execute
     * @param mixed $workload Any type of workload is supported (as long as it's serializable)
     * @param bool $background If it's a background job
     * @param int $priority A priority level from Gearman::PRIORTIY_*
     */
    public function execute($name, $workload, $background = true, $priority = Gearman::PRIORITY_NORMAL) {
        $func = 'do';
        switch ($priority) {
            case Gearman::PRIORITY_LOW:
                $func .= 'Low';
                break;
            case Gearman::PRIORITY_HIGH:
                $func .= 'High';
                break;
            case Gearman::PRIORITY_NORMAL:
                if (!$background) {
                    $func .= 'Normal';
                }
                break;
        }
        $func .= ($background) ? 'Background' : '';

        if (is_array($workload) || is_object($workload)) {
            $workload = serialize($workload);
        }

        $response = $this->gearmanClient()->{$func}($name, $workload);

        DebugJob::add($name, $workload, $background, $priority);

        if ($background) {
            return $response;
        }

        $serializedResponse = unserialize($response);

        if ($serializedResponse !== false) {
            $response = $serializedResponse;
        }

        return $response;
    }

}
