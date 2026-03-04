<?php
require_once 'auth_check.php';

$search = $_GET['search'] ?? '';

// Build query
$query = "
    SELECT u.*, 
    (SELECT COUNT(*) FROM applications WHERE user_id = u.id) as app_count
    FROM users u 
    WHERE u.role = 'citizen'
";

$params = [];
if (!empty($search)) {
    $query .= " AND (u.username LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
}

$query .= " ORDER BY u.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Registry | DLCSMS Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .admin-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
            margin-top: 30px;
        }
        .user-card {
            background: white;
            padding: 24px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s ease;
        }
        .user-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }
        .avatar {
            width: 56px;
            height: 56px;
            background: rgba(37, 99, 235, 0.1);
            color: var(--secondary);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
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
                <a href="manage_applications.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--text-muted); font-weight: 600; border-radius: 12px; margin-top: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    Applications
                </a>
                <a href="manage_users.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--secondary); background: rgba(37, 99, 235, 0.05); font-weight: 700; border-radius: 12px; margin-top: 8px;">
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
                <h2 style="font-size: 2.5rem; margin-bottom: 8px;">Citizen Registry</h2>
                <p class="text-muted">Manage the secure identities of all citizens registered on the DLCSMS network.</p>
            </header>

            <form action="manage_users.php" method="GET" style="margin-bottom: 32px;">
                <div style="display: flex; gap: 20px;">
                    <input type="text" name="search" class="form-control" value="<?= h($search) ?>" placeholder="Filter by Identity Handle or Email..." style="flex: 1; height: 56px;">
                    <button type="submit" class="btn btn-primary" style="padding: 0 40px; height: 56px;">Filter Registry</button>
                </div>
            </form>

            <div class="user-list">
                <?php foreach($users as $user): ?>
                <div class="user-card">
                    <div style="display: flex; gap: 24px; align-items: center;">
                        <div class="avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
                        <div>
                            <div style="font-weight: 800; font-size: 1.15rem; color: var(--primary);"><?= h($user['username']) ?></div>
                            <div style="color: var(--text-muted); font-size: 0.9rem; font-weight: 500;"><?= h($user['email']) ?></div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 60px; text-align: right;">
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Joined</div>
                            <div style="font-weight: 600;"><?= date('M Y', strtotime($user['created_at'])) ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Protocols</div>
                            <div style="font-weight: 600; color: var(--secondary);"><?= $user['app_count'] ?> Applications</div>
                        </div>
                        <div style="display: flex; align-items: center;">
                            <a href="manage_applications.php?search=<?= h($user['username']) ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.8rem; border-radius: 10px; font-weight: 700;">Trace History</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($users)): ?>
                <div class="glass-card" style="padding: 100px; text-align: center;">
                    <p class="text-muted" style="font-size: 1.1rem;">No registered citizens found in the telemetry logs.</p>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
