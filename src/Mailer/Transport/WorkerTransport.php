<?php

namespace CvoTechnologies\Gearman\Mailer\Transport;

use Cake\Core\Configure;
use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Email;
use CvoTechnologies\Gearman\JobAwareTrait;

class WorkerTransport extends AbstractTransport
{

    use JobAwareTrait;

    /**
     * Default config for this class.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'transport'  => 'default',
        'background' => true,
    ];

    /**
     * Send email.
     *
     * @param \Cake\Mailer\Email $email Email instance.
     *
     * @return array|bool
     */
    public function send(Email $email)
    {
        $result = $this->execute('emailWithWorker', [
            'email'       => $email,
            'transport'   => $this->config('transport'),
            'fullBaseUrl' => Configure::read('App.fullBaseUrl'),
        ], $this->config('background'));

        if (!$this->config('background')) {
            return $result;
        }

        return true;
    }
}
