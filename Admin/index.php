<?php
session_start();
include 'database/db.php';

if(!isset($_SESSION['admin'])){
    header("Location:../index.php?page=login");
}

// Fonction pour charger le contenu des pages
function loadPage($page) {
    $file = "pages/$page.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<p>Page non trouvée.</p>";
    }
}

// Déterminer quelle page afficher
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administratif - Tourisme Bénin</title>
    <link rel="shortcut icon" href="img/benin-flag.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #008751; /* Couleur du drapeau béninois */
            --secondary-color: #FCD116; /* Jaune du drapeau béninois */
            --accent-color: #E8702A; /* Orange africain */
            --text-color: #333;
            --light-color: #f9f9f9;
            --transition-speed: 0.3s;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all var(--transition-speed) ease;
        }
        .sidebar {
            background-color: var(--primary-color);
            color: white;
            min-height: 100vh;
            padding-top: 20px;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            transition: all var(--transition-speed) ease;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            z-index: 900;
        }
        .sidebar.collapsed {
            width: 70px;
            overflow: hidden;
        }
        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            margin-bottom: 5px;
            transition: all var(--transition-speed) ease;
            border-radius: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar .nav-link:hover {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            background-color: var(--accent-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .nav-section {
            margin-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.2);
            padding-top: 15px;
            transition: all var(--transition-speed) ease;
        }
        .nav-section-title {
            color: var(--secondary-color);
            padding: 8px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .container-fluid {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding-left: 0;
            padding-right: 0;
            transition: all var(--transition-speed) ease;
        }
        .content {
            flex: 1;
            overflow: auto;
            padding: 30px;
            background-color: var(--light-color);
            color: var(--text-color);
        }
        .header {
            background-color: white;
            color: var(--primary-color);
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0, 135, 81, 0.15);
            transition: all var(--transition-speed) ease;
            margin-bottom: 25px;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 30px rgba(0, 135, 81, 0.25);
        }
        .card-body {
            padding: 25px;
        }
        .card-title {
            color: var(--primary-color);
            font-weight: 600;
        }
        .btn-edit {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            transition: all var(--transition-speed) ease;
        }
        .btn-edit:hover {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            transform: scale(1.05);
        }
        .btn-danger {
            border-radius: 50px;
            padding: 10px 20px;
        }
        .header h1 {
            font-weight: 700;
            color: var(--primary-color);
        }
        .lead {
            color: var(--text-color);
        }
        
        /* Styles pour le bouton toggle */
        .sidebar-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: all var(--transition-speed) ease;
        }
        .sidebar-toggle:hover {
            background-color: var(--accent-color);
            transform: scale(1.1);
        }
        .sidebar-toggle.active {
            left: 220px;
        }
        
        .icon-text {
            display: inline-block;
            transition: opacity var(--transition-speed) ease;
        }
        .sidebar.collapsed .icon-text {
            opacity: 0;
            width: 0;
        }
        
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 15px;
            margin-bottom: 20px;
        }
        
        .logo-full {
            transition: opacity var(--transition-speed) ease;
        }
        .logo-small {
            display: none;
            transition: opacity var(--transition-speed) ease;
        }
        .sidebar.collapsed .logo-full {
            display: none;
        }
        .sidebar.collapsed .logo-small {
            display: block;
            margin: 0 auto;
        }
        
        .sidebar.collapsed .nav-section-title,
        .sidebar.collapsed .icon-text,
        .sidebar.collapsed .form-footer {
            display: none;
        }
        
        main {
            transition: margin-left var(--transition-speed) ease;
            margin-left: 250px;
        }
        main.expanded {
            margin-left: 70px;
        }
        
        .nav-icon {
            margin-right: 10px;
            font-size: 16px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar.collapsed .nav-link {
            text-align: center;
            padding: 15px 5px;
        }
        
        .sidebar.collapsed .nav-icon {
            margin-right: 0;
            font-size: 20px;
        }
        
        .mobile-toggle {
            display: none;
            background-color: transparent;
            color: var(--accent-color);
            border: none;
            padding: 10px;
            font-size: 1.5rem;
            cursor: pointer;
            z-index: 1001;
        }
        
        /* Style pour les petits écrans */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
                width: 250px;
            }
            .sidebar.active {
                left: 0;
            }
            .sidebar-toggle {
                display: none;
            }
            main {
                margin-left: 0;
            }
            main.expanded {
                margin-left: 0;
            }
            .header h1 {
                font-size: 1.5rem;
            }
            .card {
                margin-bottom: 15px;
            }
            .mobile-toggle {
                display: block;
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 1001;
            }
            .sidebar.collapsed {
                width: 250px;
                left: -250px;
            }
        }
    </style>
</head>
<body>
    <!-- Bouton pour les appareils mobiles -->
    <button class="mobile-toggle" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Bouton toggle pour desktop -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="sidebar" id="sidebar">
                <div class="sidebar-header">
                    <div class="logo-full">
                        <h3 class="text-center mb-4 text-white">Tourisme Bénin</h3>
                    </div>
                    <div class="logo-small">
                        <i class="fas fa-umbrella-beach text-white" style="font-size: 24px;"></i>
                    </div>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'accueil' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-home nav-icon"></i><span class="icon-text">Tableau de bord</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'page_accueil' ? 'active' : ''; ?>" href="../index.php">
                            <i class="fas fa-globe nav-icon"></i><span class="icon-text">Page d'accueil</span>
                        </a>
                    </li>
                    
                    <!-- Section Tourisme -->
                    <div class="nav-section">
                        <p class="nav-section-title"><i class="fas fa-umbrella-beach nav-icon"></i>Tourisme</p>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'evenements' ? 'active' : ''; ?>" href="index.php?page=evenements">
                                <i class="fas fa-calendar-alt nav-icon"></i><span class="icon-text">Évènements culturels</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'sites' ? 'active' : ''; ?>" href="index.php?page=sites">
                                <i class="fas fa-landmark nav-icon"></i><span class="icon-text">Sites touristiques</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'hotels' ? 'active' : ''; ?>" href="index.php?page=hotels">
                                <i class="fas fa-hotel nav-icon"></i><span class="icon-text">Hôtels</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'circuits' ? 'active' : ''; ?>" href="index.php?page=circuits">
                                <i class="fas fa-route nav-icon"></i><span class="icon-text">Visites touristiques</span>
                            </a>
                        </li>
                    </div>
                    
                    <!-- Section Services -->
                    <div class="nav-section">
                        <p class="nav-section-title"><i class="fas fa-concierge-bell nav-icon"></i>Services</p>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'reservations' ? 'active' : ''; ?>" href="index.php?page=reservations">
                                <i class="fas fa-calendar-check nav-icon"></i><span class="icon-text">Réservations</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'guides_touristiques' ? 'active' : ''; ?>" href="index.php?page=guides_touristiques">
                                <i class="fas fa-user-tie nav-icon"></i><span class="icon-text">Guides touristiques</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'transports' ? 'active' : ''; ?>" href="index.php?page=transports">
                                <i class="fas fa-bus nav-icon"></i><span class="icon-text">Transport</span>
                            </a>
                        </li>
                    </div>
                    
                    <!-- Autres sections -->
                    <div class="nav-section">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'parametres' ? 'active' : ''; ?>" href="index.php?page=parametres">
                                <i class="fas fa-cog nav-icon"></i><span class="icon-text">Paramètres</span>
                            </a>
                        </li>
                    </div>
                </ul>
                <div class="mt-5 text-center">
                    <a href="index.php?page=logout" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt nav-icon"></i><span class="icon-text">Déconnexion</span>
                    </a>
                </div>
            </nav>

            <!-- Content -->
            <main id="main">
                <div class="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1>Dashboard de Gestion du Tourisme Béninois</h1>
                        <img src="./img/benin.png" alt="Drapeau du Bénin" width="80" height="80">
                    </div>
                    <p class="lead">Gérez le contenu de la plateforme touristique du Bénin</p>
                </div>

                <div class="content">
                    <?php if ($page == 'accueil'): ?>
                        <!-- Section Tourisme -->
                        <h2 class="mb-4"><i class="fas fa-umbrella-beach me-2"></i> Gestion du Tourisme</h2>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Sites Touristiques</h5>
                                        <p class="card-text">Gérez les informations sur les sites touristiques du Bénin.</p>
                                        <a href="index.php?page=sites" class="btn btn-edit">Gérer</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Hotels</h5>
                                        <p class="card-text">Administrez les hôtels et autres options d'hébergement.</p>
                                        <a href="index.php?page=hotels" class="btn btn-edit">Gérer</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Circuits Guidés</h5>
                                        <p class="card-text">Créez et gérez les circuits touristiques recommandés.</p>
                                        <a href="index.php?page=circuits" class="btn btn-edit">Gérer</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section Services -->
                        <h2 class="mb-4 mt-5"><i class="fas fa-concierge-bell me-2"></i> Gestion des Services</h2>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Reservations</h5>
                                        <p class="card-text">Suivez et gérez les réservations des utilisateurs.</p>
                                        <a href="index.php?page=reservations" class="btn btn-edit">Gérer</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Guides Touristiques</h5>
                                        <p class="card-text">Gérez les informations sur les guides disponibles.</p>
                                        <a href="index.php?page=guides_touristiques" class="btn btn-edit">Gérer</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Transport</h5>
                                        <p class="card-text">Administrez les options de transport pour les touristes.</p>
                                        <a href="index.php?page=transport" class="btn btn-edit">Gérer</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Autres sections -->
                        <h2 class="mb-4 mt-5"><i class="fas fa-cog me-2"></i> Autres Modules</h2>
                       
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Paramètres</h5>
                                        <p class="card-text">Configurez les paramètres généraux du site.</p>
                                        <a href="index.php?page=parametres" class="btn btn-edit">Configurer</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php loadPage($page); ?>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
       $(document).ready(function() {
            // Mémoriser l'état du menu dans localStorage
            const sidebarState = localStorage.getItem('sidebarCollapsed');
            
            if (sidebarState === 'true') {
                $('#sidebar').addClass('collapsed');
                $('#main').addClass('expanded');
                $('#sidebarToggle').css('left', '80px');
            }
            
            // Toggle du sidebar pour Desktop
            $('#sidebarToggle').on('click', function() {
                $('#sidebar').toggleClass('collapsed');
                $('#main').toggleClass('expanded');
                
                const isCollapsed = $('#sidebar').hasClass('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
                
                // Déplacer le bouton toggle
                if (isCollapsed) {
                    $(this).css('left', '80px');
                } else {
                    $(this).css('left', '220px');
                }
            });
            
            // Toggle pour mobile
            $('#mobileToggle').on('click', function() {
                $('#sidebar').toggleClass('active');
            });
            
            // Fermer le menu mobile quand on clique ailleurs
            $(document).on('click', function(e) {
                const windowWidth = window.innerWidth;
                if (windowWidth <= 768) {
                    if (!$(e.target).closest('#sidebar').length && 
                        !$(e.target).closest('#mobileToggle').length && 
                        $('#sidebar').hasClass('active')) {
                        $('#sidebar').removeClass('active');
                    }
                }
            });
            
            // Gestion de la déconnexion avec SweetAlert
            $('.mt-5.text-center .btn-danger').on('click', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');
                Swal.fire({
                    title: 'Voulez-vous vraiment vous déconnecter ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#008751',
                    cancelButtonColor: '#e74c3c',
                    confirmButtonText: 'Oui, me déconnecter',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.location.href = href;
                    }
                });
            });
            
            // Ajuster l'interface en fonction de la taille de l'écran
            function adjustLayout() {
                const windowWidth = window.innerWidth;
                
                if (windowWidth <= 768) {
                    $('#sidebarToggle').hide();
                    $('#mobileToggle').show();
                    $('#main').removeClass('expanded');
                    
                    // Sur mobile, le sidebar est toujours en mode complet
                    if ($('#sidebar').hasClass('collapsed')) {
                        $('#sidebar').removeClass('collapsed');
                    }
                } else {
                    $('#mobileToggle').hide();
                    $('#sidebarToggle').show();
                    
                    // Restaurer l'état enregistré sur desktop
                    if (localStorage.getItem('sidebarCollapsed') === 'true') {
                        $('#sidebar').addClass('collapsed');
                        $('#main').addClass('expanded');
                        $('#sidebarToggle').css('left', '80px');
                    } else {
                        $('#sidebar').removeClass('collapsed');
                        $('#main').removeClass('expanded');
                        $('#sidebarToggle').css('left', '220px');
                    }
                }
            }
            
            // Appliquer au chargement et au redimensionnement
            adjustLayout();
            $(window).resize(function() {
                adjustLayout();
            });
        });
    </script>
</body>
</html>