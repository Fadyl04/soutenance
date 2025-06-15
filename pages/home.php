<section id="hero">
        <div class="slideshow">
            <div class="slide active"></div>
            <div class="slide"></div>
            <div class="slide"></div>
            <div class="slide"></div>
            <div class="slide"></div>
        </div>

        <div class="container">
            <h1>Bienvenue au Bénin</h1>
            <h2><em>Joyau de l'Afrique de l'Ouest</em></h2>
            <a href="#about" class="btn-get-started">Découvrez le Bénin</a>
        </div>

        <div class="slideshow-indicators">
            <div class="indicator active" data-index="0"></div>
            <div class="indicator" data-index="1"></div>
            <div class="indicator" data-index="2"></div>
            <div class="indicator" data-index="3"></div>
            <div class="indicator" data-index="4"></div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuration du diaporama
            const slides = document.querySelectorAll('.slide');
            const indicators = document.querySelectorAll('.indicator');
            const slideshowInterval = 20000; // 20 secondes
            let currentSlide = 0;
            
            // Fonction pour passer à la diapositive suivante
            function nextSlide() {
                slides[currentSlide].classList.remove('active');
                indicators[currentSlide].classList.remove('active');
                
                currentSlide = (currentSlide + 1) % slides.length;
                
                slides[currentSlide].classList.add('active');
                indicators[currentSlide].classList.add('active');
            }
            
            // Démarrer le diaporama automatique
            let slideInterval = setInterval(nextSlide, slideshowInterval);
            
            // Permettre la navigation manuelle via les indicateurs
            indicators.forEach(indicator => {
                indicator.addEventListener('click', function() {
                    // Arrêter le défilement automatique momentanément
                    clearInterval(slideInterval);
                    
                    // Masquer la diapositive actuelle
                    slides[currentSlide].classList.remove('active');
                    indicators[currentSlide].classList.remove('active');
                    
                    // Afficher la diapositive sélectionnée
                    currentSlide = parseInt(this.dataset.index);
                    slides[currentSlide].classList.add('active');
                    indicators[currentSlide].classList.add('active');
                    
                    // Redémarrer le défilement automatique
                    slideInterval = setInterval(nextSlide, slideshowInterval);
                });
            });
        });
    </script>

<main id="main">
    <!-- Why Us Section -->
    <section id="why-us" class="why-us">
  <style>
    .why-us {
      padding: 60px 15px;
      background: #f9f9f9;
    }
    .why-us .main-block .more-btn:hover {
      background: #d4a026;
    }

    /* Style for the main content block */
    .why-us .main-block {
      background: #fff;
      color: #333;
      border-radius: 15px;
      padding: 40px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .why-us .main-block h3 {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .why-us .main-block p {
      font-size: 18px;
      line-height: 1.8;
      margin-bottom: 20px;
    }

    .why-us .main-block .more-btn {
      background: #e9b732;
      color: #fff;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      display: inline-block;
      transition: background 0.3s;
    }

    /* Style for icon boxes */
    .why-us .icon-box {
      background: #fff;
      border-radius: 15px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }

    .why-us .icon-box:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .why-us .icon-box img {
      display: block;
      margin: 0 auto 15px;
      border-radius: 50%;
      width: 120px;
      height: 120px;
      object-fit: cover;
    }

    .why-us .icon-box h4 {
      font-size: 20px;
      font-weight: bold;
      margin-top: 15px;
      margin-bottom: 10px;
    }

    .why-us .icon-box p {
      font-size: 15px;
      color: #555;
      line-height: 1.6;
    }
  </style>

  <div class="container">
    <!-- Main Block -->
    <div class="row justify-content-center mb-5">
      <div class="col-lg-10">
        <div class="main-block">
          <h3>Découvrir les Merveilles du Bénin</h3>
          <p>
            Embarquez pour un voyage inoubliable au cœur du Bénin, terre de culture, d'histoire et de paysages époustouflants. Le Bénin vous invite à découvrir ses trésors naturels, son patrimoine ancestral et la chaleur de son accueil.
          </p>
          <a href="?page=sites" class="more-btn">Nos destinations <i class="bx bx-chevron-right"></i></a>
        </div>
      </div>
    </div>

    <!-- Icon Boxes -->
    <div class="icon-boxes">
      <div class="row gy-4">
        <div class="col-lg-4">
          <div class="icon-box">
            <img src="img/cul2.jpeg" alt="Culture et Traditions">
            <h4>Culture et Traditions</h4>
            <p>Un patrimoine unique, berceau du Vodun et des anciens royaumes.</p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="icon-box">
            <img src="img/tourisme1.jpg" alt="Paysages et Nature">
            <h4>Paysages et Nature</h4>
            <p>Des plages de sable fin aux parcs nationaux, découvrez une biodiversité exceptionnelle.</p>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="icon-box">
            <img src="img/gastro.jpeg" alt="Gastronomie">
            <h4>Gastronomie</h4>
            <p>Savourez les délices de la cuisine béninoise, mélange de saveurs et d'influences.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
    <!-- End Why Us Section -->

  <!-- Partners Section - Redesigned -->
<section id="partners" class="partners-section">
  <style>
    .partners-section {
      padding: 80px 0;
      background-color: #fff;
      position: relative;
    }
    
    .partners-section::after {
      content: "";
      position: absolute;
      bottom: 0;
      right: 0;
      width: 200px;
      height: 200px;
      background-color: rgba(0, 135, 81, 0.05);
      border-radius: 50%;
      z-index: 0;
    }
    
    .partners-heading {
      text-align: center;
      margin-bottom: 40px;
    }
    
    .partners-heading h2 {
      font-size: 36px;
      font-weight: 700;
      color: #008751;
      margin-bottom: 15px;
    }
    
    .partners-heading p {
      font-size: 18px;
      color: #555;
      max-width: 800px;
      margin: 0 auto;
    }
    
    .partners-content {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 40px;
    }
    
    .partners-text {
      flex: 1;
      padding-right: 30px;
    }
    
    .partners-text h3 {
      font-size: 24px;
      color: #333;
      margin-bottom: 20px;
    }
    
    .partners-text ul {
      list-style: none;
      padding: 0;
      margin-bottom: 25px;
    }
    
    .partners-text ul li {
      position: relative;
      padding-left: 25px;
      margin-bottom: 12px;
      color: #555;
    }
    
    .partners-text ul li:before {
      content: "\2713";
      position: absolute;
      left: 0;
      color: #E8702A;
      font-weight: bold;
    }
    
    .partners-image {
      flex: 1;
      text-align: center;
    }
    
    .partners-image img {
      max-width: 100%;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .partner-types {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin: 40px 0;
    }
    
    .partner-type {
      width: 180px;
      text-align: center;
      padding: 25px 15px;
      border: 2px solid #ddd;
      border-radius: 10px;
      transition: all 0.3s;
      background-color: #fff;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .partner-type:hover {
      border-color: #008751;
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .partner-type i {
      font-size: 32px;
      color: #008751;
      margin-bottom: 15px;
      display: block;
    }
    
    .partner-type span {
      display: block;
      font-weight: 500;
      font-size: 18px;
      color: #333;
    }
    
    .cta-container {
      text-align: center;
      margin-top: 40px;
    }
    
    .btn-register {
      display: inline-block;
      background-color: #008751;
      color: white;
      border: none;
      padding: 15px 35px;
      border-radius: 50px;
      font-size: 18px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s;
      box-shadow: 0 5px 15px rgba(0, 135, 81, 0.3);
    }
    
    .btn-register:hover {
      background-color: #006b41;
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 107, 65, 0.4);
    }
    
    .btn-register i {
      margin-left: 8px;
    }

    .benefits-cards {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 40px;
      justify-content: center;
    }
    
    .benefit-card {
      background-color: #f9f9f9;
      border-radius: 10px;
      padding: 25px;
      flex: 1;
      min-width: 250px;
      max-width: 300px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: all 0.3s;
    }
    
    .benefit-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .benefit-card i {
      font-size: 36px;
      color: #FCD116;
      margin-bottom: 15px;
      display: block;
    }
    
    .benefit-card h4 {
      font-size: 20px;
      margin-bottom: 15px;
      color: #008751;
    }
    
    .benefit-card p {
      color: #555;
      line-height: 1.6;
    }

    @media (max-width: 991px) {
      .partners-content {
        flex-direction: column;
      }
      
      .partners-text {
        padding-right: 0;
        margin-bottom: 30px;
      }
      
      .benefit-card {
        min-width: 100%;
      }
    }
  </style>
  
  <div class="container">
    <div class="partners-heading">
      <h2>Devenez Partenaire</h2>
      <p>Rejoignez notre réseau de partenaires pour promouvoir vos services et contribuer au développement touristique du Bénin</p>
    </div>
    
    <div class="partners-content">
      <div class="partners-text">
        <h3>Pourquoi devenir partenaire ?</h3>
        <ul>
          <li>Visibilité accrue auprès des touristes nationaux et internationaux</li>
          <li>Intégration à notre système de réservation en ligne</li>
          <li>Participation aux campagnes de promotion touristique</li>
          <li>Accès aux statistiques et tendances du marché touristique</li>
          <li>Formations et accompagnement professionnel</li>
          <li>Mise en relation avec des tour-opérateurs internationaux</li>
        </ul>
      </div>
      
      <div class="partners-image">
        <img src="img/partenaire.jpg" alt="Partenariat Touristique">
      </div>
    </div>
    
    <div class="partner-types">
      <div class="partner-type">
        <i class="bi bi-person"></i>
        <span>Administrateur</span>
      </div>
      <div class="partner-type">
        <i class="bi bi-building"></i>
        <span>Prestataire</span>
      </div>
    </div>
    
    <div class="benefits-cards">
      <div class="benefit-card">
        <i class="bi bi-graph-up-arrow"></i>
        <h4>Développez votre activité</h4>
        <p>Accédez à une nouvelle clientèle et augmentez votre visibilité grâce à notre plateforme touristique nationale.</p>
      </div>
      <div class="benefit-card">
        <i class="bi bi-gear"></i>
        <h4>Outils professionnels</h4>
        <p>Bénéficiez d'outils de gestion performants et d'un accompagnement sur mesure pour développer votre offre.</p>
      </div>
      <div class="benefit-card">
        <i class="bi bi-people"></i>
        <h4>Réseau d'excellence</h4>
        <p>Rejoignez une communauté de professionnels engagés pour promouvoir l'excellence du tourisme béninois.</p>
      </div>
    </div>
    
    <div class="cta-container">
      <a href="index.php?page=partenaire" class="btn-register">S'inscrire comme partenaire <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</section>
<!-- End Partners Section -->
    <!-- Counts Section -->
    <section id="counts" class="counts">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="count-box">
                        <i class="bi bi-map"></i>
                        <span data-purecounter-start="0" data-purecounter-end="114763" data-purecounter-duration="1" class="purecounter"></span>
                        <p>Superficie (km²)</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-5 mt-md-0">
                    <div class="count-box">
                        <i class="bi bi-bank"></i>
                        <span data-purecounter-start="0" data-purecounter-end="12" data-purecounter-duration="1" class="purecounter"></span>
                        <p>Départements</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
                    <div class="count-box">
                        <i class="bi bi-house"></i>
                        <span data-purecounter-start="0" data-purecounter-end="77" data-purecounter-duration="1" class="purecounter"></span>
                        <p>Communes</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mt-5 mt-lg-0">
                    <div class="count-box">
                        <i class="bi bi-people"></i>
                        <span data-purecounter-start="0" data-purecounter-end="14769460" data-purecounter-duration="1" class="purecounter"></span>
                        <p>Population</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- Services Section - Converted to Destinations Section -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
  .services {
    padding: 60px 15px;
    background: #f8f9fa;
  }

  .services .section-title {
    text-align: center;
    margin-bottom: 40px;
  }

  .services .section-title h2 {
    font-size: 32px;
    font-weight: bold;
    text-transform: uppercase;
    color: #333;
    margin-bottom: 10px;
  }

  .services .section-title p {
    margin: 0 auto;
    font-size: 16px;
    color: #555;
    max-width: 600px;
  }

  .icon-box {
    text-align: center;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease-in-out;
    margin-bottom: 30px;
  }

  .icon-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
  }

  .icon-box .icon {
    margin-bottom: 15px;
    font-size: 36px;
    color: #e9b732;
  }

  .icon-box h4 {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #333;
  }

  .icon-box p {
    font-size: 14px;
    color: #555;
    line-height: 1.8;
  }

  /* Grid responsivity */
  .services .row {
    display: grid;
    grid-template-columns: repeat(1, 1fr); /* Default: 1 per row */
    gap: 30px; /* Space between items */
  }

  @media (min-width: 768px) {
    .services .row {
      grid-template-columns: repeat(2, 1fr); /* 2 per row on medium screens */
    }
  }

  @media (min-width: 1200px) {
    .services .row {
      grid-template-columns: repeat(3, 1fr); /* 3 per row on large screens */
    }
  }
</style>

<section id="destinations" class="services">
  <div class="container">
    <div class="section-title">
      <h2>Destinations Incontournables</h2>
      <p>Découvrez les sites touristiques exceptionnels du Bénin qui vous offriront des expériences inoubliables.</p>
    </div>
    <div class="row">
      <div class="icon-box">
        <div class="icon"><i class="bi bi-building"></i></div>
        <h4 class="title"><a href="">Palais Royaux d'Abomey</a></h4>
        <p class="description">Classés au patrimoine mondial de l'UNESCO, ces palais témoignent de la puissance du royaume du Dahomey et abritent un musée historique fascinant.</p>
      </div>
      <div class="icon-box">
        <div class="icon"><i class="bi bi-water"></i></div>
        <h4 class="title"><a href="">Ganvié, la Venise d'Afrique</a></h4>
        <p class="description">Découvrez ce village lacustre unique bâti sur pilotis sur le lac Nokoué, où les habitants se déplacent exclusivement en pirogue.</p>
      </div>
      <div class="icon-box">
        <div class="icon"><i class="bi bi-tree"></i></div>
        <h4 class="title"><a href="">Parc National de la Pendjari</a></h4>
        <p class="description">L'une des dernières réserves de savane d'Afrique de l'Ouest où vous pourrez observer éléphants, lions, guépards et de nombreuses espèces d'antilopes.</p>
      </div>
      <div class="icon-box">
        <div class="icon"><i class="bi bi-door-open"></i></div>
        <h4 class="title"><a href="">Porte du Non-Retour à Ouidah</a></h4>
        <p class="description">Monument commémoratif sur la Route des Esclaves, témoignant de la traite négrière transatlantique et lieu de mémoire essentiel.</p>
      </div>
      <div class="icon-box">
        <div class="icon"><i class="bi bi-geo-alt"></i></div>
        <h4 class="title"><a href="">Cotonou, capitale économique</a></h4>
        <p class="description">Découvrez le dynamisme de cette métropole avec son grand marché Dantokpa, la Fondation Zinsou d'art contemporain et sa vie nocturne animée.</p>
      </div>
      <div class="icon-box">
        <div class="icon"><i class="bi bi-stars"></i></div>
        <h4 class="title"><a href="">Temple des Pythons à Ouidah</a></h4>
        <p class="description">Un sanctuaire unique où le culte vodun se pratique encore, avec des dizaines de pythons sacrés vivant librement dans le temple.</p>
      </div>
    </div>
  </div>
</section>



<!-- End Services Section -->


    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <div class="section-title">
                <h2>Informations Touristiques</h2>
         
            </div>
            <div class="row">
                <div class="col-lg-5 d-flex align-items-stretch">
                    <div class="info">
                        <div class="address">
                            <i class="bi bi-geo-alt"></i>
                            <h4>Office du Tourisme :</h4>
                            <p>Boulevard de la Marina, Cotonou, Bénin</p>
                        </div>
                        <div class="email">
                            <i class="bi bi-envelope"></i>
                            <h4>Email :</h4>
                            <p>info@tourisme-benin.bj</p>
                        </div>
                        <div class="phone">
                            <i class="bi bi-phone"></i>
                            <h4>Téléphone :</h4>
                            <p>+229 21 30 10 48</p>
                        </div>
                        <div class="website">
                            <i class="bi bi-globe"></i>
                            <h4>Site web :</h4>
                            <p>www.tourisme-benin.bj</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 mt-5 mt-lg-0 d-flex align-items-stretch">
                <iframe src="https://maps.google.com/maps?q=Benin&t=&z=7&ie=UTF8&iwloc=&output=embed" frameborder="0" style="border:0; width: 100%; height: 290px;" allowfullscreen></iframe>

                </div>
            </div>
        </div>
    </section>
    <!-- End Contact Section -->
</main>
<!-- End #main -->

<script>
    const lightbox = GLightbox({
        selector: '.glightbox'
    });
</script>