<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
require 'db.php';

$admin_id = $_SESSION['user_id'];

// Handle request approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $request_id = $_POST['id'];
    $new_status = $_POST['status'];

    // If rejection, also store the rejection reason
    if (isset($_POST['rejection_reason'])) {
        $rejection_reason = $_POST['rejection_reason'];
        $stmt = $conn->prepare("UPDATE requests SET status = ?, feedback = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_status, $rejection_reason, $request_id);
    } else {
        $stmt = $conn->prepare("UPDATE requests SET status = ?, feedback = 'Approved and ready for setup.'  WHERE id = ?");
        $stmt->bind_param("si", $new_status, $request_id);
    }
    $stmt->execute();
    $stmt->close();

    // Redirect to avoid resubmitting the form on page refresh
    header("Location: admin_dashboard.php");
    exit;
}

// Fetch all requests along with user details
$all_requests = [];
$result = $conn->query("
    SELECT requests.*, users.name, users.registration_no, users.email, users.role 
    FROM requests 
    JOIN users ON requests.user_id = users.id
	WHERE requests.status != 'pending'
");
while ($row = $result->fetch_assoc()) {
    $all_requests[] = $row;
}

// Fetch pending requests along with user details
$pending_requests = [];
$result = $conn->query("
    SELECT requests.*, users.name, users.registration_no, users.email, users.role 
    FROM requests 
    JOIN users ON requests.user_id = users.id 
    WHERE requests.status = 'pending'
");
while ($row = $result->fetch_assoc()) {
    $pending_requests[] = $row;
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

        
        h1, h2 {
            color: white;
            margin: 20px 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
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
            margin: 5px;
            
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            background-color: orange;
            color: #fff;
            transition: background 0.3s, transform 0.2s;
        }
        .green{
            background-color: green;
            width: 100px;
        }
        .red{
            background-color: red;
            width: 100px;
            }

        button:hover {
            transform: translateY(-3px);
            filter: brightness(1.1);
        }

        button:active {
            transform: translateY(1px);
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: black;
            color: white;
        }

        td form {
            display: inline;
        }

        .hidden {
            display: none;
        }

        .rejection-reason {
            margin-top: 10px;
            display: none;
        }

        .rejection-reason textarea {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .details {
            display: none;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
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
    <script>
        function togglePendingRequests() {
            var pendingSection = document.getElementById("pendingRequestsSection");
            var toggleButton = document.getElementById("togglePendingButton");
            if (pendingSection.style.display === "none") {
                pendingSection.style.display = "block";
                toggleButton.textContent = "Hide Pending Requests";
            } else {
                pendingSection.style.display = "none";
                toggleButton.textContent = "Show Pending Requests";
            }
        }

        function showDetails(button) {
            var detailsBox = button.closest("tr").querySelector(".details");
            detailsBox.style.display = detailsBox.style.display === "block" ? "none" : "block";
        }
		
		function toggleEditButtons(button) {
			var editButtons = button.closest(".details").querySelector(".edit-buttons");
			editButtons.classList.toggle("hidden");
		}

        function showRejectionReason(button) {
            var reasonBox = button.closest("tr").querySelector(".rejection-reason");
            reasonBox.style.display = reasonBox.style.display === "block" ? "none" : "block";
        }
    </script>
</head>
<body>
    <button id="logoutButton" onclick="location.href='logout.php'">Logout</button>
    <h1>Dashboard</h1>

    <button id="togglePendingButton" onclick="togglePendingRequests()">Show Pending Requests</button>

    <div id="pendingRequestsSection" style="display: none;">
        <h2>Pending Requests</h2>
        <table>
            <tr>
                <th>Subject</th>
                <th>Date</th>
                <th>Details</th>
                <th>Approve</th>
                <th>Reject</th>
            </tr>
            <?php foreach ($pending_requests as $request): ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['subject']); ?></td>
                    <td><?php echo htmlspecialchars($request['date']); ?></td>
                    <td>
                        <button type="button" onclick="showDetails(this)">View Details</button>
                        <div class="details">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($request['name']); ?></p>
                            <p><strong>Registration Number:</strong> <?php echo htmlspecialchars($request['registration_no']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($request['email']); ?></p>
                            <p><strong>Type:</strong> <?php echo htmlspecialchars($request['role']); ?></p>
							<p><strong>Auditorium:</strong> <?php echo htmlspecialchars($request['auditorium']); ?></p>
                            <p><strong>Additional Details:</strong> <?php echo !empty($request['description']) ? htmlspecialchars($request['description']) : 'N/A'; ?>
							</p>

                        </div>
                    </td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $request['id']; ?>">
                            <button class="green" type="submit" name="status" value="approved">Approve</button>
                        </form>
                    </td>
                    <td>
                        <button class="red" type="button" onclick="showRejectionReason(this)">Reject</button>
                        <div class="rejection-reason">
                            <form method="POST">
                                <input type="hidden" name="id" value="<?php echo $request['id']; ?>">
                                <textarea name="rejection_reason" placeholder="Enter rejection reason..." required></textarea>
                                <button type="submit" name="status" value="rejected">Submit</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <h2>All Events</h2>
	<table>
		<tr>
			<th>Subject</th>
			<th>Date</th>
			<th>Status</th>
			<th>Details</th>
		</tr>
		<?php foreach ($all_requests as $request): ?>
			<tr>
				<td><?php echo htmlspecialchars($request['subject']); ?></td>
				<td><?php echo htmlspecialchars($request['date']); ?></td>
                <td>
                <span style="
                    padding: 5px 10px;
                    border-radius: 5px;
                    color: white;
                    background-color: <?php echo ($request['status'] === 'approved') ? 'green' : ($request['status'] === 'rejected' ? 'red' : 'yellow'); ?>">
                    <?php echo htmlspecialchars(ucfirst($request['status'])); ?>
                </span>
                </td>
				<td>
					<button type="button" onclick="showDetails(this)">View Details</button>
					<div class="details">
						<p><strong>Name:</strong> <?php echo htmlspecialchars($request['name']); ?></p>
						<p><strong>Registration Number:</strong> <?php echo htmlspecialchars($request['registration_no']); ?></p>
						<p><strong>Email:</strong> <?php echo htmlspecialchars($request['email']); ?></p>
						<p><strong>Type:</strong> <?php echo htmlspecialchars($request['role']); ?></p>
						<p><strong>Auditorium:</strong> <?php echo htmlspecialchars($request['auditorium']); ?></p>
						<p><strong>Additional Details:</strong> <?php echo !empty($request['description']) ? htmlspecialchars($request['description']) : 'N/A'; ?></p>
						
						<!-- Edit Button to Toggle Approve/Reject Buttons -->
						<button type="button" onclick="toggleEditButtons(this)">Edit</button>
						
						<!-- Approve and Reject Buttons Hidden by Default -->
						<div class="edit-buttons hidden">
							<!-- Approve Button Form -->
							<form method="POST">
								<input type="hidden" name="id" value="<?php echo $request['id']; ?>">
								<button class="green" type="submit" name="status" value="approved">Approve</button>
							</form>
							<!-- Reject Button Form -->
							<button class="red" type="button" onclick="showRejectionReason(this)">Reject</button>
							<div class="rejection-reason">
								<form method="POST">
									<input type="hidden" name="id" value="<?php echo $request['id']; ?>">
									<textarea name="rejection_reason" placeholder="Enter rejection reason..." required></textarea>
									<button type="submit" name="status" value="rejected">Submit</button>
								</form>
							</div>
						</div>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
    <footer>
    <p>Designed by AyushkEmpire &copy; 2024 AyushkEmpire. All rights reserved.</p>
    </footer>
</body>
</html>
