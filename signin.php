<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
        /* Styling for the form container */
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }

        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px; /* Fixed width for the form */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2>Sign In</h2>
            <form action="signin_action.php" method="POST">
                <label for="role">Role:</label>
                <select id="role" name="user_type" onchange="updateFields()" required>
                    <option value="" disabled selected>Select</option>
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                </select>

                <!-- Roll Number for Students -->
                <div id="rollNumberField" style="display: none;">
                    <label for="roll_number">Roll Number:</label>
                    <input type="text" id="roll_number" name="roll_number">
                </div>

                <!-- Username for Staff, Mentor, HOD, Principal, Admin -->
                <div id="usernameField">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username">
                </div>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <input type="hidden" id="login_time" name="login_time" value="">
                <button type="submit" onclick="setLoginTime()">Sign In</button>
                <p style="text-align: center; margin-top: 20px;">Don't have an account? <a href="signup.php">Sign Up</a></p>
                </form>
        </div>
    </div>

    <script>
        function updateFields() {
    const role = document.getElementById('role').value;
    const rollNumberField = document.getElementById('rollNumberField');
    const usernameField = document.getElementById('usernameField');
    const rollNumberInput = document.getElementById('roll_number');
    const hiddenRole = document.getElementById('hiddenRole');

    if (role === 'student') {
        rollNumberField.style.display = 'block';
        usernameField.style.display = 'none';
        rollNumberInput.setAttribute('required', 'required');
    } else {
        rollNumberField.style.display = 'none';
        usernameField.style.display = 'block';
        rollNumberInput.removeAttribute('required');
    }

        }
        function setLoginTime() {
            const loginTimeInput = document.getElementById('login_time');
            const currentTime = new Date();
            loginTimeInput.value = currentTime.toISOString(); // Store as ISO string for database
        }
    </script>
</body>
</html>