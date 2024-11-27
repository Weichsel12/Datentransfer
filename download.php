<?php
session_start();
include('dbconnect.php');

if (isset($_SESSION['user_id']) && isset($_GET['file_id'])) {
    $file_id = intval($_GET['file_id']);  // File-ID aus der URL holen

    $stmt = $conn->prepare("SELECT Pfad FROM tbl_docs WHERE doc_id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $file = $result->fetch_assoc();
        $filepath = $file['Pfad'];

        // Datei herunterladen
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } else {
            echo "Datei nicht gefunden.";
        }
    } else {
        echo "Ungültige Datei-ID.";
    }
} else {
    echo "Nicht autorisiert.";
}
?>
