<?php

use Cake\Core\Configure;

\Cake\Core\Configure::write('Jobs.emailWithWorker', [
    'className' => 'CvoTechnologies/Gearman.Email'
]);

