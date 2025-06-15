
<?php
include 'database/db.php';

// Récupérer les circuits actifs pour l'affichage public
try {
    $stmt = $db->query("SELECT c.*, 
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
                       WHERE c.status = 'active'
                       GROUP BY c.id
                       ORDER BY c.creation_date DESC");
    $public_circuits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Erreur lors de la récupération des circuits : " . $e->getMessage();
    $public_circuits = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Circuits Touristiques du Bénin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    /* Global Styles */
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
        height: 80vh;
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
        font-size: 48px;
        font-weight: 700;
        line-height: 56px;
        color: #fff;
        margin-bottom: 20px;
    }
    
    #hero h2 {
        color: #eee;
        margin: 10px 0 30px 0;
        font-size: 24px;
        font-weight: 300;
    }
    
    .btn-get-started {
        font-weight: 500;
        font-size: 16px;
        display: inline-block;
        padding: 15px 30px;
        border-radius: 50px;
        transition: 0.5s;
        color: #fff;
        background: #e9b732;
        box-shadow: 0px 15px 25px rgba(0, 0, 0, 0.15);
        text-decoration: none;
    }
    
    .btn-get-started:hover {
        background: #d4a026;
        color: #fff;
    }
    
    /* Main Content */
    .main-content {
        padding: 80px 0;
    }
    
    .section-title {
        text-align: center;
        padding-bottom: 50px;
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
    
    /* Style amélioré pour les cartes de circuits */
.tour-block-two {
    margin-bottom: 40px;
}

.tour-block-two .inner-box {
    display: flex;
    background: #fff;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0px 10px 30px 0px rgba(0, 0, 0, 0.1);
    transition: all 0.5s ease;
    position: relative;
}

.tour-block-two .inner-box:hover {
    transform: translateY(-8px);
    box-shadow: 0px 20px 40px 0px rgba(0, 0, 0, 0.2);
}

.tour-block-two .image-box {
    position: relative;
    overflow: hidden;
    width: 40%;
    min-height: 300px;
    margin: 0;
}

.tour-block-two .image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    transition: transform 0.7s ease;
}

.tour-block-two .inner-box:hover .image-box img {
    transform: scale(1.1);
}

.tour-block-two .image-box::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to right, rgba(0,0,0,0.4), rgba(0,0,0,0));
    opacity: 0;
    transition: all 0.5s ease;
}

.tour-block-two .inner-box:hover .image-box::after {
    opacity: 1;
}

.tour-block-two .image-box a {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 60px;
    background: #e9b732;
    color: #fff;
    border-radius: 50%;
    text-align: center;
    line-height: 60px;
    opacity: 0;
    z-index: 1;
    transition: all 0.5s ease;
    box-shadow: 0px 5px 20px rgba(233, 183, 50, 0.3);
}

.tour-block-two .inner-box:hover .image-box a {
    opacity: 1;
}

.tour-block-two .image-box a:hover {
    background: #fff;
    color: #e9b732;
}

.tour-block-two .content-box {
    padding: 35px 40px;
    width: 60%;
}

.tour-block-two .content-box h3 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 12px;
    line-height: 1.3;
}

.tour-block-two .content-box h3 a {
    color: #333;
    transition: all 0.3s ease;
}

.tour-block-two .content-box h3 a:hover {
    color: #e9b732;
    text-decoration: none;
}

.tour-block-two .content-box h4 {
    font-size: 20px;
    color: #e9b732;
    margin-bottom: 15px;
    font-weight: 600;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}

.tour-block-two .content-box h4 span {
    font-size: 15px;
    color: #777;
    font-weight: normal;
    margin-left: 10px;
}

.tour-block-two .content-box p {
    color: #666;
    margin-bottom: 20px;
    text-align: justify;
    line-height: 1.7;
    font-size: 16px;
}

/* Meta info */
.tour-block-two .meta-info {
    display: flex;
    flex-wrap: wrap;
    margin-bottom: 20px;
    gap: 10px;
}

.tour-block-two .meta-info .badge {
    font-size: 14px;
    font-weight: 500;
    padding: 8px 15px;
    border-radius: 50px;
    box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
}

.tour-block-two .meta-info .badge i {
    margin-right: 5px;
}

.tour-block-two .btn-box {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.tour-block-two .btn-box a {
    display: inline-block;
    padding: 12px 25px;
    color: #fff;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
}

.tour-block-two .btn-box .btn-primary {
    background: #3498db;
    box-shadow: 0px 8px 15px rgba(52, 152, 219, 0.2);
}

.tour-block-two .btn-box .btn-primary:hover {
    background: #2980b9;
    transform: translateY(-3px);
}

.tour-block-two .btn-box .btn-success {
    background: #e9b732;
    box-shadow: 0px 8px 15px rgba(233, 183, 50, 0.2);
}

.tour-block-two .btn-box .btn-success:hover {
    background: #d4a026;
    transform: translateY(-3px);
}

/* Animation */
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(233, 183, 50, 0.4);
    }
    70% {
        box-shadow: 0 0 0 15px rgba(233, 183, 50, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(233, 183, 50, 0);
    }
}

.tour-block-two .content-box .highlight {
    animation: pulse 2s infinite;
}

/* Responsive design for tour cards */
@media (max-width: 991px) {
    .tour-block-two .inner-box {
        flex-direction: column;
    }
    
    .tour-block-two .image-box,
    .tour-block-two .content-box {
        width: 100%;
    }
    
    .tour-block-two .image-box {
        height: 300px;
    }
    
    .tour-block-two .content-box {
        padding: 25px 20px;
    }
}

@media (max-width: 767px) {
    .tour-block-two .btn-box {
        flex-direction: column;
        gap: 10px;
    }
    
    .tour-block-two .btn-box a {
        width: 100%;
        text-align: center;
    }
    
    .tour-block-two .meta-info {
        flex-direction: column;
        align-items: flex-start;
    }
}
    
    /* Types Section */
    .types {
        padding: 80px 0;
        background-color: #f9f9f9;
    }
    
    .type-item {
        text-align: center;
        padding: 40px 20px;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.05);
        transition: all 0.4s ease;
        margin-bottom: 40px; /* Augmenté de 30px à 40px */
        height: 100%;
    }
    
    /* Ajout d'espace dans la rangée des Types */
    .types .row {
        margin-bottom: 20px; /* Ajoute une marge en bas de la rangée */
        row-gap: 30px; /* Ajoute un espacement entre les rangées */
    }
    
    .type-item:hover {
        transform: translateY(-10px);
        box-shadow: 0px 20px 40px rgba(0, 0, 0, 0.1);
    }
    
    .type-item i {
        font-size: 48px;
        color: #e9b732;
        margin-bottom: 20px;
    }
    
    .type-item h3 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }
    
    .type-item p {
        font-size: 15px;
        color: #666;
        line-height: 1.7;
    }
    
    /* Planning Section */
    .planning-section {
        padding: 80px 0;
    }
    
    .planning-container {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.05);
        padding: 40px;
    }
    
    .planning-step {
        display: flex;
        margin-bottom: 40px;
        position: relative;
    }
    
    .planning-step:last-child {
        margin-bottom: 0;
    }
    
    .step-number {
        flex: 0 0 60px;
        height: 60px;
        border-radius: 50%;
        background: #e9b732;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 24px;
        margin-right: 25px;
        box-shadow: 0px 5px 15px rgba(233, 183, 50, 0.2);
    }
    
    .step-content {
        flex: 1;
    }
    
    .step-content h4 {
        font-weight: 700;
        margin-bottom: 10px;
        color: #333;
        font-size: 20px;
    }
    
    .step-content p {
        color: #666;
        margin-bottom: 0;
    }
    
    /* Testimonials Section */
    .testimonials {
        padding: 80px 0;
        background-color: #fff;
    }
    
    .testimonial-item {
        text-align: center;
        padding: 40px 30px;
        border-radius: 15px;
        background: #f9f9f9;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        transition: all 0.4s ease;
    }
    
    .testimonial-item:hover {
        transform: translateY(-10px);
        box-shadow: 0px 20px 40px rgba(0, 0, 0, 0.1);
    }
    
    .testimonial-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin: 0 auto 20px auto;
        overflow: hidden;
        border: 4px solid #fff;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .testimonial-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .testimonial-item h3 {
        font-size: 18px;
        font-weight: 700;
        margin: 10px 0 5px 0;
        color: #333;
    }
    
    .testimonial-item h4 {
        font-size: 14px;
        color: #777;
        margin: 0 0 15px 0;
    }
    
    .testimonial-item p {
        font-style: italic;
        margin: 0 0 15px 0;
        color: #666;
    }
    
    .stars {
        color: #e9b732;
        margin-bottom: 15px;
    }
    
    /* Grid system */
    .container {
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
        max-width: 1140px;
    }
    
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    
    .col-md-4, .col-lg-4, .col-md-6, .col-lg-6, .col-md-12, .col-lg-12 {
        position: relative;
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
    }
    
    @media (min-width: 768px) {
        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        .col-md-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
    
    @media (min-width: 992px) {
        .col-lg-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
        .col-lg-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        .col-lg-12 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
    
    /* Responsive Styles */
    @media (max-width: 991px) {
        .tour-block-two .inner-box {
            flex-direction: column;
        }
        
        .tour-block-two .image-box,
        .tour-block-two .content-box {
            width: 100%;
        }
        
        .tour-block-two .image-box {
            height: 250px;
        }
    }
    
    @media (max-width: 767px) {
        #hero h1 {
            font-size: 36px;
            line-height: 42px;
        }
        
        #hero h2 {
            font-size: 18px;
        }
        
        .section-title h2 {
            font-size: 28px;
        }
        
        .planning-container {
            padding: 30px 20px;
        }
        
        /* Ajustement de l'espacement pour mobile */
        .types .row {
            row-gap: 20px;
        }
        
        .type-item {
            margin-bottom: 30px;
        }
    }
</style>
</head>
<body>

<section id="hero" class="d-flex align-items-center">
    <div class="container">
        <h1>visites touristiques</h1>
        <h2><i>Vivez des expériences inoubliables au Bénin</i></h2>
    </div>
</section>

<main id="main">
    <!-- Section des circuits -->
    <section id="circuits" class="main-content">
        <div class="container">
            <div class="section-title">
                <h2>Nos visites</h2>
                <p>Parcourez le Bénin à travers des itinéraires soigneusement élaborés</p>
            </div>
            
            <div class="row">
                <?php if(!empty($public_circuits)): ?>
                    <?php foreach($public_circuits as $circuit): ?>
                        <div class="col-lg-12 mb-4">
                            <div class="tour-block-two">
                                <div class="inner-box">
                                    <figure class="image-box">
                                        <img src="<?= htmlspecialchars($circuit['image_url'] ?? 'images/default-circuit.jpg') ?>" 
                                             alt="<?= htmlspecialchars($circuit['name']) ?>">
                                        <a href="circuit_detail.php?id=<?= $circuit['id'] ?>"><i class="fas fa-link"></i></a>
                                    </figure>
                                    <div class="content-box">
                                        <h3>
                                            <a href="circuit_detail.php?id=<?= $circuit['id'] ?>">
                                                <?= htmlspecialchars($circuit['name']) ?>
                                            </a>
                                        </h3>
                                        <h4>
                                            À partir de <?= number_format(min(
                                                $circuit['price_economy'], 
                                                $circuit['price_comfort'], 
                                                $circuit['price_premium']
                                            ), 0, ',', ' ') ?> CFA
                                            <span>/ Par personne</span>
                                        </h4>
                                        <p><?= htmlspecialchars(substr($circuit['description'], 0, 200)) ?>...</p>
                                        <div class="meta-info">
                                            <div class="badge bg-primary me-2">
                                                <i class="fas fa-clock"></i> 
                                                <?= $circuit['duration'] ?> jours
                                            </div>
                                            <?php if(!empty($circuit['sites'])): ?>
                                            <div class="badge bg-success me-2">
                                                <i class="fas fa-map-marker-alt"></i> 
                                                <?= htmlspecialchars($circuit['sites']) ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="btn-box mt-3">
                                            <a href="circuit_detail.php?id=<?= $circuit['id'] ?>" class="btn btn-primary">
                                                Voir les détails
                                            </a>
                                            <a href="index.php?page=reservation&circuit=<?= $circuit['id'] ?>" class="btn btn-success">
                                                Réserver maintenant
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">Aucun circuit disponible pour le moment</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

     <!-- Types of Circuits Section -->
     <section id="types" class="types">
        <div class="container">
            <div class="section-title">
                <h2>Types de Circuits</h2>
                <p>Choisissez votre aventure selon vos centres d'intérêt</p>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="type-item">
                        <i class="bi bi-bank"></i>
                        <h3>Circuits Culturels</h3>
                        <p>Immergez-vous dans l'histoire et la culture du Bénin à travers ses sites historiques, 
                        ses musées et ses festivals traditionnels.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="type-item">
                        <i class="bi bi-tree"></i>
                        <h3>Ecotourisme</h3>
                        <p>Explorez les parcs nationaux, les réserves naturelles et les écosystèmes uniques 
                        du Bénin tout en respectant l'environnement.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="type-item">
                        <i class="bi bi-water"></i>
                        <h3>Circuits Balnéaires</h3>
                        <p>Profitez des plages magnifiques du littoral béninois 
                        et détendez-vous au bord de l'océan Atlantique.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="type-item">
                        <i class="bi bi-stars"></i>
                        <h3>Tourisme Religieux</h3>
                        <p>Découvrez les sites spirituels et religieux importants 
                        du pays, y compris les lieux sacrés du Vodun.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="type-item">
                        <i class="bi bi-people"></i>
                        <h3>Circuits Ethniques</h3>
                        <p>Partez à la rencontre des différentes ethnies du Bénin 
                        et découvrez leurs traditions et modes de vie.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="type-item">
                        <i class="bi bi-compass"></i>
                        <h3>Aventure et Sport</h3>
                        <p>Pour les amateurs de sensations fortes, explorez le Bénin à travers 
                        des activités sportives et des aventures en plein air.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Planning Section -->
    <section class="planning-section">
        <div class="container">
            <div class="section-title">
                <h2>Comment Planifier Votre visite</h2>
                <p>Suivez ces étapes pour organiser votre séjour touristique parfait au Bénin</p>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="planning-container">
                        <div class="planning-step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Choisissez votre visite</h4>
                                <p>Sélectionnez le circuit qui correspond à vos centres d'intérêt, 
                                à votre budget et à la durée de votre séjour.</p>
                            </div>
                        </div>
                        
                        <div class="planning-step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Définissez les dates</h4>
                                <p>Réservez votre circuit en fonction de la saison. 
                                La période idéale pour visiter le Bénin est de novembre à avril (saison sèche).</p>
                            </div>
                        </div>
                        
                        <div class="planning-step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Réservez vos hébergements</h4>
                                <p>Choisissez parmi notre sélection d'hébergements authentiques 
                                pour chaque étape de votre circuit.</p>
                            </div>
                        </div>
                        
                        <div class="planning-step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h4>Préparez votre voyage</h4>
                                <p>Vérifiez les formalités (visa, vaccins), préparez vos bagages 
                                et informez-vous sur les coutumes locales.</p>
                            </div>
                        </div>
                        
                        <div class="planning-step">
                            <div class="step-number">5</div>
                            <div class="step-content">
                                <h4>Vivez l'expérience!</h4>
                                <p>Profitez de votre circuit accompagné par nos guides locaux experts 
                                et créez des souvenirs inoubliables.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </section>

    
</main>

<!-- [Scripts JavaScript] -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>