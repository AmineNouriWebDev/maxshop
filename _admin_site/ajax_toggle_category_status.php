<?php
session_start();
include("includes/include.php");
include("includes/security.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
    $id = sanitize($_POST['id']);
    $status = $_POST['status'] === '1' ? '1' : '0';

    $query = "UPDATE categories_blog SET etat = '$status' WHERE id = '$id' OR idparent = '$id' OR idparent IN (SELECT temp.id FROM (SELECT id FROM categories_blog WHERE idparent = '$id') as temp)";
    if (mysqli_query($connexion, $query)) {
        // Fetch subcategories and grandchildren to update UI
        $subs = [];
        $resSubs = mysqli_query($connexion, "SELECT id FROM categories_blog WHERE idparent = '$id' OR idparent IN (SELECT temp.id FROM (SELECT id FROM categories_blog WHERE idparent = '$id') as temp)");
        if ($resSubs) {
            while ($row = mysqli_fetch_assoc($resSubs)) {
                $subs[] = $row['id'];
            }
        }
        echo json_encode(['status' => 'success', 'subs' => $subs]);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($connexion)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Requête invalide']);
}
?>
