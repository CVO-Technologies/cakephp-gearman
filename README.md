# CakePHP Gearman plugin
A gearman plugin for CakePHP 3.x

## How to use

### Installation

First load this plugin using Composer:
```
composer require cvo-technologies/cakephp-gearman
```
Add the plugin to your bootstrap:
```
bin/cake plugin load CvoTechnologies/Gearman
```

Now it's loaded and you should be able to start a worker:
```
bin/cake worker
```

Add the following to your config:
```php
    'Gearman' => [
        'Servers' => [
            '127.0.0.1'
        ]
    ],
    'Jobs' => [
    ]
```

### Create a job
We use a Shell Task as job, so for example we create a `SleepTask` located at `src/Shell/Task/SleepTask.php` with the this main method:

```php
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
```

The plugin takes care of arrays and objects. When you submit an array in the job, you will receive an array in the workload.

Add the job to the job configuration `Jobs`:
```php
'sleep' => [
    'className' => 'Sleep'
],
```

### Start a job
Use the `JobAwareTrait` trait in your class and use `$this->execute` to execute a job. You can pass the following parameters to this method:
* $name
  * Name of the job (task in cakephp)
* $workload
  * Mixed, can be either an array, string, int or everything else.
* $background = true
  * Run in background. This function returns the unique id.
* $priority = Gearman::PRIORITY_NORMAL.
  * _LOW, _NORMAL or _HIGH.

### E-mail job
By default, we ship a e-mail job. This job will sent e-mails as in a worker, which dramaticly improves page load times. If you want to use this e-mail job, add a new EmailTransporter to your `EmailTransport` config:
```php
'worker' => [
    'className' => 'CvoTechnologies/Gearman.Worker',
    'transport' => 'default',
    'background' => true
]
```

Then use this EmailTransporter to send the e-mails. The job will send the e-mails using the EmailTransporter defined in the `transport` key.
