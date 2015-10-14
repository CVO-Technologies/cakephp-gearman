<?php

use Cake\Core\Configure;
use Cake\Utility\Hash;

\Cake\Core\Configure::write('Jobs.emailWithWorker', [
    'className' => 'CvoTechnologies/Gearman.Email'
]);

Configure::write('DebugKit.panels', Hash::merge((array)Configure::read('DebugKit.panels'), [
    'CvoTechnologies/Gearman.Job'
]));
