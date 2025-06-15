<?php
// Connexion à la base de données
include 'database/db.php';

// Récupération de tous les sites touristiques pour la page d'accueil
$query = $db->query("SELECT * FROM sites_touristiques ORDER BY id DESC LIMIT 6");
$sites = $query->fetchAll(PDO::FETCH_ASSOC);

// Définition des catégories pour l'affichage
$categories = [
    'Monument' => 'Monuments',
    'Musée' => 'Musées',
    'Parc' => 'Parcs',
    'Plage' => 'Plages',
    'Site historique' => 'Sites Historiques',
    'Site naturel' => 'Sites Naturels',
    'Autre' => 'Autres sites'
];

// Correspondance entre catégories et icônes
$category_icons = [
    'Monument' => 'bi-building-fill',
    'Musée' => 'bi-bank',
    'Parc' => 'bi-tree-fill',
    'Plage' => 'bi-water',
    'Site historique' => 'bi-hourglass-split',
    'Site naturel' => 'bi-flower1',
    'Autre' => 'bi-geo-alt-fill'
];

// Correspondance entre régions et classes de couleurs
$region_colors = [
    'Cotonou' => 'bg-primary',
    'Porto-Novo' => 'bg-success',
    'Abomey' => 'bg-danger',
    'Ouidah' => 'bg-warning',
    'Parakou' => 'bg-info',
    'Natitingou' => 'bg-secondary',
    'Bohicon' => 'bg-dark',
    'Dassa-Zoumé' => 'bg-primary-subtle',
    'Grand-Popo' => 'bg-success-subtle',
    'Djougou' => 'bg-danger-subtle'
];

// Fonction pour obtenir une couleur de région
function getRegionColor($region, $region_colors) {
    // Si la région existe dans notre mapping, on utilise la couleur correspondante
    if (isset($region_colors[$region])) {
        return $region_colors[$region];
    }
    // Sinon on utilise une couleur par défaut
    return 'bg-secondary';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Découvrez le Bénin - Sites Touristiques</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #009245;
            --secondary-color: #e94e1b;
            --accent-color: #fcb912;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-color);
            background-color: #f9f9f9;
        }
        
        /* Hero Section */
        #hero {
            width: 100%;
            height: 80vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/benin-hero.jpg') center/cover no-repeat;
            position: relative;
            color: white;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #hero .container {
            position: relative;
            z-index: 2;
        }
        
        #hero h1 {
            margin: 0;
            font-size: 3.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            color: #fff;
        }
        
        #hero h2 {
            color: #eee;
            margin: 20px 0 0 0;
            font-size: 1.5rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        .btn-custom {
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary-custom:hover {
            background-color: #007d3c;
            border-color: #007d3c;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        
        .btn-secondary-custom {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }
        
        .btn-secondary-custom:hover {
            background-color: #d03d0f;
            border-color: #d03d0f;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        
        /* Main Content */
        .section-padding {
            padding: 80px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 20px;
            position: relative;
            color: var(--primary-color);
            display: inline-block;
        }
        
        .section-title h2::after {
            content: '';
            position: absolute;
            display: block;
            width: 80px;
            height: 4px;
            background: var(--secondary-color);
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .section-title p {
            font-size: 1.1rem;
            color: #555;
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Site Cards */
        .site-card {
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            background: #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            height: 100%;
        }
        
        .site-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .site-img {
            position: relative;
            overflow: hidden;
            height: 250px;
        }
        
        .site-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .site-card:hover .site-img img {
            transform: scale(1.1);
        }
        
        .category-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 8px 15px;
            border-radius: 30px;
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            z-index: 2;
        }
        
        .site-info {
            padding: 25px;
            position: relative;
        }
        
        .site-info h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--dark-color);
        }
        
        .site-info .region {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .site-info p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.7;
        }
        
        .site-meta {
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 1px solid #eee;
            color: #777;
            font-size: 0.85rem;
        }
        
        .read-more {
            display: inline-block;
            background: var(--secondary-color);
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            transition: all 0.3s;
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }
        
        .read-more:hover {
            background: #d03d0f;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        
        /* Filter Buttons */
        .filter-container {
            margin-bottom: 40px;
        }
        
        .filter-btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 30px;
            background: #fff;
            color: var(--dark-color);
            margin: 0 5px 10px;
            transition: all 0.3s;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
        }
        
        .filter-btn i {
            margin-right: 8px;
        }
        
        .filter-btn.active, .filter-btn:hover {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 5px 15px rgba(0, 146, 69, 0.3);
        }
        
        /* Category boxes */
        .category-box {
            text-align: center;
            padding: 40px 20px;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .category-box::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            top: -100%;
            left: 0;
            background: var(--primary-color);
            transition: all 0.4s ease;
            z-index: -1;
        }
        
        .category-box:hover::before {
            top: 0;
        }
        
        .category-box i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            transition: all 0.4s ease;
        }
        
        .category-box h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--dark-color);
            transition: all 0.4s ease;
        }
        
        .category-box p {
            color: #666;
            margin-bottom: 25px;
            transition: all 0.4s ease;
        }
        
        .category-box .category-link {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            background: var(--secondary-color);
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.4s ease;
        }
        
        .category-box:hover i {
            color: white;
        }
        
        .category-box:hover h3, .category-box:hover p {
            color: white;
        }
        
        .category-box:hover .category-link {
            background: white;
            color: var(--primary-color);
        }
        
        /* Footer */
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 60px 0 30px;
        }
        
        footer h5 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }
        
        footer h5::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: var(--secondary-color);
        }
        
        footer ul {
            padding-left: 0;
            list-style: none;
        }
        
        footer ul li {
            margin-bottom: 15px;
        }
        
        footer ul li a {
            color: #ddd;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        footer ul li a:hover {
            color: var(--secondary-color);
            padding-left: 5px;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 40px;
            font-size: 0.9rem;
        }
        
        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            color: white;
            margin-right: 10px;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background: var(--secondary-color);
            transform: translateY(-3px);
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            #hero h1 {
                font-size: 2.5rem;
            }
            
            #hero h2 {
                font-size: 1.2rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .filter-btn {
                padding: 8px 15px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section id="hero" class="d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1>Découvrez les Merveilles du Bénin</h1>
                    <h2>Un voyage à travers l'histoire, la culture et la nature béninoise</h2>
                    <div class="mt-4">
                        <a href="#featured-sites" class="btn btn-custom btn-primary-custom me-3">Nos sites touristiques</a>
                        <a href="#categories" class="btn btn-custom btn-secondary-custom">Catégories</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main id="main">
        <!-- Featured Sites Section -->
        <section id="featured-sites" class="section-padding">
            <div class="container">
                <div class="section-title">
                    <h2>Sites Touristiques Populaires</h2>
                    <p>Découvrez les joyaux cachés et les attractions emblématiques du Bénin, un pays aux mille facettes où histoire, culture et nature se rencontrent pour vous offrir une expérience unique</p>
                </div>
                
                <!-- Filtres par catégorie -->
                <div class="filter-container text-center">
                    <a href="#featured-sites" class="filter-btn active" data-filter="all">
                        <i class="bi bi-grid-3x3-gap-fill"></i> Tous les sites
                    </a>
                    <?php foreach ($categories as $key => $value): ?>
                        <a href="#featured-sites" class="filter-btn" data-filter="<?= $key ?>">
                            <i class="bi <?= $category_icons[$key] ?? 'bi-geo-alt' ?>"></i> <?= $value ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <div class="row">
                    <?php if (count($sites) > 0): ?>
                        <?php foreach ($sites as $site): ?>
                            <!-- Site Card -->
                            <div class="col-lg-4 col-md-6 mb-4 site-item <?= $site['categorie'] ?>">
                                <div class="site-card">
                                    <div class="site-img">
                                        <span class="category-badge"><?= htmlspecialchars($site['categorie']) ?></span>
                                        <?php if (!empty($site['image'])): ?>
                                            <img src="uploads/sites_touristiques/<?= htmlspecialchars($site['image']) ?>" alt="<?= htmlspecialchars($site['nom']) ?>">
                                        <?php else: ?>
                                            <img src="img/placeholder.jpg" alt="<?= htmlspecialchars($site['nom']) ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="site-info">
                                        <span class="region <?= getRegionColor($site['localisation'], $region_colors) ?>"><?= htmlspecialchars($site['localisation']) ?></span>
                                        <h3><?= htmlspecialchars($site['nom']) ?></h3>
                                        <p><?= substr(strip_tags($site['description']), 0, 120) ?>...</p>
                                        
                                        <div class="site-meta">
                                            <?php if (!empty($site['horaires'])): ?>
                                                <span><i class="bi bi-clock"></i> <?= htmlspecialchars($site['horaires']) ?></span>
                                            <?php endif; ?>
                                            <span><i class="bi bi-calendar-date"></i> Ajouté le <?= date('d/m/Y', strtotime($site['date_ajout'])) ?></span>
                                        </div>
                                        
                                        <div class="text-center mt-4">
                                            <a href="detail-site.php?id=<?= $site['id'] ?>" class="read-more">
                                                <i class="bi bi-arrow-right-circle"></i> Découvrir
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Aucun site touristique n'a été ajouté pour le moment.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Bouton pour voir tous les sites -->
                <!-- <div class="text-center mt-5">
                    <a href="tous-les-sites.php" class="btn btn-custom btn-primary-custom">
                        <i class="bi bi-grid-3x3-gap-fill"></i> Découvrir tous nos sites touristiques
                    </a>
                </div> -->
            </div>
        </section>
        
        <!-- Categories Section -->
        <section id="categories" class="section-padding" style="background-color: #f5f9f7;">
            <div class="container">
                <div class="section-title">
                    <h2>Explorez par Catégorie</h2>
                    <p>Que vous soyez passionné d'histoire, amoureux de la nature ou à la recherche d'expériences culturelles authentiques, le Bénin offre une diversité exceptionnelle de sites à découvrir</p>
                </div>
                
                <div class="row">
                    <?php foreach ($categories as $key => $value): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="category-box">
                            <i class="bi <?= $category_icons[$key] ?? 'bi-geo-alt' ?>"></i>
                            <h3><?= $value ?></h3>
                            <p>Explorez les <?= strtolower($value) ?> emblématiques du Bénin et plongez dans une aventure inoubliable au cœur de notre patrimoine exceptionnel.</p>
                            <a href="categorie.php?cat=<?= $key ?>" class="category-link">
                                <i class="bi bi-arrow-right"></i> Explorer
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        
        <!-- About Benin Section -->
        <section id="about" class="section-padding">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <img src="img/benin-map.jpg" alt="Carte du Bénin" class="img-fluid rounded-4 shadow">
                    </div>
                    <div class="col-lg-6">
                        <div class="section-title text-start">
                            <h2>Le Bénin, Terre de Découvertes</h2>
                            <p class="mb-4">Situé en Afrique de l'Ouest, le Bénin est un joyau culturel et naturel à découvrir</p>
                        </div>
                        <p>Berceau du vodun, terre des rois d'Abomey et pays aux paysages variés, le Bénin vous invite à un voyage extraordinaire à travers ses traditions ancestrales, son histoire fascinante et ses écosystèmes préservés.</p>
                        <ul class="list-unstyled mt-4">
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Un patrimoine historique et culturel unique</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Des parcs nationaux riches en biodiversité</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Des plages de sable fin le long du golfe de Guinée</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Une gastronomie savoureuse et diversifiée</li>
                            <li class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> Un peuple accueillant aux traditions vivantes</li>
                        </ul>
                        <a href="a-propos.php" class="btn btn-custom btn-secondary-custom mt-3">
                            <i class="bi bi-info-circle"></i> En savoir plus sur le Bénin
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <h5>À Propos</h5>
                    <p>Découvrez le Bénin est votre guide touristique en ligne pour explorer les merveilles du Bénin, de ses sites historiques à ses paysages naturels exceptionnels.</p>
                    <div class="social-links mt-4">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5>Liens Rapides</h5>
                    <ul>
                        <li><a href="index.php"><i class="bi bi-chevron-right"></i> Accueil</a></li>
                        <li><a href="tous-les-sites.php"><i class="bi bi-chevron-right"></i> Sites Touristiques</a></li>
                        <li><a href="a-propos.php"><i class="bi bi-chevron-right"></i> À Propos</a></li>
                        <li><a href="contact.php"><i class="bi bi-chevron-right"></i> Contact</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5>Catégories</h5>
                    <ul>
                        <?php foreach (array_slice($categories, 0, 5) as $key => $value): ?>
                        <li>
                            <a href="categorie.php?cat=<?= $key ?>">
                                <i class="bi bi-chevron-right"></i> <?= $value ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="bi bi-geo-alt-fill me-2"></i> Cotonou, Bénin
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-envelope-fill me-2"></i> info@decouvrirbenin.com
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-telephone-fill me-2"></i> +229 xx xx xx xx
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="container">
            <div class="footer-bottom">
                <p class="mb-0">&copy; <?= date('Y') ?> Découvrez le Bénin. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script de filtrage des sites par catégorie
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const siteItems = document.querySelectorAll('.site-item');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Supprimer la classe active de tous les boutons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Ajouter la classe active au bouton cliqué
                    this.classList.add('active');
                    
                    // Récupérer la valeur du filtre
                    const filterValue = this.getAttribute('data-filter');
                    
                    // Filtrer les sites
                    siteItems.forEach(item => {
                        if (filterValue === 'all') {
                            item.style.display = 'block';
                        } else {
                            if (item.classList.contains(filterValue)) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });
                });
            });
            
            // Animation des nombres au défilement
            const counters = document.querySelectorAll('.counter');
            
            function checkVisible(elm) {
                const rect = elm.getBoundingClientRect();
                const viewHeight = Math.max(document.documentElement.clientHeight, window.innerHeight);
                return !(rect.bottom < 0 || rect.top - viewHeight >= 0);
            }
            
            function startCount() {
                counters.forEach(counter => {
                    if (checkVisible(counter) && !counter.classList.contains('counted')) {
                        counter.classList.add('counted');
                        const value = +counter.getAttribute('data-count');
                        const target = counter.innerText;
                        const increment = target / 100;
                        let current = 0;
                        
                        const updateCounter = () => {
                            current += increment;
                            counter.innerText = Math.ceil(current);
                            
                            if (current < target) {
                                setTimeout(updateCounter, 10);
                            } else {
                                counter.innerText = target;
                            }
                        };
                        
                        updateCounter();
                    }
                });
            }
            
            // Vérifier les compteurs au chargement et au défilement
            window.addEventListener('scroll', startCount);
            window.addEventListener('load', startCount);
            
            // Smooth scrolling pour les liens d'ancrage
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('href');
                    if (targetId === '#') return;
                    
                    const targetElement = document.querySelector(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - 80,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>