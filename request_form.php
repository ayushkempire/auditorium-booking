<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Student' && $_SESSION['role'] !== 'Faculty')) {
    header('Location: index.php');
    exit;
}
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO requests (user_id, auditorium, subject, date, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['auditorium'],
        $_POST['subject'],
        $_POST['date'],
        $_POST['additional_details']
    ]);
    header('Location: user_dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>UMS - Auditorium Booking Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            text-align: center;
        }

        h1 {
            color: white;
            margin: 20px 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            flex-direction: column;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-container h3 {
            color: #222;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
            align-items: flex-start;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            width: 100%;
            text-align: left;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 96.5%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
        }
        
        .form-group select {
           width: 100%;
       }

        .form-group textarea {
            height: 100px;
        }

        .form-container button {
            background-color: orange;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            width: 100%;
        }

        .form-container button:hover {
            transform: translateY(-3px);
            filter: brightness(1.1);
        }

        .form-container button:active {
            transform: translateY(1px);
        }

        .form-container .reset-button {
            background-color: red;
            margin-top: 10px;
        }

        .form-container button:disabled {
            background-color: grey;
            cursor: not-allowed;
        }

        .form-container .success-message {
            color: green;
            font-weight: bold;
            margin-top: 20px;
        }

        #logoutButton {
            position: absolute;
            top: 10px;
            right: 20px;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            background-color: orange;
            color: #fff;
            transition: background 0.3s, transform 0.2s;
        }

        #logoutButton:hover {
            transform: translateY(-3px);
            filter: brightness(1.1);
        }

        #logoutButton:active {
            transform: translateY(1px);
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
    </style>
</head>
<body>
    <button id="logoutButton" onclick="location.href='logout.php'">Logout</button>
    <h1>New Booking Request</h1>

    <div class="container">
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="auditorium">Auditorium:</label>
                    <select name="auditorium" id="auditorium" required>
                        <option>Select</option>
                        <option value="Baldev Raj Mittal Auditorium">Baldev Raj Mittal Auditorium</option>
                        <option value="Shanti Devi Mittal Auditorium">Shanti Devi Mittal Auditorium</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" required>
                </div>

                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>

                <div class="form-group">
                    <label for="additional_details">Additional Details:</label>
                    <textarea id="additional_details" name="additional_details"></textarea>
                </div>

                <button type="submit">Submit Request</button>
                <button type="reset" class="reset-button">Reset</button>
            </form>
        </div>
    </div>
    <footer>
    <p>Designed by AyushkEmpire &copy; 2024 AyushkEmpire. All rights reserved.</p>
    </footer>
</body>
</html>