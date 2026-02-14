<?php
    // Capture the title section once so it can be reused
    ob_start();
    echo $this->renderSection('title');
    $pageTitle = ob_get_clean();
    $pageTitle = $pageTitle ?: 'Admin Control Panel';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle; ?> | <?= get_setting('site_name', 'RevSecLabs') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('static/css/admin_custom.css') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('static/images/revseclabs-logo.png') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <?= $this->renderSection('extra_css') ?>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <img src="<?= base_url('static/images/revseclabs-logo.png') ?>" alt="RevSecLabs" height="30" class="me-2">
            <span><?= get_setting('site_name', 'RevSecLabs') ?></span>
        </div>
        <ul class="sidebar-nav">
            <li><a href="<?= base_url('admin') ?>" class="<?= (current_url() == base_url('admin')) ? 'active' : '' ?>"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a></li>
            <li><a href="<?= base_url('admin/users') ?>" class="<?= (current_url() == base_url('admin/users')) ? 'active' : '' ?>"><i class="bi bi-people-fill"></i><span>Users</span></a></li>
            <li><a href="<?= base_url('admin/quizzes') ?>" class="<?= (current_url() == base_url('admin/quizzes')) ? 'active' : '' ?>"><i class="bi bi-journal-text"></i><span>Quizzes</span></a></li>
            <li>
                <a href="<?= base_url('admin/assignments') ?>" class="<?= (current_url() == base_url('admin/assignments')) ? 'active' : '' ?>">
                    <i class="bi bi-clipboard-check-fill"></i>
                    <span>Assignments</span>
                    <?php if (isset($retest_requests_count) && $retest_requests_count > 0): ?>
                        <span id="retest-badge" class="badge rounded-pill bg-danger ms-auto"><?= $retest_requests_count ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="<?= base_url('admin/questions') ?>" class="<?= (current_url() == base_url('admin/questions')) ? 'active' : '' ?>"><i class="bi bi-database-fill"></i><span>Question Bank</span></a></li>
            <li>
                <a href="<?= base_url('admin/profile-requests') ?>" class="<?= (current_url() == base_url('admin/profile-requests')) ? 'active' : '' ?>">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Approvals</span>
                    <?php if (isset($profile_requests_count) && $profile_requests_count > 0): ?>
                        <span id="profile-badge" class="badge rounded-pill bg-danger ms-auto"><?= $profile_requests_count ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="<?= base_url('admin/analytics') ?>" class="<?= (current_url() == base_url('admin/analytics')) ? 'active' : '' ?>"><i class="bi bi-bar-chart-fill"></i><span>Reports</span></a></li>
            <li><a href="<?= base_url('admin/audit-logs') ?>" class="<?= (current_url() == base_url('admin/audit-logs')) ? 'active' : '' ?>"><i class="bi bi-file-text-fill"></i><span>Audit Logs</span></a></li>
            <li><a href="<?= base_url('admin/settings') ?>" class="<?= (current_url() == base_url('admin/settings')) ? 'active' : '' ?>"><i class="bi bi-gear-fill"></i><span>Settings</span></a></li>
            <li class="mt-4"><a href="<?= base_url('dashboard') ?>"><i class="bi bi-arrow-left-circle-fill"></i><span>User Panel</span></a></li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- TOP HEADER -->
        <header class="top-header">
            <div class="header-info d-flex align-items-center justify-content-between w-100 px-4">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-link text-white p-0 d-lg-none" id="sidebarToggle">
                        <i class="bi bi-list fs-3"></i>
                    </button>
                    <span class="header-quiz-name">
                        <?php echo $pageTitle; ?>
                    </span>
                </div>
                <div class="header-actions d-flex align-items-center gap-3">
                    <?php if (isset($pending_requests_count) && $pending_requests_count > 0): ?>
                        <a href="<?= base_url('admin/profile-requests') ?>" class="text-white position-relative me-2" title="Pending Profile Requests">
                            <i class="bi bi-bell-fill fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">New requests</span>
                            </span>
                        </a>
                    <?php endif; ?>
                    <span class="me-3 text-muted d-none d-md-inline">Welcome, <?= session()->get('first_name') ?></span>
                    <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm">Logout</a>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <div class="dashboard-content p-4">
            <?= $this->renderSection('content') ?>
        </div>

        <!-- FOOTER -->
        <footer class="text-center py-3 border-top bg-light" style="font-size: 0.875rem; color: #6c757d;">
            <span>© <?= date('Y') ?> <?= get_setting('site_name', 'RevSecLabs') ?>. All rights reserved.</span>
            <span class="mx-2">|</span>
            <span>Support: <a href="mailto:<?= get_setting('contact_email', 'revseclabs@gmail.com') ?>" class="text-decoration-none"><?= get_setting('contact_email', 'revseclabs@gmail.com') ?></a></span>
        </footer>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
    <script>
        // Real-time Notification Polling
        let currentProfileCount = <?= $profile_requests_count ?? 0 ?>;
        let currentRetestCount = <?= $retest_requests_count ?? 0 ?>;

        function updateNotifications() {
            fetch('<?= base_url('admin/get-pending-count') ?>')
                .then(response => response.json())
                .then(data => {
                    const totalNew = data.profile_count + data.retest_count;
                    const totalOld = currentProfileCount + currentRetestCount;

                    if (totalNew > totalOld) {
                        // Show Browser Notification
                        if (Notification.permission === "granted") {
                            new Notification("New Admin Action Required", {
                                body: `You have new requests: ${data.profile_count} Approvals, ${data.retest_count} Retests.`,
                                icon: '<?= base_url('static/images/revseclabs-logo.png') ?>'
                            });
                        }
                    }

                    if (data.profile_count !== currentProfileCount || data.retest_count !== currentRetestCount) {
                        currentProfileCount = data.profile_count;
                        currentRetestCount = data.retest_count;
                        refreshNotificationUI(data.profile_count, data.retest_count);
                    }
                })
                .catch(err => console.error('Notification poll failed:', err));
        }

        function updateTabTitle(count) {
            const baseTitle = "<?php echo $pageTitle; ?> | <?= get_setting('site_name', 'RevSecLabs') ?>";
            if (count > 0) {
                document.title = `(${count}) ${baseTitle}`;
            } else {
                document.title = baseTitle;
            }
        }

        function refreshNotificationUI(pCount, rCount) {
            // 1. Update Profile Badge
            const pBadge = document.getElementById('profile-badge');
            const pLink = document.querySelector('a[href*="profile-requests"]');
            if (pCount > 0) {
                if (pBadge) {
                    pBadge.textContent = pCount;
                } else if (pLink) {
                    const badge = document.createElement('span');
                    badge.id = 'profile-badge';
                    badge.className = 'badge rounded-pill bg-danger ms-auto';
                    badge.textContent = pCount;
                    pLink.appendChild(badge);
                }
            } else if (pBadge) {
                pBadge.remove();
            }

            // 2. Update Retest Badge
            const rBadge = document.getElementById('retest-badge');
            const rLink = document.querySelector('a[href*="admin/assignments"]');
            if (rCount > 0) {
                if (rBadge) {
                    rBadge.textContent = rCount;
                } else if (rLink) {
                    const badge = document.createElement('span');
                    badge.id = 'retest-badge';
                    badge.className = 'badge rounded-pill bg-danger ms-auto';
                    badge.textContent = rCount;
                    rLink.appendChild(badge);
                }
            } else if (rBadge) {
                rBadge.remove();
            }

            // 3. Update Browser Tab Title
            updateTabTitle(pCount + rCount);
        }

        // Request permission on first interaction
        if (Notification.permission !== "granted" && Notification.permission !== "denied") {
            document.addEventListener('click', function() {
                Notification.requestPermission();
            }, { once: true });
        }

        // Poll every 30 seconds
        setInterval(updateNotifications, 30000);
    </script>
    <?= $this->renderSection('extra_js') ?>
</body>
</html>
