<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culture du Bénin - Traditions et expressions culturelles</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Encapsulation de tout le CSS dans une classe unique pour éviter d'affecter d'autres pages */
        .benin-culture-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .benin-culture-page .container {
            max-width: 1200px;
        }
        
        .benin-culture-page .content-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 40px;
            margin-bottom: 40px;
        }
        
        .benin-culture-page .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .benin-culture-page .header h1 {
            color: #ff6b6b;
            font-weight: 700;
        }
        
        .benin-culture-page .header p {
            color: #6c757d;
        }
        
        .benin-culture-page .culture-section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
        
        .benin-culture-page .section-title {
            color: #ff6b6b;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .benin-culture-page .section-title i {
            margin-right: 10px;
            color: #ff6b6b;
        }
        
        .benin-culture-page .image-carousel {
            margin: 20px auto;
            position: relative;
            width: 100%;
            max-width: 600px;
            height: 350px;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .benin-culture-page .carousel-inner {
            height: 100%;
        }
        
        .benin-culture-page .carousel-item {
            height: 100%;
        }
        
        .benin-culture-page .carousel-item img {
            height: 100%;
            object-fit: cover;
        }
        
        .benin-culture-page .quote-box {
            background-color: #ffe8e8;
            border-left: 4px solid #ff6b6b;
            padding: 15px;
            margin: 20px 0;
            font-style: italic;
            border-radius: 0 10px 10px 0;
        }
    </style>
</head> <br> <br> <br> <br>
<body class="benin-culture-page">
    <div class="container">
        <div class="content-container">
            <div class="header">
                <h1><i class="fas fa-masks-theater"></i> Culture du Bénin</h1>
                <p>Découvrez les richesses culturelles et traditions ancestrales du Bénin</p>
            </div>
            
            <!-- Section Musique et danse -->
            <div class="culture-section">
                <h3 class="section-title"><i class="fas fa-music"></i> Musique et Danse</h3>
                <p>La musique béninoise est d'une richesse exceptionnelle, mêlant rythmes traditionnels et influences modernes. Le Tchinkounmè, le Agbadja et le Zinli sont des danses traditionnelles du sud, tandis que le Tékè et le Bata caractérisent les régions centrales et septentrionales.</p>
                <p>Le Bénin a donné naissance à des genres musicaux internationalement reconnus comme le Zinli, le Tchinkoumè et surtout le Vodoun-Jazz. Angélique Kidjo, artiste béninoise de renommée mondiale, puise son inspiration dans ces traditions pour créer une musique engagée et universelle.</p>
                
                <div class="quote-box">
                    "La musique béninoise est comme une rivière qui descend des collines, emportant avec elle les pierres du passé et les feuilles du présent, pour nourrir l'océan de notre avenir culturel."
                </div>
                
                <!-- Carousel Musique -->
                <div id="musiqueCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Danse traditionnelle béninoise">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Musiciens béninois">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#musiqueCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#musiqueCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Art et Artisanat -->
            <div class="culture-section">
                <h3 class="section-title"><i class="fas fa-palette"></i> Art et Artisanat</h3>
                <p>L'artisanat béninois témoigne d'un savoir-faire ancestral transmis de génération en génération. Les sculptures sur bois, notamment les statues vaudou et les masques cérémoniels, sont mondialement reconnues pour leur expressivité et leur finesse.</p>
                <p>Les artisans de Ganvié et d'Abomey excellent dans la création de tapisseries appliquées racontant l'histoire et les mythes du royaume. À Cotonou et Porto-Novo, le Centre de Promotion de l'Artisanat et la Fondation Zinsou exposent ces œuvres qui allient tradition et modernité.</p>
                
                <div class="quote-box">
                    "Chaque pièce artisanale béninoise porte en elle l'âme de son créateur et les murmures de nos ancêtres. Leurs mains façonnent la matière comme le temps façonne notre mémoire collective."
                </div>
                
                <!-- Carousel Artisanat -->
                <div id="artisanatCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Sculptures béninoises">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Tapisseries d'Abomey">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#artisanatCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#artisanatCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Gastronomie -->
            <div class="culture-section">
                <h3 class="section-title"><i class="fas fa-utensils"></i> Cuisine et Gastronomie</h3>
                <p>La cuisine béninoise est un savoureux mélange de saveurs et d'influences. Le plat national, la sauce d'arachide accompagnée de pâte de maïs (akassa) ou d'igname pilée (foutou), varie selon les régions et les traditions familiales.</p>
                <p>Les spécialités comme le kuli-kuli (beignets d'arachide), le djenkoumé (maïs fermenté), et le fromage peulh témoignent de la diversité culinaire du pays. Sur la côte, les fruits de mer et le poisson sont cuisinés en sauce avec de l'huile de palme pour des plats comme le "poisson au coco" très appréciés.</p>
                
                <div class="quote-box">
                    "Notre cuisine est comme notre culture : généreuse, variée et accueillante. Chaque plat raconte l'histoire d'un terroir, la saga d'un peuple qui a su transformer la simplicité en art de vivre."
                </div>
                
                <!-- Carousel Gastronomie -->
                <div id="gastronomieCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Plats béninois">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Marché alimentaire">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#gastronomieCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#gastronomieCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Festivals -->
            <div class="culture-section">
                <h3 class="section-title"><i class="fas fa-calendar-day"></i> Fêtes et Festivals</h3>
                <p>Le calendrier béninois est rythmé par de nombreuses célébrations. Le Festival International du Vodun en janvier est l'un des plus importants, attirant des visiteurs du monde entier pour découvrir cette spiritualité ancestrale à travers danses, rituels et cérémonies.</p>
                <p>Le Carnaval de Ouidah, la Fête de Gani chez les Batammariba, et le Festival des Masques de Nikki sont d'autres moments forts où les traditions se perpétuent. Plus récemment, des événements comme les Récréâtrales, le Festival International de Théâtre du Bénin et le Festival des Arts du Vodun témoignent de la vitalité culturelle du pays.</p>
                
                <div class="quote-box">
                    "Dans nos festivals, le temps s'arrête pour que danse l'éternité. Les tambours battent au rythme de nos cœurs, et les masques révèlent ce que nos visages cachent."
                </div>
                
                <!-- Carousel Festivals -->
                <div id="festivalsCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Festival du Vodun">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Carnaval de Ouidah">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#festivalsCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#festivalsCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Littérature -->
            <div class="culture-section">
                <h3 class="section-title"><i class="fas fa-book"></i> Littérature et Oralité</h3>
                <p>Le Bénin possède une riche tradition de littérature orale, avec des contes, proverbes et chants transmis de génération en génération. Les griots, détenteurs de cette mémoire collective, jouent encore un rôle important dans la préservation des savoirs ancestraux.</p>
                <p>Des écrivains béninois comme Olympe Bhêly-Quenum, Jean Pliya et plus récemment Florent Couao-Zotti ont contribué à faire connaître la littérature béninoise au-delà des frontières. Le renouveau littéraire se manifeste également par l'émergence de jeunes auteurs et de festivals comme les "Littératures métisses" qui célèbrent la diversité culturelle du pays.</p>
                
                <div class="quote-box">
                    "Les mots sont nos ponts entre hier et demain. Quand nos écrivains prennent la plume, c'est la voix millénaire de notre terre qui s'élève pour dialoguer avec le monde."
                </div>
                
                <!-- Carousel Littérature -->
                <div id="litteratureCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Conteur béninois">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Festival littéraire">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#litteratureCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#litteratureCarousel" data-bs-slide="next">
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