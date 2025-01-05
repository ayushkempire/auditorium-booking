<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'Student' && $_SESSION['role'] !== 'Faculty')) {
    header('Location: index.php');
    exit;
}

require 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch user requests
$requests = [];
$result = $conn->query("SELECT * FROM requests WHERE user_id = $user_id");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}

// Fetch the user's name
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_name);
$stmt->fetch();
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
            color: white;
            margin: 20px 0;
            font-weight: 1000;
            font-size: 50px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
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

        button {
            margin: 20px;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            background-color: orange;
            color: #fff;
            transition: background 0.3s, transform 0.2s;
        }

        button:hover {
            transform: translateY(-3px);
            filter: brightness(1.1);
        }

        button:active {
            transform: translateY(1px);
        }

        .requests-table {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-collapse: collapse;
        }

        .requests-table th,
        .requests-table td {
            padding: 15px;
            width: 100%;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .requests-table th {
            background-color: orange;
            color: white;
        }

        .requests-table td {
            background-color: #f9f9f9;
        }

        .status-box {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100px;
            text-align: center;
        }

        .status-pending {
            width: 100px;
            background-color: yellow;
            color: #333;
        }

        .status-rejected {
            width: 100px;

            background-color: red;
            color: white;
        }

        .status-approved {
            width: 100px;
            background-color: green;
            color: white;
        }

        .requests-table a {
            text-decoration: none;
            color: #fff;
            background-color: #ff7f50;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .requests-table a:hover {
            background-color: #ff6347;
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin: 10px 0;
        }

        .logout-link {
            text-decoration: none;
            color: #fff;
            background-color: red;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 20px;
            display: inline-block;
        }

        .logout-link:hover {
            background-color: #c13c3c;
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
    <h1>Welcome,
        <?php echo htmlspecialchars($user_name); ?>!
    </h1>

    <!-- New Request Button -->
    <a href="request_form.php">
        <button type="button">Click here for New Request</button>
    </a>



    <div class="requests-table">
    <?php
                    // Display success message if it exists
                    if (isset($_SESSION['success_message'])) {
                        echo "<p class='success-message'>" . htmlspecialchars($_SESSION['success_message']) . "</p>";
                        unset($_SESSION['success_message']);
                    }
    ?>
        <h3>Your Requests</h3>
        <table>
            <tr>
                <th>Subject</th>
                <th>Status</th>
                <th>Edit Request</th>
            </tr>
            <?php foreach ($requests as $request): ?>
            <tr>
                <td>
                    <?php echo htmlspecialchars($request['subject']); ?>
                </td>
                <td>
                    <div class="status-box 
                            <?php 
                                // Dynamically add the appropriate status class
                                if ($request['status'] == 'pending') {
                                    echo 'status-pending';
                                } elseif ($request['status'] == 'rejected') {
                                    echo 'status-rejected';
                                } elseif ($request['status'] == 'approved') {
                                    echo 'status-approved';
                                }
                            ?>">
                        <?php echo htmlspecialchars($request['status']); ?>
                    </div>
                    <?php if ($request['status'] == 'rejected'): ?>
                    <div>
                        <strong>Feedback:</strong>
                        <?php echo htmlspecialchars($request['feedback']); ?>
                    </div>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="edit_request.php?id=<?php echo $request['id']; ?>">Edit</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <footer>
    <p>Designed by AyushkEmpire &copy; 2024 AyushkEmpire. All rights reserved.</p>
    </footer>
</body>

</html>