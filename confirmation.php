<?php
// Démarrer la session pour récupérer les informations
session_start();

// Vérifier si l'utilisateur a bien terminé l'enregistrement d'un hôtel
if (!isset($_SESSION['hotel_registered']) || $_SESSION['hotel_registered'] !== true) {
    // Rediriger vers la page d'accueil si l'utilisateur arrive directement sur cette page
    header("Location: index.php");
    exit();
}

// Récupérer les informations de l'hôtel enregistré
$hotel_name = isset($_SESSION['hotel_name']) ? $_SESSION['hotel_name'] : "votre établissement";
$contact_email = isset($_SESSION['contact_email']) ? $_SESSION['contact_email'] : "votre adresse email";

// Effacer les variables de session après utilisation
unset($_SESSION['hotel_registered']);
unset($_SESSION['hotel_name']);
unset($_SESSION['contact_email']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'Enregistrement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .success-icon {
            font-size: 5rem;
            color: #198754;
            margin-bottom: 20px;
        }
        .confirmation-title {
            color: #198754;
            margin-bottom: 20px;
        }
        .next-steps {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }
        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-container">
            <div class="text-center">
                <i class="bi bi-check-circle-fill success-icon"></i>
                <h1 class="confirmation-title">Enregistrement réussi!</h1>
                <p class="lead">Merci d'avoir enregistré "<?php echo htmlspecialchars($hotel_name); ?>" sur notre plateforme de référencement hôtelier.</p>
            </div>
            
            <div class="alert alert-success mt-4">
                <p><strong>Votre demande d'enregistrement a été soumise avec succès.</strong></p>
                <p>Votre dossier est maintenant en attente de validation par l'administrateur.</p>
            </div>
            
            <div class="next-steps">
                <h3>Prochaines étapes :</h3>
                <ol>
                    <li>Notre équipe administrative va examiner votre demande dans un délai de 24 à 48 heures ouvrables.</li>
                    <li>Une fois votre demande approuvée, vous recevrez un email à l'adresse <strong><?php echo htmlspecialchars($contact_email); ?></strong> contenant vos identifiants de connexion.</li>
                    <li>Vous pourrez ensuite vous connecter à votre espace hôtelier pour compléter votre profil et ajouter plus d'informations.</li>
                </ol>
            </div>
            
            <div class="contact-info">
                <h4>Besoin d'aide ?</h4>
                <p>Si vous avez des questions concernant votre enregistrement, n'hésitez pas à contacter notre service d'assistance :</p>
                <p><i class="bi bi-envelope"></i> support@ad.com</p>
                <p><i class="bi bi-telephone"></i> +229 XX XX XX XX</p>
            </div>
            
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-outline-primary">Retour à l'accueil</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>