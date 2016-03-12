<?php

namespace CvoTechnologies\Gearman\Test\TestCase\Shell\Task;

use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;
use CvoTechnologies\Gearman\Shell\Task\EmailTask;

class EmailTaskTest extends TestCase
{
    public function setUp()
    {
        Email::configTransport('default', [
            'className' => 'Debug',
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Transport config "default" is missing.
     */
    public function testMissingTransport()
    {
        Email::dropTransport('default');

        $emailTask = new EmailTask();
        $emailTask->main([
            'email'       => new Email(),
            'fullBaseUrl' => 'http://example.com',
            'transport'   => 'default',
        ]);
    }

    public function testNoFullBaseUrl()
    {
        $emailTask = new EmailTask();

        $emailTask->main([
            'email' => new Email([
                'from' => 'from@example.com',
                'to'   => 'to@example.com',
            ]),
            'transport' => 'default',
        ]);
    }

    public function testUsingDebugTransport()
    {
        $emailTask = new EmailTask();

        $result = $emailTask->main([
            'email' => new Email([
                'from' => 'from@example.com',
                'to'   => 'to@example.com',
            ]),
            'fullBaseUrl' => 'http://example.com',
            'transport' => 'default',
        ]);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('headers', $result);
        $this->assertArrayHasKey('headers', $result);
    }

    public function tearDown()
    {
        Email::dropTransport('default');
    }
}
