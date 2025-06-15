<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Paramètres de connexion à la base de données
define('DB_HOST', 'sql104.infinityfree.com');
define('DB_NAME', 'if0_36717473_adjarra');
define('DB_USER', 'if0_36717473');
define('DB_PASS', 'gQLAf68bzFnfO');

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

function updateViews($pdo, $id) {
    $sql = "UPDATE documents SET `reads` = `reads` + 1 WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute(['id' => $id]);
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT file_path, name FROM documents WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($document) {
        $filePath = 'Admin/uploads/' . $document['file_path'];
        if (file_exists($filePath)) {
            $fileExtension = strtolower(pathinfo($document['file_path'], PATHINFO_EXTENSION));
            if ($fileExtension !== 'pdf') {
                echo "Type de fichier non autorisé.";
                exit;
            }

            try {
                updateViews($pdo, $id);
                ?>
                <!DOCTYPE html>
                <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title><?= htmlspecialchars($document['name']) ?></title>
                    <style>
                        body, html {
                            margin: 0;
                            padding: 0;
                            height: 100%;
                            overflow: hidden;
                        }
                        iframe {
                            width: 100%;
                            height: 100%;
                            border: none;
                        }
                    </style>
                </head>
                <body>
                    <iframe src="<?= $filePath ?>" type="application/pdf"></iframe>
                </body>
                </html>
                <?php
                exit;
            } catch (Exception $e) {
                error_log($e->getMessage());
                echo "Une erreur est survenue lors de la mise à jour du compteur de vues.";
                exit;
            }
        } else {
            echo "Le fichier n'existe pas.";
            exit;
        }
    } else {
        echo "Document non trouvé.";
        exit;
    }
} else {
    echo "ID du document non spécifié.";
    exit;
}
?>