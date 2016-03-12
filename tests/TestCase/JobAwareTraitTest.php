<?php

namespace CvoTechnologies\Gearman\Test\TestCase\Shell\Task;

use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;
use CvoTechnologies\Gearman\Gearman;
use CvoTechnologies\Gearman\JobAwareTrait;
use CvoTechnologies\Gearman\Shell\Task\EmailTask;

class JobAwareTraitClass
{
    use JobAwareTrait;
}

class JobAwareTraitTest extends TestCase
{
    public function setUp()
    {
        Configure::write('Gearman.Servers', [
            '127.0.0.1'
        ]);
    }

    public function testGearmanClient()
    {
        $jobAwareTraitClass = new JobAwareTraitClass();
        $gearmanClient = $jobAwareTraitClass->gearmanClient();

        $this->assertInstanceOf('\GearmanClient', $gearmanClient);
    }

    public function testGearmanWorker()
    {
        $jobAwareTraitClass = new JobAwareTraitClass();
        $gearmanClient = $jobAwareTraitClass->gearmanWorker();

        $this->assertInstanceOf('\GearmanWorker', $gearmanClient);
    }

    /**
     * @expectedException \Cake\Core\Exception\Exception
     * @expectedExceptionMessage Invalid Gearman configuration: you must configure at least one server
     */
    public function testInvalidServerConfiguration()
    {
        Configure::drop('Gearman.Servers');
        Configure::write('Gearman.Servers', []);

        $jobAwareTraitClass = new JobAwareTraitClass();
        $gearmanClient = $jobAwareTraitClass->gearmanWorker();

        $this->assertInstanceOf('\GearmanWorker', $gearmanClient);
    }

    /**
     * @dataProvider executeProvider
     */
    public function testExecute($job, $workload, $background, $priority)
    {
        $jobAwareTraitClass = new JobAwareTraitClass();
        $result = $jobAwareTraitClass->execute($job, $workload, $background, $priority);

        if ($background) {
            $this->assertContains(':', $result);
        } else {
            $this->assertEquals($workload, $result);
        }
    }

    public function executeProvider()
    {
        return [
            ['testJob', ['data'], false, Gearman::PRIORITY_NORMAL],
            ['testJob', ['data'], true, Gearman::PRIORITY_NORMAL],
            ['testJob', ['data'], false, Gearman::PRIORITY_LOW],
            ['testJob', ['data'], true, Gearman::PRIORITY_HIGH]
        ];
    }

    public function tearDown()
    {
        Configure::drop('Gearman');
    }
}
