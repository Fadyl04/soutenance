<!-- Header -->
<header id="header" class="fixed-top">
    <div class="container d-flex align-items-center">
        <a href="index.php" class="logo me-auto"><img src="assets/img/tourisme.png" alt="Tourisme Bénin" class="img-fluid"></a>
        
        <nav id="navbar" class="navbar order-last order-lg-0">
            <ul>
                <li><a class="nav-link scrollto" href="index.php?page=home">Accueil</a></li>
                
                <li class="dropdown">
                    <a href="index.php?page=tourisme"><span>Tourisme</span> <i class="bi bi-chevron-down"></i></a>
                    <ul>
                        <li><a href="index.php?page=evenemennts">Evènements culturels</a></li>
                        <li><a href="index.php?page=sites">Sites touristiques</a></li>
                        <li><a href="index.php?page=circuits">visites touristiques</a></li>
                        
                    </ul>
                </li>
                
                <li class="dropdown">
                    <a href="index.php?page=benin"><span>Bénin</span> <i class="bi bi-chevron-down"></i></a>
                    <ul>
                        <li><a href="index.php?page=histoire" >Histoire</a></li>
                        <li><a href="index.php?page=culture">Culture</a></li>
                        <li><a href="index.php?page=local">Économie</a></li>
                        
                    </ul>
                </li>
                
                <li class="dropdown">
                    <a href="index.php?page=services"><span>Partenaires</span> <i class="bi bi-chevron-down"></i></a>
                    <ul>
                        <li><a href="index.php?page=pguide">Guides touristiques</a></li>
                        <li><a href="index.php?page=photel">Hotels</a></li>
                        <li><a href="index.php?page=ptransport">Transports</a></li>
                    </ul>
                </li>
                
                
                
                <li><a class="nav-link scrollto" href="index.php?page=home#contact">Contacts</a></li>
            </ul>
            
            <i class="bi bi-list mobile-nav-toggle"></i>
        </nav>
        
        <div class="header-buttons">
            <a  class="search-btn mx-2"><i class="bi bi-search"></i></a>

            <a href="connexion.php" class="appointment-btn scrollto">Se Connecter</a>
        </div>
    </div>
</header>
<!-- End Header -->

<!-- CSS adapté pour harmoniser header et footer -->
<style>

/* Style pour le header - fond blanc et texte noir */
#header {
  background-color: #ffffff;
  box-shadow: 0px 2px 15px rgba(0, 0, 0, 0.1);
}

#header .logo img {
  max-height: 60px;
}

#navbar ul li a {
  color: #333333;
  font-weight: 500;
  font-size: 15px;
  padding: 12px 15px;
  transition: all 0.3s ease;
}

#navbar ul li a:hover, 
#navbar .active, 
#navbar li:hover > a {
  color: #000000;
  border-bottom: 2px solid #000000;
}

#navbar .dropdown ul {
  background: #ffffff;
  box-shadow: 0px 0px 30px rgba(127, 137, 161, 0.25);
}

#navbar .dropdown ul a {
  color: #333333;
}

#navbar .dropdown ul a:hover {
  color: #000000;
  background-color: #f8f8f8;
}

.header-buttons .search-btn {
  color: #333333;
  cursor: pointer;
}

/* Suppression des styles personnalisés pour les boutons d'inscription et de connexion */
/* Les boutons conserveront ainsi leurs styles d'origine définis ailleurs dans le CSS */

.mobile-nav-toggle {
  color: #333333;
}

</style>