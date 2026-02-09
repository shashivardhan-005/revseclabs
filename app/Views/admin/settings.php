<?= $this->extend('layout/admin') ?>

<?= $this->section('title') ?>Settings<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-white">Admin Settings</h2>
        <p class="text-white-50">Manage global configurations for branding, emails, and quiz behavior.</p>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs nav-fill border-0" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 fw-bold" id="branding-tab" data-bs-toggle="tab" data-bs-target="#branding" type="button" role="tab" aria-selected="true">
                            <i class="bi bi-palette2 me-2"></i> Branding
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 fw-bold" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab" aria-selected="false">
                            <i class="bi bi-envelope-at me-2"></i> Email Config
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 fw-bold" id="quiz-tab" data-bs-toggle="tab" data-bs-target="#quiz" type="button" role="tab" aria-selected="false">
                            <i class="bi bi-shield-lock me-2"></i> Quiz Defaults
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4 bg-white">
                <form action="<?= base_url('admin/settings/save') ?>" method="POST" id="settingsForm">
                    <?= csrf_field() ?>
                    <div class="tab-content" id="settingsTabsContent">
                        
                        <!-- Branding Tab -->
                        <div class="tab-pane fade show active" id="branding" role="tabpanel">
                            <div class="mb-4">
                                <label for="site_name" class="form-label fw-bold text-muted small text-uppercase">Site Name</label>
                                <input type="text" class="form-control form-control-lg bg-light border-0" id="site_name" name="site_name" value="<?= esc($settings['site_name'] ?? 'RevSecLabs') ?>" required>
                                <div class="form-text mt-2 text-muted">This name appears in browser tabs and emails.</div>
                            </div>
                            <div class="mb-4">
                                <label for="contact_email" class="form-label fw-bold text-muted small text-uppercase">Support Contact Email</label>
                                <input type="email" class="form-control form-control-lg bg-light border-0" id="contact_email" name="contact_email" value="<?= esc($settings['contact_email'] ?? 'revseclabs@gmail.com') ?>" required>
                                <div class="form-text mt-2 text-muted">Displayed in footers and contact sections.</div>
                            </div>
                            <div class="mb-4">
                                <label for="time_format" class="form-label fw-bold text-muted small text-uppercase">Global Time Format</label>
                                <select class="form-select form-control-lg bg-light border-0" id="time_format" name="time_format">
                                    <option value="12h" <?= ($settings['time_format'] ?? '24h') === '12h' ? 'selected' : '' ?>>12-hour (AM/PM)</option>
                                    <option value="24h" <?= ($settings['time_format'] ?? '24h') === '24h' ? 'selected' : '' ?>>24-hour (Military Time)</option>
                                </select>
                                <div class="form-text mt-2 text-muted">Choose how time is displayed and selected across the platform.</div>
                            </div>
                        </div>

                        <!-- Email Config Tab -->
                        <div class="tab-pane fade" id="email" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="email_service_mode" class="form-label fw-bold text-muted small text-uppercase">Email Service Mode</label>
                                    <select class="form-select form-control-lg bg-light border-0" id="email_service_mode" name="email_service_mode">
                                        <option value="smtp" <?= ($settings['email_service_mode'] ?? 'smtp') === 'smtp' ? 'selected' : '' ?>>Standard SMTP</option>
                                        <option value="api" <?= ($settings['email_service_mode'] ?? 'smtp') === 'api' ? 'selected' : '' ?>>External API (Custom)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="email_api_key" class="form-label fw-bold text-muted small text-uppercase">API Key (Future Use)</label>
                                    <input type="password" class="form-control form-control-lg bg-light border-0" id="email_api_key" name="email_api_key" value="<?= esc($settings['email_api_key'] ?? '') ?>" placeholder="Enter API Key">
                                </div>
                            </div>

                            <div class="smtp-settings mt-4">
                                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-server me-2"></i>SMTP Credentials</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-8">
                                        <label for="email_smtp_host" class="form-label fw-bold text-muted small text-uppercase">SMTP Host</label>
                                        <input type="text" class="form-control form-control-lg bg-light border-0" id="email_smtp_host" name="email_smtp_host" value="<?= esc($settings['email_smtp_host'] ?? 'smtp.gmail.com') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="email_smtp_port" class="form-label fw-bold text-muted small text-uppercase">Port</label>
                                        <input type="number" class="form-control form-control-lg bg-light border-0" id="email_smtp_port" name="email_smtp_port" value="<?= esc($settings['email_smtp_port'] ?? '587') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email_smtp_user" class="form-label fw-bold text-muted small text-uppercase">SMTP Username</label>
                                        <input type="text" class="form-control form-control-lg bg-light border-0" id="email_smtp_user" name="email_smtp_user" value="<?= esc($settings['email_smtp_user'] ?? 'revseclabs@gmail.com') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email_smtp_pass" class="form-label fw-bold text-muted small text-uppercase">SMTP Password</label>
                                        <input type="password" class="form-control form-control-lg bg-light border-0" id="email_smtp_pass" name="email_smtp_pass" value="<?= esc($settings['email_smtp_pass'] ?? 'qjcjuhtnxvxuaucu') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email_smtp_crypto" class="form-label fw-bold text-muted small text-uppercase">Encryption</label>
                                        <select class="form-select form-control-lg bg-light border-0" id="email_smtp_crypto" name="email_smtp_crypto">
                                            <option value="tls" <?= ($settings['email_smtp_crypto'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (Recommended)</option>
                                            <option value="ssl" <?= ($settings['email_smtp_crypto'] ?? 'tls') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                            <option value="" <?= ($settings['email_smtp_crypto'] ?? 'tls') === '' ? 'selected' : '' ?>>None</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email_smtp_verify_ssl" class="form-label fw-bold text-muted small text-uppercase">SSL Certificate Verification</label>
                                        <select class="form-select form-control-lg bg-light border-0" id="email_smtp_verify_ssl" name="email_smtp_verify_ssl">
                                            <option value="1" <?= ($settings['email_smtp_verify_ssl'] ?? '1') === '1' ? 'selected' : '' ?>>Enabled (Production)</option>
                                            <option value="0" <?= ($settings['email_smtp_verify_ssl'] ?? '1') === '0' ? 'selected' : '' ?>>Disabled (Trial/Local)</option>
                                        </select>
                                        <div class="form-text mt-2 text-muted">Disable this if you get "SSL certificate verify failed" errors on local servers.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-3 border-top">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="email_sender_name" class="form-label fw-bold text-muted small text-uppercase">Sender Display Name</label>
                                        <input type="text" class="form-control form-control-lg bg-light border-0" id="email_sender_name" name="email_sender_name" value="<?= esc($settings['email_sender_name'] ?? 'RevSecLabs Admin') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email_sender_email" class="form-label fw-bold text-muted small text-uppercase">Sender Email Address</label>
                                        <input type="email" class="form-control form-control-lg bg-light border-0" id="email_sender_email" name="email_sender_email" value="<?= esc($settings['email_sender_email'] ?? 'revseclabs@gmail.com') ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quiz Defaults Tab -->
                        <div class="tab-pane fade" id="quiz" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="default_passing_score" class="form-label fw-bold text-muted small text-uppercase">Passing Score (%)</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control form-control-lg bg-light border-0" id="default_passing_score" name="default_passing_score" value="<?= esc($settings['default_passing_score'] ?? '70') ?>" min="0" max="100" required>
                                        <span class="input-group-text bg-light border-0 fw-bold text-muted">%</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="default_violation_limit" class="form-label fw-bold text-muted small text-uppercase">Security Violation Limit</label>
                                    <input type="number" class="form-control form-control-lg bg-light border-0" id="default_violation_limit" name="default_violation_limit" value="<?= esc($settings['default_violation_limit'] ?? '3') ?>" min="1" max="10" required>
                                    <div class="form-text mt-2 text-muted">Max fullscreen exits/tab switches allowed.</div>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <hr class="my-4 opacity-10">
                    
                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-outline-secondary px-4 py-2 border-0 fw-bold">Discard Changes</button>
                        <button type="button" onclick="confirmFormSubmit(document.getElementById('settingsForm'), 'Save global application settings?')" class="btn btn-primary px-5 py-2 shadow-sm fw-bold">
                            <i class="bi bi-cloud-check-fill me-2"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); border-radius: 15px;">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-info-circle" style="font-size: 3rem;"></i>
                </div>
                <h4 class="fw-bold mb-3">System Information</h4>
                <p class="mb-4 small opacity-75">
                    Modifying these settings will affect the entire platform experience for all users. Changes take effect immediately upon saving.
                </p>
                <div class="p-3 bg-white bg-opacity-10 rounded-3 mb-3 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small opacity-75">Framework</span>
                        <span class="small fw-bold">CodeIgniter 4</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small opacity-75">Base URL</span>
                        <span class="small fw-bold overflow-hidden" style="max-width: 150px; text-overflow: ellipsis;"><?= base_url() ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="small opacity-75">Server Time</span>
                        <span class="small fw-bold"><?= date('H:i') ?> (IST)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        color: #64748b;
        border-bottom: 2px solid transparent;
        transition: all 0.3s;
    }
    .nav-tabs .nav-link.active {
        color: #3b82f6;
        background: transparent;
        border-bottom-color: #3b82f6;
    }
    .form-control:focus {
        background-color: #f8fafc !important;
        box-shadow: none;
        border-color: #3b82f6 !important;
        border-width: 0 0 2px 0 !important;
        border-radius: 0;
    }
</style>
<?= $this->endSection() ?>
