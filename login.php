<?php
session_start();
require 'db.php';

$role = $_GET['role'] ?? 'Student';
$error = ''; // Variable to store the error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the statement
	if ($role === "user") {
		$stmt = $conn->prepare("SELECT id, role FROM users WHERE username = ? AND password = ? AND (role = 'Student' OR role = 'Faculty')");
		$stmt->bind_param("ss", $username, $password);
	}
	else{
		$stmt = $conn->prepare("SELECT id, role FROM users WHERE username = ? AND password = ? AND role = ?");
		$stmt->bind_param("sss", $username, $password, $role);
	}
     
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user as an associative array
        $user = $result->fetch_assoc();
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on the role
        header("Location: " . ($user['role'] === 'Admin' ? 'admin_dashboard.php' : 'user_dashboard.php'));
        exit;
    } else {
        $error = "Invalid credentials."; // Set error message
    }

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Auditorium Booking Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            text-align: center;
        }

        h2 {
            margin-top: 10vh;
            font-size: 2.5em;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        form {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            width: 35vw;
            height: auto;
            border-radius: 8px;
            display: inline-block;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            text-align: left;
        }

        form div {
            margin: 15px 0;
            display: flex;
            align-items: center;
        }

        form label {
            width: 20%;
            font-size: 1em;
            text-align: left;
            margin-right: 10px;
        }

        form input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            background-color: #f4f4f4;
            color: #333;
        }

        form button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            font-size: 1em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background-color: orange;
            color: #fff;
            transition: background 0.3s, transform 0.2s;
        }

        form button:hover {
            transform: translateY(-3px);
            filter: brightness(1.1);
        }

        form button:active {
            transform: translateY(1px);
        }

        .error {
            color: red;
            margin-bottom: 10px;
            font-size: 1em;
        }
        footer {
    text-align: center;
    height: 50px;
    background-color: #000000;
    color: white;
    position: fixed;
    right: 0;
    left: 0;
    bottom: 0;
}

        @media (max-width: 600px) {
            h2 {
                font-size: 1.4em;
            }

            form {
                width: 90%;
                padding: 15px;
            }
            
            form div {
                display: block;
            }

            form input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <h2><?php echo ucfirst($role); ?> Login</h2>
    <form method="POST">
        
        <div>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <button type="submit">Login</button>
    </form>
    <footer>
    <p>Designed by AyushkEmpire &copy; 2024 AyushkEmpire. All rights reserved.</p>
    </footer>
</body>
</html>