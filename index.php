<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Auditorium Booking Management</title>
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
            margin-top: 40vh;
            font-size: 3em;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        a.button {
            display: inline-block;
            margin: 15px;
            padding: 15px 30px;
            font-size: 1em;
            text-decoration: none;
            color: #fff;
            background-color: orange;
            border-radius: 8px;
            transition: background 0.3s, transform 0.2s;
            text-align: center;
        }

        a.button:hover {
            transform: translateY(-3px);
            filter: brightness(1.1);
        }

        a.button:active {
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
.details{
    text-decoration: none;
    color: #fff;
    background-color: black;
    border-radius: 8px;
    padding: 10px 15px;
}

        @media (max-width: 600px) {
            h2 {
                font-size: 1.5em;
            }
            
            a.button {
                width: 90%;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <h2>Request for University Auditorium Booking</h2>
    <a href="login.php?role=user" class="button">User Sign In</a>
    <a href="login.php?role=admin" class="button">Admin Sign In</a>
    <p><a href="details.html" class="details" target="_blank">For Login Details Click here</a></p>
    <footer>
    <p>Designed by AyushkEmpire &copy; 2024 AyushkEmpire. All rights reserved.</p>
    </footer>
</body>
</html>
