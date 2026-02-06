<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Audit Logs<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="card-title mb-0">System Audit Logs</h3>
        <span class="badge bg-primary">Live Monitoring</span>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Timestamp</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="text-nowrap small"><?= date('Y-m-d H:i:s', strtotime($log['timestamp'])) ?></td>
                    <td><?= $log['email'] ?: '<span class="text-muted">System</span>' ?></td>
                    <td>
                        <span class="badge <?= 
                            ($log['action'] == 'CHEAT_VIOLATION') ? 'bg-danger' : 
                            (($log['action'] == 'QUIZ_SUBMIT') ? 'bg-success' : 'bg-secondary') 
                        ?>">
                            <?= $log['action'] ?>
                        </span>
                    </td>
                    <td class="small"><?= $log['details'] ?></td>
                    <td class="small text-muted"><?= $log['ip_address'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        <?= $pager->links() ?>
    </div>
</div>
<?= $this->endSection() ?>
