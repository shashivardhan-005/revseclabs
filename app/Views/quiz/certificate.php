<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - <?= esc($quiz['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Montserrat:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= base_url('static/images/revseclabs-logo.png') ?>">
    <style>
        :root {
            --cert-gold: #c5a059;
            --cert-blue: #0f172a;
            --cert-border: #e2e8f0;
        }
        body {
            background: #f8fafc;
            margin: 0;
            padding: 50px 0;
            font-family: 'Montserrat', sans-serif;
        }
        /* Container for the PDF generation */
        #certificate-to-print {
            width: 1122px; /* A4 Landscape width at 96dpi */
            height: 793px;  /* A4 Landscape height at 96dpi */
            margin: auto;
            background: white;
            padding: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .certificate-container {
            width: 100%;
            height: 100%;
            background: white;
            padding: 40px;
            border: 20px solid var(--cert-blue);
            position: relative;
            box-sizing: border-box;
        }
        .certificate-inner {
            border: 2px solid var(--cert-gold);
            padding: 30px;
            height: 100%;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-sizing: border-box;
        }
        .cert-header {
            margin-bottom: 25px;
        }
        .cert-logo {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--cert-blue);
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        .cert-title {
            font-family: 'Libre Baskerville', serif;
            font-size: 3.5rem;
            color: var(--cert-blue);
            margin: 10px 0;
            text-transform: uppercase;
            line-height: 1;
        }
        .cert-presentation {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 20px;
        }
        .recipient-name {
            font-family: 'Libre Baskerville', serif;
            font-size: 3rem;
            font-weight: 700;
            color: var(--cert-gold);
            border-bottom: 2px solid #f1f5f9;
            display: inline-block;
            margin: 15px 0;
            padding: 0 40px 10px;
        }
        .cert-description {
            font-size: 1.1rem;
            color: #475569;
            max-width: 650px;
            margin: 20px auto;
            line-height: 1.6;
        }
        .cert-meta {
            margin-top: 40px;
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
        }
        .signature-block {
            text-align: center;
            width: 220px;
        }
        .signature-line {
            border-top: 1px solid var(--cert-blue);
            margin-top: 10px;
            padding-top: 5px;
            font-size: 0.9rem;
            color: #64748b;
        }
        .cert-footer {
            margin-top: 50px;
            font-size: 0.8rem;
            color: #94a3b8;
        }
        .seal {
            width: 130px;
            height: 130px;
            background: var(--cert-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            position: absolute;
            bottom: 60px;
            right: 60px;
            opacity: 0.95;
            transform: rotate(-15deg);
            box-shadow: 0 10px 20px rgba(197, 160, 89, 0.3);
            border: 4px double white;
        }
        @media print {
            body { background: white; padding: 0; }
            #certificate-to-print { width: 100%; height: auto; box-shadow: none; border: none; }
            .no-print { display: none; }
        }
        /* UI overrides for the preview */
        #certificate-to-print {
            box-shadow: 0 40px 100px rgba(0,0,0,0.1);
            transform: scale(0.8);
            transform-origin: top center;
        }
        @media (max-width: 1200px) {
            #certificate-to-print { transform: scale(0.6); }
        }
        @media (max-width: 800px) {
            #certificate-to-print { transform: scale(0.4); }
        }
    </style>
</head>
<body>
    <div class="container text-center mb-0 no-print" style="position: relative; z-index: 100;">
        <button id="download-cert" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill">
            <i class="bi bi-file-earmark-pdf me-2"></i> Download Professional PDF
        </button>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary px-4 py-2 ms-2 rounded-pill">Back to Dashboard</a>
    </div>

    <div id="certificate-wrapper" style="width: 100%; overflow: hidden;">
        <div id="certificate-to-print">
            <div class="certificate-container">
                <div class="certificate-inner">
                    <div class="cert-header">
                        <div class="cert-logo">VIYONA FINTECH</div>
                        <div class="cert-title">Certificate</div>
                        <div class="cert-presentation">of Completion</div>
                    </div>

                    <div class="cert-body">
                        <div class="cert-presentation">This is to certify that</div>
                        <div class="recipient-name"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></div>
                        <div class="cert-description">
                            has successfully demonstrated professional competency in<br>
                            <strong style="color: var(--cert-blue);"><?= esc($quiz['name']) ?></strong><br>
                            by achieving a passing score of <strong><?= round($assignment['score'], 1) ?>%</strong>.
                        </div>
                    </div>

                    <div class="cert-meta">
                        <div class="signature-block">
                            <div style="font-family: 'Libre Baskerville', serif; font-size: 1.2rem; color: var(--cert-blue); font-style: italic;">Viyona Fintech</div>
                            <div class="signature-line">Official Verification</div>
                        </div>
                        <div class="signature-block">
                            <div style="font-family: 'Montserrat', sans-serif; font-weight: 600;"><?= date('F d, Y', strtotime($assignment['completed_at'])) ?></div>
                            <div class="signature-line">Date of Issue</div>
                        </div>
                    </div>

                    <div class="cert-footer">
                        Certificate ID: <?= strtoupper(substr(md5($assignment['id']), 0, 12)) ?> • Issued by Viyona Fintech Cybersecurity Division
                    </div>
                </div>
                <div class="seal">
                    <div class="text-center">
                        <div style="font-size: 0.7rem;">OFFICIAL</div>
                        <div style="font-size: 1.2rem; line-height: 1;">VALID</div>
                        <div style="font-size: 0.7rem;">ACADEMY</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.getElementById('download-cert').addEventListener('click', function () {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Generating PDF Achievement...';
            btn.disabled = true;

            const element = document.getElementById('certificate-to-print');

            const opt = {
                margin:       0,
                filename:     'Certificate_<?= str_replace(' ', '_', esc($quiz['name'])) ?>.pdf',
                image:        { type: 'jpeg', quality: 1.0 },
                html2canvas:  { 
                    scale: 3, 
                    useCORS: true, 
                    logging: false,
                    letterRendering: true,
                    allowTaint: false,
                    onclone: (clonedDoc) => {
                        // Ensure the cloned version for capture is not scaled
                        const clonedEl = clonedDoc.getElementById('certificate-to-print');
                        clonedEl.style.transform = 'none';
                        clonedEl.style.boxShadow = 'none';
                        clonedEl.style.margin = '0';
                        clonedEl.style.padding = '0';
                        clonedEl.style.width = '1122px';
                        clonedEl.style.height = '793px';
                    }
                },
                jsPDF:        { unit: 'px', format: [1122, 793], orientation: 'landscape' },
                pagebreak:    { mode: 'avoid-all' }
            };

            // Capture and save
            html2pdf().set(opt).from(element).save().then(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }).catch(err => {
                console.error('PDF Generation Error:', err);
                // Fallback to simpler method if high-res fails
                html2pdf().from(element).set({
                    margin: 0,
                    filename: 'Certificate.pdf',
                    jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
                }).save();
                
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>
