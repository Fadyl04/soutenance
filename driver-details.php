<?php
// Établir la connexion à la base de données
try {
    $host = "localhost";
    $dbname = "adjarra";
    $username = "root";
    $password = "Fadyl111";
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si l'ID est fourni
    if(empty($_GET['id'])) {
        header("Location: transport.php");
        exit;
    }
    
    $id = intval($_GET['id']);
    
    // Récupérer les détails du transporteur
    $stmt = $conn->prepare("SELECT * FROM transporters WHERE id = :id AND status = 'approved'");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $driver = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$driver) {
        $notFoundError = "Ce transporteur n'existe pas ou n'a pas été approuvé.";
    }
    
    // Récupérer les commentaires/évaluations (si une table reviews existe)
    $reviews = [];
    try {
        $reviewStmt = $conn->prepare("SELECT * FROM reviews WHERE transporter_id = :id ORDER BY created_at DESC LIMIT 10");
        $reviewStmt->bindParam(':id', $id);
        $reviewStmt->execute();
        $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        // Si la table n'existe pas, on ignore simplement cette partie
    }
    
} catch(PDOException $e) {
    $errorMsg = "Erreur de connexion à la base de données : " . $e->getMessage();
}

// Fonction utilitaire pour accéder aux clés de façon sécurisée
function safeGet($array, $key, $default = '') {
    return isset($array[$key]) ? $array[$key] : $default;
}

// Fonction pour convertir texte en tableau (pour services, langues, etc.)
function textToArray($text) {
    if (empty($text)) return [];
    
    // Si c'est déjà au format JSON
    if (substr($text, 0, 1) === '[' || substr($text, 0, 1) === '{') {
        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
    }
    
    // Sinon, on suppose une liste séparée par des virgules
    return array_map('trim', explode(',', $text));
}



// Fonction pour générer un ID de réservation unique
function generateBookingId() {
    return 'BK-' . strtoupper(substr(md5(uniqid()), 0, 8));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo !empty($driver) ? htmlspecialchars(safeGet($driver, 'first_name') . ' ' . safeGet($driver, 'last_name')) : 'Détails du transporteur'; ?> - Adjarra</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Raleway", sans-serif;
        }

        body {
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }

        .container {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
            max-width: 1140px;
        }

        /* Header */
        header {
            background-color: #fff;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
        }

        .header-logo {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-logo h1 {
            font-size: 24px;
            font-weight: 700;
            color: #333;
        }

        .header-logo h1 span {
            color: #e9b732;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: #555;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .back-link i {
            margin-right: 8px;
        }

        .back-link:hover {
            color: #e9b732;
        }

        /* Main Content */
        .main-content {
            padding: 120px 0 60px;
        }

        /* Driver Details */
        .driver-profile {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .profile-header {
            position: relative;
            height: 250px;
            background-color: #f5f5f5;
            overflow: hidden;
        }

        .profile-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: #fff;
            padding: 30px;
        }

        .profile-type {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #e9b732;
            color: #fff;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 600;
        }

        .profile-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .profile-location {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .profile-location i {
            margin-right: 8px;
        }

        .profile-rating {
            display: flex;
            align-items: center;
        }

        .star {
            color: #ffc107;
            font-size: 16px;
            margin-right: 2px;
        }

        .profile-content {
            padding: 30px;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }

        .profile-about {
            margin-bottom: 30px;
        }

        .profile-section-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
            padding-bottom: 10px;
        }

        .profile-section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: #e9b732;
        }

        .profile-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.7;
        }

        .profile-features-list {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
            gap: 15px;
        }

        .profile-feature {
            display: flex;
            align-items: center;
            padding: 8px 15px;
            background: #f8f8f8;
            border-radius: 8px;
            color: #555;
        }

        .profile-feature i {
            margin-right: 8px;
            color: #e9b732;
        }

        .profile-regions {
            margin-bottom: 30px;
        }

        .region-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .badge {
            background: #f0f0f0;
            color: #666;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .badge-primary {
            background: #e9f5ff;
            color: #0077cc;
        }

        .profile-gallery {
            margin-bottom: 30px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            height: 120px;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .profile-reviews {
            margin-bottom: 30px;
        }

        .review {
            background: #f8f8f8;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .review-name {
            font-weight: 600;
        }

        .review-date {
            color: #888;
            font-size: 14px;
        }

        .review-rating {
            margin-bottom: 10px;
        }

        .review-text {
            color: #666;
        }

        .view-all-link {
            display: inline-block;
            color: #e9b732;
            font-weight: 600;
            text-decoration: none;
            margin-top: 10px;
        }

        .view-all-link:hover {
            text-decoration: underline;
        }

        /* Sidebar */
        .profile-sidebar {
            align-self: start;
        }

        .sidebar-widget {
            background: #f8f8f8;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .widget-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .rate-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .rate-label {
            color: #666;
        }

        .rate-value {
            font-weight: 600;
            color: #333;
        }

        .total-rate {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-weight: 700;
            font-size: 18px;
        }

        .book-btn, .contact-btn {
            display: block;
            width: 100%;
            padding: 12px;
            text-align: center;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-bottom: 15px;
        }

        .book-btn {
            background: #e9b732;
            color: #fff;
        }

        .book-btn:hover {
            background: #d4a026;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(233, 183, 50, 0.3);
        }

        .contact-btn {
            background: #fff;
            color: #333;
            border: 1px solid #ddd;
        }

        .contact-btn:hover {
            background: #f0f0f0;
        }

        .contact-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #666;
        }

        .contact-info i {
            width: 30px;
            color: #e9b732;
            margin-right: 10px;
        }

        .contact-value {
            color: #333;
            font-weight: 500;
        }

        .map-container {
            height: 200px;
            background: #eee;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
        }

        .map-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .map-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .view-map-btn {
            background: #fff;
            color: #333;
            padding: 8px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .view-map-btn:hover {
            background: #e9b732;
            color: #fff;
        }

        /* Booking Form Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
            transition: all 0.3s ease;
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 5px 50px rgba(0,0,0,0.2);
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal.show {
            backdrop-filter: blur(5px);
        }

        .modal.show .modal-content {
            transform: translateY(0);
            opacity: 1;
        }

        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .close-modal:hover {
            color: #e9b732;
        }

        .modal-title {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            font-size: 22px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #e9b732;
            outline: none;
            box-shadow: 0 0 0 3px rgba(233, 183, 50, 0.2);
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .submit-btn {
            background: #e9b732;
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background: #d4a026;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(233, 183, 50, 0.3);
        }

        /* Success Message */
        .booking-success {
            display: none;
            text-align: center;
            padding: 20px 0;
        }

        .booking-success i {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
        }

        .booking-id {
            background: #f0f9ff;
            display: inline-block;
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 600;
            margin: 15px 0;
            border: 1px dashed #0077cc;
            color: #0077cc;
        }

        /* Alert styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 8px;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        /* Photo swipe gallery overlay */
        .photo-overlay {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
            overflow: hidden;
        }

        .photo-content {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .photo-wrapper {
            position: relative;
            width: 90%;
            max-width: 1000px;
            height: 80%;
            display: flex;
            flex-direction: column;
        }

        .photo-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .photo-main img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .photo-close {
            position: absolute;
            top: -40px;
            right: 0;
            color: #fff;
            font-size: 28px;
            cursor: pointer;
            z-index: 10;
        }

        .photo-nav {
            display: flex;
            justify-content: space-between;
            position: absolute;
            width: 100%;
            top: 50%;
            transform: translateY(-50%);
        }

        .photo-nav-btn {
            background: rgba(255,255,255,0.2);
            color: #fff;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .photo-nav-btn:hover {
            background: rgba(255,255,255,0.4);
        }

        /* Responsive Media Queries */
        @media (max-width: 992px) {
            .profile-content {
                grid-template-columns: 1fr;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .profile-header {
                height: 200px;
            }
            
            .profile-name {
                font-size: 24px;
            }
            
            .profile-overlay {
                padding: 20px;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }

        @media (max-width: 576px) {
            .profile-header {
                height: 180px;
            }
            
            .profile-name {
                font-size: 22px;
            }
            
            .gallery-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-logo">
                <h1>Explorer <span>Le Bénin</span></h1>
                <a href="index.php?page=ptransport" class="back-link"><i class="fas fa-chevron-left"></i> Retour aux transporteurs</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <?php if(isset($errorMsg)): ?>
                <div class="alert alert-danger">
                    <?php echo $errorMsg; ?>
                </div>
            <?php elseif(isset($notFoundError)): ?>
                <div class="alert alert-danger">
                    <?php echo $notFoundError; ?>
                    <br><br>
                    <a href="transport.php" class="back-link"><i class="fas fa-chevron-left"></i> Retourner à la liste des transporteurs</a>
                </div>
            <?php elseif(!empty($driver)): ?>
                <?php
                    // Préparer les données
                    $fullName = safeGet($driver, 'first_name', 'Anonyme') . ' ' . safeGet($driver, 'last_name', '');
                    $vehicleType = !empty(safeGet($driver, 'vehicle_type')) ? $driver['vehicle_type'] : 'Autre';
                    $capacity = !empty(safeGet($driver, 'passenger_capacity')) ? intval($driver['passenger_capacity']) : 0;
                    $description = !empty(safeGet($driver, 'description')) ? $driver['description'] : 'Aucune description disponible pour ce transporteur.';
                    
                    // Services offerts
                    $services = [];
                    if(!empty(safeGet($driver, 'services'))) {
                        $services = textToArray($driver['services']);
                    }
                    
                    // Régions/destinations
                    $regions = [];
                    if(!empty(safeGet($driver, 'regions'))) {
                        $regions = textToArray($driver['regions']);
                    }
                    
                    // Photos du véhicule
                    $vehiclePhotos = [];
                    if(!empty(safeGet($driver, 'vehicle_photos'))) {
                        $vehiclePhotos = textToArray($driver['vehicle_photos']);
                    }
                    
                    // Si aucune photo, utiliser des placeholders
                    if(empty($vehiclePhotos)) {
                        $vehiclePhotos = [
                            '/api/placeholder/300/300',
                            '/api/placeholder/300/300',
                            '/api/placeholder/300/300'
                        ];
                    }
                    
                    // Photo de profil principale
                    $profilePhoto = '/api/placeholder/800/400';
                    if(!empty(safeGet($driver, 'profile_photo'))) {
                        $profilePhoto = $driver['profile_photo'];
                    } elseif(!empty($vehiclePhotos) && isset($vehiclePhotos[0])) {
                        $profilePhoto = $vehiclePhotos[0];
                    }
                    
                    // Rating (générer un aléatoire entre 3.5 et 5 pour démonstration)
                    $rating = mt_rand(35, 50) / 10;
                    
                    // Icône selon le type de véhicule
                    $typeIcon = 'fas fa-car';
                    $displayType = 'Autre';
                    
                    if(stripos($vehicleType, 'voiture') !== false) {
                        $typeIcon = 'fas fa-car';
                        $displayType = 'Voiture';
                    } elseif(stripos($vehicleType, 'minibus') !== false) {
                        $typeIcon = 'fas fa-shuttle-van';
                        $displayType = 'Minibus';
                    } elseif(stripos($vehicleType, 'bus') !== false) {
                        $typeIcon = 'fas fa-bus';
                        $displayType = 'Bus';
                    } elseif(stripos($vehicleType, 'moto') !== false || stripos($vehicleType, 'zem') !== false) {
                        $typeIcon = 'fas fa-motorcycle';
                        $displayType = 'Moto/Zem';
                    } elseif(stripos($vehicleType, '4x4') !== false || stripos($vehicleType, 'suv') !== false) {
                        $typeIcon = 'fas fa-truck-monster';
                        $displayType = '4x4/SUV';
                    } elseif(stripos($vehicleType, 'taxi') !== false) {
                        $typeIcon = 'fas fa-taxi';
                        $displayType = 'Taxi';
                    }
                    
                    // Tarif par km ou par défaut
                    $ratePerKm = !empty(safeGet($driver, 'rate_per_km')) ? intval($driver['rate_per_km']) : 500;
                    
                    // Langues parlées par le chauffeur
                    $languages = ['Français'];
                    if(!empty(safeGet($driver, 'languages'))) {
                        $languages = textToArray($driver['languages']);
                    }
                    if(empty($languages)) {
                        $languages = ['Français', 'Fon'];
                    }
                    
                    // Générer un numéro de téléphone fictif si non disponible
                    $phone = !empty(safeGet($driver, 'phone')) ? $driver['phone'] : '+229 ' . rand(60000000, 99999999);
                    
                    // Email
                    $email = !empty(safeGet($driver, 'email')) ? $driver['email'] : strtolower(str_replace(' ', '', $fullName)) . '@example.com';
                    
                    // Ville/localisation
                    $location = !empty(safeGet($driver, 'city')) ? $driver['city'] : 'Adjarra, Bénin';
                    if(!empty(safeGet($driver, 'vehicle_model'))) {
                        $location = $driver['vehicle_model'] . ', ' . $location;
                    }
                ?>
                <div class="driver-profile">
                    <div class="profile-header">
                        <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="<?php echo htmlspecialchars($fullName); ?>">
                        <div class="profile-overlay">
                            <h1 class="profile-name"><?php echo htmlspecialchars($fullName); ?></h1>
                            <div class="profile-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($location); ?>
                            </div>
                            <div class="profile-rating">
                                <?php 
                                    $fullStars = floor($rating);
                                    $halfStar = ($rating - $fullStars) >= 0.5;
                                    
                                    for($i = 0; $i < $fullStars; $i++): 
                                ?>
                                    <span class="star"><i class="fas fa-star"></i></span>
                                <?php endfor; ?>
                                
                                <?php if($halfStar): ?>
                                    <span class="star"><i class="fas fa-star-half-alt"></i></span>
                                <?php endif; ?>
                                
                                <?php for($i = $fullStars + ($halfStar ? 1 : 0); $i < 5; $i++): ?>
                                    <span class="star"><i class="far fa-star"></i></span>
                                <?php endfor; ?>
                                
                                <span style="margin-left: 5px;"><?php echo number_format($rating, 1); ?>/5</span>
                            </div>
                        </div>
                        <div class="profile-type"><i class="<?php echo $typeIcon; ?>"></i> <?php echo htmlspecialchars($displayType); ?></div>
                    </div>
                    
                    <div class="profile-content">
                        <div class="profile-main">
                            <div class="profile-about">
                                <h3 class="profile-section-title">À propos</h3>
                                <p class="profile-description"><?php echo nl2br(htmlspecialchars($description)); ?></p>
                                
                                <div class="profile-features-list">
                                    <div class="profile-feature">
                                        <i class="fas fa-users"></i> <?php echo $capacity; ?> passagers
                                    </div>
                                    <div class="profile-feature">
                                        <i class="fas fa-language"></i> <?php echo implode(', ', array_map('htmlspecialchars', $languages)); ?>
                                    </div>
                                    <div class="profile-feature">
                                        <i class="fas fa-calendar-check"></i> Disponible
                                    </div>
                                </div>
                            </div>
                            
                            <div class="profile-services">
                                <h3 class="profile-section-title">Services offerts</h3>
                                <div class="profile-features-list">
                                    <?php if(empty($services)): ?>
                                        <?php 
                                            // Services par défaut
                                            $defaultServices = [
                                                ['icon' => 'fas fa-route', 'name' => 'Transport urbain'],
                                                ['icon' => 'fas fa-plane-departure', 'name' => 'Transfert aéroport'],
                                                ['icon' => 'fas fa-map-marked-alt', 'name' => 'Excursions'],
                                                ['icon' => 'fas fa-luggage-cart', 'name' => 'Transport de bagages']
                                            ];
                                            
                                            // Prendre un nombre aléatoire de services
                                            shuffle($defaultServices);
                                            $numServices = mt_rand(2, count($defaultServices));
                                            $services = array_slice($defaultServices, 0, $numServices);
                                            
                                            foreach($services as $service):
                                        ?>
                                            <div class="profile-feature">
                                                <i class="<?php echo $service['icon']; ?>"></i> <?php echo htmlspecialchars($service['name']); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php 
                                            $serviceIcons = [
                                                'transport' => 'fas fa-route',
                                                'airport' => 'fas fa-plane-departure',
                                                'excursion' => 'fas fa-map-marked-alt',
                                                'touristique' => 'fas fa-map-marked-alt',
                                                'visite' => 'fas fa-map-marked-alt',
                                                'bagages' => 'fas fa-luggage-cart',
                                                'aéroport' => 'fas fa-plane-departure',
                                                'jour' => 'fas fa-sun',
                                                'nuit' => 'fas fa-moon',
                                                'express' => 'fas fa-shipping-fast',
                                                'groupe' => 'fas fa-users',
                                                'vip' => 'fas fa-star'
                                            ];
                                            
                                            foreach($services as $service):
                                                $icon = 'fas fa-check-circle';
                                                
                                                // Chercher un icône approprié
                                                foreach($serviceIcons as $keyword => $serviceIcon) {
                                                    if(stripos($service, $keyword) !== false) {
                                                        $icon = $serviceIcon;
                                                        break;
                                                    }
                                                }
                                        ?>
                                            <div class="profile-feature">
                                                <i class="<?php echo $icon; ?>"></i> <?php echo htmlspecialchars($service); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="profile-regions">
                                <h3 class="profile-section-title">Destinations & régions couvertes</h3>
                                <div class="region-badges">
                                    <?php if(empty($regions)): ?>
                                        <?php 
                                            // Régions par défaut
                                            $defaultRegions = [
                                                'Adjarra', 'Porto-Novo', 'Cotonou', 'Ouidah', 'Ganvié', 
                                                'Abomey', 'Grand-Popo', 'Parakou', 'Natitingou', 'Lokossa'
                                            ];
                                            
                                            // Prendre un nombre aléatoire de régions
                                            shuffle($defaultRegions);
                                            $numRegions = mt_rand(3, 6);
                                            $regions = array_slice($defaultRegions, 0, $numRegions);
                                            
                                            foreach($regions as $region):
                                        ?>
                                            <span class="badge badge-primary"><?php echo htmlspecialchars($region); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <?php foreach($regions as $region): ?>
                                            <span class="badge badge-primary"><?php echo htmlspecialchars($region); ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="profile-gallery">
                                <h3 class="profile-section-title">Photos du véhicule</h3>
                                <div class="gallery-grid">
                                    <?php for($i = 0; $i < min(count($vehiclePhotos), 6); $i++): ?>
                                        <div class="gallery-item" data-index="<?php echo $i; ?>">
                                            <img src="<?php echo htmlspecialchars($vehiclePhotos[$i]); ?>" alt="Photo du véhicule">
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            
                        </div>
                        
                        <div class="profile-sidebar">
                            <div class="sidebar-widget">
                                <h3 class="widget-title">Tarifs</h3>
                                <div class="rate-info">
                                    <div class="rate-label">Tarif par km</div>
                                    <div class="rate-value"><?php echo number_format($ratePerKm, 0, ',', ' '); ?> FCFA</div>
                                </div>
                                <div class="rate-info">
                                    <div class="rate-label">Tarif minimum</div>
                                    <div class="rate-value"><?php echo number_format($ratePerKm * 5, 0, ',', ' '); ?> FCFA</div>
                                </div>
                                <div class="rate-info">
                                    <div class="rate-label">Excursion (jour)</div>
                                    <div class="rate-value"><?php echo number_format($ratePerKm * 50, 0, ',', ' '); ?> FCFA</div>
                                </div>
                                
                                
                                <a href="tel:<?php echo str_replace(' ', '', $phone); ?>" class="contact-btn"><i class="fas fa-phone-alt"></i> Appeler directement</a>
                            </div>
                            
                            <div class="sidebar-widget">
                                <h3 class="widget-title">Contact</h3>
                                <div class="contact-info">
                                    <i class="fas fa-phone-alt"></i>
                                    <div class="contact-value"><?php echo htmlspecialchars($phone); ?></div>
                                </div>
                                <div class="contact-info">
                                    <i class="fas fa-envelope"></i>
                                    <div class="contact-value"><?php echo htmlspecialchars($email); ?></div>
                                </div>
                                <div class="contact-info">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div class="contact-value"><?php echo htmlspecialchars($location); ?></div>
                                </div>
                            </div>
                            
                            <div class="sidebar-widget">
                                <h3 class="widget-title">Localisation</h3>
                                <div class="map-container">
                                    <img src="/api/placeholder/400/200" alt="Carte">
                                    <div class="map-overlay">
                                        <a href="#" class="view-map-btn">Voir sur la carte</a>
                                    </div>
                                </div>
                                <div class="profile-feature">
                                    <i class="fas fa-map-marker-alt"></i> Base à <?php echo explode(',', $location)[0]; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal" id="bookingModal">
        <div class="modal-content">
            <span class="close-modal" id="closeModal">&times;</span>
            
            <div class="booking-form">
                <h2 class="modal-title">Réserver avec <?php echo isset($fullName) ? htmlspecialchars($fullName) : 'ce transporteur'; ?></h2>
                
                <form id="transportBookingForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">Prénom</label>
                            <input type="text" class="form-control" id="firstName" placeholder="Votre prénom" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Nom</label>
                            <input type="text" class="form-control" id="lastName" placeholder="Votre nom" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="Votre adresse email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Numéro de téléphone</label>
                        <input type="tel" class="form-control" id="phone" placeholder="Votre numéro de téléphone" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="pickupLocation">Lieu de prise en charge</label>
                            <input type="text" class="form-control" id="pickupLocation" placeholder="Adresse de départ" required>
                        </div>
                        <div class="form-group">
                            <label for="dropoffLocation">Destination</label>
                            <input type="text" class="form-control" id="dropoffLocation" placeholder="Adresse d'arrivée" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="pickupDate">Date de départ</label>
                            <input type="date" class="form-control" id="pickupDate" required>
                        </div>
                        <div class="form-group">
                            <label for="pickupTime">Heure</label>
                            <input type="time" class="form-control" id="pickupTime" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="passengers">Nombre de passagers</label>
                        <input type="number" class="form-control" id="passengers" min="1" max="<?php echo isset($capacity) ? $capacity : 10; ?>" value="1" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes / Instructions spéciales</label>
                        <textarea class="form-control" id="notes" rows="3" placeholder="Informations supplémentaires, bagages, besoins spécifiques..."></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">Confirmer la réservation</button>
                </form>
            </div>
            
            <div class="booking-success">
                <i class="fas fa-check-circle"></i>
                <h2>Réservation confirmée !</h2>
                <p>Votre demande de réservation a été envoyée avec succès.</p>
                <div class="booking-id">Référence: <span id="bookingReference"></span></div>
                <p>Nous vous contacterons bientôt pour confirmer les détails de votre réservation.</p>
                <button class="submit-btn" id="closeSuccess">Fermer</button>
            </div>
        </div>
    </div>
    
    <!-- Photo Viewer Overlay -->
    <div class="photo-overlay" id="photoViewer">
        <div class="photo-content">
            <div class="photo-wrapper">
                <span class="photo-close" id="closePhotoViewer">&times;</span>
                <div class="photo-main">
                    <img id="currentPhoto" src="" alt="Photo agrandie">
                </div>
                <div class="photo-nav">
                    <button class="photo-nav-btn" id="prevPhoto"><i class="fas fa-chevron-left"></i></button>
                    <button class="photo-nav-btn" id="nextPhoto"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Variables pour la galerie de photos
        const galleryItems = document.querySelectorAll('.gallery-item');
        const photoViewer = document.getElementById('photoViewer');
        const currentPhoto = document.getElementById('currentPhoto');
        const closePhotoViewer = document.getElementById('closePhotoViewer');
        const prevPhoto = document.getElementById('prevPhoto');
        const nextPhoto = document.getElementById('nextPhoto');
        let currentIndex = 0;
        const photos = [];
        
        // Collecter toutes les photos de la galerie
        galleryItems.forEach(item => {
            const img = item.querySelector('img');
            photos.push(img.src);
            
            // Ajouter l'événement de clic
            item.addEventListener('click', () => {
                currentIndex = parseInt(item.getAttribute('data-index'));
                showPhoto(currentIndex);
                photoViewer.style.display = 'block';
            });
        });
        
        // Fonction pour afficher une photo
        function showPhoto(index) {
            if (index < 0) index = photos.length - 1;
            if (index >= photos.length) index = 0;
            
            currentIndex = index;
            currentPhoto.src = photos[currentIndex];
        }
        
        // Navigation dans les photos
        prevPhoto.addEventListener('click', () => {
            showPhoto(currentIndex - 1);
        });
        
        nextPhoto.addEventListener('click', () => {
            showPhoto(currentIndex + 1);
        });
        
        // Fermer la visionneuse de photos
        closePhotoViewer.addEventListener('click', () => {
            photoViewer.style.display = 'none';
        });
        
        // Modal de réservation
        const bookingModal = document.getElementById('bookingModal');
        const openBookingModal = document.getElementById('openBookingModal');
        const closeModal = document.getElementById('closeModal');
        const bookingForm = document.querySelector('.booking-form');
        const bookingSuccess = document.querySelector('.booking-success');
        const bookingReference = document.getElementById('bookingReference');
        const closeSuccess = document.getElementById('closeSuccess');
        const transportBookingForm = document.getElementById('transportBookingForm');
        
        // Ouvrir le modal
        if (openBookingModal) {
            openBookingModal.addEventListener('click', () => {
                bookingModal.style.display = 'block';
                setTimeout(() => {
                    bookingModal.classList.add('show');
                }, 10);
            });
        }
        
        // Fermer le modal
        if (closeModal) {
            closeModal.addEventListener('click', () => {
                bookingModal.classList.remove('show');
                setTimeout(() => {
                    bookingModal.style.display = 'none';
                }, 300);
            });
        }
        
        // Gestion du formulaire de réservation
        if (transportBookingForm) {
            transportBookingForm.addEventListener('submit', (e) => {
                e.preventDefault();
                
                // Simuler l'envoi du formulaire
                setTimeout(() => {
                    bookingForm.style.display = 'none';
                    bookingSuccess.style.display = 'block';
                    
                    // Générer un numéro de réservation
                    bookingReference.textContent = '<?php echo generateBookingId(); ?>';
                }, 1000);
            });
        }
        
        // Fermer le message de succès
        if (closeSuccess) {
            closeSuccess.addEventListener('click', () => {
                bookingModal.classList.remove('show');
                setTimeout(() => {
                    bookingModal.style.display = 'none';
                    // Réinitialiser pour la prochaine ouverture
                    setTimeout(() => {
                        bookingForm.style.display = 'block';
                        bookingSuccess.style.display = 'none';
                        transportBookingForm.reset();
                    }, 300);
                }, 300);
            });
        }
        
        // Fermer le modal en cliquant en dehors
        window.addEventListener('click', (e) => {
            if (e.target === bookingModal) {
                bookingModal.classList.remove('show');
                setTimeout(() => {
                    bookingModal.style.display = 'none';
                }, 300);
            }
        });
    </script>
</body>
</html>