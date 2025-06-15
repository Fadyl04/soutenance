<?php
// Établir la connexion à la base de données
try {
    $host = "localhost";
    $dbname = "adjarra";
    $username = "root";
    $password = "Fadyl111";
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Initialiser les filtres
    $whereConditions = ["status = 'approved'"];
    $params = [];
    
    // Filtrer par type de véhicule
    if(!empty($_GET['type'])) {
        $whereConditions[] = "vehicle_type LIKE :vehicle_type";
        $params[':vehicle_type'] = '%' . $_GET['type'] . '%';
    }
    
    // Filtrer par destination/région
    if(!empty($_GET['destination'])) {
        $whereConditions[] = "regions LIKE :regions";
        $params[':regions'] = '%' . $_GET['destination'] . '%';
    }
    
    // Filtrer par caractéristique/service
    if(!empty($_GET['feature'])) {
        $whereConditions[] = "services LIKE :services";
        $params[':services'] = '%' . $_GET['feature'] . '%';
    }
    
    // Recherche par nom ou autre info
    if(!empty($_GET['search'])) {
        $whereConditions[] = "(first_name LIKE :search OR last_name LIKE :search OR username LIKE :search OR vehicle_model LIKE :search OR city LIKE :search)";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }
    
    // Construire la requête SQL
    $sql = "SELECT * FROM transporters WHERE " . implode(" AND ", $whereConditions);
    
    $stmt = $conn->prepare($sql);
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $transports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services de Transport - Adjarra</title>
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

        .section-title {
            text-align: center;
            padding-bottom: 30px;
        }

        .section-title h2 {
            font-size: 32px;
            font-weight: bold;
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
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }

        /* Transport List */
        .transports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .transport-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .transport-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12);
        }

        .transport-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }

        .transport-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .transport-card:hover .transport-image img {
            transform: scale(1.1);
        }

        .transport-type {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #e9b732;
            color: #fff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 1;
        }

        .transport-details {
            padding: 20px;
        }

        .transport-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }

        .transport-location {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #666;
        }

        .transport-location i {
            color: #e9b732;
            margin-right: 8px;
        }

        .transport-features {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .transport-feature {
            display: flex;
            align-items: center;
            margin-right: 15px;
            margin-bottom: 8px;
            color: #666;
            font-size: 14px;
        }

        .transport-feature i {
            color: #e9b732;
            margin-right: 5px;
        }

        .transport-capacity {
            margin-bottom: 15px;
            color: #666;
            font-size: 14px;
        }

        .transport-capacity i {
            color: #e9b732;
            margin-right: 5px;
        }

        .transport-rate {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .transport-rate span {
            color: #e9b732;
        }

        .transport-link {
            display: inline-block;
            padding: 10px 25px;
            background: #e9b732;
            color: #fff;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .transport-link:hover {
            background: #d4a026;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(233, 183, 50, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 0;
        }

        .empty-state i {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 24px;
            color: #555;
            margin-bottom: 15px;
        }

        .empty-state p {
            color: #888;
            max-width: 500px;
            margin: 0 auto 20px;
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

        /* Responsive Media Queries */
        @media (max-width: 992px) {
            .transports-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 100px 0 40px;
            }
            
            .section-title h2 {
                font-size: 28px;
            }
            
            .transports-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 576px) {
            .transports-grid {
                grid-template-columns: 1fr;
            }
            
            .transport-card {
                max-width: 400px;
                margin: 0 auto;
            }
        }
        
        /* Filtres et recherche */
        .filter-bar {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }
        
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 14px;
        }
        
        .filter-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .filter-button {
            margin-top: 24px;
            padding: 10px 25px;
            background: #e9b732;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .filter-button:hover {
            background: #d4a026;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            list-style: none;
        }
        
        .page-item {
            margin: 0 5px;
        }
        
        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            color: #333;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .page-link:hover, .page-item.active .page-link {
            background: #e9b732;
            color: #fff;
        }
        
        /* Rating */
        .transport-rating {
            display: flex;
            margin-bottom: 15px;
        }
        
        .star {
            color: #ffc107;
            font-size: 16px;
            margin-right: 2px;
        }

        /* Badges */
        .transport-badges {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
            gap: 10px;
        }

        .badge {
            background: #f0f0f0;
            color: #666;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-primary {
            background: #e9f5ff;
            color: #0077cc;
        }

        .badge-success {
            background: #e6f8e6;
            color: #28a745;
        }

        .badge-warning {
            background: #fff8e1;
            color: #ffa000;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-logo">
                <h1>Explorer <span>Adjarra</span></h1>
                <a href="index.php" class="back-link"><i class="fas fa-chevron-left"></i> Retour à l'accueil</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="section-title">
                <h2>Nos Services de Transport</h2>
                <p>Découvrez les différentes options de transport disponibles pour explorer Adjarra et ses environs. Des véhicules confortables et sécurisés pour tous vos déplacements.</p>
            </div>

            

            <?php if(isset($errorMsg)): ?>
                <div class="alert alert-danger">
                    <?php echo $errorMsg; ?>
                </div>
            <?php elseif(empty($transports)): ?>
                <div class="empty-state">
                    <i class="fas fa-car"></i>
                    <h3>Aucun service de transport trouvé</h3>
                    <p>Il n'y a actuellement aucun service de transport approuvé dans notre base de données.</p>
                </div>
            <?php else: ?>
                <div class="transports-grid">
                    <?php foreach($transports as $transport): ?>
                        <?php 
                            // Récupérer la photo du profil
                            $photo = '/api/placeholder/400/300';
                            if(!empty(safeGet($transport, 'profile_photo'))) {
                                $photo = $transport['profile_photo'];
                            } elseif(!empty(safeGet($transport, 'vehicle_photos'))) {
                                // Si photos véhicule disponibles, prendre la première
                                $vehiclePhotos = textToArray($transport['vehicle_photos']);
                                if(!empty($vehiclePhotos) && isset($vehiclePhotos[0])) {
                                    $photo = $vehiclePhotos[0];
                                }
                            }
                            
                            // Récupérer les services offerts
                            $services = [];
                            if(!empty(safeGet($transport, 'services'))) {
                                $services = textToArray($transport['services']);
                            }
                            
                            // Récupérer les régions/destinations
                            $regions = [];
                            if(!empty(safeGet($transport, 'regions'))) {
                                $regions = textToArray($transport['regions']);
                            }
                            
                            // Obtenir la capacité
                            $capacity = !empty(safeGet($transport, 'passenger_capacity')) ? intval($transport['passenger_capacity']) : 0;
                            
                            // Rating (générer un aléatoire entre 3 et 5 pour démonstration)
                            $rating = mt_rand(30, 50) / 10;

                            // Obtenir le type de véhicule
                            $vehicleType = !empty(safeGet($transport, 'vehicle_type')) ? $transport['vehicle_type'] : 'Autre';
                            
                            // Construction du nom complet
                            $fullName = safeGet($transport, 'first_name', 'Anonyme') . ' ' . safeGet($transport, 'last_name', '');
                            
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
                        ?>
                        <div class="transport-card">
                            <div class="transport-image">
                                <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($fullName); ?>">
                                <div class="transport-type"><?php echo htmlspecialchars($displayType); ?></div>
                            </div>
                            <div class="transport-details">
                                <h3 class="transport-name"><?php echo htmlspecialchars($fullName); ?></h3>
                                <div class="transport-rating">
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
                                        <span class="star" style="color: #ddd;"><i class="fas fa-star"></i></span>
                                    <?php endfor; ?>
                                    
                                    <?php if($rating > 0): ?>
                                        <span style="margin-left: 5px; font-size: 14px; color: #666;">(<?php echo number_format($rating, 1); ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="transport-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php 
                                        $locationInfo = [];
                                        if(!empty(safeGet($transport, 'vehicle_model'))) $locationInfo[] = $transport['vehicle_model'];
                                        if(!empty(safeGet($transport, 'city'))) $locationInfo[] = $transport['city'];
                                        echo htmlspecialchars(implode(', ', $locationInfo) ?: 'Adjarra');
                                    ?>
                                </div>
                                
                                <?php if(!empty($regions)): ?>
                                <div class="transport-badges">
                                    <?php foreach($regions as $region): ?>
                                        <span class="badge badge-primary"><?php echo htmlspecialchars(ucfirst($region)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if(!empty($services)): ?>
                                <div class="transport-features">
                                    <?php 
                                        $featuresShown = 0;
                                        foreach($services as $service): 
                                            if($featuresShown >= 3) break; // Limiter à 3 services affichés
                                            $featuresShown++;
                                    ?>
                                        <?php 
                                            $icon = 'fas fa-check';
                                            $label = ucfirst($service);
                                            
                                            if(stripos($service, 'climat') !== false) {
                                                $icon = 'fas fa-snowflake';
                                                $label = 'Climatisé';
                                            } elseif(stripos($service, 'wifi') !== false) {
                                                $icon = 'fas fa-wifi';
                                                $label = 'Wifi';
                                            } elseif(stripos($service, 'access') !== false || stripos($service, 'pmr') !== false || stripos($service, 'handicap') !== false) {
                                                $icon = 'fas fa-wheelchair';
                                                $label = 'Accessible';
                                            } elseif(stripos($service, 'eco') !== false || stripos($service, 'électr') !== false || stripos($service, 'hybride') !== false) {
                                                $icon = 'fas fa-leaf';
                                                $label = 'Écologique';
                                            } elseif(stripos($service, 'guide') !== false) {
                                                $icon = 'fas fa-user-tie';
                                                $label = 'Guide';
                                            } elseif(stripos($service, 'bagage') !== false) {
                                                $icon = 'fas fa-suitcase';
                                                $label = 'Bagages';
                                            } elseif(stripos($service, 'enfant') !== false || stripos($service, 'siège') !== false) {
                                                $icon = 'fas fa-baby';
                                                $label = 'Siège enfant';
                                            }
                                        ?>
                                        <div class="transport-feature"><i class="<?php echo $icon; ?>"></i> <?php echo $label; ?></div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($capacity > 0): ?>
                                <div class="transport-capacity">
                                    <i class="fas fa-users"></i> Capacité: <?php echo $capacity; ?> personne<?php echo $capacity > 1 ? 's' : ''; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="transport-rate">
                                    <span>Tarif:</span> 
                                    <?php 
                                        if(!empty(safeGet($transport, 'rate_per_km'))) {
                                            echo number_format($transport['rate_per_km'], 0, ',', ' ') . ' FCFA/km';
                                        } else {
                                            echo 'Sur demande';
                                        }
                                    ?>
                                </div>
                                
                                
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination (simple pour démonstration) -->
                <ul class="pagination">
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Pour gérer les filtres - préremplir les champs en fonction des paramètres d'URL
            const urlParams = new URLSearchParams(window.location.search);
            
            const typeParam = urlParams.get('type');
            if(typeParam) {
                document.getElementById('type').value = typeParam;
            }
            
            const destinationParam = urlParams.get('destination');
            if(destinationParam) {
                document.getElementById('destination').value = destinationParam;
            }
            
            const featureParam = urlParams.get('feature');
            if(featureParam) {
                document.getElementById('feature').value = featureParam;
            }
            
            const searchParam = urlParams.get('search');
            if(searchParam) {
                document.getElementById('search').value = searchParam;
            }
        });
    </script>
</body>
</html>