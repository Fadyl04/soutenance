<?php
session_start();

// Initialiser les variables
$error = "";
$username = ""; 
$type = "hotel"; // Valeur par défaut

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $type = trim($_POST['type']);
    
    // Validation de base
    if (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        try {
            // Connexion à la base de données
            $host = "localhost";
            $dbname = "adjarra";
            $db_username = "root";
            $password_db = "";
            
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $db_username, $password_db);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Table à utiliser en fonction du type d'utilisateur
            $table = "";
            $id_field = "";
            $dash_redirect = "";
            
            switch ($type) {
                case 'hotel':
                    $table = "hotels";
                    $id_field = "id";
                    $dash_redirect = "Hotels/index.php";
                    break;
                case 'guide':
                    $table = "guides";
                    $id_field = "id";
                    $dash_redirect = "Guides/index.php";
                    break;
                case 'transport':
                    $table = "transporters";
                    $id_field = "id";
                    $dash_redirect = "transport-dashboard.php";
                    break;
                default:
                    $error = "Type d'utilisateur non valide.";
                    break;
            }
            
            if (empty($error)) {
                // Vérifier les informations d'identification
                $sql = "SELECT * FROM $table WHERE username = :username AND status != 'pending'";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                
                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Vérifier le mot de passe
                    if (password_verify($password, $user['password'])) {
                        // Créer les variables de session
                        $_SESSION['user_id'] = $user[$id_field];
                        $_SESSION['user_type'] = $type;
                        $_SESSION['username'] = $user['username'];
                        
                        // Récupérer le nom selon le type d'utilisateur
                        if ($type === 'hotel') {
                            $_SESSION['user_name'] = $user['hotel_name'];
                        } elseif ($type === 'guide') {
                            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                        } else {
                            $_SESSION['user_name'] = $user['company_name'];
                        }
                        
                        $_SESSION['status'] = $user['status'];
                        
                        // Définir les variables de session 'prestataire' et 'prestataire_id' pour tous les types
                        $_SESSION['prestataire'] = true;
                        $_SESSION['prestataire_id'] = $user[$id_field];
                        
                        // Rediriger vers le dashboard approprié
                        header("Location: $dash_redirect");
                        exit();
                    } else {
                        $error = "Mot de passe incorrect.";
                    }
                } else {
                    $error = "Aucun compte trouvé avec ce nom d'utilisateur ou votre compte a été rejeté.";
                }
            }
            
        } catch (PDOException $e) {
            $error = "Erreur de connexion : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Réinitialisation globale */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden; /* Empêche le défilement horizontal */
            width: 100%;
            max-width: 100%;
        }

        /* Styles spécifiques pour le formulaire de connexion */
        .login-section {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            width: 100%;
            max-width: 100%;
        }

        .login-container {
            width: 100%;
            max-width: 500px;
            padding: 40px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .login-header p {
            color: #666;
        }

        .login-form .form-group {
            margin-bottom: 25px;
        }

        .login-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        .login-form .form-control {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .login-form .form-control:focus {
            border-color: #e9b732;
            box-shadow: 0 0 0 3px rgba(233, 183, 50, 0.2);
            outline: none;
        }

        .user-type-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
            width: 100%;
        }

        .user-type-option {
            flex: 1;
            min-width: 120px;
        }

        .user-type-label {
            position: relative;
            display: block;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-type-label:hover {
            border-color: #e9b732;
        }

        .user-type-radio {
            position: absolute;
            opacity: 0;
        }

        .user-type-radio:checked + .user-type-label {
            border-color: #e9b732;
            background-color: rgba(233, 183, 50, 0.1);
        }

        .user-type-icon {
            display: block;
            font-size: 24px;
            margin-bottom: 8px;
            color: #666;
        }

        .user-type-radio:checked + .user-type-label .user-type-icon {
            color: #e9b732;
        }

        .user-type-text {
            font-weight: 600;
            color: #444;
        }

        .login-button {
            width: 100%;
            padding: 15px;
            background: #e9b732;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-button:hover {
            background: #d4a026;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(233, 183, 50, 0.3);
        }

        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
            text-align: center;
        }

        .other-links {
            margin-top: 25px;
            text-align: center;
        }

        .other-links a {
            color: #e9b732;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .other-links a:hover {
            color: #d4a026;
            text-decoration: underline;
        }

        .divider {
            margin: 0 10px;
            color: #ddd;
        }

        /* Responsive adjustments */
        @media (max-width: 576px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .user-type-selector {
                flex-direction: column;
            }
            
            .user-type-option {
                width: 100%;
            }
        }
    </style>
</head> <br> <br> <br> <br>
<body>
    <!-- Section de connexion -->
    <section class="login-section">
        <div class="login-container">
            <div class="login-header">
                <h2>Connexion</h2>
                <p>Accédez à votre tableau de bord professionnel</p>
            </div>
            
            <?php if(!empty($error)): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="user-type-selector">
                    <div class="user-type-option">
                        <input type="radio" name="type" id="type-hotel" value="hotel" class="user-type-radio" <?php echo $type === 'hotel' ? 'checked' : ''; ?>>
                        <label for="type-hotel" class="user-type-label">
                            <i class="fas fa-hotel user-type-icon"></i>
                            <span class="user-type-text">Hôtel</span>
                        </label>
                    </div>
                    <div class="user-type-option">
                        <input type="radio" name="type" id="type-guide" value="guide" class="user-type-radio" <?php echo $type === 'guide' ? 'checked' : ''; ?>>
                        <label for="type-guide" class="user-type-label">
                            <i class="fas fa-user-tie user-type-icon"></i>
                            <span class="user-type-text">Guides</span>
                        </label>
                    </div>
                    <div class="user-type-option">
                        <input type="radio" name="type" id="type-transport" value="transport" class="user-type-radio" <?php echo $type === 'transport' ? 'checked' : ''; ?>>
                        <label for="type-transport" class="user-type-label">
                            <i class="fas fa-bus user-type-icon"></i>
                            <span class="user-type-text">Transport</span>
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="login-button">Se connecter</button>
                
                <div class="other-links">
                    <a href="#">Mot de passe oublié?</a>
                    <span class="divider">|</span>
                    <a href="index.php?page=partenaire">Créer un compte</a>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation des boutons radio
            const radioButtons = document.querySelectorAll('.user-type-radio');
            
            radioButtons.forEach(button => {
                button.addEventListener('change', function() {
                    // Réinitialiser tous les styles
                    radioButtons.forEach(btn => {
                        const label = btn.nextElementSibling;
                        const icon = label.querySelector('.user-type-icon');
                    });
                });
            });
        });
    </script>
</body>
</html>