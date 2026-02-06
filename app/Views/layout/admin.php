<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?> | RevSecLabs Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('static/css/admin_custom.css') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('static/images/revseclabs-logo.png') ?>">
    <?= $this->renderSection('extra_css') ?>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <img src="<?= base_url('static/images/revseclabs-logo.png') ?>" alt="RevSecLabs" height="30" class="me-2">
            <span>RevSecLabs</span>
        </div>
        <ul class="sidebar-nav">
            <li><a href="<?= base_url('admin') ?>" class="<?= (current_url() == base_url('admin')) ? 'active' : '' ?>"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a></li>
            <li><a href="<?= base_url('admin/users') ?>" class="<?= (current_url() == base_url('admin/users')) ? 'active' : '' ?>"><i class="bi bi-people-fill"></i><span>Users</span></a></li>
            <li><a href="<?= base_url('admin/quizzes') ?>" class="<?= (current_url() == base_url('admin/quizzes')) ? 'active' : '' ?>"><i class="bi bi-journal-text"></i><span>Quizzes</span></a></li>
            <li><a href="<?= base_url('admin/assignments') ?>" class="<?= (current_url() == base_url('admin/assignments')) ? 'active' : '' ?>"><i class="bi bi-clipboard-check-fill"></i><span>Assignments</span></a></li>
            <li><a href="<?= base_url('admin/questions') ?>" class="<?= (current_url() == base_url('admin/questions')) ? 'active' : '' ?>"><i class="bi bi-database-fill"></i><span>Question Bank</span></a></li>
            <li><a href="<?= base_url('admin/profile-requests') ?>" class="<?= (current_url() == base_url('admin/profile-requests')) ? 'active' : '' ?>"><i class="bi bi-check-circle-fill"></i><span>Approvals</span></a></li>
            <li><a href="<?= base_url('admin/analytics') ?>" class="<?= (current_url() == base_url('admin/analytics')) ? 'active' : '' ?>"><i class="bi bi-bar-chart-fill"></i><span>Reports</span></a></li>
            <li><a href="<?= base_url('admin/audit-logs') ?>" class="<?= (current_url() == base_url('admin/audit-logs')) ? 'active' : '' ?>"><i class="bi bi-file-text-fill"></i><span>Audit Logs</span></a></li>
            <li><a href="#"><i class="bi bi-gear-fill"></i><span>Settings</span></a></li>
            <li class="mt-4"><a href="<?= base_url('dashboard') ?>"><i class="bi bi-arrow-left-circle-fill"></i><span>User Panel</span></a></li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- TOP HEADER -->
        <header class="top-header">
            <div class="header-info d-flex align-items-center justify-content-between w-100 px-4">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-link text-white p-0 d-md-none" id="sidebarToggle">
                        <i class="bi bi-list fs-3"></i>
                    </button>
                    <span class="header-quiz-name">
                        Admin Control Panel
                    </span>
                </div>
                <div class="header-actions">
                    <span class="me-3 text-muted d-none d-md-inline">Welcome, <?= session()->get('first_name') ?></span>
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm">Logout</a>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <div class="dashboard-content p-4">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Generic Confirmation Modal -->
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Action</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="lead mb-0" id="confirmMessage">Are you sure you want to proceed?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmBtn" class="btn btn-danger px-4">Confirm</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile Sidebar Toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const body = document.body;

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                body.classList.toggle('sidebar-open');
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function() {
                body.classList.remove('sidebar-open');
            });
        }

        let confirmCallback = null;
        const confirmBtn = document.getElementById('confirmBtn');

        function resetModal() {
            confirmBtn.onclick = null;
            confirmBtn.removeAttribute('href');
            confirmCallback = null;
        }

        function confirmModal(url, message = 'Are you sure you want to perform this action? This cannot be undone.') {
            resetModal();
            document.getElementById('confirmBtn').setAttribute('href', url);
            document.getElementById('confirmMessage').innerText = message;
            
            const modal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
            modal.show();
        }

        function confirmFormSubmit(form, message = 'Are you sure?') {
            resetModal();
            document.getElementById('confirmMessage').innerText = message;
            confirmBtn.setAttribute('href', '#'); // Dummy href
            
            confirmBtn.onclick = function(e) {
                e.preventDefault();
                form.submit();
            };

            const modal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
            modal.show();
        }
    </script>
    <?= $this->renderSection('extra_js') ?>
</body>
</html>
