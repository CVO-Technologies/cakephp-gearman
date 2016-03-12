<?php

namespace CvoTechnologies\Gearman\Shell\Task;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Psr\Log\LogLevel;

class EmailTask extends Shell
{
    public function main(array $workload)
    {
        $defineFullBaseUrl = !empty($workload['fullBaseUrl']);

        if ($defineFullBaseUrl) {
            $previousFullBaseUrl = Configure::read('App.fullBaseUrl', $workload['fullBaseUrl']);

            Configure::write('App.fullBaseUrl', $workload['fullBaseUrl']);
        } else {
            $this->log(__('Could not set full base URL when sending email'), LogLevel::WARNING);
        }

        /* @var \Cake\Mailer\Email $email */
        $email = $workload['email'];

        if ($defineFullBaseUrl) {
            Configure::write('App.fullBaseUrl', $previousFullBaseUrl);
        }

        $this->log(__('Sending email with subject {0} to {1}', $email->subject(), implode(',', $email->to())), LogLevel::INFO);

        return $email->transport($workload['transport'])->send();
    }
}
