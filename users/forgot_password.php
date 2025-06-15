<?php
// Configuration de la base de données
$host = 'localhost';      // Hôte de la base de données (généralement localhost)
$dbname = 'adjarra';      // Nom de votre base de données
$username = 'root';       // Nom d'utilisateur de la base de données
$password = 'Fadyl111';           // Mot de passe de la base de données

// Initialisation des messages d'erreur et de succès
$error = '';
$success = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer l'email du formulaire
    $user_email = trim($_POST['user_email']);
    
    // Validation côté serveur
    if (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Veuillez entrer une adresse email valide.";
    } else {
        try {
            // Connexion à la base de données
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            // Configurer PDO pour qu'il lance des exceptions en cas d'erreur
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Vérifier si l'email existe dans la base de données
            $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
            $stmt->execute([$user_email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Générer un token de réinitialisation unique
                $token = bin2hex(random_bytes(50));
                
                // Définir la date d'expiration (24 heures)
                $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                // Enregistrer le token dans la base de données
                $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE user_email = ?");
                $stmt->execute([$token, $expiry, $user_email]);
                
                // Dans un environnement de production, envoyer un email avec le lien de réinitialisation
                // Pour ce tutoriel, nous affichons simplement le lien
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
                
                $success = "Un email contenant les instructions pour réinitialiser votre mot de passe a été envoyé à votre adresse si elle existe dans notre système.<br>Lien de réinitialisation (pour démonstration) : <a href='$reset_link'>$reset_link</a>";
            } else {
                // Pour des raisons de sécurité, ne pas indiquer si l'email existe ou non
                $success = "Un email contenant les instructions pour réinitialiser votre mot de passe a été envoyé à votre adresse si elle existe dans notre système.";
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la demande de réinitialisation: " . $e->getMessage();
        }
        
        // Fermer la connexion
        $conn = null;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 40px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #4a90e2;
            outline: none;
        }
        
        .btn {
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #357abd;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        
        .form-footer a {
            color: #4a90e2;
            text-decoration: none;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .success-message {
            background-color: #2ecc71;
            color: white;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .error-alert {
            background-color: #e74c3c;
            color: white;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Mot de passe oublié</h1>
            <p>Entrez votre adresse email pour réinitialiser votre mot de passe</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success-message">
                <?php echo $success; ?>
            </div>
        <?php else: ?>
            <form id="forgotPasswordForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="user_email">Adresse email</label>
                    <input type="email" class="form-control" id="user_email" name="user_email" required>
                </div>
                
                <button type="submit" class="btn">Réinitialiser le mot de passe</button>
            </form>
        <?php endif; ?>
        
        <div class="form-footer">
            <a href="connexion.php">Retour à la page de connexion</a>
        </div>
    </div>

    <script>
        document.getElementById('forgotPasswordForm')?.addEventListener('submit', function(e) {
            // Validate email
            const email = document.getElementById('user_email').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Veuillez entrer une adresse email valide');
            }
        });
    </script>
</body>
</html>