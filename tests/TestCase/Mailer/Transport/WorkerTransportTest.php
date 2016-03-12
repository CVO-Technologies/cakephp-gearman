<?php

namespace CvoTechnologies\Gearman\Test\TestCase\Mailer\Transport;

use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;

class WorkerTransportTest extends TestCase
{
    public function setUp()
    {
        Configure::write('Gearman.Servers', [
            '127.0.0.1',
        ]);
    }

    public function testSendBackground()
    {
        $mock = $this->getMockBuilder('CvoTechnologies\Gearman\Mailer\Transport\WorkerTransport')
            ->setMethods(['execute']);

        $email = new Email();

        $transport = $mock->getMock();
        $transport->expects($this->once())
            ->method('execute')
            ->with('emailWithWorker', [
                'email'       => $email,
                'transport'   => 'default',
                'fullBaseUrl' => 'http://localhost',
            ]);

        $this->assertTrue($transport->send($email));
    }

    public function testSendForeground()
    {
        $mock = $this->getMockBuilder('CvoTechnologies\Gearman\Mailer\Transport\WorkerTransport')
            ->setMethods(['execute'])
            ->setConstructorArgs([
                [
                    'background' => false,
                ],
            ]);

        $email = new Email();

        $transport = $mock->getMock();
        $transport->expects($this->once())
            ->method('execute')
            ->with('emailWithWorker', [
                'email' => $email,
                'transport' => 'default',
                'fullBaseUrl' => 'http://localhost'
            ])
            ->willReturn(true);

        $this->assertTrue($transport->send($email));
    }

    public function tearDown()
    {
        Configure::drop('Gearman');
    }
}
