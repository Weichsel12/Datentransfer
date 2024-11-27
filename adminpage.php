<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin-Seite</title>
</head>
<body>

<h1>Willkommen, Administrator!</h1>

<!-- Buttons für die Verwaltung -->
<form method="POST" action="adminpage.php">
    <input type="submit" name="manage_user" value="Benutzer anlegen">
    <input type="submit" name="manage_course" value="Kurse verwalten">
</form>

<?php
// Navigiere zu den entsprechenden Seiten basierend auf dem Button-Klick
if (isset($_POST['manage_user'])) {
    header('Location: adminbenutzer.php'); // Weiterleitung zur Benutzer-Anlage-Seite
    exit();
}

if (isset($_POST['manage_course'])) {
    header('Location: adminkurse.php'); // Weiterleitung zur Kursverwaltungs-Seite
    exit();
}
?>

</body>
</html>
