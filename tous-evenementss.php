<?php
// Connexion à la base de données
include 'database/db.php';

// Récupération de tous les événements sans limite
$query = $db->query("SELECT * FROM events ORDER BY start_date DESC");
$events = $query->fetchAll(PDO::FETCH_ASSOC);

// Définition des catégories pour l'affichage
$categories = [
    'concert' => 'Concerts',
    'festival' => 'Festivals',
    'exposition' => 'Expositions',
    'conference' => 'Conférences',
    'sport' => 'Événements Sportifs'
];

// Correspondance entre catégories et icônes
$category_icons = [
    'concert' => 'bi-music-note-beamed',
    'festival' => 'bi-calendar-event',
    'exposition' => 'bi-easel',
    'conference' => 'bi-mic',
    'sport' => 'bi-trophy'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les Événements au Bénin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Hero Section */
        #hero {
            width: 100%;
            height: 50vh;
            background: url('img/events.jpg') top center;
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
            font-size: 48px;
            font-weight: 700;
            line-height: 56px;
            color: #fff;
        }
        
        #hero h2 {
            color: #eee;
            margin: 10px 0 0 0;
            font-size: 24px;
        }
        
        .btn-get-started {
            font-family: "Raleway", sans-serif;
            font-weight: 500;
            font-size: 16px;
            letter-spacing: 1px;
            display: inline-block;
            padding: 10px 30px;
            border-radius: 50px;
            transition: 0.5s;
            color: #fff;
            background: #e9b732;
            margin-top: 30px;
            text-decoration: none;
        }
        
        .btn-get-started:hover {
            background: #d4a026;
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
            font-weight: bold;
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
            color: #555;
        }
        
        /* Event Cards */
        .event-card {
            margin-bottom: 30px;
            overflow: hidden;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .event-img {
            position: relative;
            overflow: hidden;
        }
        
        .event-img img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.4s;
        }
        
        .event-card:hover .event-img img {
            transform: scale(1.05);
        }
        
        .event-date {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #e9b732;
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .event-info {
            padding: 25px 20px;
        }
        
        .event-info h4 {
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 20px;
            color: #333;
        }
        
        .event-info .location {
            display: inline-block;
            background-color: #f8f9fa;
            color: #333;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 15px;
        }
        
        .event-info .price {
            display: inline-block;
            background-color: #28a745;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
            margin-bottom: 15px;
        }
        
        .event-info .available {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
            margin-bottom: 15px;
        }
        
        .event-info p {
            color: #555;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        
        .read-more {
            display: inline-block;
            background: #e9b732;
            color: #fff;
            padding: 8px 22px;
            border-radius: 50px;
            transition: 0.3s;
            text-decoration: none;
            font-weight: 500;
        }
        
        .read-more:hover {
            background: #d4a026;
        }
        
        /* Category Filters */
        .category-filters {
            margin-bottom: 30px;
        }
        
        .filter-btn {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 5px 10px 5px;
            background: #f5f5f5;
            color: #333;
            border-radius: 30px;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .filter-btn.active, .filter-btn:hover {
            background: #e9b732;
            color: #fff;
        }
        
        /* Breadcrumbs */
        .breadcrumbs {
            padding: 15px 0;
            background: #f5f5f5;
            margin-bottom: 30px;
        }
        
        .breadcrumbs h2 {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        
        .breadcrumbs ol {
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumbs ol li {
            font-size: 14px;
            color: #666;
            padding-right: 10px;
            position: relative;
        }
        
        .breadcrumbs ol li + li:before {
            content: "/";
            display: inline-block;
            padding-right: 10px;
            color: #888;
        }
        
        .breadcrumbs ol li a {
            color: #666;
            text-decoration: none;
            transition: 0.3s;
        }
        
        .breadcrumbs ol li a:hover {
            color: #e9b732;
        }
        
        /* Back to top button */
        .back-to-top {
            position: fixed;
            visibility: hidden;
            opacity: 0;
            right: 15px;
            bottom: 15px;
            z-index: 996;
            background: #e9b732;
            width: 40px;
            height: 40px;
            border-radius: 50px;
            transition: all 0.4s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .back-to-top i {
            font-size: 24px;
            color: #fff;
            line-height: 0;
        }
        
        .back-to-top:hover {
            background: #d4a026;
        }
        
        .back-to-top.active {
            visibility: visible;
            opacity: 1;
        }
        
        /* Pagination */
        .pagination {
            margin-top: 30px;
            justify-content: center;
        }
        
        .pagination .page-link {
            color: #333;
            border: none;
            border-radius: 5px;
            margin: 0 5px;
            padding: 8px 16px;
            transition: all 0.3s;
        }
        
        .pagination .page-link:hover {
            background-color: #e9b732;
            color: #fff;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #e9b732;
            border-color: #e9b732;
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section id="hero" class="d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="d-flex flex-column justify-content-center">
                    <h1>Tous les Événements</h1>
                    <h2>Découvrez tous les événements disponibles au Bénin</h2>
                </div>
            </div>
        </div>
    </section>

    <!-- Breadcrumbs -->
    <section class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Tous les Événements</h2>
                <ol>
                    <li><a href="index.php">Accueil</a></li>
                    <li>Tous les Événements</li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main id="main">
        <!-- All Events Section -->
        <section id="all-events" class="main-content">
            <div class="container">
                <div class="section-title">
                    <h2>Catalogue Complet</h2>
                    <p>Découvrez tous les événements culturels, sportifs et festifs qui animent le Bénin</p>
                </div>
                
                <!-- Filtres par catégorie -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="category-filters text-center">
                            <a href="#all-events" class="filter-btn active" data-filter="all">Tous</a>
                            <?php foreach ($categories as $key => $value): ?>
                                <a href="#all-events" class="filter-btn" data-filter="<?= $key ?>">
                                    <i class="bi <?= $category_icons[$key] ?? 'bi-calendar' ?>"></i> <?= $value ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <?php if (count($events) > 0): ?>
                        <?php foreach ($events as $event): ?>
                            <!-- Event Dynamique -->
                            <div class="col-md-4 mb-4 event-item <?= $event['category'] ?? 'other' ?>">
                                <div class="event-card">
                                    <div class="event-img">
                                        <?php if (!empty($event['picture_event'])): ?>
                                            <img src="uploads/events/<?= $event['picture_event'] ?>" alt="<?= $event['label_event'] ?>">
                                        <?php else: ?>
                                            <img src="/api/placeholder/400/300" alt="<?= $event['label_event'] ?>">
                                        <?php endif; ?>
                                        <div class="event-date">
                                            <?= date('d M Y', strtotime($event['start_date'])) ?>
                                        </div>
                                    </div>
                                    <div class="event-info">
                                        <span class="location"><i class="bi bi-geo-alt"></i> <?= $event['localisation'] ?></span>
                                        <span class="price"><i class="bi bi-tag"></i> <?= number_format($event['amount_event'], 0, ',', ' ') ?> FCFA</span>
                                        <span class="available"><i class="bi bi-people"></i> <?= $event['number_available_event'] ?> places</span>
                                        <h4><?= $event['label_event'] ?></h4>
                                        <p><?= substr(strip_tags($event['description_event']), 0, 100) ?>...</p>
                                        <a href="details-events.php?id=<?= $event['id'] ?>" class="read-more">En savoir plus</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p>Aucun événement n'a été ajouté pour le moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination (à implémenter si nécessaire) -->
                <!--
                <div class="row mt-4">
                    <div class="col-12">
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                -->
                
                <!-- Bouton de retour -->
                <div class="text-center mt-4">
                    <a href="index.php" class="btn-get-started">Retour aux événements</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Back to top button -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script de filtrage des événements par catégorie
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const eventItems = document.querySelectorAll('.event-item');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Supprimer la classe active de tous les boutons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Ajouter la classe active au bouton cliqué
                    this.classList.add('active');
                    
                    // Récupérer la valeur du filtre
                    const filterValue = this.getAttribute('data-filter');
                    
                    // Filtrer les événements
                    eventItems.forEach(item => {
                        if (filterValue === 'all' || item.classList.contains(filterValue)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });
            
            // Script pour le bouton back to top
            const backToTopButton = document.querySelector('.back-to-top');
            
            window.addEventListener('scroll', () => {
                if (window.scrollY > 100) {
                    backToTopButton.classList.add('active');
                } else {
                    backToTopButton.classList.remove('active');
                }
            });
            
            backToTopButton.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>