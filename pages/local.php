<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Économie du Bénin - Secteurs clés et développement</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Encapsulation de tout le CSS dans une classe unique pour éviter d'affecter d'autres pages */
        .benin-economy-page {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .benin-economy-page .container {
            max-width: 1200px;
        }
        
        .benin-economy-page .content-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 40px;
            margin-bottom: 40px;
        }
        
        .benin-economy-page .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .benin-economy-page .header h1 {
            color: #38b000;
            font-weight: 700;
        }
        
        .benin-economy-page .header p {
            color: #6c757d;
        }
        
        .benin-economy-page .economy-section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
        
        .benin-economy-page .section-title {
            color: #38b000;
            margin-bottom: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .benin-economy-page .section-title i {
            margin-right: 10px;
            color: #38b000;
        }
        
        .benin-economy-page .image-carousel {
            margin: 20px auto;
            position: relative;
            width: 100%;
            max-width: 600px;
            height: 350px;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .benin-economy-page .carousel-inner {
            height: 100%;
        }
        
        .benin-economy-page .carousel-item {
            height: 100%;
        }
        
        .benin-economy-page .carousel-item img {
            height: 100%;
            object-fit: cover;
        }
        
        .benin-economy-page .stats-box {
            background-color: #e8f5e9;
            border-left: 4px solid #38b000;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 10px 10px 0;
        }
        
        .benin-economy-page .stats-figure {
            font-size: 2.5rem;
            font-weight: bold;
            color: #38b000;
            margin-bottom: 5px;
        }
        
        .benin-economy-page .stats-title {
            font-weight: 600;
            color: #555;
        }
        
        .benin-economy-page .stats-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin: 0 -10px;
        }
        
        .benin-economy-page .stats-col {
            flex: 1;
            min-width: 200px;
            padding: 10px;
            margin: 10px;
            text-align: center;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head> <br> <br> <br> <br>
<body class="benin-economy-page">
    <div class="container">
        <div class="content-container">
            <div class="header">
                <h1><i class="fas fa-chart-line"></i> Économie du Bénin</h1>
                <p>Découvrez les secteurs clés et les dynamiques de l'économie béninoise</p>
            </div>
            
            <!-- Vue d'ensemble économique -->
            <div class="economy-section">
                <h3 class="section-title"><i class="fas fa-globe"></i> Vue d'ensemble</h3>
                <p>L'économie du Bénin, bien que modeste à l'échelle mondiale, joue un rôle important dans la région ouest-africaine. Historiquement axée sur l'agriculture, l'économie béninoise se diversifie progressivement, avec un développement notable dans les secteurs des services et du commerce régional.</p>
                <p>Depuis les années 2010, le pays a connu une croissance économique relativement stable, tirée par les réformes structurelles, les investissements dans les infrastructures et le développement du port autonome de Cotonou, véritable poumon économique du pays.</p>
                
                <div class="stats-box">
                    <div class="stats-row">
                        <div class="stats-col">
                            <div class="stats-figure">5,6%</div>
                            <div class="stats-title">Croissance moyenne (2017-2023)</div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-figure">12,7 Mds $</div>
                            <div class="stats-title">PIB (2022)</div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-figure">1 250 $</div>
                            <div class="stats-title">PIB par habitant</div>
                        </div>
                    </div>
                </div>
                
                <!-- Carousel Vue d'ensemble -->
                <div id="overviewCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Port de Cotonou">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Marchés béninois">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#overviewCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#overviewCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Agriculture -->
            <div class="economy-section">
                <h3 class="section-title"><i class="fas fa-tractor"></i> Agriculture</h3>
                <p>L'agriculture reste le pilier de l'économie béninoise, employant près de 70% de la population active et contribuant pour environ 30% au PIB national. Le coton est la principale culture d'exportation, plaçant le Bénin parmi les plus grands producteurs africains, avec une production annuelle dépassant 600 000 tonnes.</p>
                <p>Les cultures vivrières comme le maïs, le sorgho, le manioc et l'igname assurent la sécurité alimentaire du pays, tandis que l'anacarde (noix de cajou) et l'ananas sont devenus d'importantes sources de revenus à l'exportation. Des efforts sont déployés pour moderniser le secteur et développer l'agro-industrie, notamment dans la transformation du coton et des fruits tropicaux.</p>
                
                <div class="stats-box">
                    <div class="stats-row">
                        <div class="stats-col">
                            <div class="stats-figure">70%</div>
                            <div class="stats-title">Population active dans l'agriculture</div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-figure">30%</div>
                            <div class="stats-title">Contribution au PIB</div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-figure">600 000+</div>
                            <div class="stats-title">Tonnes de coton par an</div>
                        </div>
                    </div>
                </div>
                
                <!-- Carousel Agriculture -->
                <div id="agricultureCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Champs de coton">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Récolte d'ananas">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#agricultureCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#agricultureCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Commerce et Services -->
            <div class="economy-section">
                <h3 class="section-title"><i class="fas fa-shopping-cart"></i> Commerce et Services</h3>
                <p>Le secteur tertiaire représente plus de 40% du PIB béninois, en grande partie grâce au commerce transfrontalier. Le Port Autonome de Cotonou est une infrastructure stratégique qui dessert non seulement le Bénin mais aussi les pays enclavés comme le Niger, le Mali et le Burkina Faso.</p>
                <p>Le commerce informel reste important, particulièrement avec le Nigeria voisin. Le secteur bancaire s'est considérablement développé, avec l'implantation de nombreuses institutions financières et l'expansion des services de mobile money qui facilitent les transactions, notamment dans les zones rurales. Le tourisme, bien que modeste, se développe autour des sites historiques comme Ouidah et les palais d'Abomey.</p>
                
                <div class="stats-box">
                    <div class="stats-row">
                        <div class="stats-col">
                            <div class="stats-figure">40%+</div>
                            <div class="stats-title">Contribution des services au PIB</div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-figure">12 Mio</div>
                            <div class="stats-title">Tonnes de marchandises/an au port</div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-figure">300 000+</div>
                            <div class="stats-title">Visiteurs annuels</div>
                        </div>
                    </div>
                </div>
                
                <!-- Carousel Commerce -->
                <div id="commerceCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Port de Cotonou">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Marché Dantokpa">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#commerceCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#commerceCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Industrie et Énergie -->
            <div class="economy-section">
                <h3 class="section-title"><i class="fas fa-industry"></i> Industrie et Énergie</h3>
                <p>Le secteur industriel béninois est en phase de développement, représentant environ 15% du PIB. Il est principalement axé sur la transformation des produits agricoles, la production de ciment, et l'industrie textile liée au coton. Les zones industrielles de Sèmè-Kpodji et de Glo-Djigbé visent à attirer les investissements directs étrangers.</p>
                <p>En matière d'énergie, le Bénin importe encore une grande partie de son électricité, principalement du Ghana et du Nigeria, mais développe des projets solaires ambitieux comme la centrale photovoltaïque de Pobè. Le projet de construction d'un pipeline d'exportation pour le pétrole et le gaz du Niger représente également une opportunité pour le pays.</p>
                
                <div class="stats-box">
                    <div class="stats-row">
                        <div class="stats-col">
                            <div class="stats-figure">15%</div>
                            <div class="stats-title">Contribution de l'industrie au PIB</div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-figure">40%</div>
                            <div class="stats-title">Taux d'électrification</div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-figure">25 MW</div>
                            <div class="stats-title">Capacité solaire installée</div>
                        </div>
                    </div>
                </div>
                
                <!-- Carousel Industrie -->
                <div id="industrieCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Zone industrielle">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Centrale solaire">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#industrieCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#industrieCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Suivant</span>
                    </button>
                </div>
            </div>
            
            <!-- Section Défis et Perspectives -->
            <div class="economy-section">
                <h3 class="section-title"><i class="fas fa-road"></i> Défis et Perspectives</h3>
                <p>L'économie béninoise fait face à plusieurs défis structurels comme la dépendance aux importations, une infrastructure encore insuffisante malgré les récents investissements, et la vulnérabilité aux chocs externes. Le Programme d'Actions du Gouvernement (PAG) met l'accent sur la diversification économique et l'amélioration de la gouvernance.</p>
                <p>Les perspectives d'avenir sont encouragées par les investissements dans le tourisme, le développement du numérique notamment avec l'expansion de la fibre optique, et les projets d'aménagement urbanistique comme la modernisation de Cotonou et le développement du site touristique de la Porte du Non-Retour à Ouidah. L'intégration régionale au sein de la CEDEAO et l'Accord de Libre-Échange Continental Africain offrent également de nouvelles opportunités.</p>
                
                <div class="stats-box">
                    <div class="stats-row">
                        <div class="stats-col">
                            <div class="stats-figure">4,5%</div>
                            <div class="stats-title">Prévision de croissance 2025</div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-figure">30%</div>
                            <div class="stats-title">Population sous le seuil de pauvreté</div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-figure">$2 Mds</div>
                            <div class="stats-title">Investissements prévus en infrastructures</div>
                        </div>
                    </div>
                </div>
                
                <!-- Carousel Perspectives -->
                <div id="perspectivesCarousel" class="carousel slide image-carousel" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Projet urbanistique à Cotonou">
                        </div>
                        <div class="carousel-item">
                            <img src="/api/placeholder/600/350" class="d-block w-100" alt="Infrastructure numérique">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#perspectivesCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Précédent</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#perspectivesCarousel" data-bs-slide="next">
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