<?php
require_once 'auth_check.php';

$filter = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

$sql = "SELECT a.*, u.username, u.email, s.name as service_name, s.category 
        FROM applications a 
        JOIN users u ON a.user_id = u.id 
        JOIN services s ON a.service_id = s.id";

$params = [];
if ($filter !== 'all' || $search !== '') {
    $sql .= " WHERE 1=1";
    if ($filter !== 'all') {
        $sql .= " AND a.status = ?";
        $params[] = $filter;
    }
    if ($search !== '') {
        $sql .= " AND (u.username LIKE ? OR s.name LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
}
$sql .= " ORDER BY a.submitted_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications | DLCSMS Registry</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="">
    <div class="app-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <?php include '../includes/header_dashboard.php'; ?>
            
            <main class="main-content animate-up">
                <header style="margin-bottom: 40px;">
                    <h2 style="font-size: 2.5rem; margin-bottom: 8px;">Application Registry</h2>
                    <p class="text-muted">Review, adjudicate, and audit all council service requests.</p>
                </header>

                <div class="glass-card" style="padding: 32px; margin-bottom: 40px;">
                    <form method="GET" style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
                        <div style="flex: 2; min-width: 280px;">
                            <label class="form-label">Global Search</label>
                            <input type="text" name="search" class="form-control" value="<?= h($search) ?>" placeholder="Search by citizen name or service node...">
                        </div>
                        <div style="flex: 1; min-width: 200px;">
                            <label class="form-label">Status Filter</label>
                            <select name="status" class="form-control">
                                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Statuses</option>
                                <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                                <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= $filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 14px 32px;">Update View</button>
                    </form>
                </div>

                <div class="glass-card">
                    <div style="padding: 40px;">
                        <table class="main-table" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <th style="padding: 16px 10px;">Citizen Info</th>
                                    <th style="padding: 16px 10px;">Service Node</th>
                                    <th style="padding: 16px 10px;">Submission Date</th>
                                    <th style="padding: 16px 10px;">Status</th>
                                    <th style="padding: 16px 10px; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.02);">
                                    <td style="padding: 20px 10px;">
                                        <div style="font-weight: 700; color: var(--primary);"><?= h($app['username']) ?></div>
                                        <div style="font-size: 0.7rem; color: var(--text-muted);"><?= h($app['email']) ?></div>
                                    </td>
                                    <td style="padding: 20px 10px;">
                                        <div style="font-weight: 600;"><?= h($app['service_name']) ?></div>
                                        <div style="font-size: 0.7rem; color: var(--secondary); font-weight: 700;"><?= h($app['category']) ?></div>
                                    </td>
                                    <td style="padding: 20px 10px; font-size: 0.85rem; color: var(--text-muted);"><?= date('M d, Y', strtotime($app['submitted_at'])) ?></td>
                                    <td style="padding: 20px 10px;"><span class="status-pill status-<?= $app['status'] ?>"><?= $app['status'] ?></span></td>
                                    <td style="padding: 20px 10px; text-align: right;">
                                        <a href="review_application.php?id=<?= $app['id'] ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.8rem; font-weight: 700;">Review</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
