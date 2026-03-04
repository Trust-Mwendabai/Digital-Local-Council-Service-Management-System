<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$app_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.*, s.name as service_name, s.category 
    FROM applications a 
    JOIN services s ON a.service_id = s.id 
    WHERE a.id = ? AND a.user_id = ?
");
$stmt->execute([$app_id, $user_id]);
$app = $stmt->fetch();

if (!$app) {
    redirect('dashboard.php');
}

$form_data = json_decode($app['form_data'], true);

// Handle Application Resubmission (Phase 21)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resubmit_application'])) {
    $updated_data = $form_data;
    if (isset($_POST['full_name'])) $updated_data['full_name'] = trim($_POST['full_name']);
    if (isset($_POST['nrc_number'])) $updated_data['nrc_number'] = trim($_POST['nrc_number']);
    if (isset($_POST['contact_number'])) $updated_data['contact_number'] = trim($_POST['contact_number']);
    if (isset($_POST['address'])) $updated_data['address'] = trim($_POST['address']);
    
    $json_data = json_encode($updated_data);
    $stmt = $pdo->prepare("UPDATE applications SET form_data = ?, status = 'pending', updated_at = NOW() WHERE id = ?");
    if ($stmt->execute([$json_data, $app_id])) {
        // Log notification to admin
        dispatch_notification($pdo, $app['user_id'], "Application Revised: Your update for " . $app['service_name'] . " has been sent back to pending status for review.", "Application Revised");
        
        log_activity($pdo, $app['user_id'], 'Application Revised', "Citizen resubmitted application ID #$app_id with updated data.");
        
        $success_msg = "Application successfully revised and sent back for council adjudication.";
        
        // Refresh app data
        $stmt = $pdo->prepare("SELECT a.*, s.name as service_name, s.category FROM applications a JOIN services s ON a.service_id = s.id WHERE a.id = ?");
        $stmt->execute([$app_id]);
        $app = $stmt->fetch();
        $form_data = json_decode($app['form_data'], true);
    }
}

// Handle Feedback Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['feedback_comment']);
    
    // Check if feedback already exists
    $check = $pdo->prepare("SELECT id FROM feedback WHERE application_id = ?");
    $check->execute([$app_id]);
    
    if (!$check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO feedback (application_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$app_id, $user_id, $rating, $comment]);
        $success_msg = "Thank you for your feedback! Your protocol experience has been recorded.";
    }
}

// Fetch existing feedback
$stmt = $pdo->prepare("SELECT * FROM feedback WHERE application_id = ?");
$stmt->execute([$app_id]);
$feedback = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Protocol | DLCSMS</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .view-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .data-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 32px;
        }
        .data-block {
            padding: 24px;
            background: rgba(0,0,0,0.02);
            border-radius: 16px;
        }
        .label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 800;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
        }
        .comment-box {
            background: rgba(37, 99, 235, 0.05);
            border-left: 4px solid var(--secondary);
            padding: 32px;
            border-radius: 0 16px 16px 0;
            margin-top: 40px;
        }

        /* Stepper Styles */
        .stepper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 48px;
            position: relative;
            padding: 0 40px;
        }

        .stepper::before {
            content: '';
            position: absolute;
            top: 24px;
            left: 80px;
            right: 80px;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            width: 120px;
        }

        .step-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #94a3b8;
            transition: all 0.3s ease;
        }

        .step.active .step-circle {
            border-color: var(--secondary);
            color: var(--secondary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .step.completed .step-circle {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .step-label {
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-muted);
            text-align: center;
        }

        .step.active .step-label { color: var(--secondary); }
        .step.completed .step-label { color: var(--success); }

        /* Rating System */
        .rating-group {
            display: flex;
            gap: 12px;
            margin: 16px 0;
        }

        .rating-star {
            cursor: pointer;
            color: #e2e8f0;
            font-size: 1.5rem;
            transition: color 0.2s;
        }

        .rating-star.active { color: #fbbf24; }
    </style>
</head>
<body class="">
    <div class="app-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <?php include 'includes/header_dashboard.php'; ?>
            
            <main class="main-content animate-up">
                <div class="view-container">
                    <?php if(isset($success_msg)): ?>
                        <div class="glass-card" style="padding: 20px; margin-bottom: 32px; border-left: 4px solid var(--success); background: rgba(34, 197, 94, 0.05);">
                            <p style="color: var(--success); font-weight: 700;"><?= h($success_msg) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Progress Stepper -->
                    <div class="stepper">
                        <div class="step completed">
                            <div class="step-circle"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                            <div class="step-label">Protocol<br>Received</div>
                        </div>
                        
                        <?php 
                        $review_class = ($app['status'] == 'pending' || $app['status'] == 'more_info') ? 'active' : 'completed';
                        $final_class = ($app['status'] == 'approved' || $app['status'] == 'rejected') ? 'completed' : '';
                        ?>

                        <div class="step <?= $review_class ?>">
                            <div class="step-circle"><?= ($review_class == 'completed') ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>' : '02' ?></div>
                            <div class="step-label">Under<br>Review</div>
                        </div>

                        <div class="step <?= $final_class ? $final_class : ($review_class == 'active' ? '' : 'active') ?>">
                            <div class="step-circle"><?= ($final_class == 'completed') ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>' : '03' ?></div>
                            <div class="step-label">Final<br>Adjudication</div>
                        </div>
                    </div>

                    <header style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 48px;">
                        <div>
                            <span style="font-size: 0.8rem; font-weight: 800; color: var(--secondary); text-transform: uppercase;">Application Node: #<?= str_pad($app['id'], 5, '0', STR_PAD_LEFT) ?></span>
                            <h2 style="font-size: 2.5rem; margin-top: 12px;"><?= h($app['service_name']) ?></h2>
                        </div>
                        <div style="text-align: right;">
                            <span class="status-pill status-<?= $app['status'] ?>" style="font-size: 1rem; padding: 10px 24px; display: block; margin-bottom: 12px;"><?= strtoupper(h($app['status'])) ?></span>
                            <?php if($app['status'] == 'approved'): ?>
                                <a href="download_document.php?id=<?= $app['id'] ?>" class="btn btn-primary" style="font-size: 0.8rem; font-weight: 700;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    Download Official Document
                                </a>
                            <?php endif; ?>
                        </div>
                    </header>

                    <div class="glass-card" style="padding: 48px; position: relative; overflow: hidden;">
                        <div style="position: absolute; top: 0; right: 0; width: 4px; height: 100%; background: var(--<?= ($app['status'] == 'approved') ? 'success' : (($app['status'] == 'rejected') ? 'error' : 'warning') ?>);"></div>
                        
                        <h3 style="font-size: 1.5rem; margin-bottom: 24px;">Telemetry Data Submissions</h3>
                        
                        <?php if($app['status'] == 'more_info'): ?>
                        <form method="POST">
                            <div class="data-grid">
                                <div class="data-block">
                                    <div class="label">Legal Full Name</div>
                                    <input type="text" name="full_name" class="form-control" value="<?= h($form_data['full_name'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                                </div>
                                <div class="data-block">
                                    <div class="label">NRC Identification</div>
                                    <input type="text" name="nrc_number" class="form-control" value="<?= h($form_data['nrc_number'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                                </div>
                                <div class="data-block">
                                    <div class="label">Service Node Category</div>
                                    <div class="value" style="padding-left: 8px; color: var(--text-muted);"><?= h($app['category']) ?> <span style="font-size: 0.65rem;">(IMMUTABLE)</span></div>
                                </div>
                                <div class="data-block">
                                    <div class="label">Contact Endpoint</div>
                                    <input type="text" name="contact_number" class="form-control" value="<?= h($form_data['contact_number'] ?? '') ?>" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit;">
                                </div>
                            </div>
                            
                            <div class="data-block" style="margin-top: 24px;">
                                <div class="label">Physical Domicile Address</div>
                                <textarea name="address" class="form-control" style="width: 100%; min-height: 80px; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-family: inherit; margin-top: 8px;"><?= h($form_data['address'] ?? '') ?></textarea>
                            </div>
                            
                            <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(0,0,0,0.05); text-align: right;">
                                <p style="font-size: 0.8rem; color: var(--secondary); margin-bottom: 12px; font-weight: 600;">⚠️ Updating these fields will reset your status to PENDING.</p>
                                <button type="submit" name="resubmit_application" class="btn btn-primary" style="padding: 12px 32px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);">Resubmit for Adjudication</button>
                            </div>
                        </form>
                        <?php else: ?>
                            <div class="data-grid">
                                <div class="data-block"><div class="label">Legal Full Name</div><div class="value"><?= h($form_data['full_name'] ?? 'N/A') ?></div></div>
                                <div class="data-block"><div class="label">NRC Identification</div><div class="value"><?= h($form_data['nrc_number'] ?? 'N/A') ?></div></div>
                                <div class="data-block"><div class="label">Service Node Category</div><div class="value"><?= h($app['category']) ?></div></div>
                                <div class="data-block"><div class="label">Contact Endpoint</div><div class="value"><?= h($form_data['contact_number'] ?? 'N/A') ?></div></div>
                            </div>
                            
                            <div class="data-block" style="margin-top: 24px; min-height: 120px;">
                                <div class="label">Physical Domicile Address</div>
                                <div class="value"><?= h($form_data['address'] ?? 'N/A') ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if(!empty($app['admin_comment'])): ?>
                        <div class="comment-box">
                            <div class="label" style="color: var(--secondary);">Council Adjudication Findings</div>
                            <div class="value" style="font-weight: 500; line-height: 1.6; color: var(--text-main);"><?= nl2br(h($app['admin_comment'])) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Feedback System -->
                    <?php if(($app['status'] == 'approved' || $app['status'] == 'rejected') && !$feedback): ?>
                    <div class="glass-card" style="margin-top: 40px; padding: 48px;">
                        <h3 style="font-size: 1.5rem; margin-bottom: 12px;">Protocol Experience Feedback</h3>
                        <p class="text-muted" style="margin-bottom: 32px;">Please rate your experience with this service node to help us improve council efficiency.</p>
                        
                        <form method="POST">
                            <div class="label">Service Satifaction Rating</div>
                            <div class="rating-group" id="rating-stars">
                                <span class="rating-star" data-value="1">★</span>
                                <span class="rating-star" data-value="2">★</span>
                                <span class="rating-star" data-value="3">★</span>
                                <span class="rating-star" data-value="4">★</span>
                                <span class="rating-star" data-value="5">★</span>
                            </div>
                            <input type="hidden" name="rating" id="rating-input" value="5" required>
                            
                            <div style="margin-top: 24px;">
                                <div class="label">Observations & Comments</div>
                                <textarea name="feedback_comment" class="form-control" style="width: 100%; min-height: 120px; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; font-family: inherit; margin-top: 8px;" placeholder="Describe your experience with the council service node..."></textarea>
                            </div>
                            
                            <button type="submit" name="submit_feedback" class="btn btn-primary" style="margin-top: 24px; padding: 14px 32px;">Submit Evaluation</button>
                        </form>
                        
                        <script>
                            document.querySelectorAll('.rating-star').forEach(star => {
                                star.addEventListener('click', function() {
                                    const value = this.dataset.value;
                                    document.getElementById('rating-input').value = value;
                                    document.querySelectorAll('.rating-star').forEach(s => {
                                        s.classList.toggle('active', s.dataset.value <= value);
                                    });
                                });
                            });
                            // Set default active stars
                            const defaultVal = document.getElementById('rating-input').value;
                            document.querySelectorAll('.rating-star').forEach(s => {
                                if(s.dataset.value <= defaultVal) s.classList.add('active');
                            });
                        </script>
                    </div>
                    <?php elseif($feedback): ?>
                    <div class="glass-card" style="margin-top: 40px; padding: 40px; border-top: 4px solid #fbbf24;">
                        <div class="label">Your Submitted Evaluation</div>
                        <div style="display: flex; align-items: center; gap: 12px; margin-top: 8px;">
                            <div style="color: #fbbf24; font-size: 1.2rem; font-weight: 800;">
                                <?= str_repeat('★', $feedback['rating']) . str_repeat('☆', 5 - $feedback['rating']) ?>
                            </div>
                            <div class="text-muted" style="font-size: 0.85rem; font-weight: 600;">Rating: <?= $feedback['rating'] ?>/5</div>
                        </div>
                        <?php if(!empty($feedback['comment'])): ?>
                            <p style="margin-top: 16px; font-style: italic; color: var(--text-muted);"><?= h($feedback['comment']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div style="margin-top: 40px; text-align: center;">
                        <p class="text-muted" style="font-size: 0.9rem;">Protocol Submitted on <?= date('F d, Y • H:i', strtotime($app['submitted_at'])) ?></p>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
