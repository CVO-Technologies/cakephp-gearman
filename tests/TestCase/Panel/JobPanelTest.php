<?php

namespace CvoTechnologies\Gearman\Test\TestCase\Shell\Task;

use Cake\Core\Configure;
use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;
use CvoTechnologies\Gearman\DebugJob;
use CvoTechnologies\Gearman\Gearman;
use CvoTechnologies\Gearman\Panel\JobPanel;
use CvoTechnologies\Gearman\Shell\Task\EmailTask;

class JobPanelTest extends TestCase
{
    public function setUp()
    {
        DebugJob::$jobs = [
            [
                'name' => 'job',
                'workload' => [],
                'background' => true,
                'priority' => Gearman::PRIORITY_HIGH
            ],
            [
                'name' => 'job',
                'workload' => [],
                'background' => true,
                'priority' => Gearman::PRIORITY_LOW
            ],
            [
                'name' => 'job',
                'workload' => [],
                'background' => true,
                'priority' => Gearman::PRIORITY_NORMAL
            ]
        ];
    }

    public function testSummary()
    {
        $panel = new JobPanel();

        $this->assertEquals(3, $panel->summary());
    }

    public function testData()
    {
        $panel = new JobPanel();

        $this->assertEquals([
            'jobs' => [
                [
                    'name' => 'job',
                    'workload' => [],
                    'background' => true,
                    'priority' => Gearman::PRIORITY_HIGH
                ],
                [
                    'name' => 'job',
                    'workload' => [],
                    'background' => true,
                    'priority' => Gearman::PRIORITY_LOW
                ],
                [
                    'name' => 'job',
                    'workload' => [],
                    'background' => true,
                    'priority' => Gearman::PRIORITY_NORMAL
                ]
            ]
        ], $panel->data());
    }

    public function tearDown()
    {
        DebugJob::$jobs = [];
    }
}
