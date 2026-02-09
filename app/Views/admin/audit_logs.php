<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Audit Logs<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="card-title mb-0">System Audit Logs</h3>
        <span class="badge bg-primary">Live Monitoring</span>
    </div>

    <!-- Filters -->
    <form action="<?= base_url('admin/audit-logs') ?>" method="get" class="row g-3 mb-4">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by email, name or details..." value="<?= esc($filters['search']) ?>">
            </div>
        </div>
        <div class="col-md-3">
            <select name="action" class="form-select">
                <option value="">All Actions</option>
                <?php foreach ($actions as $act): ?>
                    <option value="<?= esc($act) ?>" <?= $filters['action'] == $act ? 'selected' : '' ?>><?= str_replace('_', ' ', $act) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100">Filter Logs</button>
        </div>
        <?php if (!empty($filters['search']) || !empty($filters['action'])): ?>
            <div class="col-md-2">
                <a href="<?= base_url('admin/audit-logs') ?>" class="btn btn-outline-secondary w-100">Clear Filters</a>
            </div>
        <?php endif; ?>
    </form>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Timestamp</th>
                    <th>User Email</th>
                    <th>Full Name</th>
                    <th>Action</th>
                    <th style="width: 40%;">Details</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $timeFormatSetting = get_setting('time_format', '24h');
                    $displayFormat = ($timeFormatSetting === '12h') ? 'Y-m-d h:i:s A' : 'Y-m-d H:i:s';
                ?>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="text-nowrap small"><?= date($displayFormat, strtotime($log['timestamp'])) ?></td>
                    <td><?= $log['email'] ?: '<span class="text-muted">System</span>' ?></td>
                    <td><?= $log['first_name'] ? ($log['first_name'] . ' ' . $log['last_name']) : '<span class="text-muted">N/A</span>' ?></td>
                    <td>
                        <span class="badge <?= 
                            ($log['action'] == 'CHEAT_VIOLATION') ? 'bg-danger' : 
                            (($log['action'] == 'QUIZ_SUBMIT') ? 'bg-success' : 
                            (($log['action'] == 'LOGIN') ? 'bg-info' : 
                            (($log['action'] == 'LOGIN_FAILED') ? 'bg-warning text-dark' : 
                            (($log['action'] == 'ADMIN_ACCESS') ? 'bg-secondary' : 'bg-dark')))) 
                        ?>">
                            <?= str_replace('_', ' ', $log['action']) ?>
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
        <?= $pager->links('default', 'bootstrap_full') ?>
    </div>
</div>
<?= $this->endSection() ?>
