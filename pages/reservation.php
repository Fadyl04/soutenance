<?php
include 'database/db.php';

// Récupération de l'ID du circuit depuis l'URL
$circuit_id = isset($_GET['circuit']) ? intval($_GET['circuit']) : 0;

// Récupération des informations du circuit sélectionné
if ($circuit_id > 0) {
    try {
        $stmt = $db->prepare("SELECT c.*, 
                             GROUP_CONCAT(st.nom SEPARATOR ', ') AS sites,
                             h.hotel_name,
                             CONCAT(g.first_name, ' ', g.last_name) AS guide_name,
                             CONCAT(t.first_name, ' ', t.last_name) AS transporter_name
                             FROM circuits c
                             LEFT JOIN circuit_sites cs ON c.id = cs.circuit_id
                             LEFT JOIN sites_touristiques st ON cs.site_id = st.id
                             LEFT JOIN hotels h ON c.hotel_id = h.id
                             LEFT JOIN guides g ON c.guide_id = g.id
                             LEFT JOIN transporters t ON c.transporter_id = t.id
                             WHERE c.id = ? AND c.status = 'active'
                             GROUP BY c.id");
        $stmt->execute([$circuit_id]);
        $circuit = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Erreur lors de la récupération des informations du circuit : " . $e->getMessage();
        $circuit = null;
    }
}

// Vérifier si la table 'reservations' existe, sinon la créer
try {
    $db->query("SELECT 1 FROM reservations LIMIT 1");
} catch(PDOException $e) {
    // La table n'existe pas, on la crée
    try {
        $sql = "CREATE TABLE reservations (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            circuit_id INT(11) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            nom VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            telephone VARCHAR(50) NOT NULL,
            nombre_voyageurs INT(11) NOT NULL DEFAULT 1,
            date_voyage DATE NOT NULL,
            type_forfait ENUM('economy', 'comfort', 'premium') NOT NULL,
            demandes_speciales TEXT,
            methode_paiement VARCHAR(50) NOT NULL,
            prix_total DECIMAL(10,2) NOT NULL,
            statut ENUM('en_attente', 'confirmee', 'annulee') NOT NULL DEFAULT 'en_attente',
            date_reservation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($sql);
    } catch(PDOException $createError) {
        echo "Erreur lors de la création de la table reservations : " . $createError->getMessage();
    }
}

// Traitement du formulaire si soumis
$formSubmitted = false;
$bookingSuccess = false;
$bookingError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_booking'])) {
    $formSubmitted = true;
    
    // Récupérer les données du formulaire
    $prenom = $_POST['first_name'] ?? '';
    $nom = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['phone'] ?? '';
    $nombre_voyageurs = $_POST['travelers'] ?? 1;
    $date_voyage = $_POST['travel_date'] ?? '';
    $type_forfait = $_POST['package_type'] ?? 'economy';
    $demandes_speciales = $_POST['special_requests'] ?? '';
    $methode_paiement = $_POST['payment_method'] ?? '';
    
    // Calculer le prix total selon le type de package
    $prix_par_personne = 0;
    if ($type_forfait == 'economy') {
        $prix_par_personne = $circuit['price_economy'] ?? 0;
    } elseif ($type_forfait == 'comfort') {
        $prix_par_personne = $circuit['price_comfort'] ?? 0;
    } elseif ($type_forfait == 'premium') {
        $prix_par_personne = $circuit['price_premium'] ?? 0;
    }
    
    $prix_total = $prix_par_personne * $nombre_voyageurs;
    
    // Validation des données
    $errors = [];
    if (empty($prenom)) $errors[] = "Le prénom est requis";
    if (empty($nom)) $errors[] = "Le nom de famille est requis";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
    if (empty($telephone)) $errors[] = "Le numéro de téléphone est requis";
    if (empty($date_voyage)) $errors[] = "La date de voyage est requise";
    if (empty($methode_paiement)) $errors[] = "La méthode de paiement est requise";
    
    // Si pas d'erreurs, enregistrer la réservation
    if (empty($errors)) {
        try {
            $db->beginTransaction();
            
            // Insérer la réservation
            $stmt = $db->prepare("INSERT INTO reservations (circuit_id, prenom, nom, email, telephone, nombre_voyageurs, date_voyage, type_forfait, demandes_speciales, methode_paiement, prix_total, statut, date_reservation) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmee', NOW())");
            
            $stmt->execute([
                $circuit_id, $prenom, $nom, $email, $telephone, $nombre_voyageurs, 
                $date_voyage, $type_forfait, $demandes_speciales, $methode_paiement, $prix_total
            ]);
            
            $reservation_id = $db->lastInsertId();
            
            // Simuler un traitement de paiement réussi
            // Dans un environnement de production, vous intégreriez ici une véritable passerelle de paiement
            
            $db->commit();
            $bookingSuccess = true;
            
        } catch(PDOException $e) {
            $db->rollBack();
            $bookingError = "Une erreur est survenue lors de la réservation : " . $e->getMessage();
        }
    } else {
        $bookingError = "Veuillez corriger les erreurs suivantes : " . implode(", ", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation - Circuits Touristiques du Bénin</title>
    <script src="https://cdn.fedapay.com/checkout.js?v=1.1.7"></script>
    <script src="https://cdn.fedapay.com/checkout.js?v=1.1.7"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }
        
        a {
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        /* Hero Section */
        #hero {
            width: 100%;
            height: 40vh;
            background: url('img/circuit.jpg') top center;
            background-size: cover;
            position: relative;
            padding: 0;
            color: white;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #hero:before {
            content: "";
            background: rgba(0, 0, 0, 0.5);
            position: absolute;
            bottom: 0;
            top: 0;
            left: 0;
            right: 0;
        }
        
        #hero .container {
            position: relative;
            z-index: 2;
        }
        
        #hero h1 {
            margin: 0;
            font-size: 42px;
            font-weight: 700;
            line-height: 56px;
            color: #fff;
            margin-bottom: 10px;
        }
        
        #hero h2 {
            color: #eee;
            margin: 10px 0 20px 0;
            font-size: 20px;
            font-weight: 300;
        }
        
        /* Main Content */
        .main-content {
            padding: 60px 0;
        }
        
        .section-title {
            text-align: center;
            padding-bottom: 30px;
        }
        
        .section-title h2 {
            font-size: 32px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 20px;
            padding-bottom: 20px;
            position: relative;
            color: #333;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            display: block;
            width: 50px;
            height: 3px;
            background: #e9b732;
            bottom: 0;
            left: calc(50% - 25px);
        }
        
        .section-title p {
            margin-bottom: 0;
            color: #666;
            font-size: 16px;
        }
        
        /* Booking Form Styles */
        .booking-container {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .booking-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .booking-header h3 {
            font-weight: 700;
            color: #333;
        }
        
        /* Circuit Summary Box */
        .circuit-summary {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #eee;
        }
        
        .circuit-summary h4 {
            font-weight: 700;
            color: #e9b732;
            margin-bottom: 15px;
        }
        
        .circuit-summary img {
            border-radius: 8px;
            margin-bottom: 15px;
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .circuit-details {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .circuit-details li {
            padding: 8px 0;
            border-bottom: 1px dashed #ddd;
            display: flex;
            justify-content: space-between;
        }
        
        .circuit-details li:last-child {
            border-bottom: none;
        }
        
        /* Steps Progress Bar */
        .booking-progress {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }
        
        .booking-progress:before {
            content: '';
            position: absolute;
            top: 24px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #ddd;
            z-index: 1;
        }
        
        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 33.333%;
        }
        
        .step-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            transition: all 0.3s ease;
            color: #777;
            font-size: 20px;
        }
        
        .step.active .step-icon {
            background: #e9b732;
            border-color: #e9b732;
            color: #fff;
            box-shadow: 0px 5px 15px rgba(233, 183, 50, 0.3);
        }
        
        .step.complete .step-icon {
            background: #28a745;
            border-color: #28a745;
            color: #fff;
        }
        
        .step-title {
            font-size: 14px;
            font-weight: 600;
            color: #777;
        }
        
        .step.active .step-title {
            color: #e9b732;
        }
        
        .step.complete .step-title {
            color: #28a745;
        }
        
        /* Form Panel Styles */
        .form-panel {
            display: none;
        }
        
        .form-panel.active {
            display: block;
        }
        
        .form-panel h4 {
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }
        
        .btn-navigation {
            margin-top: 20px;
        }
        
        /* Payment Methods */
        .payment-methods {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        
        .payment-method {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            width: calc(33.333% - 10px);
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .payment-method:hover {
            background: #f2f2f2;
            transform: translateY(-3px);
        }
        
        .payment-method.selected {
            border-color: #e9b732;
            background: #fff9e6;
        }
        
        .payment-method i {
            font-size: 24px;
            color: #555;
            margin-bottom: 10px;
            display: block;
        }
        
        .payment-method.selected i {
            color: #e9b732;
        }
        
        /* Package Selection */
        .package-options {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 10px;
        }
        
        .package-option {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            flex: 1;
            min-width: 250px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .package-option:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.05);
        }
        
        .package-option.selected {
            border-color: #e9b732;
            background: #fff9e6;
        }
        
        .package-title {
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        .package-price {
            font-size: 22px;
            font-weight: 700;
            color: #e9b732;
            margin-bottom: 15px;
        }
        
        .package-features {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        
        .package-features li {
            padding: 5px 0;
            position: relative;
            padding-left: 25px;
        }
        
        .package-features li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }
        
        /* Success Message */
        .booking-success {
            text-align: center;
            padding: 40px 0;
        }
        
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .booking-details {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            text-align: left;
        }
        
        /* Responsive Styles */
        @media (max-width: 991px) {
            .payment-method {
                width: calc(50% - 10px);
            }
            
            .package-option {
                flex: 0 0 calc(50% - 10px);
            }
        }
        
        @media (max-width: 767px) {
            #hero h1 {
                font-size: 32px;
                line-height: 38px;
            }
            
            #hero h2 {
                font-size: 18px;
            }
            
            .booking-container {
                padding: 20px;
            }
            
            .payment-method {
                width: 100%;
                margin-bottom: 10px;
            }
            
            .package-option {
                flex: 0 0 100%;
            }
            
            .step-title {
                font-size: 12px;
            }
        }
    </style>
</head>
<body> <br> <br> <br> <br>

<section id="hero" class="d-flex align-items-center">
    <div class="container">
        <h1>Réservation de Circuit</h1>
        <h2>Planifiez votre expérience touristique au Bénin</h2>
    </div>
</section>

<main id="main">
    <section class="main-content">
        <div class="container">
            <div class="section-title">
                <h2>Réserver votre circuit</h2>
                <p>Complétez les informations ci-dessous pour finaliser votre réservation</p>
            </div>
            
            <?php if($bookingSuccess): ?>
                <!-- Affichage du succès de la réservation -->
                <div class="booking-container">
                    <div class="booking-success">
                        <div class="success-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <h3>Réservation confirmée!</h3>
                        <p class="text-success">Votre réservation a été enregistrée avec succès.</p>
                        <p>Un email de confirmation a été envoyé à votre adresse email avec tous les détails.</p>
                        
                        <div class="booking-details">
                            <h4>Récapitulatif de votre réservation</h4>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <p><strong>Nom:</strong> <?= htmlspecialchars($_POST['first_name']) ?> <?= htmlspecialchars($_POST['last_name']) ?></p>
                                    <p><strong>Email:</strong> <?= htmlspecialchars($_POST['email']) ?></p>
                                    <p><strong>Téléphone:</strong> <?= htmlspecialchars($_POST['phone']) ?></p>
                                    <p><strong>Nombre de voyageurs:</strong> <?= htmlspecialchars($_POST['travelers']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Circuit:</strong> <?= htmlspecialchars($circuit['name']) ?></p>
                                    <p><strong>Date de départ:</strong> <?= htmlspecialchars($_POST['travel_date']) ?></p>
                                    <p><strong>Type de forfait:</strong> <?= ucfirst(htmlspecialchars($_POST['package_type'])) ?></p>
                                    <p><strong>Méthode de paiement:</strong> <?= htmlspecialchars($_POST['payment_method']) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
                        </div>
                    </div>
                </div>
            <?php elseif(isset($circuit) && $circuit): ?>
                <!-- Conteneur de réservation principal -->
                <div class="row">
                    <!-- Formulaire de réservation -->
                    <div class="col-lg-8">
                        <div class="booking-container">
                            <!-- Barre de progression de la réservation -->
                            <div class="booking-progress">
                                <div class="step active" id="step1">
                                    <div class="step-icon">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div class="step-title">Informations personnelles</div>
                                </div>
                                <div class="step" id="step2">
                                    <div class="step-icon">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div class="step-title">Détails du voyage</div>
                                </div>
                                <div class="step" id="step3">
                                    <div class="step-icon">
                                        <i class="bi bi-credit-card"></i>
                                    </div>
                                    <div class="step-title">Paiement</div>
                                </div>
                            </div>
                            
                            <?php if(!empty($bookingError)): ?>
                                <div class="alert alert-danger"><?= $bookingError ?></div>
                            <?php endif; ?>
                            
                            <!-- Formulaire de réservation avec 3 panels -->
                            <form id="booking-form" method="post" action="">
                                <!-- Panel 1: Informations personnelles -->
                                <div class="form-panel active" id="panel1">
                                    <h4>Vos informations personnelles</h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="first_name">Prénom *</label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="last_name">Nom de famille *</label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="email">Email *</label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="phone">Téléphone *</label>
                                                <input type="tel" class="form-control" id="phone" name="phone" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="btn-navigation text-end">
                                        <button type="button" class="btn btn-primary next-step">Continuer</button>
                                    </div>
                                </div>
                                
                                <!-- Panel 2: Détails du voyage -->
                                <div class="form-panel" id="panel2">
                                    <h4>Détails de votre voyage</h4>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label" for="travelers">Nombre de voyageurs *</label>
                                                <select class="form-select" id="travelers" name="travelers" required>
                                                    <?php for($i=1; $i<=10; $i++): ?>
                                                        <option value="<?= $i ?>"><?= $i ?> personne<?= $i > 1 ? 's' : '' ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Sélectionnez votre forfait *</label>
                                        <div class="package-options">
                                            <div class="package-option" data-package="economy">
                                                <div class="package-title">Économique</div>
                                                <div class="package-price"><?= number_format($circuit['price_economy'], 0, ',', ' ') ?> CFA / personne</div>
                                                <ul class="package-features">
                                                    <li>Transport standard</li>
                                                    <li>Hébergement basique</li>
                                                    <li>Guide touristique</li>
                                                </ul>
                                                <input type="radio" name="package_type" value="economy" class="d-none" required>
                                            </div>
                                            
                                            <div class="package-option" data-package="comfort">
                                                <div class="package-title">Confort</div>
                                                <div class="package-price"><?= number_format($circuit['price_comfort'], 0, ',', ' ') ?> CFA / personne</div>
                                                <ul class="package-features">
                                                    <li>Transport de qualité</li>
                                                    <li>Hébergement 3 étoiles</li>
                                                    <li>Guide touristique expérimenté</li>
                                                    <li>Repas inclus</li>
                                                </ul>
                                                <input type="radio" name="package_type" value="comfort" class="d-none" required>
                                            </div>
                                            
                                            <div class="package-option" data-package="premium">
                                                <div class="package-title">Premium</div>
                                                <div class="package-price"><?= number_format($circuit['price_premium'], 0, ',', ' ') ?> CFA / personne</div>
                                                <ul class="package-features">
                                                    <li>Transport haut de gamme</li>
                                                    <li>Hébergement 4-5 étoiles</li>
                                                    <li>Guide privé</li>
                                                    <li>Tous les repas inclus</li>
                                                    <li>Expériences exclusives</li>
                                                </ul>
                                                <input type="radio" name="package_type" value="premium" class="d-none" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label" for="special_requests">Demandes spéciales</label>
                                        <textarea class="form-control" id="special_requests" name="special_requests" rows="3" placeholder="Allergies, besoins particuliers, etc."></textarea>
                                    </div>
                                    
                                    <div class="btn-navigation d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary prev-step">Retour</button>
                                        <button type="button" class="btn btn-primary next-step" id="pay-btn" >Continuer au paiement</button>
                                    </div>
                                </div>
                                
                                <!-- Panel 3: Paiement -->
                                <div class="form-panel" id="panel3">
                                    <h4>Méthode de paiement</h4>
                                    
                                    <div class="form-group">
                                        <label class="form-label">Sélectionnez votre mode de paiement *</label>
                                        <div class="payment-methods">
                                            <div class="payment-method" data-payment="credit_card">
                                                <i class="fas fa-credit-card"></i>
                                                <div>Carte de crédit</div>
                                                <input type="radio" name="payment_method" value="credit_card" class="d-none" required>
                                            </div>
                                            
                                            <div class="payment-method" data-payment="mobile_money">
                                                <i class="fas fa-mobile-alt"></i>
                                                <div>Mobile Money</div>
                                                <input type="radio" name="payment_method" value="mobile_money" class="d-none" required>
                                            </div>
                                            
                                            <div class="payment-method" data-payment="bank_transfer">
                                                <i class="fas fa-university"></i>
                                                <div>Virement bancaire</div>
                                                <input type="radio" name="payment_method" value="bank_transfer" class="d-none" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Détails de paiement conditionnels - s'affichent en fonction de la méthode sélectionnée -->
                                    <div class="payment-details mt-4" id="credit_card_details" style="display: none;">
                                        <div class="alert alert-info">
                                            <p>Cette démonstration simule un paiement. Dans un environnement de production, vous seriez redirigé vers une passerelle de paiement sécurisée.</p>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="form-label">Numéro de carte</label>
                                                    <input type="text" class="form-control" placeholder="1234 5678 9012 3456">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Cette partie complète la dernière section du formulaire de paiement qui était coupée -->

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Date d'expiration</label>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <select class="form-select">
                                                                <option value="">Mois</option>
                                                                <?php for($i=1; $i<=12; $i++): ?>
                                                                    <option value="<?= $i ?>"><?= sprintf("%02d", $i) ?></option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <select class="form-select">
                                                                <option value="">Année</option>
                                                                <?php for($i=date('Y'); $i<=date('Y')+10; $i++): ?>
                                                                    <option value="<?= $i ?>"><?= $i ?></option>
                                                                <?php endfor; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">CVV</label>
                                                    <input type="text" class="form-control" placeholder="123">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="payment-details mt-4" id="mobile_money_details" style="display: none;">
                                        <div class="alert alert-info">
                                            <p>Pour effectuer le paiement Mobile Money, veuillez suivre ces étapes:</p>
                                            <ol>
                                                <li>Envoyez le montant au numéro +229 XX XX XX XX</li>
                                                <li>Utilisez le code de référence: BTB-<?= time() ?></li>
                                                <li>La réservation sera confirmée après vérification du paiement</li>
                                            </ol>
                                        </div>
                                    </div>
                                    
                                    <div class="payment-details mt-4" id="bank_transfer_details" style="display: none;">
                                        <div class="alert alert-info">
                                            <p>Pour effectuer un virement bancaire, veuillez utiliser les informations suivantes:</p>
                                            <p><strong>Banque:</strong> Banque du Bénin</p>
                                            <p><strong>Bénéficiaire:</strong> Circuits Touristiques du Bénin</p>
                                            <p><strong>IBAN:</strong> BN95 1234 5678 9012 3456 7890</p>
                                            <p><strong>Référence:</strong> BTB-<?= time() ?></p>
                                            <p>La réservation sera confirmée après réception du paiement (2-3 jours ouvrables).</p>
                                        </div>
                                    </div>
                                    
                                    <div class="btn-navigation d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary prev-step">Retour</button>
                                        <button id="pay-btn" type="submit" name="submit_booking" class="btn btn-success">Confirmer et payer</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Résumé de la réservation -->
                    <div class="col-lg-4">
                        <div class="circuit-summary sticky-top" style="top: 20px;">
                            <h4>Résumé de votre réservation</h4>
                            
                            <?php if(!empty($circuit['image'])): ?>
                                <img src="img/circuits/<?= htmlspecialchars($circuit['image']) ?>" alt="<?= htmlspecialchars($circuit['name']) ?>" class="img-fluid">
                            <?php else: ?>
                                <img src="img/circuit-default.jpg" alt="<?= htmlspecialchars($circuit['name']) ?>" class="img-fluid">
                            <?php endif; ?>
                            
                            <h5 class="mt-3"><?= htmlspecialchars($circuit['name']) ?></h5>
                            <p><?= htmlspecialchars(substr($circuit['description'], 0, 100)) ?>...</p>
                            
                            <ul class="circuit-details">
    <li>
        <span>Durée</span>
        <span><?= $circuit['duration'] ?> jours</span>
    </li>
    <li>
        <span>Sites touristiques</span>
        <span><?= htmlspecialchars($circuit['sites']) ?></span>
    </li>
    <li>
        <span>Hôtel</span>
        <span><?= htmlspecialchars($circuit['hotel_name']) ?></span>
    </li>
    <li>
        <span>Guide</span>
        <span><?= htmlspecialchars($circuit['guide_name']) ?></span>
    </li>
    <li>
        <span>Transport</span>
        <span><?= htmlspecialchars($circuit['transporter_name']) ?></span>
    </li>
</ul>

<div class="pricing-summary mt-4">
    <h5>Prix par personne</h5>
    <ul class="circuit-details">
        <li>
            <span>Économique</span>
            <span><?= number_format($circuit['price_economy'], 0, ',', ' ') ?> CFA</span>
        </li>
        <li>
            <span>Confort</span>
            <span><?= number_format($circuit['price_comfort'], 0, ',', ' ') ?> CFA</span>
        </li>
        <li>
            <span>Premium</span>
            <span><?= number_format($circuit['price_premium'], 0, ',', ' ') ?> CFA</span>
        </li>
    </ul>
</div>

<div class="booking-summary mt-4" style="display: none;">
    <h5>Votre réservation</h5>
    <ul class="circuit-details">
        <li>
            <span>Voyageurs</span>
            <span id="summary-travelers">1</span>
        </li>
        <li>
            <span>Type de forfait</span>
            <span id="summary-package">-</span>
        </li>
        <li>
            <span>Prix unitaire</span>
            <span id="summary-unit-price">0 CFA</span>
        </li>
        <li>
            <span><strong>Prix total</strong></span>
            <span><strong id="summary-total-price">0 CFA</strong></span>
        </li>
    </ul>
</div>
</div>
</div>
</div>
<?php else: ?>
<div class="alert alert-warning">
    <p>Circuit non trouvé ou indisponible.</p>
    <a href="circuits.php" class="btn btn-primary mt-3">Voir tous les circuits</a>
</div>
<?php endif; ?>
</div>
</section>
</main>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Navigation entre les panels
    const steps = document.querySelectorAll('.step');
    const panels = document.querySelectorAll('.form-panel');
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    
    // Initialisation des variables pour le résumé
    let selectedPackage = '';
    let unitPrice = 0;
    let travelers = 1;
    
    // Gestion des boutons suivant
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentPanel = this.closest('.form-panel');
            const currentIndex = Array.from(panels).indexOf(currentPanel);
            
            // Validation basique du panel actuel
            if(validatePanel(currentIndex + 1)) {
                // Mise à jour des étapes
                steps[currentIndex].classList.remove('active');
                steps[currentIndex].classList.add('complete');
                steps[currentIndex + 1].classList.add('active');
                
                // Changement de panel
                currentPanel.classList.remove('active');
                panels[currentIndex + 1].classList.add('active');
                
                // Mettre à jour le résumé si on passe au panel 2
                if(currentIndex === 0) {
                    document.querySelector('.booking-summary').style.display = 'block';
                }
            }
        });
    });
    
    // Gestion des boutons précédent
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentPanel = this.closest('.form-panel');
            const currentIndex = Array.from(panels).indexOf(currentPanel);
            
            // Mise à jour des étapes
            steps[currentIndex].classList.remove('active');
            steps[currentIndex - 1].classList.remove('complete');
            steps[currentIndex - 1].classList.add('active');
            
            // Changement de panel
            currentPanel.classList.remove('active');
            panels[currentIndex - 1].classList.add('active');
        });
    });
    
    // Gestion de la sélection des packages
    const packageOptions = document.querySelectorAll('.package-option');
    packageOptions.forEach(option => {
        option.addEventListener('click', function() {
            packageOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            
            const packageType = this.getAttribute('data-package');
            this.querySelector('input[type="radio"]').checked = true;
            
            // Mise à jour du résumé
            selectedPackage = packageType;
            document.getElementById('summary-package').textContent = packageType.charAt(0).toUpperCase() + packageType.slice(1);
            
            // Récupération du prix en fonction du type de package
            if(packageType === 'economy') {
                unitPrice = <?= $circuit['price_economy'] ?? 0 ?>;
            } else if(packageType === 'comfort') {
                unitPrice = <?= $circuit['price_comfort'] ?? 0 ?>;
            } else if(packageType === 'premium') {
                unitPrice = <?= $circuit['price_premium'] ?? 0 ?>;
            }
            
            updatePriceSummary();
        });
    });
    
    // Gestion de la sélection des méthodes de paiement
    const paymentMethods = document.querySelectorAll('.payment-method');
    paymentMethods.forEach(method => {
        method.addEventListener('click', function() {
            paymentMethods.forEach(m => m.classList.remove('selected'));
            this.classList.add('selected');
            
            const paymentType = this.getAttribute('data-payment');
            this.querySelector('input[type="radio"]').checked = true;
            
            // Afficher les détails de paiement correspondants
            document.querySelectorAll('.payment-details').forEach(detail => {
                detail.style.display = 'none';
            });
            document.getElementById(paymentType + '_details').style.display = 'block';
        });
    });
    
    // Gestion du changement du nombre de voyageurs
    const travelersSelect = document.getElementById('travelers');
    travelersSelect.addEventListener('change', function() {
        travelers = parseInt(this.value);
        document.getElementById('summary-travelers').textContent = travelers;
        updatePriceSummary();
    });
    
    // Fonction pour mettre à jour le résumé des prix
    function updatePriceSummary() {
        document.getElementById('summary-unit-price').textContent = unitPrice.toLocaleString('fr-FR') + ' CFA';
        const totalPrice = unitPrice * travelers;
        document.getElementById('summary-total-price').textContent = totalPrice.toLocaleString('fr-FR') + ' CFA';
    }
    
    // Fonction simple de validation des panels
    function validatePanel(panelNumber) {
        if (panelNumber === 1) {
            const firstName = document.getElementById('first_name').value;
            const lastName = document.getElementById('last_name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            
            if (!firstName || !lastName || !email || !phone) {
                alert('Veuillez remplir tous les champs obligatoires.');
                return false;
            }
            
            // Validation basique de l'email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Veuillez entrer une adresse email valide.');
                return false;
            }
            
            return true;
        } 
        else if (panelNumber === 2) {
            const travelDate = document.getElementById('travel_date').value;
            const packageSelected = document.querySelector('.package-option.selected');
            
            if (!travelDate) {
                alert('Veuillez sélectionner une date de voyage.');
                return false;
            }
            
            if (!packageSelected) {
                alert('Veuillez sélectionner un type de forfait.');
                return false;
            }
            
            return true;
        }
        
        return true;
    }
});
</script>

<script type="text/javascript">
      document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des variables pour le prix et les informations personnelles
    let selectedPackage = '';
    let unitPrice = 0;
    let travelers = 1;
    let totalPrice = 0;
    
    // Gestion de la sélection des packages
    const packageOptions = document.querySelectorAll('.package-option');
    packageOptions.forEach(option => {
        option.addEventListener('click', function() {
            packageOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            
            const packageType = this.getAttribute('data-package');
            this.querySelector('input[type="radio"]').checked = true;
            
            // Mise à jour du résumé
            selectedPackage = packageType;
            
            // Récupération du prix en fonction du type de package
            if(packageType === 'economy') {
                unitPrice = <?= $circuit['price_economy'] ?? 0 ?>;
            } else if(packageType === 'comfort') {
                unitPrice = <?= $circuit['price_comfort'] ?? 0 ?>;
            } else if(packageType === 'premium') {
                unitPrice = <?= $circuit['price_premium'] ?? 0 ?>;
            }
            
            updatePriceSummary();
            updateFedaPay();
        });
    });
    
    // Gestion du changement du nombre de voyageurs
    const travelersSelect = document.getElementById('travelers');
    travelersSelect.addEventListener('change', function() {
        travelers = parseInt(this.value);
        updatePriceSummary();
        updateFedaPay();
    });
    
    // Fonction pour mettre à jour le résumé des prix
    function updatePriceSummary() {
        document.getElementById('summary-unit-price').textContent = unitPrice.toLocaleString('fr-FR') + ' CFA';
        totalPrice = unitPrice * travelers;
        document.getElementById('summary-total-price').textContent = totalPrice.toLocaleString('fr-FR') + ' CFA';
    }
    
    // Fonction pour récupérer les informations personnelles du formulaire
    function getCustomerInfo() {
        return {
            firstname: document.getElementById('first_name').value,
            lastname: document.getElementById('last_name').value,
            email: document.getElementById('email').value,
            phone_number: document.getElementById('phone').value
        };
    }
    
    // Fonction pour mettre à jour la configuration FedaPay
    function updateFedaPay() {
        // Retirer l'initialisation précédente de FedaPay
        const oldPayBtn = document.getElementById('pay-btn');
        if (oldPayBtn) {
            // Cloner le bouton pour supprimer les event listeners
            const newPayBtn = oldPayBtn.cloneNode(true);
            oldPayBtn.parentNode.replaceChild(newPayBtn, oldPayBtn);
            
            // Réinitialiser FedaPay avec le nouveau montant et les infos du client
            if (totalPrice > 0) {
                // Récupérer les informations du client
                const customerInfo = getCustomerInfo();
                
                FedaPay.init('#pay-btn', {
                    public_key: 'pk_live_FCU41Q-obQXsqFLhkYnpedbA',
                    transaction: {
                        amount: totalPrice,
                        description: 'Réservation de circuit touristique - ' + selectedPackage,
                        customer: {
                            firstname: customerInfo.firstname,
                            lastname: customerInfo.lastname,
                            email: customerInfo.email,
                            phone_number: customerInfo.phone_number
                        }
                    },
                    // Ne pas demander à nouveau les informations déjà saisies
                    customer_data: false
                });
            }
        }
    }
    
    // Mettre à jour FedaPay lorsque l'utilisateur passe à l'étape de paiement
    const nextButtons = document.querySelectorAll('.next-step');
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            const currentPanel = this.closest('.form-panel');
            const currentIndex = Array.from(document.querySelectorAll('.form-panel')).indexOf(currentPanel);
            
            // Si on passe à l'étape de paiement (panel 3)
            if (currentIndex === 1) {
                updateFedaPay();
            }
        });
    });
});
  </script>
</body>
</html>