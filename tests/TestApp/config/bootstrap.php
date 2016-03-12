<?php

\Cake\Core\Configure::write('App.namespace', 'TestApp');
\Cake\Core\Configure::write('Gearman.Jobs', [
    'testJob' => [
        'className' => 'TestJob',
    ],
]);
