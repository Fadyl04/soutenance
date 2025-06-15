<?php
// Connexion à la base de données
require_once 'database/db.php';

// Vérification de l'ID du site
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

// Récupération des détails du site
$query = $db->prepare("SELECT * FROM sites_touristiques WHERE id = ?");
$query->execute([$id]);
$site = $query->fetch(PDO::FETCH_ASSOC);

// Redirection si le site n'existe pas
if (!$site) {
    header('Location: index.php');
    exit();
}

// Définition des catégories
$categories = [
    'naturel' => 'Site naturel',
    'historique' => 'Site historique',
    'culturel' => 'Site culturel',
    'religieux' => 'Site religieux',
    'loisir' => 'Site de loisirs'
];

// Nom de la catégorie
$categorie_nom = isset($categories[$site['categorie']]) ? $categories[$site['categorie']] : $site['categorie'];

// Récupération des sites similaires (même catégorie, mais pas le même site)
$query = $db->prepare("SELECT * FROM sites_touristiques WHERE categorie = ? AND id != ? LIMIT 3");
$query->execute([$site['categorie'], $id]);
$sites_similaires = $query->fetchAll(PDO::FETCH_ASSOC);

// Titre de la page
$page_title = $site['nom'] . ' - Sites Touristiques du Bénin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #d4a017; /* Jaune plus sombre */
            --secondary-color: #b8870b; /* Teinte plus sombre du jaune */
            --accent-color: #e6b800; /* Jaune vif pour accentuation */
            --dark-color: #4a4112; /* Brun-jaune foncé */
            --light-color: #f8f5e6; /* Jaune très clair presque blanc */
            --text-color: #3a3100; /* Couleur de texte foncée */
            --background-color: #f7f3dc; /* Fond légèrement teinté de jaune */
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            background-color: var(--background-color);
        }
        
        .breadcrumbs {
            padding: 30px 0;
            background-color: #f0e9c0; /* Jaune très pâle */
            border-bottom: 1px solid #e6d580;
        }
        
        .breadcrumbs h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .breadcrumbs ol {
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 14px;
        }
        
        .breadcrumbs ol li + li {
            padding-left: 10px;
        }
        
        .breadcrumbs ol li + li::before {
            display: inline-block;
            padding-right: 10px;
            color: #88750d;
            content: "/";
        }
        
        .site-details {
            padding: 60px 0;
        }
        
        .site-image {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(150, 120, 0, 0.2);
        }
        
        .site-image img {
            width: 100%;
            height: 450px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .site-image:hover img {
            transform: scale(1.03);
        }
        
        .site-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        
        .badge {
            padding: 8px 16px;
            font-weight: 500;
            border-radius: 30px;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .site-description {
            line-height: 1.8;
            font-size: 16px;
            color: #3a3000;
        }
        
        .sidebar-widget {
            background-color: #f4edc3;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(150, 120, 0, 0.1);
        }
        
        .sidebar-title {
            font-size: 20px;
            margin-bottom: 20px;
            color: var(--dark-color);
            position: relative;
            padding-bottom: 10px;
        }
        
        .sidebar-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 3px;
            width: 70px;
            background-color: var(--primary-color);
        }
        
        .map-container {
            border-radius: 10px;
            overflow: hidden;
            height: 300px;
            border: 3px solid #e6d076;
        }
        
        .similar-site-item {
            transition: all 0.3s ease;
            background-color: #e8e0ae;
        }
        
        .similar-site-item:hover {
            box-shadow: 0 5px 15px rgba(150, 120, 0, 0.2);
            transform: translateY(-5px);
        }
        
        .similar-site-item img {
            border-radius: 8px;
            height: 80px;
            width: 100%;
            object-fit: cover;
            border: 2px solid #d4b82e;
        }
        
        .similar-site-item h5 a {
            color: var(--dark-color);
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: color 0.3s ease;
        }
        
        .similar-site-item h5 a:hover {
            color: #886c00;
        }
        
        .social-share {
            background-color: #efe7b8;
            padding: 20px;
            border-radius: 10px;
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-links .facebook {
            background-color: #d4a017;
        }
        
        .social-links .twitter {
            background-color: #c29016;
        }
        
        .social-links .whatsapp {
            background-color: #b8870b;
        }
        
        .social-links a:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(150, 120, 0, 0.3);
        }
        
        .site-info-badges {
            margin-top: 20px;
            margin-bottom: 25px;
        }
        
        .info-badge {
            display: inline-flex;
            align-items: center;
            background-color: #e6d580;
            border-radius: 30px;
            padding: 10px 20px;
            margin-right: 10px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(150, 120, 0, 0.1);
        }
        
        .info-badge i {
            margin-right: 8px;
            font-size: 18px;
            color: #a38a10;
        }
        
        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
        }
        
        .btn-primary:hover {
            background-color: #b8870b !important;
            border-color: #b8870b !important;
        }
        
        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }
        
        .btn-outline-primary:hover {
            background-color: #f4edc3 !important;
            color: #886c00 !important;
        }
        
        a {
            color: #886c00;
        }
        
        .text-warning {
            color: #d4a017 !important;
        }
        
        @media (max-width: 768px) {
            .site-image img {
                height: 300px;
            }
            .breadcrumbs {
                padding: 20px 0;
            }
            .site-details {
                padding: 40px 0;
            }
        }
    </style>
</head>
<body>

<main id="main">
    <!-- Hero Section with Site Image -->
    <section class="site-hero" style="background-image: url('<?php echo !empty($site['image']) ? 'uploads/sites/'.$site['image'] : '/api/placeholder/1200/500'; ?>'); background-size: cover; background-position: center; height: 50vh; position: relative;">
        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(73, 58, 0, 0.8), transparent); padding: 30px;">
            <div class="container">
                <h1 style="color: white; font-size: 36px; font-weight: 700; margin-bottom: 0;"><?php echo $site['nom']; ?></h1>
                <div style="color: white; opacity: 0.9;">
                    <span class="badge bg-primary me-2"><?php echo $categorie_nom; ?></span>
                    <span><i class="bi bi-geo-alt"></i> <?php echo $site['localisation']; ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Breadcrumbs -->
    <section class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <ol>
                    <li><a href="index.php" style="color: var(--primary-color); text-decoration: none;">Accueil</a></li>
                    <li><a href="index.php?page=sites" style="color: var(--primary-color); text-decoration: none;">Sites Touristiques</a></li>
                    <li style="color: #6c6012;"><?php echo $site['nom']; ?></li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Site Details Section -->
    <section class="site-details">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="site-details-content">
                        <!-- Site Info Badges -->
                        <div class="site-info-badges">
                            <div class="info-badge">
                                <i class="bi bi-tag"></i>
                                <span><?php echo $categorie_nom; ?></span>
                            </div>
                            <div class="info-badge">
                                <i class="bi bi-geo-alt"></i>
                                <span><?php echo $site['localisation']; ?></span>
                            </div>
                            <?php if(!empty($site['horaires'])): ?>
                            <div class="info-badge">
                                <i class="bi bi-clock"></i>
                                <span><?php echo $site['horaires']; ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="site-description mb-4">
                            <h3 class="mb-4" style="color: #634f00;">À propos de ce site</h3>
                            <div style="line-height: 1.8;">
                                <?php echo nl2br($site['description']); ?>
                            </div>
                        </div>
                        
                        <?php if(!empty($site['image'])): ?>
                        <div class="site-gallery mt-5 mb-5">
                            <h3 class="mb-4" style="color: #634f00;">Photos</h3>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="site-image mb-4">
                                        <img src="uploads/sites_touristiques/<?php echo $site['image']; ?>" alt="<?php echo $site['nom']; ?>" class="img-fluid rounded">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Partage sur les réseaux sociaux -->
                        <div class="social-share mt-5">
                            <h4 style="color: #634f00; margin-bottom: 15px;">Partagez ce site</h4>
                            <div class="social-links mt-3">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" class="facebook" target="_blank"><i class="bi bi-facebook"></i></a>
                                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($site['nom']); ?>&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" class="twitter" target="_blank"><i class="bi bi-twitter"></i></a>
                                <a href="https://wa.me/?text=<?php echo urlencode($site['nom'] . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" class="whatsapp" target="_blank"><i class="bi bi-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="site-sidebar">
                        <!-- Action Buttons -->
                        <div class="sidebar-widget">
                            <a href="#" class="btn btn-primary w-100 mb-3"><i class="bi bi-calendar-check me-2"></i> Planifier une visite</a>
                            <a href="#" class="btn btn-outline-primary w-100"><i class="bi bi-heart me-2"></i> Ajouter aux favoris</a>
                        </div>
                        
                        <!-- Carte de localisation -->
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Localisation</h4>
                            <div class="map-container">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4032442.1059084896!2d0.7766389779151727!3d9.322448217831306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1023542b047a5695%3A0x7c4028aff4620a4c!2zQsOpbmlu!5e0!3m2!1sfr!2sfr!4v1650128463201!5m2!1sfr!2sfr" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                        
                        <!-- Sites similaires -->
                        <?php if(isset($sites_similaires) && count($sites_similaires) > 0): ?>
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Sites similaires</h4>
                            <div class="similar-sites">
                                <?php foreach($sites_similaires as $similar): ?>
                                <div class="similar-site-item mb-3 p-3 border-0 rounded">
                                    <div class="row g-0">
                                        <div class="col-4">
                                            <?php if(!empty($similar['image'])): ?>
                                                <img src="uploads/sites_touristiques/<?php echo $similar['image']; ?>" alt="<?php echo $similar['nom']; ?>" class="img-fluid rounded">
                                            <?php else: ?>
                                                <img src="/api/placeholder/100/100" alt="<?php echo $similar['nom']; ?>" class="img-fluid rounded">
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-8 ps-3">
                                            <h5 class="mb-1"><a href="detail-site.php?id=<?php echo $similar['id']; ?>"><?php echo $similar['nom']; ?></a></h5>
                                            <span class="text-muted small"><i class="bi bi-geo-alt-fill"></i> <?php echo $similar['localisation']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Weather Info -->
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Météo locale</h4>
                            <div class="d-flex align-items-center">
                                <div class="fs-1 me-3">
                                    <i class="bi bi-sun text-warning"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold">28°C</div>
                                    <div class="text-muted">Ensoleillé</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>