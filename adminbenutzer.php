<?php
session_start();
include('dbconnect.php');

// Sicherstellen, dass der Benutzer ein Administrator ist
if (!isset($_SESSION['internal_id']) || $_SESSION['internal_id'] == '') {
    header('Location: adminlogin.php');
    exit();
}

// Wenn das Formular abgesendet wurde, neuen Benutzer anlegen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_user'])) {
    // Formular-Daten
    $vorname = trim($_POST['vorname']);
    $nachname = trim($_POST['nachname']);
    $adresse = trim($_POST['adresse']);
    $plz = trim($_POST['plz']);
    $geburtsdatum = trim($_POST['geburtsdatum']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role']; // Rolle: 'user', 'trainer', 'admin'
    $kurs_id = $_POST['kurs_id']; // Der Kurs, dem der Benutzer zugeordnet wird
    $trainer_id = $_POST['trainer_id']; // Trainer zuweisen

    // Passwort verschlüsseln
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Benutzer in die tbl_user Tabelle einfügen
    if ($role == "user") {
        $stmt_user = $conn->prepare("INSERT INTO tbl_user (Vorname, Nachname, Addresse, PLZ, Geburtsdatum, username, password, tbl_kurs_Kurs_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_user->bind_param("sssssssi", $vorname, $nachname, $adresse, $plz, $geburtsdatum, $username, $hashed_password, $kurs_id);

        if ($stmt_user->execute()) {
            echo "Benutzer erfolgreich hinzugefügt!<br>";
        } else {
            echo "Fehler beim Hinzufügen des Benutzers.<br>";
        }
        $stmt_user->close();
    }

    // Nur wenn der Benutzer ein Trainer oder Admin ist, füge ihn auch in die tbl_internal Tabelle ein
    if ($role == 'trainer' || $role == 'admin') {
        $admin_value = ($role == 'admin') ? 1 : 0; // Admin ist 1, Trainer ist 0
        $bezeichnung = ($admin_value == 1) ? 'Admin' : 'Trainer'; // Bezeichnung je nach Rolle

        // Insert in die tbl_internal Tabelle
        $stmt_internal = $conn->prepare("INSERT INTO tbl_internal (Vorname, Nachname, Adresse, PLZ, Admin, internal_username, internal_password, Bezeichnung) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_internal->bind_param("ssssisss", $vorname, $nachname, $adresse, $plz, $admin_value, $username, $hashed_password, $bezeichnung);

        if ($stmt_internal->execute()) {
            echo "Trainer/Admin erfolgreich hinzugefügt!<br>";
        } else {
            echo "Fehler beim Hinzufügen des Trainers/Admin.<br>";
        }
        $stmt_internal->close();
    }
}

// Alle Kurse anzeigen
$kurs_query = "SELECT Kurs_ID, Kursbezeichnung, Datum, tbl_internal_internal_id FROM tbl_kurs";
$kurs_result = $conn->query($kurs_query);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzer anlegen</title>
</head>
<body>

<h1>Neuen Benutzer anlegen</h1>
<form method="POST" action="adminbenutzer.php">
    <label for="vorname">Vorname:</label>
    <input type="text" name="vorname" id="vorname" required><br>

    <label for="nachname">Nachname:</label>
    <input type="text" name="nachname" id="nachname" required><br>

    <label for="adresse">Adresse:</label>
    <input type="text" name="adresse" id="adresse" required><br>

    <label for="plz">PLZ:</label>
    <input type="text" name="plz" id="plz" required><br>

    <label for="geburtsdatum">Geburtsdatum:</label>
    <input type="date" name="geburtsdatum" id="geburtsdatum" required><br>

    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required><br>

    <label for="password">Passwort:</label>
    <input type="password" name="password" id="password" required><br>

    <label for="role">Rolle:</label>
    <select name="role" id="role" required>
        <option value="user">Benutzer</option>
        <option value="trainer">Trainer</option>
        <option value="admin">Administrator</option>
    </select><br>

    <label for="kurs_id">Kurs:</label>
    <select name="kurs_id" id="kurs_id" required>
        <?php
        while ($row = $kurs_result->fetch_assoc()) {
            echo "<option value='" . $row['Kurs_ID'] . "'>" . $row['Kursbezeichnung'] . "</option>";
        }
        ?>
    </select><br>

    <input type="submit" name="submit_user" value="Benutzer hinzufügen">
</form>
<footer>
    <a href="logoutadmin.php">Logout</a>
</footer>
</body>
</html>
