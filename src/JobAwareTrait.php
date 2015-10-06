<?php
namespace CvoTechnologies\Gearman;

use Cake\Core\Configure;
use GearmanClient;
use GearmanWorker;

trait JobAwareTrait
{

    /**
     * @return GearmanClient
     */
    public function gearmanClient() {
        $client = new GearmanClient();

        $servers = Configure::read('Gearman.Servers');

        $client->addServers(implode(',', $servers));

        return $client;
    }

    /**
     * @return GearmanWorker
     */
    public function gearmanWorker() {
        $client = new GearmanWorker();

        $servers = Configure::read('Gearman.Servers');

        $client->addServers(implode(',', $servers));

        return $client;
    }

    /**
     * @param string $name
     * @param mixed $workload
     * @param bool $background
     * @param int $priority
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
