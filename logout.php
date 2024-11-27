<?php
session_start();
session_unset(); // Alle Session-Variablen entfernen
session_destroy(); // Die Session zerstören

header("Location: index.php"); // Weiterleitung zur User-Login-Seite
exit();
?>