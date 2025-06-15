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
    
    // Filtrer par specialisation
    if(!empty($_GET['specialization'])) {
        $whereConditions[] = "specializations LIKE :specializations";
        $params[':specializations'] = '%' . $_GET['specialization'] . '%';
    }
    
    // Filtrer par région
    if(!empty($_GET['region'])) {
        $whereConditions[] = "regions LIKE :regions";
        $params[':regions'] = '%' . $_GET['region'] . '%';
    }
    
    // Filtrer par langue
    if(!empty($_GET['language'])) {
        $whereConditions[] = "languages LIKE :languages";
        $params[':languages'] = '%' . $_GET['language'] . '%';
    }
    
    // Recherche par nom ou autre info
    if(!empty($_GET['search'])) {
        $whereConditions[] = "(first_name LIKE :search OR last_name LIKE :search OR username LIKE :search OR city LIKE :search OR biography LIKE :search)";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }
    
    // Construire la requête SQL
    $sql = "SELECT * FROM guides WHERE " . implode(" AND ", $whereConditions);
    
    $stmt = $conn->prepare($sql);
    foreach($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $errorMsg = "Erreur de connexion à la base de données : " . $e->getMessage();
}

// Fonction utilitaire pour accéder aux clés de façon sécurisée
function safeGet($array, $key, $default = '') {
    return isset($array[$key]) ? $array[$key] : $default;
}

// Fonction pour convertir texte en tableau (pour spécialisations, langues, régions, etc.)
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
    <title>Guides Touristiques - Adjarra</title>
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

        /* Guides Grid */
        .guides-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .guide-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .guide-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12);
        }

        .guide-image {
            height: 250px;
            position: relative;
            overflow: hidden;
        }

        .guide-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .guide-card:hover .guide-image img {
            transform: scale(1.1);
        }

        .guide-experience {
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

        .guide-details {
            padding: 20px;
        }

        .guide-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }

        .guide-location {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #666;
        }

        .guide-location i {
            color: #e9b732;
            margin-right: 8px;
        }

        .guide-specializations {
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

        .guide-languages {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .language-item {
            display: flex;
            align-items: center;
            margin-right: 15px;
            margin-bottom: 8px;
            color: #666;
            font-size: 14px;
        }

        .language-item i {
            color: #e9b732;
            margin-right: 5px;
        }

        .guide-bio {
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

        .guide-rate {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .guide-rate span {
            color: #e9b732;
        }

        .guide-link {
            display: inline-block;
            padding: 10px 25px;
            background: #e9b732;
            color: #fff;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .guide-link:hover {
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

        /* Rating */
        .guide-rating {
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
            .guides-grid {
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
            
            .guides-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 576px) {
            .guides-grid {
                grid-template-columns: 1fr;
            }
            
            .guide-card {
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
                <h2>Nos Guides Touristiques</h2>
                <p>Découvrez les guides disponibles pour vous faire découvrir Adjarra et ses environs. Bénéficiez de l'expertise locale et vivez une expérience authentique.</p>
            </div>

            <div class="filter-bar">
                <form class="filter-form" method="GET" action="guides.php">
                    <div class="filter-group">
                        <label for="specialization">Spécialisation</label>
                        <select id="specialization" name="specialization" class="filter-control">
                            <option value="">Toutes les spécialisations</option>
                            <option value="culturel">Guide culturel</option>
                            <option value="nature">Nature et écologie</option>
                            <option value="gastronomie">Gastronomie locale</option>
                            <option value="histoire">Histoire et patrimoine</option>
                            <option value="aventure">Aventure et sport</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="region">Région</label>
                        <select id="region" name="region" class="filter-control">
                            <option value="">Toutes les régions</option>
                            <option value="adjarra">Adjarra centre</option>
                            <option value="aglogbe">Aglogbe</option>
                            <option value="mededjonou">Médédjonou</option>
                            <option value="honvie">Honvié</option>
                            <option value="porto-novo">Porto-Novo</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="language">Langue</label>
                        <select id="language" name="language" class="filter-control">
                            <option value="">Toutes les langues</option>
                            <option value="francais">Français</option>
                            <option value="anglais">Anglais</option>
                            <option value="fon">Fon</option>
                            <option value="yoruba">Yoruba</option>
                            <option value="goun">Goun</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="search">Recherche</label>
                        <input type="text" id="search" name="search" class="filter-control" placeholder="Nom, lieu, expertise...">
                    </div>
                    <button type="submit" class="filter-button">Filtrer</button>
                </form>
            </div>

            <?php if(isset($errorMsg)): ?>
                <div class="alert alert-danger">
                    <?php echo $errorMsg; ?>
                </div>
            <?php elseif(empty($guides)): ?>
                <div class="empty-state">
                    <i class="fas fa-map-signs"></i>
                    <h3>Aucun guide touristique trouvé</h3>
                    <p>Il n'y a actuellement aucun guide touristique approuvé dans notre base de données.</p>
                </div>
            <?php else: ?>
                <div class="guides-grid">
                    <?php foreach($guides as $guide): ?>
                        <?php 
                            // Récupérer la photo du profil
                            $photo = '/api/placeholder/400/300';
                            if(!empty(safeGet($guide, 'profile_photo'))) {
                                $photo = $guide['profile_photo'];
                            }
                            
                            // Récupérer les spécialisations
                            $specializations = [];
                            if(!empty(safeGet($guide, 'specializations'))) {
                                $specializations = textToArray($guide['specializations']);
                            }
                            
                            // Récupérer les langues
                            $languages = [];
                            if(!empty(safeGet($guide, 'languages'))) {
                                $languages = textToArray($guide['languages']);
                            }
                            
                            // Récupérer les régions
                            $regions = [];
                            if(!empty(safeGet($guide, 'regions'))) {
                                $regions = textToArray($guide['regions']);
                            }
                            
                            // Obtenir l'expérience
                            $experience = !empty(safeGet($guide, 'experience_years')) ? intval($guide['experience_years']) : 0;
                            
                            // Rating (générer un aléatoire entre 3.5 et 5 pour démonstration)
                            $rating = mt_rand(35, 50) / 10;
                            
                            // Construction du nom complet
                            $fullName = safeGet($guide, 'first_name', 'Anonyme') . ' ' . safeGet($guide, 'last_name', '');
                        ?>
                        <div class="guide-card">
                            <div class="guide-image">
                                <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($fullName); ?>">
                                <div class="guide-experience"><?php echo $experience; ?> an<?php echo $experience > 1 ? 's' : ''; ?> d'expérience</div>
                            </div>
                            <div class="guide-details">
                                <h3 class="guide-name"><?php echo htmlspecialchars($fullName); ?></h3>
                                <div class="guide-rating">
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
                                <div class="guide-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars(safeGet($guide, 'city', 'Adjarra')); ?>
                                </div>
                                
                                <?php if(!empty($specializations)): ?>
                                <div class="guide-specializations">
                                    <?php foreach($specializations as $specialization): ?>
                                        <?php
                                            $badgeClass = 'badge-primary';
                                            if(stripos($specialization, 'nature') !== false || stripos($specialization, 'écolo') !== false) {
                                                $badgeClass = 'badge-success';
                                            } elseif(stripos($specialization, 'gastro') !== false || stripos($specialization, 'culinaire') !== false) {
                                                $badgeClass = 'badge-warning';
                                            }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars(ucfirst($specialization)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if(!empty($languages)): ?>
                                <div class="guide-languages">
                                    <?php 
                                        $langShown = 0;
                                        foreach($languages as $language): 
                                            if($langShown >= 3) break; // Limiter à 3 langues affichées
                                            $langShown++;
                                    ?>
                                        <div class="language-item"><i class="fas fa-comments"></i> <?php echo htmlspecialchars(ucfirst($language)); ?></div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                                
                                <?php if(!empty(safeGet($guide, 'biography'))): ?>
                                <div class="guide-bio">
                                    <?php echo htmlspecialchars(substr($guide['biography'], 0, 150)) . (strlen($guide['biography']) > 150 ? '...' : ''); ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="guide-rate">
                                    <span>Tarif:</span> 
                                    <?php 
                                        if(!empty(safeGet($guide, 'daily_rate'))) {
                                            echo number_format($guide['daily_rate'], 0, ',', ' ') . ' FCFA/jour';
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
            
            const specializationParam = urlParams.get('specialization');
            if(specializationParam) {
                document.getElementById('specialization').value = specializationParam;
            }
            
            const regionParam = urlParams.get('region');
            if(regionParam) {
                document.getElementById('region').value = regionParam;
            }
            
            const languageParam = urlParams.get('language');
            if(languageParam) {
                document.getElementById('language').value = languageParam;
            }
            
            const searchParam = urlParams.get('search');
            if(searchParam) {
                document.getElementById('search').value = searchParam;
            }
        });
    </script>
</body>
</html>