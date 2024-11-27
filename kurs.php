<?php
session_start();
include('dbconnect.php');

if (isset($_SESSION['Kurs_ID'])) {
    $Kurs_ID = $_SESSION['Kurs_ID'];  // Kurs-ID aus der Session holen

    // Kursdaten für den Benutzer abrufen
    $stmt = $conn->prepare("SELECT Kurs_ID, Kursbezeichnung, Datum FROM tbl_kurs WHERE Kurs_ID = ?");
    $stmt->bind_param("i", $Kurs_ID);  // Die Kurs-ID nutzen, um den Kurs zu finden
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $kurs = $result->fetch_assoc();
    } else {
        echo "Kein Kurs für diesen Benutzer gefunden.";
    }
} else {
    echo "Benutzer nicht eingeloggt.";
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benutzer-Kurs</title>
</head>
<body>
    <h1>Ihr Kurs</h1>
    <?php if (isset($kurs)): ?>
        <p>Kurs ID: <?php echo htmlspecialchars($kurs['Kurs_ID']); ?></p>
        <p>Kursbezeichnung: <?php echo htmlspecialchars($kurs['Kursbezeichnung']); ?></p>
        <p>Datum: <?php echo htmlspecialchars($kurs['Datum']); ?></p>

        <h2>Dokumente</h2>
        <ul>
            <?php
            // Dokumente für den Kurs abrufen
            $stmt_docs = $conn->prepare("SELECT doc_id, Bezeichnung, Pfad FROM tbl_docs WHERE tbl_kurs_Kurs_ID = ?");
            $stmt_docs->bind_param("i", $kurs['Kurs_ID']);
            $stmt_docs->execute();
            $result_docs = $stmt_docs->get_result();

            if ($result_docs->num_rows > 0):
                while ($doc = $result_docs->fetch_assoc()):
            ?>
                    <li>
                        <a href="download.php?file_id=<?php echo $doc['doc_id']; ?>">Download: <?php echo htmlspecialchars($doc['Bezeichnung']); ?></a>
                    </li>
            <?php
                endwhile;
            else:
                echo "<p>Keine Dokumente verfügbar.</p>";
            endif;

            $stmt_docs->close();
            ?>
        </ul>
    <?php else: ?>
        <p>Kein Kurs verfügbar.</p>
    <?php endif; ?>

    <footer>
        <p><a href="logout.php">Logout</a></p>
    </footer>
</body>
</html>

