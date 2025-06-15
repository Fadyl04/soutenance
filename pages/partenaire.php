<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réseau de Prestataires Touristiques du Bénin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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

        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }

        /* Column System */
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

        /* Hero Section */
        #hero {
            width: 100%;
            height: 80vh;
            background: url('img/partenaire.jpg') top center;
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

        /* Feature Cards */
        .feature-card {
            margin-bottom: 30px;
            overflow: hidden;
            border-radius: 15px;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-align: center;
            padding: 30px 20px;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .feature-icon {
            font-size: 48px;
            color: #e9b732;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
        }

        .feature-card p {
            font-size: 14px;
            color: #555;
            line-height: 1.7;
        }

/* Media queries pour responsivité */
@media (max-width: 991px) {
    .row {
        justify-content: center;
    }
    
    .col-md-6 {
        max-width: 450px;
        margin-left: auto;
        margin-right: auto;
    }
}
        /* CTA Section */
        .cta-section {
            padding: 60px 0;
            background: url('img/partenaire.jpg') center center;
            background-size: cover;
            position: relative;
            color: white;
            text-align: center;
        }

        .cta-section:before {
            content: "";
            background: rgba(0, 0, 0, 0.7);
            position: absolute;
            bottom: 0;
            top: 0;
            left: 0;
            right: 0;
        }

        .cta-section .container {
            position: relative;
            z-index: 2;
        }

        .cta-section h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .cta-section p {
            font-size: 16px;
            max-width: 700px;
            margin: 0 auto 30px;
        }


        /* Responsive Media Queries */
        @media (max-width: 768px) {
            #hero h1 {
                font-size: 36px;
                line-height: 42px;
            }
            
            #hero h2 {
                font-size: 18px;
            }
            
            .feature-card, .type-card {
                margin-bottom: 20px;
            }
            
            .section-title h2 {
                font-size: 28px;
            }
            
            .form-buttons {
                flex-direction: column;
            }
            
            .form-buttons .btn-get-started {
                width: 100%;
                margin-bottom: 10px;
            }

            .step-progress {
                flex-wrap: wrap;
            }

            .step {
                width: 50%;
                margin-bottom: 20px;
            }

            .step-progress:before {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section id="hero" class="d-flex align-items-center">
        <div class="container">
            <h1>Réseau de Prestataires</h1>
            <h2><i>Rejoignez notre plateforme touristique au Bénin</i></h2>
            
        </div>
    </section>

    <main id="main">
        <!-- Features Section -->
        <section id="features" class="main-content">
            <div class="container">
                <div class="section-title">
                    <h2>Pourquoi Nous Rejoindre</h2>
                    <p>Découvrez les avantages de faire partie de notre réseau de prestataires touristiques</p>
                </div>
                
                <div class="row">
                    <!-- Feature 1 -->
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-eye"></i>
                            </div>
                            <h3>Visibilité Accrue</h3>
                            <p>Présentez votre établissement à des milliers de visiteurs à la recherche d'expériences authentiques au Bénin.</p>
                        </div>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <h3>Réseau Professionnel</h3>
                            <p>Connectez-vous avec d'autres prestataires de qualité et créez des synergies profitables pour votre entreprise.</p>
                        </div>
                    </div>
                    
                    <!-- Feature 3 -->
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="feature-icon">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <h3>Opportunités d'affaires</h3>
                            <p>Accédez à de nouveaux marchés et augmentez vos réservations tout au long de l'année grâce à notre plateforme.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
<!-- Types de Providers Section -->
<section id="featured-types" class="main-content py-5">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2 class="mb-3">Types de Prestataires</h2>
            <p class="lead mx-auto" style="max-width: 700px;">Notre plateforme accueille quatre types essentiels de prestataires pour offrir une expérience touristique complète</p>
        </div>
        
        <div class="row" style="margin: 0 -20px;">
            <!-- Type 1 -->
            <div class="col-md-6 col-lg-3 provider-column">
                <div class="type-card">
                    <div class="type-img">
                        <img src="img/télécharger.jpeg" alt="Hôtels">
                    </div>
                    <div class="type-info">
                        <span class="service">Hébergement</span>
                        <h4>Hôtels</h4>
                        <p>Proposez des services d'hébergement de qualité pour nos circuits touristiques à travers le Bénin.</p>
                        <a href="hotel.php" class="read-more" id="openModalHotel" target="_blank">S'inscrire</a>
                    </div>
                </div>
            </div>
            
           
            
            <!-- Type 3 -->
            <div class="col-md-6 col-lg-3 provider-column">
                <div class="type-card">
                    <div class="type-img">
                        <img src="img/OIP2.jpeg" alt="Transport">
                    </div>
                    <div class="type-info">
                        <span class="service">Transport</span>
                        <h4>Transporteurs</h4>
                        <p>Assurez les déplacements confortables et sécurisés durant les circuits touristiques à travers le pays.</p>
                        <a href="transport.php" class="read-more" id="openModalTransport" target="_blank">S'inscrire</a>
                    </div>
                </div>
            </div>
            
            <!-- Type 4 -->
            <div class="col-md-6 col-lg-3 provider-column">
                <div class="type-card">
                    <div class="type-img">
                        <img src="img/OIP3.jpeg" alt="Guides Touristiques">
                    </div>
                    <div class="type-info">
                        <span class="service">Guides</span>
                        <h4>Guides Touristiques</h4>
                        <p>Partagez votre passion et votre expertise du Bénin avec les visiteurs du monde entier.</p>
                        <a href="guide.php" class="read-more" id="openModalGuide" target="_blank">S'inscrire</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Améliorations pour la section Types de Prestataires */
.main-content {
    background-color: #f8f9fa;
    padding: 80px 0;
}

.section-title {
    margin-bottom: 60px;
}

.section-title h2 {
    font-size: 36px;
    font-weight: 700;
    position: relative;
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.section-title h2:after {
    content: '';
    position: absolute;
    display: block;
    width: 60px;
    height: 3px;
    background: #e9b732;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
}

.section-title p {
    font-size: 18px;
    color: #555;
}

/* Espacement amélioré entre les cartes */
.provider-column {
    padding: 0 20px;
    margin-bottom: 40px;
}

.type-card {
    overflow: hidden;
    border-radius: 15px;
    background: #fff;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.4s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    border: none;
}

.type-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
}

.type-img {
    position: relative;
    overflow: hidden;
    height: 220px;
}

.type-img::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0) 60%, rgba(0,0,0,0.4) 100%);
    transition: all 0.3s ease;
}

.type-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s;
}

.type-card:hover .type-img img {
    transform: scale(1.1);
}

.type-info {
    padding: 30px 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    background-color: #fff;
}

.type-info .service {
    display: inline-block;
    background-color: #e9b732;
    color: #fff;
    padding: 6px 16px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 18px;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 2px 6px rgba(233, 183, 50, 0.2);
}

.type-info h4 {
    font-weight: 700;
    margin-bottom: 15px;
    font-size: 22px;
    color: #222;
    line-height: 1.3;
}

.type-info p {
    color: #666;
    font-size: 15px;
    line-height: 1.7;
    margin-bottom: 25px;
    flex-grow: 1;
}

.read-more {
    display: inline-block;
    background: #e9b732;
    color: #fff;
    padding: 12px 28px;
    border-radius: 50px;
    transition: all 0.3s ease;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    letter-spacing: 0.5px;
    margin-top: auto;
    align-self: flex-start;
    box-shadow: 0 4px 10px rgba(233, 183, 50, 0.3);
    text-align: center;
}

.read-more:hover {
    background: #d4a026;
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(233, 183, 50, 0.4);
    color: #fff;
}

/* Responsive adjustments */
@media (max-width: 991px) {
    .provider-column {
        padding: 0 15px;
        margin-bottom: 30px;
    }
}

@media (max-width: 767px) {
    .section-title h2 {
        font-size: 30px;
    }
    
    .type-info {
        padding: 25px 20px;
    }
    
    .type-info h4 {
        font-size: 20px;
    }
    
    .provider-column {
        padding: 0 25px;
        margin-bottom: 40px;
    }
}

@media (max-width: 575px) {
    .provider-column {
        padding: 0 30px;
    }
}
</style>
        
        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="section-title">
                    <h2>Prêt à Développer Votre Activité ?</h2>
                    <p>Rejoignez notre réseau de prestataires touristiques et bénéficiez d'une visibilité accrue auprès d'une clientèle internationale</p>
                </div>
                <a href="#featured-types" class="btn-get-started scrollto">S'inscrire Maintenant</a>
            </div>
        </section>
    </main>

</body>
</html>