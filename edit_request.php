<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Student' && $_SESSION['role'] !== 'Faculty')) {
    header('Location: index.php');
    exit;
}

require 'db.php';

$request_id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM requests WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $request_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();
$request = $result->fetch_assoc();

if (!$request) {
    echo "Request not found or access denied.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE requests SET auditorium = ?, subject = ?, date = ?, description = ?, status = 'pending' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssii", $_POST['auditorium'], $_POST['subject'], $_POST['date'], $_POST['additional_details'], $request_id, $user_id);
    $stmt->execute();

    $_SESSION['success_message'] = "Request updated successfully!";
    header('Location: user_dashboard.php');
    exit;
}

$stmt->close();
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
            text-align: center;
            margin-top: 20px;
            color: white;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 20px;
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        .form-container h3 {
            text-align: center;
            color: #333;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 8px;
            text-align: left;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
            width: 100%;
            box-sizing: border-box;
        }

        .form-group select {
            background-color: #fff;
            color: #333;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-container button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 1.1em;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        .form-container button:active {
            transform: translateY(2px);
        }

        .form-container button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .form-container .reset-button {
            background-color: #f44336;
            margin-top: 10px;
        }

        .form-container .reset-button:hover {
            background-color: #e53935;
        }

        .form-container .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
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
    <h1>Edit Booking Request</h1>

    <div class="container">
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="auditorium">Auditorium:</label>
                    <select name="auditorium" id="auditorium" required>
                        <option value="Baldev Raj Mittal Auditorium" <?php echo ($request['auditorium'] ?? 'Baldev Raj Mittal Auditorium') === 'Baldev Raj Mittal Auditorium' ? 'selected' : ''; ?>>Baldev Raj Mittal Auditorium</option>
                        <option value="Shanti Devi Mittal Auditorium" <?php echo ($request['auditorium'] ?? '') === 'Shanti Devi Mittal Auditorium' ? 'selected' : ''; ?>>Shanti Devi Mittal Auditorium</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($request['subject'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($request['date'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="additional_details">Additional Details:</label>
                    <textarea id="additional_details" name="additional_details"><?php echo htmlspecialchars($request['additional_details'] ?? ''); ?></textarea>
                </div>

                <button type="submit">Update Request</button>
                <button type="reset" class="reset-button">Reset</button>
            </form>
        </div>
    </div>
    <footer>
    <p>Designed by AyushkEmpire &copy; 2024 AyushkEmpire. All rights reserved.</p>
    </footer>
</body>

</html>