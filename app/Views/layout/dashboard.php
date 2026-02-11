<?php
    ob_start();
    echo $this->renderSection('title');
    $pageTitle = ob_get_clean();
    $pageTitle = $pageTitle ?: 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle; ?> | <?= get_setting('site_name', 'RevSecLabs') ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('static/images/revseclabs-logo.png') ?>">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Dashboard CSS -->
    <link href="<?= base_url('static/css/dashboard_custom.css') ?>" rel="stylesheet">

    <?= $this->renderSection('extra_css') ?>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="dash-sidebar" id="dashSidebar">
        <div class="sidebar-brand">
            <img src="<?= base_url('static/images/revseclabs-logo.png') ?>" alt="RevSecLabs" height="28">
            <span><?= get_setting('site_name', 'RevSecLabs') ?></span>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>
            <?php $currentPath = uri_string(); ?>
            <a href="<?= base_url('dashboard') ?>" class="nav-item <?= ($currentPath === 'dashboard' || $currentPath === '') ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('dashboard') ?>#assessments" class="nav-item">
                <i class="bi bi-journal-check"></i>
                <span>Assessments</span>
            </a>
            <a href="<?= base_url('dashboard') ?>#results" class="nav-item">
                <i class="bi bi-award-fill"></i>
                <span>Results & Certificates</span>
            </a>

            <div class="nav-section-label mt-4">Account</div>
            <a href="<?= base_url('profile') ?>" class="nav-item <?= ($currentPath === 'profile') ? 'active' : '' ?>">
                <i class="bi bi-person-fill"></i>
                <span>View Profile</span>
            </a>
            <a href="<?= base_url('profile/edit') ?>" class="nav-item <?= ($currentPath === 'profile/edit') ? 'active' : '' ?>">
                <i class="bi bi-person-gear"></i>
                <span>Update Profile</span>
            </a>
            <a href="<?= base_url('password/change') ?>" class="nav-item <?= ($currentPath === 'password/change') ? 'active' : '' ?>">
                <i class="bi bi-key-fill"></i>
                <span>Password Update</span>
            </a>

            <?php if (session()->get('is_staff')): ?>
            <div class="nav-section-label mt-4">Admin</div>
            <a href="<?= base_url('admin') ?>" class="nav-item">
                <i class="bi bi-speedometer2"></i>
                <span>Admin Panel</span>
            </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= base_url('profile') ?>" class="user-card" style="text-decoration:none;">
                <div class="user-avatar">
                    <?= strtoupper(substr(session()->get('first_name'), 0, 1)) ?><?= strtoupper(substr(session()->get('last_name'), 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?= session()->get('first_name') ?> <?= session()->get('last_name') ?></div>
                    <div class="user-dept"><?= session()->get('department') ?: 'Employee' ?></div>
                </div>
                <i class="bi bi-chevron-right" style="color:rgba(255,255,255,0.3); margin-left:auto;"></i>
            </a>
        </div>
    </aside>

    <!-- SIDEBAR OVERLAY (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="dash-main">
        <!-- TOP HEADER -->
        <header class="dash-header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-dark p-0 d-lg-none sidebar-toggle" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div>
                    <h5 class="mb-0 fw-bold"><?php echo $pageTitle; ?></h5>
                    <small class="text-muted"><?= date('l, F d, Y') ?></small>
                </div>
            </div>
            <div class="header-actions">
                <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm px-3 rounded-pill">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </header>

        <!-- FLASH MESSAGES -->
        <div class="dash-content">
            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </div>

        <!-- FOOTER -->
        <footer class="dash-footer">
            <span>© <?= date('Y') ?> <?= get_setting('site_name', 'RevSecLabs') ?>. All rights reserved.</span>
            <span>Designed for Professional Awareness.</span>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle for Mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('dashSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                document.body.classList.toggle('sidebar-open');
            });
        }
        if (overlay) {
            overlay.addEventListener('click', () => {
                document.body.classList.remove('sidebar-open');
            });
        }
    </script>
    <?= $this->renderSection('extra_js') ?>
</body>
</html>
