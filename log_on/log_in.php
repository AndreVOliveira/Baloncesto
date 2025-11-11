<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "Home@spSENAI2025!";
$dbname = "basquete";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Query to check if email and senha match
    $sql = "SELECT * FROM cadastro WHERE email = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Login successful!";
        // Here you can start a session or redirect to a dashboard
    } else {
        $message = "Invalid email or password.";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log In</title>
</head>
<body>
    <h2>Log In</h2>
    <form method="post" action="">
        Email: <input type="email" name="email" required><br>
        Senha: <input type="password" name="senha" required><br>
        <input type="submit" value="Log In">
    </form>
    <?php if ($message) echo "<p>$message</p>"; ?>
    <br>
    <a href="cadastro.php"><button>Cadastrar</button></a>
</body>
</html>
