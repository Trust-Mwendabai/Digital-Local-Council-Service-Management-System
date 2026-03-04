<?php
require_once 'auth_check.php';

$filter_status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$query = "
    SELECT a.*, u.username, u.email, s.name as service_name, s.category
    FROM applications a 
    JOIN users u ON a.user_id = u.id 
    JOIN services s ON a.service_id = s.id 
    WHERE 1=1
";

$params = [];
if (!empty($filter_status)) {
    $query .= " AND a.status = ?";
    $params[] = $filter_status;
}
if (!empty($search)) {
    $query .= " AND (u.username LIKE ? OR u.email LIKE ? OR s.name LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$query .= " ORDER BY a.submitted_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applications | DLCSMS Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .admin-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
            margin-top: 30px;
        }
        .admin-card {
            padding: 32px;
            margin-bottom: 24px;
        }
        .filter-bar {
            display: flex;
            gap: 20px;
            background: white;
            padding: 24px;
            border-radius: 16px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-sm);
            align-items: flex-end;
        }
    </style>
</head>
<body class="animate-up">
    <nav class="container navbar">
        <a href="../index.php" class="logo">DLC<span>SMS</span> <span style="font-size: 0.5em; background: var(--primary); color: white; padding: 4px 10px; border-radius: 6px; margin-left: 10px;">CORE</span></a>
        <div style="display: flex; gap: 24px; align-items: center;">
            <div style="font-weight: 700; color: var(--primary);"><?= h($_SESSION['username']) ?></div>
            <a href="../logout.php" class="btn btn-secondary" style="padding: 10px 18px;">Exit Console</a>
        </div>
    </nav>

    <main class="container admin-layout">
        <aside>
            <div class="glass-card" style="padding: 24px; position: sticky; top: 120px;">
                 <a href="index.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--text-muted); font-weight: 600; border-radius: 12px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard
                </a>
                <a href="manage_applications.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--secondary); background: rgba(37, 99, 235, 0.05); font-weight: 700; border-radius: 12px; margin-top: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    Applications
                </a>
                <a href="manage_users.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--text-muted); font-weight: 600; border-radius: 12px; margin-top: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Citizens
                </a>
                <a href="settings.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--text-muted); font-weight: 600; border-radius: 12px; margin-top: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    Settings
                </a>
            </div>
        </aside>

        <section>
            <header style="margin-bottom: 40px;">
                <h2 style="font-size: 2.5rem; margin-bottom: 8px;">Application Registry</h2>
                <p class="text-muted">Maintain and review all citizen service requests across the network.</p>
            </header>

            <form action="manage_applications.php" method="GET" class="filter-bar">
                <div style="flex: 2;">
                    <label class="form-label">Search Registry</label>
                    <input type="text" name="search" class="form-control" value="<?= h($search) ?>" placeholder="User, Email, or Service Name...">
                </div>
                <div style="flex: 1;">
                    <label class="form-label">Status Filter</label>
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $filter_status == 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $filter_status == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        <option value="more_info" <?= $filter_status == 'more_info' ? 'selected' : '' ?>>More Info</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="height: 52px; padding: 0 32px;">Search Node</button>
            </form>

            <div class="glass-card" style="padding: 0; overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(0,0,0,0.02); text-align: left;">
                            <th style="padding: 20px 24px; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">ID</th>
                            <th style="padding: 20px 24px; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Citizen / Registry</th>
                            <th style="padding: 20px 24px; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Service Node</th>
                            <th style="padding: 20px 24px; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Submission</th>
                            <th style="padding: 20px 24px; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Status</th>
                            <th style="padding: 20px 24px; font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($applications as $app): ?>
                        <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                            <td style="padding: 20px 24px; font-weight: 800; color: var(--text-muted);">#<?= str_pad($app['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td style="padding: 20px 24px;">
                                <div style="font-weight: 700; color: var(--primary);"><?= h($app['username']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><?= h($app['email']) ?></div>
                            </td>
                            <td style="padding: 20px 24px;">
                                <div style="font-weight: 600;"><?= h($app['service_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--secondary); font-weight: 800;"><?= strtoupper(h($app['category'])) ?></div>
                            </td>
                            <td style="padding: 20px 24px; font-size: 0.85rem; color: var(--text-muted);"><?= date('M d, Y', strtotime($app['submitted_at'])) ?></td>
                            <td style="padding: 20px 24px;"><span class="status-pill status-<?= $app['status'] ?>"><?= $app['status'] ?></span></td>
                            <td style="padding: 20px 24px; text-align: right;">
                                <a href="review_application.php?id=<?= $app['id'] ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.8rem; font-weight: 700;">Review Node</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($applications)): ?>
                        <tr>
                            <td colspan="6" style="padding: 60px; text-align: center; color: var(--text-muted); font-size: 1.1rem;">No telemetry matching your search criteria.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
