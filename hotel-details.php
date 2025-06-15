<?php
// Établir la connexion à la base de données
try {
    $host = "localhost";
    $dbname = "adjarra";
    $username = "root";
    $password = "Fadyl111";
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier si l'ID de l'hôtel est présent dans l'URL
    if(isset($_GET['id']) && is_numeric($_GET['id'])) {
        $hotel_id = $_GET['id'];
        
        // Récupérer les informations de l'hôtel spécifique
        $sql = "SELECT * FROM hotels WHERE id = :id AND status = 'approved'";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $hotel_id, PDO::PARAM_INT);
        $stmt->execute();
        $hotel = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$hotel) {
            // Si l'hôtel n'existe pas ou n'est pas approuvé
            $notFoundError = "Cet hôtel n'existe pas ou n'est pas disponible.";
        }
    } else {
        // Si l'ID n'est pas spécifié ou n'est pas un nombre
        $notFoundError = "ID d'hôtel non valide.";
    }
    
} catch(PDOException $e) {
    $errorMsg = "Erreur de connexion à la base de données : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($hotel) ? htmlspecialchars($hotel['hotel_name']) : 'Détails de l\'hôtel'; ?> - Adjarra</title>
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

        /* Alert Messages */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Hotel Details Section */
        .hotel-details-section {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .hotel-header {
            position: relative;
            height: 400px;
            overflow: hidden;
        }

        .hotel-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hotel-header-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0));
            padding: 30px;
            color: #fff;
        }

        .hotel-category-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #e9b732;
            color: #fff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .hotel-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .hotel-rating {
            display: flex;
            margin-bottom: 15px;
        }

        .star {
            color: #ffc107;
            font-size: 18px;
            margin-right: 3px;
        }

        .hotel-location {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .hotel-location i {
            margin-right: 10px;
        }

        .hotel-content {
            padding: 30px;
        }

        .hotel-content h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #333;
        }

        .description {
            margin-bottom: 30px;
            line-height: 1.8;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            display: flex;
            align-items: center;
        }

        .info-item i {
            width: 40px;
            height: 40px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e9b732;
            margin-right: 15px;
            font-size: 18px;
        }

        .info-details h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        .info-details p {
            font-size: 14px;
            color: #666;
        }

        .amenities {
            margin-bottom: 30px;
        }

        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .amenity-item i {
            color: #e9b732;
            margin-right: 10px;
            font-size: 16px;
        }

        .photos-section {
            margin-bottom: 30px;
        }

        .photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }

        .photo-item {
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .photo-item:hover {
            transform: scale(1.05);
        }

        .photo-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .contact-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }

        .contact-box {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .contact-item {
            flex: 1;
            min-width: 200px;
            display: flex;
            align-items: center;
        }

        .contact-item i {
            width: 40px;
            height: 40px;
            background: #e9b732;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            margin-right: 15px;
        }

        .contact-details h4 {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        .contact-details p, .contact-details a {
            font-size: 14px;
            color: #666;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .contact-details a:hover {
            color: #e9b732;
        }

        .booking-cta {
            text-align: center;
            margin-top: 30px;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .booking-cta h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .booking-cta p {
            margin-bottom: 20px;
            color: #666;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-button {
            display: inline-block;
            padding: 12px 30px;
            background: #e9b732;
            color: #fff;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            background: #d4a026;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(233, 183, 50, 0.3);
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .hotel-header {
                height: 300px;
            }
            
            .hotel-title {
                font-size: 28px;
            }
        }

        @media (max-width: 768px) {
            .hotel-header {
                height: 250px;
            }
            
            .hotel-title {
                font-size: 24px;
            }
            
            .info-grid, .amenities-grid, .photos-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            }
        }

        @media (max-width: 576px) {
            .hotel-header {
                height: 200px;
            }
            
            .hotel-header-overlay {
                padding: 20px;
            }
            
            .hotel-title {
                font-size: 20px;
            }
            
            .hotel-content {
                padding: 20px;
            }
            
            .info-grid, .amenities-grid, .photos-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Modal for Photo Gallery */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.9);
        }

        .modal-content {
            position: relative;
            margin: auto;
            padding: 0;
            width: 80%;
            max-width: 900px;
            max-height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content img {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }

        .close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
            z-index: 1001;
        }

        .close:hover,
        .close:focus {
            color: #e9b732;
            text-decoration: none;
            cursor: pointer;
        }

        .prev,
        .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #f1f1f1;
            font-size: 30px;
            font-weight: bold;
            transition: 0.3s;
            cursor: pointer;
            z-index: 1001;
            background: rgba(0, 0, 0, 0.3);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .prev {
            left: 20px;
        }

        .next {
            right: 20px;
        }

        .prev:hover,
        .next:hover {
            background: rgba(233, 183, 50, 0.7);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-logo">
                <h1>Bénin<span>Hotels</span></h1>
                <a href="index.php?page=photel" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour à la liste des hôtels
                </a>
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
                </div>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="index.php?page=photel" class="cta-button">Voir tous les hôtels</a>
                </div>
            <?php elseif(isset($hotel)): ?>
                <?php 
                    // Traitement des photos
                    $photos = [];
                    if(!empty($hotel['photo_files'])) {
                        $photos = json_decode($hotel['photo_files'], true);
                        if(!is_array($photos) || empty($photos)) {
                            $photos = ['/api/placeholder/800/600'];
                        }
                    } else {
                        $photos = ['/api/placeholder/800/600'];
                    }
                    
                    // Déterminer le nombre d'étoiles
                    $stars = intval($hotel['hotel_category']);
                ?>
                
                <!-- Détails de l'hôtel -->
                <div class="hotel-details-section">
                    <div class="hotel-header">
                        <img src="<?php echo htmlspecialchars($photos[0]); ?>" alt="<?php echo htmlspecialchars($hotel['hotel_name']); ?>">
                        <div class="hotel-category-badge"><?php echo $stars; ?> étoile<?php echo $stars > 1 ? 's' : ''; ?></div>
                        <div class="hotel-header-overlay">
                            <h1 class="hotel-title"><?php echo htmlspecialchars($hotel['hotel_name']); ?></h1>
                            <div class="hotel-rating">
                                <?php for($i = 0; $i < $stars; $i++): ?>
                                    <span class="star"><i class="fas fa-star"></i></span>
                                <?php endfor; ?>
                                <?php for($i = $stars; $i < 5; $i++): ?>
                                    <span class="star" style="color: rgba(255,255,255,0.5);"><i class="fas fa-star"></i></span>
                                <?php endfor; ?>
                            </div>
                            <div class="hotel-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($hotel['city']); ?>, <?php echo htmlspecialchars($hotel['region']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="hotel-content">
                        <!-- Description -->
                        <h3>À propos de l'hôtel</h3>
                        <div class="description">
                            <?php if(!empty($hotel['description'])): ?>
                                <?php echo nl2br(htmlspecialchars($hotel['description'])); ?>
                            <?php else: ?>
                                <p>Aucune description disponible pour cet hôtel.</p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Informations principales -->
                        <h3>Informations générales</h3>
                        <div class="info-grid">
                            <?php if(!empty($hotel['creation_year'])): ?>
                            <div class="info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div class="info-details">
                                    <h4>Année de création</h4>
                                    <p><?php echo $hotel['creation_year']; ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="info-item">
                                <i class="fas fa-bed"></i>
                                <div class="info-details">
                                    <h4>Nombre de chambres</h4>
                                    <p><?php echo $hotel['room_count']; ?></p>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <i class="fas fa-tag"></i>
                                <div class="info-details">
                                    <h4>Fourchette de prix</h4>
                                    <p><?php echo htmlspecialchars($hotel['price_range']); ?></p>
                                </div>
                            </div>
                            
                            <?php if(!empty($hotel['check_in_time'])): ?>
                            <div class="info-item">
                                <i class="fas fa-sign-in-alt"></i>
                                <div class="info-details">
                                    <h4>Heure d'enregistrement</h4>
                                    <p><?php echo date('H:i', strtotime($hotel['check_in_time'])); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if(!empty($hotel['check_out_time'])): ?>
                            <div class="info-item">
                                <i class="fas fa-sign-out-alt"></i>
                                <div class="info-details">
                                    <h4>Heure de départ</h4>
                                    <p><?php echo date('H:i', strtotime($hotel['check_out_time'])); ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="info-item">
                                <i class="fas fa-map-marked-alt"></i>
                                <div class="info-details">
                                    <h4>Adresse complète</h4>
                                    <p><?php echo htmlspecialchars($hotel['address']); ?>, <?php echo htmlspecialchars($hotel['city']); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Commodités -->
                        <h3>Commodités</h3>
                        <div class="amenities">
                            <div class="amenities-grid">
                                <?php if($hotel['has_wifi']): ?>
                                <div class="amenity-item">
                                    <i class="fas fa-wifi"></i>
                                    <span>WiFi gratuit</span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($hotel['has_parking']): ?>
                                <div class="amenity-item">
                                    <i class="fas fa-parking"></i>
                                    <span>Parking disponible</span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($hotel['has_pool']): ?>
                                <div class="amenity-item">
                                    <i class="fas fa-swimming-pool"></i>
                                    <span>Piscine</span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($hotel['has_restaurant']): ?>
                                <div class="amenity-item">
                                    <i class="fas fa-utensils"></i>
                                    <span>Restaurant</span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($hotel['has_ac']): ?>
                                <div class="amenity-item">
                                    <i class="fas fa-snowflake"></i>
                                    <span>Climatisation</span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($hotel['has_conference']): ?>
                                <div class="amenity-item">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <span>Salle de conférence</span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Photos -->
                        <?php if(count($photos) > 1): ?>
                        <h3>Galerie photos</h3>
                        <div class="photos-section">
                            <div class="photos-grid">
                                <?php foreach($photos as $index => $photo): ?>
                                <div class="photo-item" onclick="openModal();currentSlide(<?php echo $index + 1; ?>)">
                                    <img src="<?php echo htmlspecialchars($photo); ?>" alt="Photo de l'hôtel <?php echo htmlspecialchars($hotel['hotel_name']); ?>">
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Contact -->
                        <h3>Contact</h3>
                        <div class="contact-section">
                            <div class="contact-box">
                                <div class="contact-item">
                                    <i class="fas fa-user"></i>
                                    <div class="contact-details">
                                        <h4>Personne à contacter</h4>
                                        <p><?php echo htmlspecialchars($hotel['contact_name']); ?></p>
                                    </div>
                                </div>
                                
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <div class="contact-details">
                                        <h4>Téléphone</h4>
                                        <p><a href="tel:<?php echo htmlspecialchars($hotel['contact_phone']); ?>"><?php echo htmlspecialchars($hotel['contact_phone']); ?></a></p>
                                    </div>
                                </div>
                                
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <div class="contact-details">
                                        <h4>Email</h4>
                                        <p><a href="mailto:<?php echo htmlspecialchars($hotel['contact_email']); ?>"><?php echo htmlspecialchars($hotel['contact_email']); ?></a></p>
                                    </div>
                                </div>
                                
                                <?php if(!empty($hotel['website'])): ?>
                                <div class="contact-item">
                                    <i class="fas fa-globe"></i>
                                    <div class="contact-details">
                                        <h4>Site web</h4>
                                        <p><a href="<?php echo htmlspecialchars($hotel['website']); ?>" target="_blank"><?php echo htmlspecialchars($hotel['website']); ?></a></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section CTA Réservation -->
                <div class="booking-cta">
                    <h3>Vous souhaitez séjourner à l'hôtel <?php echo htmlspecialchars($hotel['hotel_name']); ?> ?</h3>
                    <p>Contactez l'hôtel directement pour vérifier la disponibilité et effectuer votre réservation. Mentionnez que vous avez trouvé l'hôtel via AdjarraHotels pour bénéficier des meilleurs tarifs.</p>
                    <a href="tel:<?php echo htmlspecialchars($hotel['contact_phone']); ?>" class="cta-button">Appeler pour réserver</a>
                </div>
                
                <!-- Modal pour la galerie photo -->
                <div id="photoModal" class="modal">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <div class="modal-content">
                        <?php foreach($photos as $index => $photo): ?>
                        <div class="mySlides" style="display: none;">
                            <img src="<?php echo htmlspecialchars($photo); ?>" alt="Photo de l'hôtel <?php echo htmlspecialchars($hotel['hotel_name']); ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                    <a class="next" onclick="plusSlides(1)">&#10095;</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Script pour la galerie photo
        let slideIndex = 1;
        
        function openModal() {
            document.getElementById("photoModal").style.display = "block";
            showSlides(slideIndex);
        }
        
        function closeModal() {
            document.getElementById("photoModal").style.display = "none";
        }
        
        function plusSlides(n) {
            showSlides(slideIndex += n);
        }
        
        function currentSlide(n) {
            showSlides(slideIndex = n);
        }
        
        function showSlides(n) {
            let i;
            const slides = document.getElementsByClassName("mySlides");
            
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            
            slides[slideIndex-1].style.display = "block";
        }
        
        // Fermer le modal quand on clique en dehors de l'image
        window.onclick = function(event) {
            const modal = document.getElementById("photoModal");
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>