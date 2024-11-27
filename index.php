<?php
session_start();
include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Prepared Statement verwenden
        $stmt = $conn->prepare("SELECT user_id, password, tbl_kurs_Kurs_ID FROM tbl_user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Passwort überprüfen
            if (password_verify($password, $user['password'])) {
                // Login erfolgreich
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['Kurs_ID'] = $user['tbl_kurs_Kurs_ID'];
                header("Location: kurs.php");
                exit();
            } else {
                $error = "Ungültige Login-Daten.";
            }
        } else {
            $error = "Ungültige Login-Daten.";
        }
        $stmt->close();
    } else {
        $error = "Bitte alle Felder ausfüllen.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="index.php">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br />
        <label for="password">Passwort:</label>
        <input type="password" name="password" id="password" required>
        <br />
        <input type="submit" value="Login">
    </form>

        <footer>
            <p>Zum Admin-Login -> <a href="adminlogin.php">Admin</a></p>
    </footer>
</body>
</html>
