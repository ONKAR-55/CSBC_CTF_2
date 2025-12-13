<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username']; 
    $password = $_POST['password'];

    // VULNERABILITY: No input sanitization (SQL Injection)
    // Allows attacker to use: admin'# 
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Access Denied: Invalid Credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Login Portal</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #0d1117; /* Dark background */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #c9d1d9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #161b22;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            border: 1px solid #30363d;
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #58a6ff; /* Soft blue accent */
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .subtitle {
            font-size: 14px;
            color: #8b949e;
            margin-bottom: 30px;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #c9d1d9;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            background-color: #0d1117;
            border: 1px solid #30363d;
            border-radius: 6px;
            color: #c9d1d9;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #58a6ff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #238636; /* Green button */
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        button:hover {
            background-color: #2ea043;
        }

        .error-msg {
            margin-top: 20px;
            color: #f85149;
            background: rgba(248, 81, 73, 0.1);
            border: 1px solid rgba(248, 81, 73, 0.4);
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            text-align: left;
            display: flex;
            align-items: center;
        }

        .error-msg::before {
            content: "⚠️";
            margin-right: 10px;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #484f58;
            border-top: 1px solid #30363d;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>SysAdmin Portal</h1>
        <p class="subtitle">Secure Internal Access Only</p>
        
        <form method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter ID..." autocomplete="off">
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter Password...">
            </div>
            
            <button type="submit">Authenticate</button>
        </form>

        <?php if(isset($error)) echo "<div class='error-msg'>$error</div>"; ?>

        <div class="footer">
            &copy; 2025 CorpSec Systems. <br> Unauthorized access is a federal offense.
        </div>
    </div>
</body>
</html>