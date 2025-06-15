<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['doc-file'])) {
    $uploadDir = __DIR__ . '/uploads/';
    $fileName = uniqid() . '_' . $_FILES['doc-file']['name'];
    $filePath = $uploadDir . $fileName;
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    if ($_FILES['doc-file']['size'] > 5000000) { // 5 MB limit
        $error = "Le fichier est trop volumineux. La limite est de 5 MB.";
    } elseif (move_uploaded_file($_FILES['doc-file']['tmp_name'], $filePath)) {
        $fileSize = filesize($filePath);
        $stmt = $pdo->prepare("INSERT INTO documents (name, file_path, size, `reads`, downloads) VALUES (?, ?, ?, 0, 0)");
        if ($stmt->execute([$_POST['doc-name'], $fileName, round($fileSize / 1024, 2)])) {
            $success = "Document ajouté avec succès.";
        } else {
            $error = "Erreur lors de l'insertion dans la base de données: " . implode(", ", $stmt->errorInfo());
        }
    } else {
        $error = "Erreur lors de l'upload du fichier : " . $_FILES['doc-file']['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Document</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
        }
        #upload-form {
            margin-top: 30px;
        }
        #upload-form input,
        #upload-form button {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        #upload-form button {
            background-color: #2ecc71;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        #upload-form button:hover {
            background-color: #27ae60;
        }
        .btn-back {
            display: block;
            width: 200px;
            margin: 20px auto 0;
            padding: 10px 15px;
            text-align: center;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #2980b9;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter un nouveau document</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form id="upload-form" method="post" enctype="multipart/form-data">
            <input type="text" id="doc-name" name="doc-name" placeholder="Nom du document" required>
            <input type="file" id="doc-file" name="doc-file" accept=".pdf" required>
            <button type="submit">Ajouter le document</button>
        </form>
        <a href="index.php" class="btn-back">Retour</a>
    </div>
</body>
</html>