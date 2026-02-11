<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?>Quiz In Progress<?= $this->endSection() ?>

<?= $this->section('extra_css') ?>
<style>
    /* Disable selection */
    body {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    .watermark {
        position: fixed;
        bottom: 10px;
        right: 10px;
        opacity: 0.5;
        font-size: 0.8rem;
    }

    /* Ensure Violation Modal is above Fullscreen Overlay */
    #violationModal {
        z-index: 10000 !important;
    }

    #quiz-start-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #ffffff;
        z-index: 9000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Begin Quiz Overlay -->
<div id="quiz-start-overlay">
    <div class="start-box text-center p-5 border rounded bg-white shadow">
        <div class="mb-4">
            <i class="bi bi-shield-lock-fill display-1 text-primary"></i>
        </div>
        <h2>Ready to Begin?</h2>
        <p>This quiz <?= $quiz['force_full_screen'] ? 'requires <strong>Full-Screen Mode</strong>. Once you click the button below, the timer will start and you must remain in full-screen until completion.' : 'will begin once you click the button below. The timer will start immediately.' ?></p>
        <button class="btn btn-primary btn-lg px-5" onclick="startQuiz()">Begin Quiz Now</button>
    </div>
</div>

<div class="container mt-4" id="quiz-container" style="display: none; opacity: 0; transition: opacity 0.5s;">
    <div class="row mb-3">
        <div class="col d-flex justify-content-between align-items-center">
            <h4><?= $quiz['name'] ?></h4>
            <div class="badge bg-danger fs-5" id="timer">Loading...</div>
        </div>
    </div>

    <!-- Question Navigator -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h6 class="card-subtitle mb-2 text-muted">Question Navigator</h6>
            <div class="d-flex flex-wrap gap-2" id="question-navigator">
                <?php foreach ($questions as $index => $q): ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle" style="width: 35px; height: 35px; padding: 0;"
                            id="nav-btn-<?= $index ?>" onclick="showQuestion(<?= $index ?>)">
                        <?= $index + 1 ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <form method="post" action="<?= base_url('quiz/'.$assignment['id'].'/submit') ?>" id="quiz-form">
        <?= csrf_field() ?>
        <input type="hidden" name="submission_reason" id="submission_reason" value="User Submitted">

        <?php $totalQuestions = count($questions); ?>
        <?php foreach ($questions as $index => $q): ?>
        <div class="card mb-4 shadow-sm question-card <?= $index === 0 ? '' : 'd-none' ?>" id="q-card-<?= $index ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Question <?= $index + 1 ?> of <?= $totalQuestions ?></h5>
                    <span class="badge bg-light text-dark border">
                         <?= ucfirst(str_replace('_', ' ', $q['type'])) ?>
                    </span>
                </div>
                
                <p class="card-text lead"><?= $q['text'] ?></p>

                <?php if ($q['image_url']): ?>
                <div class="mb-3 text-center">
                    <img src="<?= $q['image_url'] ?>" alt="Question Image" class="img-fluid rounded border"
                        style="max-height: 400px;">
                </div>
                <?php endif; ?>

                <div class="mt-4 mb-4">
                    <?php foreach ($q['options'] as $opt): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="response_<?= $q['response_id'] ?>" id="opt_<?= $opt['id'] ?>"
                            value="<?= $opt['id'] ?>" onchange="markAnswered(<?= $index ?>)">
                        <label class="form-check-label" for="opt_<?= $opt['id'] ?>">
                            <?= $opt['text'] ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary px-4" 
                            onclick="showQuestion(<?= $index - 1 ?>)" 
                            <?= $index === 0 ? 'disabled' : '' ?>>
                        <i class="bi bi-chevron-left me-1"></i> Previous
                    </button>
                    
                    <?php if ($index < $totalQuestions - 1): ?>
                        <button type="button" class="btn btn-primary px-4" 
                                onclick="showQuestion(<?= $index + 1 ?>)">
                            Next <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-success px-5 fw-bold">
                            Submit Quiz <i class="bi bi-check-lg ms-1"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </form>
</div>

<!-- Anti-Cheat Overlay for Fullscreen -->
<div id="fullscreen-overlay"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:9999; color:white; text-align:center; padding-top:20%;">
    <h2>Full Screen Required</h2>
    <p>Please return to full screen execution to continue the quiz.</p>
    <button class="btn btn-primary" onclick="requestFullScreen()">Return to Full Screen</button>
</div>

<!-- Auto-Submit / Loading Modal -->
<div class="modal fade" id="autoSubmitModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="spinner-border text-primary mb-3" role="status"></div>
            <h5 id="autoSubmitMessage">Submitting Quiz...</h5>
            <p class="text-muted small">Please wait while we save your results.</p>
        </div>
    </div>
</div>

<!-- Violation Warning Modal -->
<div class="modal fade" id="violationModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
    data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill"></i> Violation Detected!</h5>
            </div>
            <div class="modal-body text-center">
                <h4 class="text-danger mb-3" id="violationTitle">Full Screen Exit Detected</h4>
                <p class="lead" id="violationMessage">You have exited full-screen mode.</p>
                <div class="alert alert-warning">
                    <strong>Warning <span id="violationCountDisplay">0</span>/<span
                            id="violationLimitDisplay"><?= $quiz['violation_limit'] ?></span></strong>
                    <br>
                    <small>If you reach the limit, the quiz will be auto-submitted.</small>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-danger px-4" onclick="acknowledgeViolation()">I Understand,
                    Return to Quiz</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
    const remainingSeconds = parseInt("<?= $remaining_seconds ?>", 10);
    const violationLimit = parseInt("<?= $quiz['violation_limit'] ?>", 10);
    let timeLeft = remainingSeconds;
    let isSubmitting = false;
    let quizStarted = false;
    let timerInterval;
    let currentQuestionIndex = 0;
    const totalQuestions = <?= count($questions) ?>;

    function showQuestion(index) {
        if (index < 0 || index >= totalQuestions) return;

        // Hide current
        document.getElementById('q-card-' + currentQuestionIndex).classList.add('d-none');
        document.getElementById('nav-btn-' + currentQuestionIndex).classList.remove('btn-primary');
        document.getElementById('nav-btn-' + currentQuestionIndex).classList.add('btn-outline-secondary');
        
        // Show new
        currentQuestionIndex = index;
        document.getElementById('q-card-' + currentQuestionIndex).classList.remove('d-none');
        
        // Update nav button style
        const btn = document.getElementById('nav-btn-' + currentQuestionIndex);
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-primary');
    }
    
    function markAnswered(index) {
        const btn = document.getElementById('nav-btn-' + index);
        // Add a success border or indicator to show it's answered
        btn.classList.add('border-success');
        btn.classList.add('border-2');
    }

    // Initialize first button
    document.addEventListener('DOMContentLoaded', () => {
       showQuestion(0);
    });

    function startQuiz() {
        quizStarted = true;
        document.getElementById('quiz-start-overlay').style.display = 'none';
        const container = document.getElementById('quiz-container');
        container.style.display = 'block';
        // Allow reflow
        setTimeout(() => {
            container.style.opacity = '1';
        }, 10);

        <?php if ($quiz['force_full_screen']): ?>
        requestFullScreen();
        <?php endif; ?>

        // Start Timer
        updateTimer();
        timerInterval = setInterval(updateTimer, 1000);

        // Enable anti-cheat check
        <?php if ($quiz['force_full_screen']): ?>
        checkFullScreen();
        <?php endif; ?>
    }

    // Timer Logic
    function updateTimer() {
        if (isSubmitting || !quizStarted) return;

        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        document.getElementById('timer').innerText = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

        if (timeLeft <= 0) {
            submitQuiz('Time Expired');
        } else {
            timeLeft--;
        }
    }

    // Universal Submit Function
    function submitQuiz(reason) {
        if (isSubmitting) return;
        isSubmitting = true;

        if (timerInterval) clearInterval(timerInterval);

        // Remove listeners
        window.removeEventListener('blur', blurListener);
        document.removeEventListener('fullscreenchange', fullScreenListener);

        // Show Loading Modal
        document.getElementById('submission_reason').value = reason;

        document.getElementById('autoSubmitMessage').innerText = reason === 'User Submitted' ? 'Submitting Quiz...' : reason;
        const autoSubmitModal = new bootstrap.Modal(document.getElementById('autoSubmitModal'));
        autoSubmitModal.show();

        // Submit form
        setTimeout(() => {
            document.getElementById('quiz-form').submit();
        }, 800);
    }

    // Logging Logic
    function logViolation(type) {
        if (isSubmitting || !quizStarted) return;

        fetch("<?= base_url('quiz/'.$assignment['id'].'/log-violation') ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ type: type })
        });
    }

    // Anti-Cheat: Disable Context Menu & Shortcuts
    document.addEventListener('contextmenu', event => event.preventDefault());
    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'p' || e.key === 'u')) {
            e.preventDefault();
        }
    });

    // Full Screen Enforcement
    function requestFullScreen() {
        const docElm = document.documentElement;
        if (docElm.requestFullscreen) docElm.requestFullscreen();
        else if (docElm.mozRequestFullScreen) docElm.mozRequestFullScreen();
        else if (docElm.webkitRequestFullScreen) docElm.webkitRequestFullScreen();
        else if (docElm.msRequestFullscreen) docElm.msRequestFullscreen();

        document.getElementById('fullscreen-overlay').style.display = 'none';
    }

    function checkFullScreen() {
        if (isSubmitting || !quizStarted) return;

        if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
            document.getElementById('fullscreen-overlay').style.display = 'block';
        } else {
            document.getElementById('fullscreen-overlay').style.display = 'none';
        }
    }

    // Unified Violation Handler
    let violationCount = 0;

    function handleViolation(type) {
        if (isSubmitting || !quizStarted) return;

        violationCount++;
        logViolation(type);

        if (violationCount >= violationLimit) {
            <?php if ($quiz['auto_submit_on_violation']): ?>
            submitQuiz('Cheating Violation Limit Reached (' + violationCount + '/' + violationLimit + ')');
            <?php else: ?>
            // Just warn if auto-submit is disabled
            document.getElementById('violationCountDisplay').innerText = violationCount;
            document.getElementById('violationTitle').innerText = (type === 'TAB_SWITCH') ? 'Tab Switch Detected' : 'Full Screen Exit Detected';
            document.getElementById('fullscreen-overlay').style.display = 'none';
            const violationModal = new bootstrap.Modal(document.getElementById('violationModal'));
            violationModal.show();
            <?php endif; ?>
        } else {
            // Show Violation Modal
            document.getElementById('violationCountDisplay').innerText = violationCount;
            document.getElementById('violationTitle').innerText = (type === 'TAB_SWITCH') ? 'Tab Switch Detected' : 'Full Screen Exit Detected';

            document.getElementById('fullscreen-overlay').style.display = 'none';

            const violationModal = new bootstrap.Modal(document.getElementById('violationModal'));
            violationModal.show();
        }
    }

    function acknowledgeViolation() {
        requestFullScreen();
        const modalEl = document.getElementById('violationModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }

    const fullScreenListener = function () {
        if (!quizStarted) return;
        checkFullScreen();
        if (!document.fullscreenElement && !isSubmitting) {
            handleViolation('FULLSCREEN_EXIT');
        }
    };

    <?php if ($quiz['force_full_screen']): ?>
    document.addEventListener('fullscreenchange', fullScreenListener);
    document.addEventListener('webkitfullscreenchange', fullScreenListener);
    document.addEventListener('mozfullscreenchange', fullScreenListener);
    document.addEventListener('MSFullscreenChange', fullScreenListener);
    <?php endif; ?>

    const blurListener = function () {
        handleViolation('TAB_SWITCH');
    };
    
    <?php if ($quiz['detect_tab_switch']): ?>
    window.addEventListener('blur', blurListener);
    <?php endif; ?>

</script>
<?= $this->endSection() ?>
