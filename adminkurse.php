<?php
session_start();
include('dbconnect.php');

// Sicherstellen, dass der Benutzer ein Administrator ist
if (!isset($_SESSION['internal_id']) || $_SESSION['internal_id'] == '') {
    header('Location: adminlogin.php');
    exit();
}

// Wenn das Formular abgesendet wurde, neuen Kurs anlegen oder bestehenden Kurs aktualisieren
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_kurs'])) {
    $kurs_name = trim($_POST['kurs_name']);
    $kurs_datum = trim($_POST['kurs_datum']);
    $trainer_id = intval($_POST['trainer_id']); // Trainer-ID aus Dropdown

    // Prüfen, ob wir einen Kurs aktualisieren oder einen neuen Kurs anlegen
    if (isset($_POST['kurs_id']) && !empty($_POST['kurs_id'])) {
        // Kurs aktualisieren
        $kurs_id = intval($_POST['kurs_id']);
        $stmt_kurs = $conn->prepare("UPDATE tbl_kurs SET Kursbezeichnung = ?, Datum = ?, tbl_internal_internal_id = ? WHERE Kurs_ID = ?");
        $stmt_kurs->bind_param("ssii", $kurs_name, $kurs_datum, $trainer_id, $kurs_id);

        if ($stmt_kurs->execute()) {
            echo "Kurs erfolgreich aktualisiert!<br>";
        } else {
            echo "Fehler beim Aktualisieren des Kurses: " . $conn->error . "<br>";
        }

        $stmt_kurs->close();
    } else {
        // Neuen Kurs anlegen
        $stmt_kurs = $conn->prepare("INSERT INTO tbl_kurs (Kursbezeichnung, Datum, tbl_internal_internal_id) VALUES (?, ?, ?)");
        $stmt_kurs->bind_param("ssi", $kurs_name, $kurs_datum, $trainer_id);

        if ($stmt_kurs->execute()) {
            echo "Kurs erfolgreich hinzugefügt!<br>";
        } else {
            echo "Fehler beim Hinzufügen des Kurses: " . $conn->error . "<br>";
        }

        $stmt_kurs->close();
    }
}

// Wenn ein Kurs bearbeitet wird, die Kursdaten laden
if (isset($_GET['edit'])) {
    $kurs_id = intval($_GET['edit']);
    $kurs_query = "SELECT * FROM tbl_kurs WHERE Kurs_ID = ?";
    $stmt = $conn->prepare($kurs_query);
    $stmt->bind_param("i", $kurs_id);
    $stmt->execute();
    $kurs_result = $stmt->get_result();
    $kurs = $kurs_result->fetch_assoc();
    $stmt->close();
}

// Wenn ein Kurs gelöscht wird
if (isset($_GET['delete'])) {
    $kurs_id = intval($_GET['delete']);
    $delete_query = "DELETE FROM tbl_kurs WHERE Kurs_ID = ?";
    $stmt_delete = $conn->prepare($delete_query);
    $stmt_delete->bind_param("i", $kurs_id);
    if ($stmt_delete->execute()) {
        echo "Kurs erfolgreich gelöscht!<br>";
    } else {
        echo "Fehler beim Löschen des Kurses: " . $conn->error . "<br>";
    }
    $stmt_delete->close();
}

// Alle Kurse anzeigen
$kurs_query = "SELECT Kurs_ID, Kursbezeichnung, Datum, tbl_internal_internal_id FROM tbl_kurs";
$kurs_result = $conn->query($kurs_query);

// Alle Trainer anzeigen
$trainer_query = "SELECT internal_id, Vorname, Nachname FROM tbl_internal WHERE Bezeichnung = 'Trainer'";
$trainer_result = $conn->query($trainer_query);
$trainer_drop = $conn->query($trainer_query);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurse verwalten</title>
</head>
<body>

<h1>Alle Kurse</h1>
<table>
    <tr>
        <th>Kursbezeichnung</th>
        <th>Datum</th>
        <th>Trainer</th>
        <th>Aktionen</th>
    </tr>
    <?php
    while ($row = $kurs_result->fetch_assoc()) {
        $trainer_id = $row['tbl_internal_internal_id'];
        $stmt_trainer = $conn->prepare("SELECT Vorname, Nachname FROM tbl_internal WHERE internal_id = ?");
        $stmt_trainer->bind_param("i", $trainer_id);
        $stmt_trainer->execute();
        $trainer_result = $stmt_trainer->get_result();
        $trainer = $trainer_result->fetch_assoc();

        echo "<tr>";
        echo "<td>" . $row['Kursbezeichnung'] . "</td>";
        echo "<td>" . $row['Datum'] . "</td>";
        echo "<td>" . $trainer['Vorname'] . " " . $trainer['Nachname'] . "</td>";
        echo "<td><a href='?edit=" . $row['Kurs_ID'] . "'>Bearbeiten</a> | <a href='?delete=" . $row['Kurs_ID'] . "' onclick='return confirm(\"Sind Sie sicher, dass Sie diesen Kurs löschen möchten?\")'>Löschen</a></td>";
        echo "</tr>";
    }
    ?>
</table>

<h1>Neuen Kurs anlegen</h1>
<form method="POST" action="adminkurse.php">
    <label for="kurs_name">Kursbezeichnung:</label>
    <input type="text" name="kurs_name" id="kurs_name" value="<?php echo isset($kurs) ? $kurs['Kursbezeichnung'] : ''; ?>" required><br>

    <label for="kurs_datum">Kursdatum:</label>
    <input type="date" name="kurs_datum" id="kurs_datum" value="<?php echo isset($kurs) ? $kurs['Datum'] : ''; ?>" required><br>

    <label for="trainer_id">Trainer:</label>
    <select name="trainer_id" id="trainer_id" required>
        <?php
        while ($trainer = $trainer_drop->fetch_assoc()) {
            echo "<option value='" . $trainer['internal_id'] . "'" . (isset($kurs) && $kurs['tbl_internal_internal_id'] == $trainer['internal_id'] ? ' selected' : '') . ">" . $trainer['Vorname'] . " " . $trainer['Nachname'] . "</option>";
        }
        ?>
    </select><br>

    <?php
    if (isset($kurs)) {
        echo "<input type='hidden' name='kurs_id' value='" . $kurs['Kurs_ID'] . "'>";
        echo "<input type='submit' name='submit_kurs' value='Kurs aktualisieren'>";
    } else {
        echo "<input type='submit' name='submit_kurs' value='Kurs hinzufügen'>";
    }
    ?>
</form>
<footer>
    <a href="logoutadmin.php">Logout</a>
</footer>
</body>

</html>
