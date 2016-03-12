# CakePHP Gearman plugin


[![Build Status](https://img.shields.io/travis/cvo-technologies/cakephp-gearman/master.svg?style=flat-square)](https://travis-ci.org/cvo-technologies/cakephp-gearman)
[![StyleCI Status](https://styleci.io/repos/43746752/shield)](https://styleci.io/repos/43746752)
[![Coverage Status](https://img.shields.io/codecov/c/github/cvo-technologies/cakephp-gearman/master.svg?style=flat-square)](https://codecov.io/github/cvo-technologies/cakephp-gearman)
[![Total Downloads](https://img.shields.io/packagist/dt/cvo-technologies/cakephp-gearman.svg?style=flat-square)](https://packagist.org/packages/cvo-technologies/cakephp-gearman)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.txt)

Gearman task offloading for CakePHP 3.

## Requirements

- PHP 5.5.9+
- CakePHP 3.2+
- [Gearman Job Server](http://gearman.org)
- [Gearman PHP extension](http://php.net/manual/en/book.gearman.php)

## Why use this plugin?

Use this plugin to drastically reduce page load times by offloading
time consuming processes (like sending emails and resizing uploaded images) to
a Gearman Job Server.

## Installation

Install the plugin using [Composer](https://getcomposer.org):

```
composer require cvo-technologies/cakephp-gearman
```

Now load the plugin by either running this shell command:

```
bin/cake plugin load CvoTechnologies/Gearman --bootstrap
```

or by manually adding the following line to ``config/bootstrap.php``:

```php
Plugin::load('CvoTechnologies/Gearman', ['bootstrap' => true]);
```

Lastly, add a new `Gearman` configuration section to (most likely) `app.php`:

```php
    'Gearman' => [
        'Servers' => [
            '127.0.0.1:4730'
        ],
        'Jobs' => [

        ]
    ]
```

### Optional: system verification

Before proceeding you might want to verify that the
[Gearman Job Server](http://gearman.org//getting-started) is actually up
and running on your local system.

On Ubuntu systems running `sudo netstat -peanut | grep gearman` should
produce something similar to:

```
tcp      0     0 127.0.0.1:4730     0.0.0.0:*     LISTEN     0     9727     625/gearmand
tcp6     0     0 ::1:4730           :::*          LISTEN     0     9726     625/gearmand
```

## Usage

Using this plugin comes down to:

1. Configuring your task(s)
2. Starting the `WorkerShell` on your local system
3. Offloading tasks from within your application code by using the `execute()`
function found in the `JobAwareTrait`

To start the `WorkerShell` so it will listen for incoming tasks run the
following command on your local system:

```
bin/cake worker
```

## Built-in Tasks

### Email Task

This plugin comes with a built-in email task that allows you to start
offloading emails using the worker instantly.

To enable the email task first add the following job to your Gearman
configuration section:

```php
    'Jobs' => [
        'className' => 'Email'
    ]
```

Then add the following worker configuration to your existing EmailTransporter
configuration section (most likely found in `app.php`):

```php
'worker' => [
    'className' => 'CvoTechnologies/Gearman.Worker',
    'transport' => 'default',
    'background' => true
]
```

Now all you need to do is use this EmailTransporter in your application
when sending emails and it will automatically offload all email sending to the
built-in task using the EmailTransporter defined in the `transport` key. E.g.

```php
$email = new Email('default');
$res = $email->from(['you@example.com' => 'Your Site'])
    ->to('recipient@sexample.com')
    ->subject('Testing cakephp-gearman built-in EmailTask')
    ->send('Your message');
```

If things went well you should see the worker providing feedback on tasks being
processed shown below:

![Worker feedback](/docs/screenshot-worker-email.png)

## Creating your own tasks

### 1. Create the Task

As an example we will create the following `SleepTask` that:

- will be used as a Gearman job
- must be created in `src/Shell/Task/SleepTask.php`
- must contain a `main()` function

```php
<?php
namespace CvoTechnologies\Gearman\Shell\Task;

use Cake\Console\Shell;

class SleepTask extends Shell
{

    public function main($workload, GearmanJob $job)
    {
        $job->sendStatus(0, 3);

        sleep($workload['timeout']);

        $job->sendStatus(1, 3);

        sleep($workload['timeout']);

        $job->sendStatus(2, 3);

        sleep($workload['timeout']);

        return array(
            'total_timeout' => $workload['timeout'] * 3
        );
    }
}
```

> Please note that the plugin will take care of arrays and objects. When you
> submit an array in the task, you will receive an array in the workload.

### 2. Start using the task

To start using the task:

1. include the `JobAwareTrait` in your application code
2. use the `$this->execute` function to pass the job to Gearman

Please note that the `execute()` method takes the following parameters:

- `$name`: name of the job (task in cakephp)
- `$workload`: mixed, can be either an array, string, int or everything else
- `$background`: boolean, true to run in background
- `$priority`: Gearman::PRIORITY_NORMAL, _LOW, _NORMAL or _HIGH
