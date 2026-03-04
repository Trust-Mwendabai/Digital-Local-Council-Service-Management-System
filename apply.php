<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

$stmt = $pdo->query("SELECT * FROM services ORDER BY category, name");
$services = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = $_POST['service_id'];
    $details = trim($_POST['details']);
    
    $form_data = [
        'full_name' => $_POST['full_name'] ?? '',
        'nrc_number' => $_POST['nrc_number'] ?? '',
        'contact_number' => $_POST['contact_number'] ?? '',
        'address' => $_POST['address'] ?? '',
        'purpose' => $_POST['purpose'] ?? ''
    ];

    if (empty($service_id) || empty($details)) {
        $error = 'Service selection and details are required.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO applications (user_id, service_id, form_data, admin_comment) VALUES (?, ?, ?, '')");
        $json_data = json_encode($form_data);
        
        if ($stmt->execute([$user_id, $service_id, $json_data])) {
            $success = 'Application Protocol Initialized Successfully.';
            log_activity($pdo, $user_id, 'Application Submitted', "Citizen initialized protocol for Service ID #$service_id.");
            dispatch_notification($pdo, $user_id, "Protocol started: Your application has been logged for council review.", "Application Received");
        } else {
            $error = 'Failed to initialize protocol. Registry error.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply | DLCSMS</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .form-container {
            max-width: 840px;
            margin: 0 auto;
            padding: 56px;
        }
    </style>
</head>
<body class="">
    <div class="app-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <?php include 'includes/header_dashboard.php'; ?>
            
            <main class="main-content animate-up">
                <div class="glass-card form-container">
                    <header style="margin-bottom: 48px;">
                        <span style="font-size: 0.8rem; font-weight: 800; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.1em;">Service Protocol</span>
                        <h2 style="font-size: 2.5rem; margin-top: 12px;">Initiate Application</h2>
                        <p class="text-muted">Enter your telemetry data to proceed with council certification.</p>
                    </header>

                    <?php if ($error): ?>
                        <div style="background: rgba(239, 68, 68, 0.08); color: var(--error); padding: 16px; border-radius: 12px; margin-bottom: 32px; border: 1px solid rgba(239, 68, 68, 0.1);">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div style="background: rgba(34, 197, 94, 0.08); color: var(--success); padding: 24px; border-radius: 16px; margin-bottom: 32px; border: 1px solid rgba(34, 197, 94, 0.1); text-align: center;">
                            <h4 style="color: var(--success); margin-bottom: 8px;">Success</h4>
                            <p><?= $success ?></p>
                            <a href="dashboard.php" class="btn btn-primary mt-4">View Records</a>
                        </div>
                    <?php else: ?>
                        <form action="apply.php" method="POST">
                            <div class="form-group">
                                <label class="form-label">Service Type</label>
                                <select name="service_id" class="form-control" required style="height: 56px;">
                                    <option value="">Select Service Node...</option>
                                    <?php 
                                    $current_cat = '';
                                    foreach ($services as $service): 
                                        if ($current_cat !== $service['category']):
                                            if ($current_cat !== '') echo '</optgroup>';
                                            $current_cat = $service['category'];
                                            echo '<optgroup label="NODE: ' . h($current_cat) . '">';
                                        endif;
                                    ?>
                                        <option value="<?= $service['id'] ?>"><?= h($service['name']) ?></option>
                                    <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                <div class="form-group">
                                    <label class="form-label">Legal Name</label>
                                    <input type="text" name="full_name" class="form-control" required placeholder="As on ID">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">NRC Identification</label>
                                    <input type="text" name="nrc_number" class="form-control" required placeholder="XXXXXX/XX/X">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                <div class="form-group">
                                    <label class="form-label">Secure Contact</label>
                                    <input type="text" name="contact_number" class="form-control" required placeholder="+260 XXX XXXXXX">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Physical Domicile</label>
                                    <input type="text" name="address" class="form-control" required placeholder="Plot No, Area, City">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Statement of Purpose</label>
                                <textarea name="details" class="form-control" rows="5" required placeholder="Clarify the objective of your application..."></textarea>
                            </div>

                            <div style="display: flex; gap: 20px; margin-top: 48px;">
                                <button type="submit" class="btn btn-primary" style="flex: 2; height: 56px;">Transmit Application</button>
                                <a href="dashboard.php" class="btn btn-secondary" style="flex: 1; height: 56px;">Abort</a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
