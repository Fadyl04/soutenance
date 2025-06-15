<?php
include 'database/db.php';

// Vérifier que l'ID du circuit est fourni
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$circuit_id = intval($_GET['id']);

// Récupérer les détails du circuit
try {
    $stmt = $db->prepare("SELECT c.*, 
                          h.hotel_name, h.address AS hotel_address, h.city AS hotel_city, h.hotel_category,
                          CONCAT(g.first_name, ' ', g.last_name) AS guide_name, g.specializations AS guide_speciality,
                          CONCAT(t.first_name, ' ', t.last_name) AS transporter_name, t.vehicle_type
                          FROM circuits c 
                          LEFT JOIN hotels h ON c.hotel_id = h.id 
                          LEFT JOIN guides g ON c.guide_id = g.id 
                          LEFT JOIN transporters t ON c.transporter_id = t.id 
                          WHERE c.id = :circuit_id AND c.status = 'active'");
    $stmt->bindParam(':circuit_id', $circuit_id);
    $stmt->execute();
    $circuit = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$circuit) {
        header("Location: index.php?error=circuit_not_found");
        exit();
    }
    
    // Récupérer les sites touristiques associés au circuit
    $stmt = $db->prepare("SELECT s.* FROM sites_touristiques s 
                          INNER JOIN circuit_sites cs ON s.id = cs.site_id 
                          WHERE cs.circuit_id = :circuit_id 
                          ORDER BY s.nom");
    $stmt->bindParam(':circuit_id', $circuit_id);
    $stmt->execute();
    $sites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculer la durée du séjour
    $start_date = new DateTime($circuit['start_date']);
    $end_date = new DateTime($circuit['end_date']);
    $interval = $start_date->diff($end_date);
    $days_difference = $interval->days + 1; // +1 pour inclure le jour de début
    
} catch(PDOException $e) {
    header("Location: index.php?error=database");
    exit();
}

// Récupérer le prix minimum pour affichage
$min_price = min($circuit['price_economy'], $circuit['price_comfort'], $circuit['price_premium']);

// Correspondance entre catégories et icônes (comme dans la page sites)
$category_icons = [
    'naturel' => 'bi-tree',
    'historique' => 'bi-bank',
    'culturel' => 'bi-palette',
    'religieux' => 'bi-stars',
    'loisir' => 'bi-water'
];

// Titre de la page
$page_title = "Circuit : " . htmlspecialchars($circuit['name']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Circuits Touristiques du Bénin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles/main.css">
    <style>
        /* Styles spécifiques à la page détail */
        .circuit-header {
            background-size: cover;
            background-position: center;
            color: white;
            position: relative;
            padding: 100px 0;
            margin-bottom: 30px;
        }
        
        .circuit-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        
        .circuit-header .container {
            position: relative;
            z-index: 2;
        }
        
        .price-tag {
            background: #e9b732;
            color: #fff;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: inline-block;
            margin-bottom: 15px;
        }
        
        .circuit-info {
            margin-bottom: 30px;
        }
        
        .info-badge {
            background: #f8f9fa;
            border-radius: 50px;
            padding: 8px 15px;
            margin-right: 10px;
            margin-bottom: 10px;
            display: inline-block;
            font-size: 14px;
            color: #555;
        }
        
        .info-badge i {
            margin-right: 5px;
            color: #e9b732;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 25px;
            font-weight: 700;
            text-align: center;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            display: block;
            width: 50px;
            height: 3px;
            background: #e9b732;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
        
        /* Styles de package améliorés */
        .package-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: none;
        }
        
        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        
        .package-header {
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .economy-header {
            background: linear-gradient(135deg, #6c757d, #495057);
        }
        
        .comfort-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }
        
        .premium-header {
            background: linear-gradient(135deg, #e9b732, #d4a026);
        }
        
        .package-price {
            font-size: 28px;
            font-weight: 700;
        }
        
        .package-body {
            padding: 25px;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .feature-list li i {
            color: #28a745;
            margin-right: 10px;
        }
        
        /* Style de la carte de site amélioré (inspiré de la page des sites) */        
        .site-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            margin-bottom: 20px;
            height: 100%;
            background: #fff;
            border: none;
        }
        
        .site-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .site-img {
            height: 250px;
            overflow: hidden;
            position: relative;
        }
        
        .site-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .site-card:hover .site-img img {
            transform: scale(1.1);
        }
        
        .site-info {
            padding: 25px 20px;
        }
        
        .site-name {
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 20px;
            color: #333;
        }
        
        .site-location {
            color: #555;
            margin-bottom: 15px;
            font-size: 14px;
            display: inline-block;
            background-color: #e9b732;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        .site-category {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            color: white;
            background: #3498db;
        }
        
        .site-card .read-more {
            display: inline-block;
            background: #e9b732;
            color: #fff;
            padding: 8px 22px;
            border-radius: 50px;
            transition: 0.3s;
            text-decoration: none;
            font-weight: 500;
            margin-top: 10px;
        }
        
        .site-card .read-more:hover {
            background: #d4a026;
            color: #fff;
        }
        
        .booking-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: sticky;
            top: 20px;
        }
        
        .cta-btn {
            display: block;
            width: 100%;
            padding: 15px;
            text-align: center;
            background: #e9b732;
            color: white;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            transition: all 0.3s ease;
            border: none;
            margin-top: 20px;
            box-shadow: 0 5px 15px rgba(233, 183, 50, 0.3);
        }
        
        .cta-btn:hover {
            background: #d4a026;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(233, 183, 50, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .providers-section {
            background: #f8f9fa;
            padding: 40px 0;
            margin: 40px 0;
        }
        
        .provider-card {
            padding: 20px;
            border-radius: 10px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            height: 100%;
            transition: all 0.3s ease;
        }
        
        .provider-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .provider-title {
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-weight: 600;
            color: #333;
        }
        
        .provider-info p {
            margin-bottom: 8px;
            color: #666;
        }
        
        .provider-info i {
            width: 20px;
            color: #e9b732;
            margin-right: 5px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            .booking-card {
                position: relative;
                top: 0;
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>


<!-- En-tête du circuit avec image de fond -->
<section class="circuit-header" style="background-image: url('<?php echo htmlspecialchars($circuit['image_url'] ?? 'images/default-circuit.jpg'); ?>');">
    <div class="container">
        <a href="index.php?page=circuits" class="btn btn-light mb-4"><i class="fas fa-arrow-left me-2"></i> Retour aux circuits</a>
        <h1><?php echo htmlspecialchars($circuit['name']); ?></h1>
        <div class="price-tag">
            À partir de <?php echo number_format($min_price, 0, ',', ' '); ?> CFA / personne
        </div>
        <div class="circuit-info">
            <span class="info-badge"><i class="fas fa-calendar-alt"></i> Du <?php echo date('d/m/Y', strtotime($circuit['start_date'])); ?> au <?php echo date('d/m/Y', strtotime($circuit['end_date'])); ?></span>
            <span class="info-badge"><i class="fas fa-clock"></i> <?php echo $days_difference; ?> jours</span>
            <span class="info-badge"><i class="fas fa-users"></i> <?php echo $circuit['max_participants']; ?> participants max.</span>
            <?php if(!empty($circuit['guide_name'])): ?>
            <span class="info-badge"><i class="fas fa-user-tie"></i> Guide: <?php echo htmlspecialchars($circuit['guide_name']); ?></span>
            <?php endif; ?>
        </div>
    </div>
</section>

<main>
    <div class="container">
        <div class="row">
            <!-- Contenu principal -->
            <div class="col-lg-8">
                <!-- Description -->
                <section class="mb-5">
                    <h2 class="section-title">Description du circuit</h2>
                    <div class="description">
                        <?php echo nl2br(htmlspecialchars($circuit['description'])); ?>
                    </div>
                </section>
                
                <!-- Sites touristiques -->
                <section class="mb-5">
                    <h2 class="section-title">Sites touristiques à découvrir</h2>
                    <div class="row">
                        <?php if(empty($sites)): ?>
                            <div class="col-12">
                                <div class="alert alert-info">Aucun site touristique spécifié pour ce circuit.</div>
                            </div>
                        <?php else: ?>
                            <?php foreach($sites as $site): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="site-card">
                                        
                                        <div class="site-info">
                                            <span class="site-location"><i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($site['localisation']); ?></span>
                                            <h4 class="site-name"><?php echo htmlspecialchars($site['nom']); ?></h4>
                                            <?php if(!empty($site['categorie'])): ?>
                                            <span class="site-category">
                                                <i class="bi <?php echo $category_icons[$site['categorie']] ?? 'bi-geo-alt'; ?>"></i>
                                                <?php echo htmlspecialchars($site['categorie']); ?>
                                            </span>
                                            <?php endif; ?>
                                            <p class="mt-3"><?php echo htmlspecialchars(substr($site['description'], 0, 100) . (strlen($site['description']) > 100 ? '...' : '')); ?></p>
                                            <a href="detail-site.php?id=<?php echo $site['id']; ?>" class="read-more">En savoir plus</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>
                
                <!-- Formules tarifaires -->
                <section class="mb-5">
                    <h2 class="section-title">Nos formules</h2>
                    <div class="row">
                        <!-- Formule Économique -->
                        <div class="col-md-4 mb-4">
                            <div class="package-card">
                                <div class="package-header economy-header">
                                    <h3>Économique</h3>
                                    <div class="package-price"><?php echo number_format($circuit['price_economy'], 0, ',', ' '); ?> CFA</div>
                                    <p>par personne</p>
                                </div>
                                <div class="package-body">
                                    <ul class="feature-list">
                                        <li><i class="fas fa-check"></i> Hébergement standard</li>
                                        <li><i class="fas fa-check"></i> Transport collectif</li>
                                        <li><i class="fas fa-check"></i> Guide pour visites principales</li>
                                        <li><i class="fas fa-times text-danger"></i> Repas non inclus</li>
                                        <li><i class="fas fa-times text-danger"></i> Activités supplémentaires</li>
                                    </ul>
                                    
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formule Confort -->
                        <div class="col-md-4 mb-4">
                            <div class="package-card">
                                <div class="package-header comfort-header">
                                    <h3>Confort</h3>
                                    <div class="package-price"><?php echo number_format($circuit['price_comfort'], 0, ',', ' '); ?> CFA</div>
                                    <p>par personne</p>
                                </div>
                                <div class="package-body">
                                    <ul class="feature-list">
                                        <li><i class="fas fa-check"></i> Hébergement 3-4 étoiles</li>
                                        <li><i class="fas fa-check"></i> Transport semi-privé</li>
                                        <li><i class="fas fa-check"></i> Guide pour toutes les visites</li>
                                        <li><i class="fas fa-check"></i> Petits déjeuners inclus</li>
                                        <li><i class="fas fa-times text-danger"></i> Activités VIP</li>
                                    </ul>
                                    
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formule Premium -->
                        <div class="col-md-4 mb-4">
                            <div class="package-card">
                                <div class="package-header premium-header">
                                    <h3>Premium</h3>
                                    <div class="package-price"><?php echo number_format($circuit['price_premium'], 0, ',', ' '); ?> CFA</div>
                                    <p>par personne</p>
                                </div>
                                <div class="package-body">
                                    <ul class="feature-list">
                                        <li><i class="fas fa-check"></i> Hébergement 4-5 étoiles</li>
                                        <li><i class="fas fa-check"></i> Transport privé</li>
                                        <li><i class="fas fa-check"></i> Guide personnel dédié</li>
                                        <li><i class="fas fa-check"></i> Tous les repas inclus</li>
                                        <li><i class="fas fa-check"></i> Activités exclusives</li>
                                    </ul>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Carte de réservation rapide -->
                <div class="booking-card mb-4">
                    <h3 class="mb-3">Réservez ce circuit</h3>
                    <p>Places disponibles, réservez maintenant pour garantir votre participation à ce circuit exceptionnel.</p>
                    
                    <form action="reservation.php" method="get">
                        <input type="hidden" name="circuit" value="<?php echo $circuit_id; ?>">
                        
                       
                        <a href="index.php?page=reservation&circuit=<?= $circuit['id'] ?>" class="cta-btn"> Réserver maintenant</a>
                        
                    </form>
                    
                    <div class="mt-4">
                        <p class="mb-2"><i class="fas fa-info-circle me-2 text-primary"></i> Réservation sans engagement</p>
                        <p class="mb-2"><i class="fas fa-exchange-alt me-2 text-primary"></i> Annulation flexible</p>
                        <p class="mb-0"><i class="fas fa-lock me-2 text-primary"></i> Paiement sécurisé</p>
                    </div>
                </div>
                
                <!-- Partage sur les réseaux sociaux -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="mb-3">Partager ce circuit</h4>
                        <div class="d-flex justify-content-between">
                            <a href="#" class="btn btn-outline-primary"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-outline-info"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-outline-success"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" class="btn btn-outline-danger"><i class="fab fa-pinterest"></i></a>
                            <a href="#" class="btn btn-outline-secondary"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
                
                <!-- Informations de contact -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Besoin d'aide ?</h4>
                        <p><i class="fas fa-phone me-2 text-primary"></i> +229 XX XX XX XX</p>
                        <p><i class="fas fa-envelope me-2 text-primary"></i> info@circuitsbenin.com</p>
                        <p class="mb-0"><i class="fas fa-comment me-2 text-primary"></i> Chat en ligne disponible</p>
                        <a href="contact.php" class="btn btn-outline-primary mt-3 w-100">Nous contacter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section prestataires -->
    <section class="providers-section">
        <div class="container">
            <h2 class="section-title text-center mb-5">Nos prestataires pour ce circuit</h2>
            <div class="row">
                <!-- Guide -->
                <div class="col-md-4 mb-4">
                    <div class="provider-card">
                        <h4 class="provider-title"><i class="fas fa-user-tie me-2 text-primary"></i> Guide</h4>
                        <div class="provider-info">
                            <?php if(!empty($circuit['guide_name'])): ?>
                                <p><i class="fas fa-user"></i> <strong><?php echo htmlspecialchars($circuit['guide_name']); ?></strong></p>
                                <?php if(!empty($circuit['guide_speciality'])): ?>
                                <p><i class="fas fa-star"></i> Spécialité: <?php echo htmlspecialchars($circuit['guide_speciality']); ?></p>
                                <?php endif; ?>
                                <p>Guide professionnel avec une excellente connaissance de la région et de son patrimoine culturel.</p>
                            <?php else: ?>
                                <p>Les informations sur le guide seront communiquées ultérieurement.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Hébergement -->
                <div class="col-md-4 mb-4">
                    <div class="provider-card">
                        <h4 class="provider-title"><i class="fas fa-hotel me-2 text-primary"></i> Hébergement</h4>
                        <div class="provider-info">
                            <?php if(!empty($circuit['hotel_name'])): ?>
                                <p><i class="fas fa-building"></i> <strong><?php echo htmlspecialchars($circuit['hotel_name']); ?></strong></p>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($circuit['hotel_city'] ?? 'Localisation non précisée'); ?></p>
                                <?php if(!empty($circuit['hotel_category'])): ?>
                                <p><i class="fas fa-star"></i> 
                                <?php 
                                    for($i = 0; $i < $circuit['hotel_category']; $i++) {
                                        echo '⭐';
                                    }
                                ?>
                                </p>
                                <?php endif; ?>
                                <p>Hébergement confortable sélectionné pour son excellent rapport qualité-prix et son emplacement.</p>
                            <?php else: ?>
                                <p>Les informations sur l'hébergement seront communiquées lors de la réservation.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Transport -->
                <div class="col-md-4 mb-4">
                    <div class="provider-card">
                        <h4 class="provider-title"><i class="fas fa-bus me-2 text-primary"></i> Transport</h4>
                        <div class="provider-info">
                            <?php if(!empty($circuit['transporter_name'])): ?>
                                <p><i class="fas fa-user"></i> <strong><?php echo htmlspecialchars($circuit['transporter_name']); ?></strong></p>
                                <?php if(!empty($circuit['vehicle_type'])): ?>
                                <p><i class="fas fa-bus"></i> Type de véhicule: <?php echo htmlspecialchars($circuit['vehicle_type']); ?></p>
                                <?php endif; ?>
                                <p>Transport confortable et sécurisé pour toutes vos excursions pendant le circuit.</p>
                            <?php else: ?>
                                <p>Les informations sur le transport seront communiquées ultérieurement.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Appel à l'action final -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="mb-4">Prêt à vivre cette aventure ?</h2>
            <p class="lead mb-4">Ne manquez pas cette opportunité de découvrir les merveilles du Bénin avec ce circuit exceptionnel.</p>
            <a href="index.php?page=reservation&circuit=<?= $circuit['id'] ?>" class="btn btn-lg cta-btn" style="max-width: 300px; margin: 0 auto;">Réserver maintenant</a>
        </div>
    </section>
</main>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Ajouter des scripts personnalisés ici
    document.addEventListener('DOMContentLoaded', function() {
        // Exemple: Animation pour les boutons de réservation
        const ctaButtons = document.querySelectorAll('.cta-btn');
        
        ctaButtons.forEach(button => {
            button.addEventListener('mouseover', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            button.addEventListener('mouseout', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
</body>
</html>