<?php
// Configuration de la base de données
$host = 'localhost';      // Hôte de la base de données (généralement localhost)
$dbname = 'adjarra';      // Nom de votre base de données
$username = 'root';       // Nom d'utilisateur de la base de données (à modifier selon votre configuration)
$password = 'Fadyl111';           // Mot de passe de la base de données (à modifier selon votre configuration)

// Configuration pour résoudre le problème avec l'auto-incrémentation de l'ID

// Initialisation des messages d'erreur et de succès
$error = '';
$success = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $user_name = trim($_POST['user_name']);
    $user_email = trim($_POST['user_email']);
    $user_phone = trim($_POST['user_phone']);
    $user_password = $_POST['user_password'];
    $confirm_password = $_POST['confirm_password']; // Utilisé seulement pour la validation, pas pour l'insertion
    $first_loging = 1; // Valeur par défaut pour les nouveaux utilisateurs
    
    // Validation côté serveur
    if (empty($user_name) || strlen($user_name) < 3) {
        $error = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
    } elseif (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Veuillez entrer une adresse email valide.";
    } elseif (empty($user_phone) || !preg_match('/^\d{10}$/', $user_phone)) {
        $error = "Veuillez entrer un numéro de téléphone valide (10 chiffres).";
    } elseif (empty($user_password) || strlen($user_password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($user_password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            // Connexion à la base de données
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            // Configurer PDO pour qu'il lance des exceptions en cas d'erreur
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Vérifier si l'ID est AUTO_INCREMENT, sinon faire une requête pour récupérer l'ID max + 1
            $stmt = $conn->prepare("SHOW COLUMNS FROM users WHERE Field = 'id'");
            $stmt->execute();
            $column_info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Si l'ID n'est pas AUTO_INCREMENT ou n'a pas de valeur par défaut, nous devrons le gérer manuellement
            $manual_id = false;
            if (!strpos(strtoupper($column_info['Extra']), 'AUTO_INCREMENT')) {
                $manual_id = true;
            }
            
            // Vérifier si l'email existe déjà
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_email = ?");
            $stmt->execute([$user_email]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $error = "Cette adresse email est déjà utilisée. Veuillez en choisir une autre.";
            } else {
                // Vérifier si le nom d'utilisateur existe déjà
                $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_name = ?");
                $stmt->execute([$user_name]);
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $error = "Ce nom d'utilisateur est déjà pris. Veuillez en choisir un autre.";
                } else {
                    // Hacher le mot de passe
                    $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
                    // Pour le confirm_password, on stocke la même valeur hachée que le mot de passe
                    $hashed_confirm_password = $hashed_password;
                    
                    // Si nous devons gérer l'ID manuellement
                    if ($manual_id) {
                        // Récupérer l'ID maximum actuel
                        $stmt = $conn->query("SELECT MAX(id) as max_id FROM users");
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $next_id = ($result['max_id'] !== null) ? $result['max_id'] + 1 : 1;
                        
                        // Préparer et exécuter la requête d'insertion en incluant l'ID
                        // Incluant le champ confirm_password selon la structure de votre table
                        $stmt = $conn->prepare("INSERT INTO users (id, user_name, user_email, user_phone, user_password, confirm_password, first_loging) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$next_id, $user_name, $user_email, $user_phone, $hashed_password, $hashed_confirm_password, $first_loging]);
                    } else {
                        // Préparer et exécuter la requête d'insertion sans spécifier l'ID (auto-increment)
                        // Incluant le champ confirm_password selon la structure de votre table
                        $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_phone, user_password, confirm_password, first_loging) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$user_name, $user_email, $user_phone, $hashed_password, $hashed_confirm_password, $first_loging]);
                    }
                    
                    // Rediriger vers la page de connexion avec un message de succès
                    $success = "Votre compte a été créé avec succès! Vous pouvez maintenant vous connecter.";
                    // Redirection vers la page de connexion après 2 secondes
                    header("connexion.php");
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de l'inscription: " . $e->getMessage();
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
    <title>Formulaire d'inscription</title>
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
            <h1>Créer un compte</h1>
            <p>Complétez le formulaire ci-dessous pour vous inscrire</p>
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
        <?php endif; ?>
        
        <form id="registrationForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="user_name">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="user_email">Adresse email</label>
                <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo isset($_POST['user_email']) ? htmlspecialchars($_POST['user_email']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="user_phone">Numéro de téléphone</label>
                <input type="tel" class="form-control" id="user_phone" name="user_phone" value="<?php echo isset($_POST['user_phone']) ? htmlspecialchars($_POST['user_phone']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="user_password">Mot de passe</label>
                <input type="password" class="form-control" id="user_password" name="user_password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            
            <input type="hidden" name="first_loging" value="1">
            
            <button type="submit" class="btn"><a href="connexio.php">S'inscrire</a></button>
        </form>
        
        <div class="form-footer">
            Vous avez déjà un compte? <a href="connexio.php">Connectez-vous ici</a>
        </div>
    </div>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            let hasError = false;
            
            // Validate username
            const username = document.getElementById('user_name').value;
            if (username.length < 3) {
                e.preventDefault();
                alert('Le nom d\'utilisateur doit contenir au moins 3 caractères');
                hasError = true;
            }
            
            // Validate email
            const email = document.getElementById('user_email').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Veuillez entrer une adresse email valide');
                hasError = true;
            }
            
            // Validate phone
            const phone = document.getElementById('user_phone').value;
            const phoneRegex = /^\d{10}$/;
            if (!phoneRegex.test(phone)) {
                e.preventDefault();
                alert('Veuillez entrer un numéro de téléphone valide (10 chiffres)');
                hasError = true;
            }
            
            // Validate password
            const password = document.getElementById('user_password').value;
            if (password.length < 8) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 8 caractères');
                hasError = true;
            }
            
            // Validate password confirmation
            const confirmPassword = document.getElementById('confirm_password').value;
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas');
                hasError = true;
            }
        });
    </script>
</body>
</html>