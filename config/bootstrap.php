<?php

use Cake\Core\Configure;
use Cake\Utility\Hash;

Configure::write('Gearman.Jobs.emailWithWorker', [
    'className' => 'CvoTechnologies/Gearman.Email'
]);

if (!Configure::read('debug')) {
    return;
}

Configure::write('DebugKit.panels', Hash::merge((array)Configure::read('DebugKit.panels'), [
    'CvoTechnologies/Gearman.Job'
]));
