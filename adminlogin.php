<?php
session_start();
include('dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $internal_username = trim($_POST['internal_username']);
    $internal_password = trim($_POST['internal_password']);

    if (!empty($internal_username) && !empty($internal_password)) {
        // Prepared Statement verwenden
        $stmt = $conn->prepare("SELECT internal_id, internal_password, Admin FROM tbl_internal WHERE internal_username = ?");
        $stmt->bind_param("s", $internal_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $internal = $result->fetch_assoc();

            if (password_verify($internal_password, $internal['internal_password'])) {

                // Wenn der Benutzer ein Admin ist, setzen wir eine Session-Variable
                if ($internal['Admin'] == 1) {
                    $_SESSION['internal_id'] = $internal['internal_id'];
                    $_SESSION['internal_admin'] = 1;  // Admin-Berechtigung setzen
                    header("Location: adminpage.php");
                    exit();
                } else {
                    $error = "Sie sind Trainer und kein Admin!";
                }
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
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Login</title>
</head>
<body>
    <h1>Admin-Login</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post" action="adminlogin.php">
        <label for="internal_username">Adminname:</label>
        <input type="text" name="internal_username" id="internal_username" required>
        <br />
        <label for="internal_password">Admin-Passwort:</label>
        <input type="password" name="internal_password" id="internal_password" required>
        <br />
        <input type="submit" value="Login">
    </form>

    <footer>
        <p>Zum User-Login -> <a href="index.php">User</a></p>
    </footer>
</body>
</html>
