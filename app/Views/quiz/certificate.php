<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Achievement - <?= esc($quiz['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@300;400;500;600;700;800&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="<?= base_url('static/images/revseclabs-logo.png') ?>">
    <style>
        :root {
            --cert-gold: #c5a059;
            --cert-gold-bright: #d4af37;
            --cert-dark: #1a1a1a;
            --cert-navy: #0a192f;
            --cert-bg: #faf9f6; /* Parchment-style background */
        }
        body {
            background: #e2e8f0;
            margin: 0;
            padding: 80px 0;
            font-family: 'Montserrat', sans-serif;
            color: var(--cert-dark);
        }
        
        #certificate-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        #certificate-to-print {
            width: 1122px; /* Fixed A4 Landscape (Approx 297mm @ 96dpi) */
            height: 793px;  /* Fixed A4 Landscape (Approx 210mm @ 96dpi) */
            background: var(--cert-bg);
            padding: 0;
            position: relative;
            box-shadow: 0 40px 80px rgba(0,0,0,0.25);
            overflow: hidden;
            box-sizing: border-box;
            border: 2px solid #ddd;
        }

        /* Luxury Frame */
        .cert-frame-outer {
            position: absolute;
            top: 30px;
            right: 30px;
            bottom: 30px;
            left: 30px;
            border: 12px double var(--cert-navy);
            box-sizing: border-box;
            z-index: 5;
        }

        .cert-frame-inner {
            position: absolute;
            top: 20px;
            right: 20px;
            bottom: 20px;
            left: 20px;
            border: 2px solid var(--cert-gold);
            box-sizing: border-box;
        }

        /* Ornamental Corners */
        .corner {
            position: absolute;
            width: 80px;
            height: 80px;
            z-index: 10;
        }
        .corner-tl { top: 0; left: 0; border-top: 5px solid var(--cert-gold); border-left: 5px solid var(--cert-gold); }
        .corner-tr { top: 0; right: 0; border-top: 5px solid var(--cert-gold); border-right: 5px solid var(--cert-gold); }
        .corner-bl { bottom: 0; left: 0; border-bottom: 5px solid var(--cert-gold); border-left: 5px solid var(--cert-gold); }
        .corner-br { bottom: 0; right: 0; border-bottom: 5px solid var(--cert-gold); border-right: 5px solid var(--cert-gold); }

        /* Subtle Watermark */
        .cert-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            width: 100%;
            pointer-events: none;
            z-index: 1;
        }
        .cert-watermark img {
            width: 450px;
        }

        .cert-content {
            position: relative;
            z-index: 10;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 100px;
            text-align: center;
        }

        .cert-header { margin-top: 20px; }
        .cert-company {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--cert-navy);
            letter-spacing: 12px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .cert-main-title {
            font-family: 'Libre Baskerville', serif;
            font-size: 3.8rem;
            color: var(--cert-navy);
            margin-bottom: 0px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        .cert-subtitle {
            font-size: 1.2rem;
            color: var(--cert-gold);
            letter-spacing: 5px;
            font-weight: 500;
            text-transform: uppercase;
            margin: 5px 0 30px;
        }

        .cert-presentation {
            font-size: 1.2rem;
            color: #64748b;
            font-style: italic;
            margin-bottom: 10px;
        }

        .recipient-name {
            font-family: 'Libre Baskerville', serif;
            font-size: 4rem;
            font-weight: 700;
            color: var(--cert-navy);
            margin: 5px 0;
            padding: 0 40px;
        }

        /* Stylish Name Separator */
        .name-separator {
            width: 600px;
            border-bottom: 3px solid var(--cert-navy);
            margin: 5px auto 30px;
            position: relative;
        }
        .name-separator::after {
            content: '♦';
            position: absolute;
            left: 50%;
            top: 100%;
            transform: translate(-50%, -50%);
            background: var(--cert-bg);
            padding: 0 15px;
            color: var(--cert-gold);
            font-size: 1.2rem;
        }

        .cert-achievement-text {
            font-size: 1.15rem;
            line-height: 1.6;
            color: #4b5563;
            max-width: 800px;
            margin-bottom: 30px;
        }

        .quiz-name {
            color: var(--cert-navy);
            font-weight: 700;
            font-size: 1.4rem;
            display: inline-block;
            padding: 0 10px;
            border-bottom: 2px dotted var(--cert-gold);
        }

        .cert-meta {
            width: 100%;
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            margin-top: 10px;
        }

        .meta-item { text-align: center; width: 320px; }
        .meta-signature {
            font-family: 'Dancing Script', cursive;
            font-size: 2rem;
            color: var(--cert-navy);
            margin-bottom: -5px;
        }
        .meta-val {
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--cert-navy);
            margin-bottom: 5px;
        }
        .meta-label {
            border-top: 1.5px solid #cbd5e1;
            padding-top: 8px;
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }

        .cert-footer-id {
            position: absolute;
            bottom: 45px;
            width: 100%;
            text-align: center;
            font-size: 0.75rem;
            color: #94a3b8;
            letter-spacing: 1.5px;
            z-index: 10;
        }

        /* Utility Buttons */
        #download-cert-btn {
            position: fixed;
            top: 25px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: flex;
            gap: 15px;
        }

        @media print {
            body { background: white; padding: 0; }
            #certificate-to-print { 
                box-shadow: none; 
                margin: 0;
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" id="download-cert-btn">
        <button id="download-cert" class="btn btn-navy shadow-lg rounded-pill px-5 border-0 text-white" style="background: var(--cert-navy)">
            <i class="bi bi-file-earmark-pdf-fill me-2"></i> DOWNLOAD HIGH-RESOLUTION CERTIFICATE
        </button>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary px-4 rounded-pill shadow-lg bg-white border-0">Back</a>
    </div>

    <div id="certificate-wrapper">
        <div id="certificate-to-print">
            <!-- Frame and Borders -->
            <div class="cert-frame-outer">
                <div class="cert-frame-inner">
                    <div class="corner corner-tl"></div>
                    <div class="corner corner-tr"></div>
                    <div class="corner corner-bl"></div>
                    <div class="corner corner-br"></div>
                </div>
            </div>

            <!-- Subtle Logo Watermark -->
            <div class="cert-watermark">
                <img src="<?= base_url('static/images/revseclabs-logo.png') ?>" alt="Watermark">
            </div>

            <!-- Central Content -->
            <div class="cert-content">
                <div class="cert-header">
                    <div class="cert-company">Viyona Fintech</div>
                    <h1 class="cert-main-title">Certificate</h1>
                    <div class="cert-subtitle">OF ACHIEVEMENT</div>
                </div>

                <p class="cert-presentation">This is to officially certify that</p>
                
                <h2 class="recipient-name">
                    <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                </h2>

                <div class="name-separator"></div>

                <p class="cert-achievement-text">
                    has successfully completed the professional assessment in<br>
                    <span class="quiz-name"><?= esc($quiz['name']) ?></span><br>
                    demonstrating an excellent proficiency with a grade of <strong><?= round($assignment['score'], 1) ?>%</strong>
                </p>

                <div class="cert-meta">
                    <div class="meta-item">
                        <div class="meta-signature">Viyona Fintech</div>
                        <div class="meta-label">Authorized Signatory</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-val"><?= date('F d, Y', strtotime($assignment['completed_at'])) ?></div>
                        <div class="meta-label">Date of Issuance</div>
                    </div>
                </div>

                <div class="cert-footer-id">
                    Verification ID: <?= strtoupper(substr(md5($assignment['id']), 0, 16)) ?> &nbsp;•&nbsp; Cybersecurity Assessment Division
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.getElementById('download-cert').addEventListener('click', function () {
            const btn = this;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Capturing High-Resolution Canvas...';
            btn.disabled = true;

            const element = document.getElementById('certificate-to-print');
            const opt = {
                margin:       0,
                filename:     '<?= esc($user['first_name']) ?>_<?= esc($user['last_name']) ?>_<?= str_replace(' ', '_', esc($quiz['name'])) ?>.pdf',
                image:        { type: 'jpeg', quality: 1.0 },
                html2canvas:  { 
                    scale: 4, 
                    useCORS: true,
                    letterRendering: true,
                    backgroundColor: '#faf9f6'
                },
                jsPDF:        { unit: 'px', format: [1122, 793], orientation: 'landscape', compress: true }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }).catch(err => {
                console.error(err);
                btn.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Error';
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>
