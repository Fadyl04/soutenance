<?php
/* // Configuration de la base de données
$host = 'localhost';      // Hôte de la base de données (généralement localhost)
$dbname = 'adjarra';      // Nom de votre base de données
$username = 'root';       // Nom d'utilisateur de la base de données (à modifier selon votre configuration)
$password = 'Fadyl111';           // Mot de passe de la base de données (à modifier selon votre configuration)

// Démarrage de la session
session_start();

// Initialisation des variables
$error = "";
$success = "";
$blockedUntil = 0;

// Initialiser le compteur de tentatives si non défini
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Initialiser le timestamp de blocage si nécessaire
if (!isset($_SESSION['blocked_until'])) {
    $_SESSION['blocked_until'] = 0;
}

// Vérifier si le délai de blocage est terminé
if ($_SESSION['blocked_until'] > 0 && time() >= $_SESSION['blocked_until']) {
    // Réinitialiser le compteur et le blocage
    $_SESSION['login_attempts'] = 0;
    $_SESSION['blocked_until'] = 0;
}

// Vérifier si l'utilisateur est actuellement bloqué
$isBlocked = ($_SESSION['blocked_until'] > 0 && time() < $_SESSION['blocked_until']);
$remainingBlockTime = $isBlocked ? $_SESSION['blocked_until'] - time() : 0;

// Traitement du formulaire lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !$isBlocked) {
    // Récupération des données du formulaire
    $email = trim($_POST['user_email']);
    $password = $_POST['user_password'];
    
    // Validation de l'email côté serveur
    if (empty($email)) {
        $error = "L'adresse email est requise";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invalide";
    }
    
    // Validation du mot de passe côté serveur
    if (empty($password)) {
        $error = "Le mot de passe est requis";
    }
    
    // Si pas d'erreur, vérification dans la base de données
    if (empty($error)) {
        try {
            // Connexion à la base de données
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            // Configuration des attributs PDO
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Préparation de la requête SQL
            $stmt = $conn->prepare("SELECT id, user_name, user_email, user_password, first_loging FROM users WHERE user_email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            // Vérification si l'utilisateur existe
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Vérification du mot de passe
                if (password_verify($password, $user['user_password'])) {
                    // Réinitialisation du compteur de tentatives en cas de succès
                    $_SESSION['login_attempts'] = 0;
                    $_SESSION['blocked_until'] = 0;
                    
                    // Stockage des informations de l'utilisateur dans la session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['user_name'];
                    $_SESSION['user_email'] = $user['user_email'];
                    
                    // Vérification si c'est le premier login
                    if ($user['first_loging'] == '0') {
                        // Mise à jour du statut de premier login
                        $updateStmt = $conn->prepare("UPDATE users SET first_loging = '1' WHERE id = :id");
                        $updateStmt->bindParam(':id', $user['id']);
                        $updateStmt->execute();
                        
                        // Redirection vers la page de bienvenue ou page d'accueil
                        header("Location: ./details-events.php");
                        exit();
                    } else {
                        // Redirection vers la page d'accueil
                        header("Location: ../details-events.php");
                        exit();
                    }
                } else {
                    // Incrémentation du compteur de tentatives
                    $_SESSION['login_attempts']++;
                    
                    if ($_SESSION['login_attempts'] >= 3) {
                        // Bloquer pendant 30 secondes
                        $_SESSION['blocked_until'] = time() + 30;
                        $error = "Trop de tentatives échouées. Veuillez attendre 30 secondes avant de réessayer.";
                        $isBlocked = true;
                        $remainingBlockTime = 30;
                    } else {
                        $remainingAttempts = 3 - $_SESSION['login_attempts'];
                        $error = "Mot de passe incorrect. Il vous reste " . $remainingAttempts . " tentative(s).";
                    }
                }
            } else {
                // Incrémentation du compteur de tentatives
                $_SESSION['login_attempts']++;
                
                if ($_SESSION['login_attempts'] >= 3) {
                    // Bloquer pendant 30 secondes
                    $_SESSION['blocked_until'] = time() + 30;
                    $error = "Trop de tentatives échouées. Veuillez attendre 30 secondes avant de réessayer.";
                    $isBlocked = true;
                    $remainingBlockTime = 30;
                } else {
                    $remainingAttempts = 3 - $_SESSION['login_attempts'];
                    $error = "Aucun compte associé à cette adresse email. Il vous reste " . $remainingAttempts . " tentative(s).";
                }
            }
        } catch(PDOException $e) {
            $error = "Erreur de connexion: " . $e->getMessage();
        }
        
        // Fermeture de la connexion
        $conn = null;
    } 
}*/
// Configuration de la base de données
$dbHost = 'localhost';
$dbName = 'adjarra';
$dbUser = 'root';
$dbPass = 'Fadyl111';

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialisation des variables
$error = "";
$success = "";
$isBlocked = false;

// Initialisation du compteur de tentatives
$_SESSION['login_attempts'] = $_SESSION['login_attempts'] ?? 0;
$_SESSION['blocked_until'] = $_SESSION['blocked_until'] ?? 0;

// Vérifie si le blocage est terminé
if ($_SESSION['blocked_until'] > 0 && time() >= $_SESSION['blocked_until']) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['blocked_until'] = 0;
}

// Vérifie si l'utilisateur est actuellement bloqué
if ($_SESSION['blocked_until'] > 0 && time() < $_SESSION['blocked_until']) {
    $isBlocked = true;
    $remainingBlockTime = $_SESSION['blocked_until'] - time();
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST" && !$isBlocked) {
    $email = trim($_POST['user_email'] ?? '');
    $inputPassword = $_POST['user_password'] ?? '';

    // Validation
    if (empty($email)) {
        $error = "L'adresse email est requise.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'adresse email invalide.";
    } elseif (empty($inputPassword)) {
        $error = "Le mot de passe est requis.";
    }

    if (empty($error)) {
        try {
            $conn = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT id, user_name, user_email, user_password, first_loging FROM users WHERE user_email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($inputPassword, $user['user_password'])) {
                    // Connexion réussie
                    $_SESSION['login_attempts'] = 0;
                    $_SESSION['blocked_until'] = 0;

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['user_name'];
                    $_SESSION['user_email'] = $user['user_email'];

                    // Redirection selon le statut de première connexion
                    if ($user['first_loging'] == '0') {
                        $update = $conn->prepare("UPDATE users SET first_loging = '1' WHERE id = :id");
                        $update->bindParam(':id', $user['id']);
                        $update->execute();
                        header("Location: ./details-events.php");
                        exit();
                    } else {
                        header("Location: ../details-events.php");
                        exit();
                    }
                } else {
                    // Mot de passe incorrect
                    $_SESSION['login_attempts']++;

                    if ($_SESSION['login_attempts'] >= 3) {
                        $_SESSION['blocked_until'] = time() + 30;
                        $error = "Trop de tentatives échouées. Réessayez dans 30 secondes.";
                    } else {
                        $remaining = 3 - $_SESSION['login_attempts'];
                        $error = "Mot de passe incorrect. Il vous reste $remaining tentative(s).";
                    }
                }
            } else {
                // Email non trouvé
                $_SESSION['login_attempts']++;

                if ($_SESSION['login_attempts'] >= 3) {
                    $_SESSION['blocked_until'] = time() + 30;
                    $error = "Aucun compte trouvé. Réessayez dans 30 secondes.";
                } else {
                    $remaining = 3 - $_SESSION['login_attempts'];
                    $error = "Aucun compte associé à cette adresse. Il vous reste $remaining tentative(s).";
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur de connexion : " . $e->getMessage();
        }
    }
}

// Affiche l'erreur s’il y en a (à intégrer dans ton HTML)
if (!empty($error)) {
    echo "<p style='color: red;'>$error</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
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
        
        .forgot-password {
            text-align: right;
            margin-bottom: 15px;
        }
        
        .forgot-password a {
            color: #666;
            font-size: 14px;
            text-decoration: none;
        }
        
        .forgot-password a:hover {
            color: #4a90e2;
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
        
        /* Style pour afficher le nombre de tentatives restantes */
        .attempts-counter {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            color: #e74c3c;
            font-weight: bold;
        }
        
        /* Style pour le compte à rebours */
        .countdown {
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Connexion</h1>
            <p>Entrez vos identifiants pour accéder à votre compte</p>
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
        
        <?php if ($isBlocked): ?>
            <div class="countdown">
                Compte temporairement bloqué. Réessayez dans <span id="countdown"><?php echo $remainingBlockTime; ?></span> secondes.
            </div>
            <script>
                // Compte à rebours pour le temps de blocage
                let timeLeft = <?php echo $remainingBlockTime; ?>;
                const countdownElement = document.getElementById('countdown');
                
                const countdownTimer = setInterval(function() {
                    timeLeft--;
                    countdownElement.textContent = timeLeft;
                    
                    if (timeLeft <= 0) {
                        clearInterval(countdownTimer);
                        // Rechargement de la page
                        location.reload();
                    }
                }, 1000);
            </script>
        <?php else: ?>
            <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="user_email">Adresse email</label>
                    <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo isset($_POST['user_email']) ? htmlspecialchars($_POST['user_email']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="user_password">Mot de passe</label>
                    <input type="password" class="form-control" id="user_password" name="user_password" required>
                </div>
                
                <div class="forgot-password">
                    <a href="forgot_password.php">Mot de passe oublié?</a>
                </div>
                
                <button type="submit" class="btn">Se connecter</button>
                
                <?php if ($_SESSION['login_attempts'] > 0): ?>
                    <div class="attempts-counter">
                        Tentatives restantes: <?php echo (3 - $_SESSION['login_attempts']); ?>
                    </div>
                <?php endif; ?>
            </form>
            
            <div class="form-footer">
                Vous n'avez pas de compte? <a href="../users/inscription.php">Créez un compte ici</a>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            let hasError = false;
            
            // Validate email
            const email = document.getElementById('user_email').value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Veuillez entrer une adresse email valide');
                hasError = true;
            }
            
            // Validate password not empty
            const password = document.getElementById('user_password').value;
            if (password.trim() === '') {
                e.preventDefault();
                alert('Veuillez entrer votre mot de passe');
                hasError = true;
            }
        });
    </script>
</body>
</html>