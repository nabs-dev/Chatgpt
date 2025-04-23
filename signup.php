<?php
require_once 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);
    if(empty($name) || empty($password)){
        $error = "Please fill all fields";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0){
            $error = "User already exists";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $hashed_password);
            if($stmt->execute()){
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sign Up - ChatGPT Clone</title>
    <style>
        /* Internal CSS for signup page */
        body {
            background: #f5f5f5;
            font-family: Arial, sans-serif;
        }
        .container {
            width: 350px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            border: none;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Sign Up</h2>
    <?php if(isset($error)){ echo '<p class="error">'.$error.'</p>'; } ?>
    <form method="post" action="">
        <input type="text" name="name" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>
    <p style="text-align:center;">Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>
