<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$flag_message = "";

// Handle the "Sensitive Data" Change
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_announcement = $_POST['announcement'];

    // Update the database
    $stmt = $conn->prepare("UPDATE config SET setting_value = ? WHERE setting_name = 'announcement'");
    $stmt->bind_param("s", $new_announcement);
    $stmt->execute();

    // THE FLAG TRIGGER
    // If the attacker successfully injects a script tag (XSS), they get the flag.
    if (strpos($new_announcement, "<script>") !== false) {
        $flag_message = "
        <div class='alert-box success'>
            <div class='alert-icon'>☠️</div>
            <div class='alert-content'>
                <h3>CRITICAL SECURITY ALERT: XSS DETECTED</h3>
                <p>System compromise confirmed. Administrator privileges escalated.</p>
                <div class='flag-code'>CSBC{SU_CSBC_CTF_2.0}</div>
            </div>
        </div>";
    } else {
        $flag_message = "
        <div class='alert-box info'>
            <div class='alert-icon'>ℹ️</div>
            <div class='alert-content'>
                <h3>System Update</h3>
                <p>Global announcement updated successfully. No security anomalies detected.</p>
            </div>
        </div>";
    }
}

// Fetch current announcement
$result = $conn->query("SELECT setting_value FROM config WHERE setting_name = 'announcement'");
$row = $result->fetch_assoc();
$current_announcement = $row['setting_value'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | CorpSec</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background-color: #0d1117;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #c9d1d9;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #161b22;
            border-right: 1px solid #30363d;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .brand {
            font-size: 20px;
            font-weight: bold;
            color: #58a6ff;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #30363d;
            letter-spacing: 1px;
        }

        .user-info {
            font-size: 14px;
            margin-bottom: 20px;
            color: #8b949e;
        }

        .user-info span { color: #f0f6fc; font-weight: bold; }

        .menu-item {
            padding: 10px;
            color: #c9d1d9;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 5px;
            transition: background 0.2s;
            display: block;
        }

        .menu-item.active { background-color: #1f6feb; color: white; }
        .menu-item:hover:not(.active) { background-color: #21262d; }

        .logout-btn {
            margin-top: auto;
            color: #f85149;
            text-decoration: none;
            padding: 10px;
            border: 1px solid #30363d;
            text-align: center;
            border-radius: 6px;
        }
        
        .logout-btn:hover { background-color: rgba(248, 81, 73, 0.1); }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 40px;
        }

        h1 { margin-bottom: 20px; font-weight: 300; }

        .panel-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            max-width: 900px;
        }

        .card {
            background-color: #161b22;
            border: 1px solid #30363d;
            border-radius: 6px;
            padding: 20px;
        }

        .card-header {
            font-size: 16px;
            font-weight: 600;
            color: #58a6ff;
            margin-bottom: 15px;
            border-bottom: 1px solid #30363d;
            padding-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }

        /* Live Preview Box */
        .preview-box {
            background-color: #0d1117;
            border: 1px dashed #30363d;
            padding: 20px;
            border-radius: 4px;
            min-height: 60px;
            font-family: 'Courier New', monospace;
            color: #e6edf3;
        }

        /* Form Styling */
        textarea {
            width: 100%;
            background-color: #0d1117;
            border: 1px solid #30363d;
            color: #c9d1d9;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', Courier, monospace;
            resize: vertical;
            min-height: 120px;
            margin-bottom: 15px;
        }

        textarea:focus {
            outline: none;
            border-color: #58a6ff;
            box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.3);
        }

        .update-btn {
            background-color: #238636;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }

        .update-btn:hover { background-color: #2ea043; }

        /* Alert Boxes */
        .alert-box {
            margin-top: 20px;
            padding: 15px;
            border-radius: 6px;
            display: flex;
            align-items: flex-start;
        }

        .alert-box.info {
            background-color: rgba(56, 139, 253, 0.1);
            border: 1px solid rgba(56, 139, 253, 0.4);
        }

        .alert-box.success {
            background-color: rgba(46, 160, 67, 0.15); /* Greenish tint */
            border: 2px solid #2ea043;
            animation: pulse 2s infinite;
        }

        .alert-icon { font-size: 24px; margin-right: 15px; }
        
        .alert-content h3 { font-size: 16px; margin-bottom: 5px; }
        .alert-content p { font-size: 14px; opacity: 0.8; }

        .flag-code {
            margin-top: 10px;
            background-color: #000;
            color: #0f0;
            padding: 10px;
            font-family: monospace;
            font-weight: bold;
            border-left: 4px solid #0f0;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(46, 160, 67, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(46, 160, 67, 0); }
            100% { box-shadow: 0 0 0 0 rgba(46, 160, 67, 0); }
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">CORE :: SYSTEM</div>
        <div class="user-info">Logged in as: <br> <span><?php echo htmlspecialchars($_SESSION['admin']); ?></span></div>
        <a href="#" class="menu-item active">Dashboard</a>
        <a href="#" class="menu-item">System Logs</a>
        <a href="#" class="menu-item">User Mgmt</a>
        <a href="index.php" class="logout-btn">Log Out</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Administration Console</h1>

        <div class="panel-grid">
            
            <!-- Live Preview Card -->
            <div class="card">
                <div class="card-header">
                    <span>📢 Live Announcement Preview</span>
                    <span style="font-size: 12px; color: #8b949e;">Visible to all users</span>
                </div>
                <!-- VULNERABILITY: Stored XSS (Reflecting the payload back) -->
                <div class="preview-box">
                    <?php echo $current_announcement; ?>
                </div>
            </div>

            <!-- Update Form Card -->
            <div class="card">
                <div class="card-header">
                    <span>📝 Update Configuration</span>
                </div>
                <form method="POST">
                    <p style="margin-bottom: 10px; font-size: 13px; color: #8b949e;">Enter HTML or Plain Text to update the global broadcast message.</p>
                    <textarea name="announcement" placeholder="<div>System Maintenance at 22:00...</div>"></textarea>
                    <button type="submit" class="update-btn">Deploy Update</button>
                </form>
            </div>

            <!-- Flag / Alert Area -->
            <?php echo $flag_message; ?>

        </div>
    </div>

</body>
</html>