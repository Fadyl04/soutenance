<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Paramètres de connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'adjarra');
define('DB_USER', 'root');
define('DB_PASS', 'Fadyl111');
// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
} catch(PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
$message = '';
$uploadDir = 'img/';
// Vérifier si le répertoire d'upload existe, sinon le créer
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $nom = $_POST["nom"] ?? '';
    $description = $_POST["description"] ?? '';
    $date_debut = $_POST["date_debut"] ?? '';
    $date_fin = $_POST["date_fin"] ?? '';
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
        // Vérifier l'extension du fichier
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!array_key_exists($ext, $allowed)) {
            $message = "Erreur : Format de fichier non autorisé.";
        } else {
            // Vérifier la taille du fichier - 5MB maximum
            $maxsize = 5 * 1024 * 1024;
            if ($filesize > $maxsize) {
                $message = "Erreur : La taille du fichier dépasse la limite autorisée.";
            } else {
                // Vérifier le type MIME du fichier
                if (in_array($filetype, $allowed)) {
                    // Générer un nouveau nom de fichier unique
                    $newname = uniqid('projet_') . "." . $ext;
                    $imagePath = $uploadDir . $newname;
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
                        // Fichier valide et téléchargé avec succès
                    } else {
                        $message = "Erreur lors du téléchargement de votre fichier.";
                    }
                } else {
                    $message = "Erreur : Il y a eu un problème de téléchargement du fichier. Réessayez.";
                }
            }
        }
    }
    // Si pas d'erreur, procéder à l'insertion dans la base de données
    if (empty($message)) {
        $sql = "INSERT INTO projets (nom, description, date_debut, date_fin, image_path)
                VALUES (:nom, :description, :date_debut, :date_fin, :image_path)";
        try {
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':nom' => $nom,
                ':description' => $description,
                ':date_debut' => $date_debut,
                ':date_fin' => $date_fin,
                ':image_path' => $imagePath
            ]);
            if ($result) {
                $message = "Nouveau projet ajouté avec succès.";
            } else {
                $message = "Erreur lors de l'insertion du projet.";
            }
        } catch(PDOException $e) {
            $message = "Erreur de base de données : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un projet</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Vos styles CSS ici */
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --background-color: #ecf0f1;
            --card-color: #ffffff;
            --text-color: #333333;
        }
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--background-color);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: var(--card-color);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        h1 {
            color: var(--secondary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--secondary-color);
        }
        input[type="text"], textarea, input[type="date"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, textarea:focus, input[type="date"]:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        input[type="submit"] {
            background-color: var(--primary-color);
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: var(--secondary-color);
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter un nouveau projet</h1>
        <?php
        if ($message) {
            echo '<div class="message ' . (strpos($message, 'succès') !== false ? 'success' : 'error') . '">' . $message . '</div>';
        }
        ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
            <label for="nom">Nom du projet:</label>
            <input type="text" id="nom" name="nom" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4"></textarea>

            <label for="date_debut">Date de début:</label>
            <input type="date" id="date_debut" name="date_debut" required>

            <label for="date_fin">Date de fin prévue:</label>
            <input type="date" id="date_fin" name="date_fin" required>

            <label for="image">Image du projet:</label>
            <input type="file" id="image" name="image" accept="image/*">

            <input type="submit" value="Ajouter le projet">
        </form>
        <a href="index.php" class="back-link">Retour </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateDebut = document.getElementById('date_debut');
            const dateFin = document.getElementById('date_fin');

            dateDebut.addEventListener('change', function() {
                dateFin.min = this.value;
            });

            dateFin.addEventListener('change', function() {
                dateDebut.max = this.value;
            });
        });
    </script>
</body>
</html>