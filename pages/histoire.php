<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histoire du Bénin - Découvrez le riche passé culturel</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Encapsulation de tout le CSS dans une classe unique pour éviter d'affecter d'autres pages */
        .benin-history-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .benin-history-page .container {
            max-width: 1200px;
        }
        
        .benin-history-page .content-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 40px;
            margin-bottom: 40px;
        }
        
        .benin-history-page .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .benin-history-page .header h1 {
            color: #ffc107;
            font-weight: 700;
        }
        
        .benin-history-page .header p {
            color: #6c757d;
        }
        
        .benin-history-page .history-section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
        
        .benin-history-page .section-title {
            color: #ffc107;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .benin-history-page .section-title i {
            margin-right: 10px;
            color: #ffc107;
        }
        
        .benin-history-page .image-carousel {
            margin: 20px auto;
            position: relative;
            width: 100%;
            max-width: 600px;
            height: 350px;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .benin-history-page .carousel-inner {
            height: 100%;
        }
        
        .benin-history-page .carousel-item {
            height: 100%;
        }
        
        .benin-history-page .carousel-item img {
            height: 100%;
            object-fit: cover;
        }
    </style>
</head> <br> <br> <br> <br>
<body class="benin-history-page">
    <div class="container">
        <div class="content-container">
            <div class="header">
                <h1><i class="fas fa-landmark"></i> Histoire du Bénin</h1>
                <p>Découvrez le riche passé historique et culturel du Bénin, berceau de grandes civilisations africaines</p>
            </div>
            
            <!-- Section Royaumes Anciens -->
            <div class="history-section">
                <h3 class="section-title"><i class="fas fa-crown"></i> Les Royaumes Anciens</h3>
                <p>L'histoire du Bénin est marquée par de puissants royaumes qui ont laissé un riche héritage. Le Royaume du Dahomey (1600-1894), avec ses palais royaux d'Abomey classés au patrimoine mondial de l'UNESCO, témoigne de cette grandeur passée.</p>
                <p>Au nord du pays, la région était autrefois le territoire des royaumes Bariba et Somba, dont l'architecture unique des Tata Somba (maisons-forteresses) constitue aujourd'hui un attrait touristique majeur.</p>
                
                <!-- Carousel Royaumes -->
                <div id="royaumesCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Palais royaux d'Abomey">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Tata Somba">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#royaumesCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#royaumesCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Route des Esclaves -->
            <div class="history-section">
                <h3 class="section-title"><i class="fas fa-route"></i> La Route des Esclaves et Ouidah</h3>
                <p>La ville côtière de Ouidah représente un témoignage poignant de la période de la traite négrière. La Route des Esclaves, parcours de 4 km que les captifs empruntaient jusqu'à la mer, est jalonnée de monuments commémoratifs.</p>
                <p>Elle mène à la Porte du Non-Retour, mémorial érigé sur la plage pour honorer les millions d'Africains déportés. Le Fort portugais de Ouidah, transformé en musée d'histoire, et le Temple des Pythons complètent ce circuit historique et spirituel incontournable.</p>
                
                <!-- Carousel Ouidah -->
                <div id="ouidahCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Porte du Non-Retour">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Temple des Pythons">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#ouidahCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#ouidahCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Période Coloniale -->
            <div class="history-section">
                <h3 class="section-title"><i class="fas fa-archway"></i> Période Coloniale et Cotonou</h3>
                <p>Durant la colonisation française, Cotonou s'est développée comme centre administratif et commercial. La Fondation Zinsou, installée dans l'ancienne Villa Ajavon, est aujourd'hui un musée d'art contemporain africain de renommée internationale.</p>
                <p>Le quartier de Ganhi avec son architecture coloniale et le Grand Marché de Dantokpa, plus grand marché à ciel ouvert d'Afrique de l'Ouest, témoignent de cette période d'échanges intenses qui a façonné la ville.</p>
                
                <!-- Carousel Colonial -->
                <div id="colonialCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Fondation Zinsou">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Marché Dantokpa">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#colonialCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#colonialCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Vodun -->
            <div class="history-section">
                <h3 class="section-title"><i class="fas fa-pray"></i> Le Berceau du Vodun</h3>
                <p>Le Bénin est considéré comme le berceau du vodun, religion ancestrale qui s'est répandue à travers le monde lors de la diaspora africaine. La Forêt Sacrée de Kpassè à Ouidah et la Forêt de la Bouche du Roi près de Grand-Popo sont des sanctuaires naturels où se déroulent encore d'importantes cérémonies religieuses.</p>
                <p>Chaque année en janvier, le Festival International du Vodun attire des milliers de visiteurs venus découvrir cette tradition spirituelle vivante, reconnue comme patrimoine culturel immatériel.</p>
                
                <!-- Carousel Vodun -->
                <div id="vodunCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Cérémonie Vodun">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Forêt Sacrée">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#vodunCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#vodunCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Bénin Moderne -->
            <div class="history-section">
                <h3 class="section-title"><i class="fas fa-city"></i> Le Bénin Moderne</h3>
                <p>Depuis son renouveau démocratique dans les années 1990, le Bénin a développé son offre culturelle et touristique. Le Musée d'Histoire de Ouidah, le Musée da Silva des Arts et de la Culture, et plus récemment le complexe touristique de la Porte du Retour, valorisent l'histoire et le patrimoine du pays.</p>
                <p>En 2022, l'ouverture du musée des trésors royaux à Abomey suite à la restitution d'œuvres par la France marque une nouvelle étape dans la reconnexion du Bénin avec son passé et la mise en valeur de son riche patrimoine historique.</p>
                
                <!-- Carousel Moderne -->
                <div id="moderneCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Musée des trésors royaux">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Cotonou moderne">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#moderneCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#moderneCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
           
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>