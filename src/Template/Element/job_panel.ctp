<table>
    <thead>

    </thead>
    <tbody>
    <?php foreach ($jobs as $job): ?>
    <tr>
        <td><?= h($job['name']); ?></td>
        <td><?= $this->Toolbar->makeNeatArray(unserialize($job['workload'])); ?></td>
        <td><?= h(($job['background']) ? __('Yes') : __('No')); ?></td>
        <td>
            <?php
            switch ($job['priority']):
                case \CvoTechnologies\Gearman\Gearman::PRIORITY_LOW:
                    echo h(__('Low'));
                    break;
                case \CvoTechnologies\Gearman\Gearman::PRIORITY_NORMAL:
                    echo h(__('Normal'));
                    break;
                case \CvoTechnologies\Gearman\Gearman::PRIORITY_HIGH:
                    echo h(__('High'));
                    break;
            endswitch;
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
