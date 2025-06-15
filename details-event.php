<?php
// Connexion à la base de données
require_once 'database/db.php';

// Vérification de l'ID de l'événement
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];

// Récupération des détails de l'événement
$query = $db->prepare("SELECT * FROM events WHERE id = ?");
$query->execute([$id]);
$event = $query->fetch(PDO::FETCH_ASSOC);

// Redirection si l'événement n'existe pas
if (!$event) {
    header('Location: index.php');
    exit();
}

// Formatage des dates
$start_date = new DateTime($event['start_date']);
$end_date = new DateTime($event['end_date']);

$formatted_start_date = $start_date->format('d/m/Y à H:i');
$formatted_end_date = $end_date->format('d/m/Y à H:i');

// Récupération d'événements similaires (même localisation, mais pas le même événement)
$query = $db->prepare("SELECT * FROM events WHERE localisation = ? AND id != ? LIMIT 3");
$query->execute([$event['localisation'], $id]);
$similar_events = $query->fetchAll(PDO::FETCH_ASSOC);

// Titre de la page
$page_title = $event['label_event'] . ' - Événements au Bénin';
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
        
        .event-details {
            padding: 60px 0;
        }
        
        .event-image {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(150, 120, 0, 0.2);
        }
        
        .event-image img {
            width: 100%;
            height: 450px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .event-image:hover img {
            transform: scale(1.03);
        }
        
        .event-meta {
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
        
        .event-description {
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
        
        .similar-event-item {
            transition: all 0.3s ease;
            background-color: #e8e0ae;
        }
        
        .similar-event-item:hover {
            box-shadow: 0 5px 15px rgba(150, 120, 0, 0.2);
            transform: translateY(-5px);
        }
        
        .similar-event-item img {
            border-radius: 8px;
            height: 80px;
            width: 100%;
            object-fit: cover;
            border: 2px solid #d4b82e;
        }
        
        .similar-event-item h5 a {
            color: var(--dark-color);
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: color 0.3s ease;
        }
        
        .similar-event-item h5 a:hover {
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
        
        .event-info-badges {
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
        
        .ticket-info {
            padding: 20px;
            background-color: #f0e9c0;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 4px solid var(--primary-color);
        }
        
        .countdown-container {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background-color: #e6d580;
            margin-bottom: 20px;
        }
        
        .countdown-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            line-height: 1;
        }
        
        .countdown-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #634f00;
        }
        
        @media (max-width: 768px) {
            .event-image img {
                height: 300px;
            }
            .breadcrumbs {
                padding: 20px 0;
            }
            .event-details {
                padding: 40px 0;
            }
        }
    </style>
</head>
<body>

<main id="main">
    <!-- Hero Section with Event Image -->
    <section class="event-hero" style="background-image: url('<?php echo !empty($event['picture_event']) ? 'uploads/events/'.$event['picture_event'] : '/api/placeholder/1200/500'; ?>'); background-size: cover; background-position: center; height: 50vh; position: relative;">
        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(73, 58, 0, 0.8), transparent); padding: 30px;">
            <div class="container">
                <h1 style="color: white; font-size: 36px; font-weight: 700; margin-bottom: 0;"><?php echo $event['label_event']; ?></h1>
                <div style="color: white; opacity: 0.9;">
                    <span class="badge bg-primary me-2">Événement</span>
                    <span><i class="bi bi-geo-alt"></i> <?php echo $event['localisation']; ?></span>
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
                    <li><a href="index.php?page=events" style="color: var(--primary-color); text-decoration: none;">Événements</a></li>
                    <li style="color: #6c6012;"><?php echo $event['label_event']; ?></li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Event Details Section -->
    <section class="event-details">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="event-details-content">
                        <!-- Event Info Badges -->
                        <div class="event-info-badges">
                            <div class="info-badge">
                                <i class="bi bi-calendar-event"></i>
                                <span>Du <?php echo $formatted_start_date; ?></span>
                            </div>
                            <div class="info-badge">
                                <i class="bi bi-calendar-check"></i>
                                <span>Au <?php echo $formatted_end_date; ?></span>
                            </div>
                            <div class="info-badge">
                                <i class="bi bi-geo-alt"></i>
                                <span><?php echo $event['localisation']; ?></span>
                            </div>
                            <div class="info-badge">
                                <i class="bi bi-cash"></i>
                                <span><?php echo number_format($event['amount_event'], 0, ',', ' '); ?> FCFA</span>
                            </div>
                        </div>
                        
                        <div class="event-description mb-4">
                            <h3 class="mb-4" style="color: #634f00;">À propos de cet événement</h3>
                            <div style="line-height: 1.8;">
                                <?php echo nl2br($event['description_event']); ?>
                            </div>
                        </div>
                        
                        <!-- Informations sur les billets -->
                        <div class="ticket-info">
                            <h4 style="color: #634f00; margin-bottom: 15px;"><i class="bi bi-ticket-perforated me-2"></i>Billets disponibles</h4>
                            <p class="mb-2">Prix du billet: <strong><?php echo number_format($event['amount_event'], 0, ',', ' '); ?> FCFA</strong></p>
                            <p class="mb-0">Places restantes: <strong><?php echo $event['number_available_event']; ?></strong></p>
                        </div>
                        
                        <?php if(!empty($event['picture_event'])): ?>
                        <div class="event-gallery mt-5 mb-5">
                            <h3 class="mb-4" style="color: #634f00;">Photos</h3>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="event-image mb-4">
                                        <img src="uploads/events/<?php echo $event['picture_event']; ?>" alt="<?php echo $event['label_event']; ?>" class="img-fluid rounded">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Partage sur les réseaux sociaux -->
                        <div class="social-share mt-5">
                            <h4 style="color: #634f00; margin-bottom: 15px;">Partagez cet événement</h4>
                            <div class="social-links mt-3">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" class="facebook" target="_blank"><i class="bi bi-facebook"></i></a>
                                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($event['label_event']); ?>&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" class="twitter" target="_blank"><i class="bi bi-twitter"></i></a>
                                <a href="https://wa.me/?text=<?php echo urlencode($event['label_event'] . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" class="whatsapp" target="_blank"><i class="bi bi-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="event-sidebar">
                        <!-- Compte à rebours -->
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Compte à rebours</h4>
                            <div class="row text-center" id="countdown">
                                <div class="col-3">
                                    <div >
                                        <div class="countdown-value" id="days">--</div>
                                        <div class="countdown-label">Jours</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div >
                                        <div class="countdown-value" id="hours">--</div>
                                        <div class="countdown-label">Heures</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div >
                                        <div class="countdown-value" id="minutes">--</div>
                                        <div class="countdown-label">Minutes</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div >
                                        <div class="countdown-value" id="seconds">--</div>
                                        <div class="countdown-label">Secondes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="sidebar-widget">
                            <a href="users/inscription.php" class="btn btn-primary w-100 mb-3"><i class="bi bi-ticket-perforated me-2"></i> Participer à l'événement</a>
                            <a href="#" class="btn btn-outline-primary w-100"><i class="bi bi-calendar-plus me-2"></i> Ajouter au calendrier</a>
                        </div>
                        
                        <!-- Carte de localisation -->
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Localisation</h4>
                            <div class="map-container">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4032442.1059084896!2d0.7766389779151727!3d9.322448217831306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1023542b047a5695%3A0x7c4028aff4620a4c!2zQsOpbmlu!5e0!3m2!1sfr!2sfr!4v1650128463201!5m2!1sfr!2sfr" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>
                        
                        <!-- Événements similaires -->
                        <?php if(isset($similar_events) && count($similar_events) > 0): ?>
                        <div class="sidebar-widget">
                            <h4 class="sidebar-title">Événements similaires</h4>
                            <div class="similar-events">
                                <?php foreach($similar_events as $similar): ?>
                                <?php
                                    $similar_start = new DateTime($similar['start_date']);
                                    $similar_formatted_date = $similar_start->format('d/m/Y');
                                ?>
                                <div class="similar-event-item mb-3 p-3 border-0 rounded">
                                    <div class="row g-0">
                                        <div class="col-4">
                                            <?php if(!empty($similar['picture_event'])): ?>
                                                <img src="uploads/events/<?php echo $similar['picture_event']; ?>" alt="<?php echo $similar['label_event']; ?>" class="img-fluid rounded">
                                            <?php else: ?>
                                                <img src="/api/placeholder/100/100" alt="<?php echo $similar['label_event']; ?>" class="img-fluid rounded">
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-8 ps-3">
                                            <h5 class="mb-1"><a href="detail-event.php?id=<?php echo $similar['id']; ?>"><?php echo $similar['label_event']; ?></a></h5>
                                            <div class="text-muted small"><i class="bi bi-calendar"></i> <?php echo $similar_formatted_date; ?></div>
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
<script>
// Compte à rebours jusqu'à la date de l'événement
function updateCountdown() {
    const eventDate = new Date("<?php echo $event['start_date']; ?>").getTime();
    const now = new Date().getTime();
    const timeLeft = eventDate - now;
    
    if (timeLeft > 0) {
        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        
        document.getElementById("days").innerHTML = days;
        document.getElementById("hours").innerHTML = hours;
        document.getElementById("minutes").innerHTML = minutes;
        document.getElementById("seconds").innerHTML = seconds;
    } else {
        document.getElementById("days").innerHTML = "0";
        document.getElementById("hours").innerHTML = "0";
        document.getElementById("minutes").innerHTML = "0";
        document.getElementById("seconds").innerHTML = "0";
    }
}

// Mettre à jour le compte à rebours chaque seconde
setInterval(updateCountdown, 1000);
updateCountdown(); // Exécuter immédiatement
</script>
</body>
</html>