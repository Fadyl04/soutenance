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
    
    // Filtrer par catégorie d'hôtel
    if(!empty($_GET['category'])) {
        $whereConditions[] = "hotel_category LIKE :category";
        $params[':category'] = '%' . $_GET['category'] . '%';
    }
    
    // Filtrer par région
    if(!empty($_GET['region'])) {
        $whereConditions[] = "region LIKE :region";
        $params[':region'] = '%' . $_GET['region'] . '%';
    }
    
    // Filtrer par ville
    if(!empty($_GET['city'])) {
        $whereConditions[] = "city LIKE :city";
        $params[':city'] = '%' . $_GET['city'] . '%';
    }
    
    // Filtrer par commodités
    $amenities = [];
    if(!empty($_GET['wifi']) && $_GET['wifi'] == '1') {
        $amenities[] = "has_wifi = 1";
    }
    if(!empty($_GET['parking']) && $_GET['parking'] == '1') {
        $amenities[] = "has_parking = 1";
    }
    if(!empty($_GET['pool']) && $_GET['pool'] == '1') {
        $amenities[] = "has_pool = 1";
    }
    if(!empty($_GET['restaurant']) && $_GET['restaurant'] == '1') {
        $amenities[] = "has_restaurant = 1";
    }
    if(!empty($_GET['ac']) && $_GET['ac'] == '1') {
        $amenities[] = "has_ac = 1";
    }
    if(!empty($_GET['conference']) && $_GET['conference'] == '1') {
        $amenities[] = "has_conference = 1";
    }
    
    if (!empty($amenities)) {
        $whereConditions[] = "(" . implode(" AND ", $amenities) . ")";
    }
    
    // Filtrer par gamme de prix
    if(!empty($_GET['price_range'])) {
        $whereConditions[] = "price_range LIKE :price_range";
        $params[':price_range'] = '%' . $_GET['price_range'] . '%';
    }
    
    // Recherche par nom ou autre info
    if(!empty($_GET['search'])) {
        $whereConditions[] = "(hotel_name LIKE :search OR address LIKE :search OR city LIKE :search OR description LIKE :search)";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }
    
    // Construire la requête SQL
    $sql = "SELECT * FROM hotels WHERE " . implode(" AND ", $whereConditions);
    
    $stmt = $conn->prepare($sql);
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $errorMsg = "Erreur de connexion à la base de données : " . $e->getMessage();
}

// Fonction utilitaire pour accéder aux clés de façon sécurisée
function safeGet($array, $key, $default = '') {
    return isset($array[$key]) ? $array[$key] : $default;
}

// Fonction pour convertir les champs binaires en valeurs lisibles
function getBooleanLabel($value) {
    return $value ? 'Oui' : 'Non';
}

// Fonction pour obtenir une liste de régions distinctes pour le filtre
function getDistinctRegions($conn) {
    try {
        $stmt = $conn->prepare("SELECT DISTINCT region FROM hotels WHERE region IS NOT NULL AND region != '' AND status = 'approved'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $e) {
        return [];
    }
}

// Fonction pour obtenir une liste de villes distinctes pour le filtre
function getDistinctCities($conn) {
    try {
        $stmt = $conn->prepare("SELECT DISTINCT city FROM hotels WHERE city IS NOT NULL AND city != '' AND status = 'approved'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $e) {
        return [];
    }
}

// Fonction pour obtenir une liste de catégories d'hôtels distinctes pour le filtre
function getDistinctCategories($conn) {
    try {
        $stmt = $conn->prepare("SELECT DISTINCT hotel_category FROM hotels WHERE hotel_category IS NOT NULL AND hotel_category != '' AND status = 'approved'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $e) {
        return [];
    }
}

// Charger les données pour les filtres
$regions = [];
$cities = [];
$categories = [];
if (isset($conn)) {
    $regions = getDistinctRegions($conn);
    $cities = getDistinctCities($conn);
    $categories = getDistinctCategories($conn);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hôtels & Hébergements - Adjarra</title>
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

        /* Hotels Grid */
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .hotel-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .hotel-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12);
        }

        .hotel-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }

        .hotel-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .hotel-card:hover .hotel-image img {
            transform: scale(1.1);
        }

        .hotel-category {
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

        .hotel-details {
            padding: 20px;
        }

        .hotel-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }

        .hotel-location {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #666;
        }

        .hotel-location i {
            color: #e9b732;
            margin-right: 8px;
        }

        .hotel-amenities {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
            gap: 10px;
        }

        .amenity-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: #f0f0f0;
            border-radius: 50%;
            color: #666;
            transition: all 0.3s ease;
        }
        
        .amenity-icon.active {
            background: #e9b732;
            color: #fff;
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

        .hotel-info {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-right: 15px;
            margin-bottom: 8px;
            color: #666;
            font-size: 14px;
        }

        .info-item i {
            color: #e9b732;
            margin-right: 5px;
        }

        .hotel-description {
            margin-bottom: 15px;
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .hotel-price {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .hotel-link {
            display: inline-block;
            padding: 10px 25px;
            background: #e9b732;
            color: #fff;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .hotel-link:hover {
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

        /* Filter Bar */
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
            align-items: flex-start;
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
        
        .filter-checkbox-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-checkbox-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 14px;
        }
        
        .checkbox-items {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
        }
        
        .checkbox-item input {
            margin-right: 5px;
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

        /* Rating */
        .hotel-rating {
            display: flex;
            margin-bottom: 15px;
        }
        
        .star {
            color: #ffc107;
            font-size: 16px;
            margin-right: 2px;
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

        /* Responsive Media Queries */
        @media (max-width: 992px) {
            .hotels-grid {
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
            
            .hotels-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 576px) {
            .hotels-grid {
                grid-template-columns: 1fr;
            }
            
            .hotel-card {
                max-width: 400px;
                margin: 0 auto;
            }
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
                <h2>Hôtels & Hébergements</h2>
                <p>Découvrez les meilleurs hôtels et hébergements disponibles à Adjarra et ses environs. Trouvez l'établissement parfait pour votre séjour.</p>
            </div>

            <div class="filter-bar">
                <form class="filter-form" method="GET" action="hotels.php">
                    <div class="filter-group">
                        <label for="category">Catégorie</label>
                        <select id="category" name="category" class="filter-control">
                            <option value="">Toutes les catégories</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>">
                                    <?php echo htmlspecialchars($category); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="region">Région</label>
                        <select id="region" name="region" class="filter-control">
                            <option value="">Toutes les régions</option>
                            <?php foreach($regions as $region): ?>
                                <option value="<?php echo htmlspecialchars($region); ?>">
                                    <?php echo htmlspecialchars($region); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="city">Ville</label>
                        <select id="city" name="city" class="filter-control">
                            <option value="">Toutes les villes</option>
                            <?php foreach($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city); ?>">
                                    <?php echo htmlspecialchars($city); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="price_range">Gamme de prix</label>
                        <select id="price_range" name="price_range" class="filter-control">
                            <option value="">Tous les prix</option>
                            <option value="économique">Économique</option>
                            <option value="modéré">Modéré</option>
                            <option value="premium">Premium</option>
                            <option value="luxe">Luxe</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="search">Recherche</label>
                        <input type="text" id="search" name="search" class="filter-control" placeholder="Nom, adresse, ville...">
                    </div>
                    <div class="filter-checkbox-group">
                        <label>Commodités</label>
                        <div class="checkbox-items">
                            <div class="checkbox-item">
                                <input type="checkbox" id="wifi" name="wifi" value="1">
                                <label for="wifi">WiFi</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="parking" name="parking" value="1">
                                <label for="parking">Parking</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="pool" name="pool" value="1">
                                <label for="pool">Piscine</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="restaurant" name="restaurant" value="1">
                                <label for="restaurant">Restaurant</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="ac" name="ac" value="1">
                                <label for="ac">Climatisation</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="conference" name="conference" value="1">
                                <label for="conference">Salle de conférence</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="filter-button">Filtrer</button>
                </form>
            </div>

            <?php if(isset($errorMsg)): ?>
                <div class="alert alert-danger">
                    <?php echo $errorMsg; ?>
                </div>
            <?php elseif(empty($hotels)): ?>
                <div class="empty-state">
                    <i class="fas fa-hotel"></i>
                    <h3>Aucun hôtel trouvé</h3>
                    <p>Il n'y a actuellement aucun hôtel correspondant à vos critères de recherche.</p>
                </div>
            <?php else: ?>
                <div class="hotels-grid">
                    <?php foreach($hotels as $hotel): ?>
                        <?php 
                            // Récupérer les photos de l'hôtel
                            $photo = '/api/placeholder/400/300';
                            if(!empty(safeGet($hotel, 'photo_files'))) {
                                // Si les photos sont stockées au format JSON, on récupère la première
                                $photoFiles = json_decode($hotel['photo_files'], true);
                                if(is_array($photoFiles) && !empty($photoFiles[0])) {
                                    $photo = $photoFiles[0];
                                }
                            }
                            
                            // Récupérer la catégorie de l'hôtel
                            $category = safeGet($hotel, 'hotel_category', 'Standard');
                            
                            // Récupérer l'année de création
                            $creationYear = !empty(safeGet($hotel, 'creation_year')) ? intval($hotel['creation_year']) : null;
                            
                            // Générer une note aléatoire pour démonstration
                            $rating = mt_rand(35, 50) / 10;
                        ?>
                        <div class="hotel-card">
                            <div class="hotel-image">
                                <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars(safeGet($hotel, 'hotel_name')); ?>">
                                <?php if(!empty($category)): ?>
                                <div class="hotel-category"><?php echo htmlspecialchars($category); ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="hotel-details">
                                <h3 class="hotel-name"><?php echo htmlspecialchars(safeGet($hotel, 'hotel_name')); ?></h3>
                                <div class="hotel-rating">
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
                                    
                                    <span style="margin-left: 5px; font-size: 14px; color: #666;">(<?php echo number_format($rating, 1); ?>)</span>
                                </div>
                                <div class="hotel-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars(safeGet($hotel, 'address')); ?>, 
                                    <?php echo htmlspecialchars(safeGet($hotel, 'city')); ?>
                                    <?php if(!empty(safeGet($hotel, 'region'))): ?>
                                        - <?php echo htmlspecialchars($hotel['region']); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="hotel-amenities">
                                    <div class="amenity-icon <?php echo safeGet($hotel, 'has_wifi') ? 'active' : ''; ?>" title="WiFi">
                                        <i class="fas fa-wifi"></i>
                                    </div>
                                    <div class="amenity-icon <?php echo safeGet($hotel, 'has_parking') ? 'active' : ''; ?>" title="Parking">
                                        <i class="fas fa-parking"></i>
                                    </div>
                                    <div class="amenity-icon <?php echo safeGet($hotel, 'has_pool') ? 'active' : ''; ?>" title="Piscine">
                                        <i class="fas fa-swimming-pool"></i>
                                    </div>
                                    <div class="amenity-icon <?php echo safeGet($hotel, 'has_restaurant') ? 'active' : ''; ?>" title="Restaurant">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                    <div class="amenity-icon <?php echo safeGet($hotel, 'has_ac') ? 'active' : ''; ?>" title="Climatisation">
                                        <i class="fas fa-snowflake"></i>
                                    </div>
                                    <div class="amenity-icon <?php echo safeGet($hotel, 'has_conference') ? 'active' : ''; ?>" title="Salle de conférence">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                </div>
                                
                                <div class="hotel-info">
                                    <div class="info-item">
                                        <i class="fas fa-bed"></i> <?php echo safeGet($hotel, 'room_count'); ?> chambres
                                    </div>
                                    <?php if(!empty($creationYear)): ?>
                                    <div class="info-item">
                                        <i class="fas fa-building"></i> Établi en <?php echo $creationYear; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if(!empty(safeGet($hotel, 'description'))): ?>
                                <div class="hotel-description">
                                    <?php echo htmlspecialchars(substr($hotel['description'], 0, 150)) . (strlen($hotel['description']) > 150 ? '...' : ''); ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="hotel-price">
                                    <span>Tarif:</span> 
                                    <?php echo htmlspecialchars(safeGet($hotel, 'price_range', 'Sur demande')); ?>
                                </div>
                                
                               
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination - Pour une implémentation future -->
                <ul class="pagination">
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer - Simple version -->
    <footer style="background-color: #333; color: #fff; padding: 30px 0; text-align: center; margin-top: 50px;">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Explorer Adjarra. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Optional JavaScript for enhancing filter interactions -->
    <script>
        // Pour un meilleur UX, réinitialiser les valeurs des filtres avec celles de la requête actuelle
        document.addEventListener('DOMContentLoaded', function() {
            // Récupérer les paramètres de l'URL
            const urlParams = new URLSearchParams(window.location.search);
            
            // Définir les valeurs des champs de sélection
            if (urlParams.has('category')) {
                document.getElementById('category').value = urlParams.get('category');
            }
            
            if (urlParams.has('region')) {
                document.getElementById('region').value = urlParams.get('region');
            }
            
            if (urlParams.has('city')) {
                document.getElementById('city').value = urlParams.get('city');
            }
            
            if (urlParams.has('price_range')) {
                document.getElementById('price_range').value = urlParams.get('price_range');
            }
            
            if (urlParams.has('search')) {
                document.getElementById('search').value = urlParams.get('search');
            }
            
            // Cocher les cases à cocher appropriées
            if (urlParams.has('wifi')) {
                document.getElementById('wifi').checked = true;
            }
            
            if (urlParams.has('parking')) {
                document.getElementById('parking').checked = true;
            }
            
            if (urlParams.has('pool')) {
                document.getElementById('pool').checked = true;
            }
            
            if (urlParams.has('restaurant')) {
                document.getElementById('restaurant').checked = true;
            }
            
            if (urlParams.has('ac')) {
                document.getElementById('ac').checked = true;
            }
            
            if (urlParams.has('conference')) {
                document.getElementById('conference').checked = true;
            }
        });
    </script>
</body>
</html>