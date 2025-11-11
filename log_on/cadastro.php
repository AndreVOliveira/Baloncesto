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
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $telefone = $_POST['telefone'];

    // Check if email or telefone already exists
    $check_sql = "SELECT * FROM cadastro WHERE email = ? OR telefone = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("si", $email, $telefone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Email or telefone already exists.";
    } else {
        // Insert new user
        $insert_sql = "INSERT INTO cadastro (nome, email, senha, telefone) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sssi", $nome, $email, $senha, $telefone);

        if ($stmt->execute()) {
            $message = "Registration successful!";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>
</head>
<body>
    <h2>Cadastro</h2>
    <form method="post" action="">
        Nome: <input type="text" name="nome" required><br>
        Email: <input type="email" name="email" required><br>
        Senha: <input type="password" name="senha" required><br>
        Telefone: <input type="number" name="telefone" required><br>
        <input type="submit" value="Cadastrar">
    </form>
    <?php if ($message) echo "<p>$message</p>"; ?>
    <br>
    <a href="log_in.php"><button>Log In</button></a>
</body>
</html>
