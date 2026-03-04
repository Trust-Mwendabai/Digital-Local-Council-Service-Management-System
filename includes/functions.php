<?php
/**
 * Iconography Helper - Digital Local Council Service Management System
 */

function get_category_icon($category) {
    $category = strtolower($category);
    
    // Permit Icon - Shield with check
    $permit_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>';
    
    // Certificate Icon - Document with seal
    $certificate_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><circle cx="12" cy="18" r="2"></circle><path d="M12 13v3"></path></svg>';
    
    // Registration Icon - Clipboard/File list
    $registration_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="M9 12h6"></path><path d="M9 16h6"></path><path d="M12 8h.01"></path></svg>';
    
    // Default Icon - Generic document
    $default_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>';

    switch ($category) {
        case 'permit':
            return $permit_svg;
        case 'certificate':
            return $certificate_svg;
        case 'registration':
            return $registration_svg;
        default:
            return $default_svg;
    }
}

/**
 * Dispatch internal and simulated external notifications
 */
function dispatch_notification($pdo, $user_id, $message, $subject = "Council Service Update") {
    // 1. Internal Database Notification
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->execute([$user_id, $message]);
    
    // 2. Fetch User Details for simulation
    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) return false;

    // 3. Simulate External Dispatch (Logging)
    $log_dir = __DIR__ . '/../logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    $log_file = $log_dir . '/communications.log';
    
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp]\n";
    $log_entry .= "CHANNEL: EMAIL\n";
    $log_entry .= "TO: {$user['email']}\n";
    $log_entry .= "SUBJECT: $subject\n";
    $log_entry .= "BODY: $message\n";
    $log_entry .= "CHANNEL: SMS\n";
    $log_entry .= "TO: +260 (Council-Verified-MSISDN)\n";
    $log_entry .= "MSG: $message\n";
    $log_entry .= "--------------------------------------------------\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    
    return true;
}

/**
 * Log activity to the database for auditing
 */
function log_activity($pdo, $user_id, $action, $details = null) {
    try {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$user_id, $action, $details, $ip_address]);
    } catch (PDOException $e) {
        // Silently fail to not break the user experience if logging fails
        return false;
    }
}
?>
