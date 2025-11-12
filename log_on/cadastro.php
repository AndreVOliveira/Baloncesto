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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Cadastro</h1>
    </header>
    <div class="form-container">
        <h2>Cadastro</h2>
        <form method="post" action="">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required><br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required><br>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required><br>
            <label for="telefone">Telefone:</label>
            <input type="number" name="telefone" id="telefone" required><br>
            <input type="submit" value="Cadastrar">
        </form>
        <?php if ($message) echo "<p>$message</p>"; ?>
        <br>
        <a href="log_in.php"><button>Log In</button></a>
    </div>
    <footer>
        <p>&copy; 2023 Baloncesto</p>
    </footer>
</body>
</html>
