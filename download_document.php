<?php
session_start();
require_once 'includes/db.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$app_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.*, s.name as service_name, s.category, u.username, u.email 
    FROM applications a 
    JOIN services s ON a.service_id = s.id 
    JOIN users u ON a.user_id = u.id
    WHERE a.id = ? AND a.user_id = ? AND a.status = 'approved'
");
$stmt->execute([$app_id, $user_id]);
$app = $stmt->fetch();

if (!$app) {
    die("Access Denied: Document not finalized or unauthorized access.");
}

$form_data = json_decode($app['form_data'], true);
$issue_date = date('F d, Y', strtotime($app['updated_at']));
$expiry_date = date('F d, Y', strtotime($app['updated_at'] . ' + 1 year'));
$cert_no = "DLCS/" . strtoupper(substr($app['category'], 0, 3)) . "/" . str_pad($app['id'], 6, '0', STR_PAD_LEFT);

// QR Verification Link (Phase 20)
$verify_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname($_SERVER['PHP_SELF']) . "/verify.php?id=" . $app['id'];
$qr_api = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($verify_url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= h($app['service_name']) ?> - Official Document</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Inter:wght@400;600;800&family=Dancing+Script:wght@700&display=swap');
        
        body {
            background: #f1f5f9;
            margin: 0;
            padding: 40px;
            font-family: 'Inter', sans-serif;
        }

        .certificate {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 60px;
            border: 20px solid #1e293b;
            position: relative;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }

        .certificate::after {
            content: '';
            position: absolute;
            top: 10px; left: 10px; right: 10px; bottom: 10px;
            border: 2px solid #e2e8f0;
            pointer-events: none;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .council-name {
            font-family: 'Cinzel', serif;
            font-size: 1.8rem;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }

        .document-type {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        .content {
            text-align: center;
        }

        .app-title {
            font-size: 2.2rem;
            color: #1e293b;
            margin: 32px 0;
            font-weight: 800;
        }

        .to-whom {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 12px;
        }

        .holder-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #000;
            text-decoration: underline;
            margin-bottom: 32px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            text-align: left;
            margin-top: 40px;
            padding-top: 40px;
            border-top: 1px solid #f1f5f9;
        }

        .detail-item .label {
            font-size: 0.7rem;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .detail-item .value {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
        }

        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .signature {
            border-top: 1px solid #1e293b;
            padding-top: 16px;
            width: 240px;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e293b;
            position: relative;
        }

        .cursive-signature {
            font-family: 'Dancing Script', cursive;
            font-size: 2rem;
            color: #1e40af;
            position: absolute;
            top: -45px;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            opacity: 0.9;
        }

        .verified-badge {
            position: absolute;
            bottom: 40px;
            right: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
            opacity: 0.5;
            transform: rotate(-5deg);
        }

        .verified-text {
            font-size: 0.6rem;
            font-weight: 800;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .seal {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px double #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            font-size: 0.6rem;
            transform: rotate(-15deg);
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }

        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .certificate { box-shadow: none; border: 15px solid #000; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="display: flex; gap: 12px; justify-content: center; align-items: center; margin-bottom: 30px;">
        <button onclick="downloadPDF()" class="btn-direct-download" style="padding: 12px 24px; background: #059669; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Download PDF
        </button>
        <button onclick="window.print()" style="padding: 12px 24px; background: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">Print Document</button>
    </div>

    <div id="certificate-container">
        <div class="certificate">
            <div class="header">
                <div class="council-name">Digital Local Council</div>
                <div class="document-type">Sovereign Registry Authority</div>
            </div>

            <div class="content">
                <div class="to-whom">Official Certification of</div>
                <div class="app-title"><?= h($app['service_name']) ?></div>
                
                <div class="to-whom">Is hereby granted and verified for</div>
                <div class="holder-name"><?= h($form_data['full_name'] ?? $app['username']) ?></div>

                <div class="details-grid">
                    <div class="detail-item">
                        <div class="label">Certificate Number</div>
                        <div class="value"><?= $cert_no ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Identification (NRC/ID)</div>
                        <div class="value"><?= h($form_data['nrc_number'] ?? 'VERIFIED_USER') ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Issuance Date</div>
                        <div class="value"><?= $issue_date ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="label">Expiration Date</div>
                        <div class="value"><?= $expiry_date ?></div>
                    </div>
                </div>
            </div>

            <div class="footer">
                <div class="signature">
                    <div class="cursive-signature">Chief Registrar</div>
                    Registry Commissioner<br>
                    Digital Council Authority
                </div>

                <div style="text-align: center;">
                    <div style="background: white; padding: 8px; border: 1px solid #f1f5f9; border-radius: 10px; display: inline-block;">
                        <img src="<?= $qr_api ?>" alt="Verification QR Code" style="display: block; width: 80px; height: 80px;">
                        <div style="font-size: 0.5rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-top: 4px; letter-spacing: 0.5px;">Verify Authenticity</div>
                    </div>
                </div>

                <div class="seal">
                    OFFICIAL<br>COUNCIL<br>SEAL
                </div>
        </div>

        <div class="verified-badge">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <div class="verified-text">Direct Registry Auto-Sync Verified</div>
        </div>
        </div>
    </div>

    <!-- PDF Generation Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            const element = document.getElementById('certificate-container');
            const opt = {
                margin:       0.5,
                filename:     'DLCSMS_Document_<?= $cert_no ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
            };

            // Add loading state
            const btn = document.querySelector('.btn-direct-download');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Generating PDF...';
            btn.disabled = true;

            html2pdf().set(opt).from(element).save().then(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>
