<?php
session_start();
include 'database/db.php';

// Gérer la déconnexion directement ici
if(isset($_GET['page']) && $_GET['page'] == 'logout') {
    // Détruire toutes les variables de session
    $_SESSION = array();
    
    // Détruire le cookie de session si présent
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    
    // Détruire la session
    session_destroy();
    
    // Rediriger vers la page de connexion
    header("Location: ../connexion.php");
    exit;
}

if(!isset($_SESSION['prestataire'])){
    header("Location:../connexion.php");
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

// Le reste du code reste inchangé...
// Récupérer les informations de l'hôtel connecté
$hotel_id = $_SESSION['prestataire_id'];
$query = "SELECT * FROM hotels WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $hotel_id, PDO::PARAM_INT);
$stmt->execute();
$hotel_data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Prestataire - <?php echo $hotel_data['hotel_name']; ?></title>
    <link rel="shortcut icon" href="img/hotel-icon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ajout de SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.4.8/sweetalert2.min.css">
    <style>
        :root {
            --primary-color: #FFC107; /* Jaune */
            --secondary-color: #FFD54F; /* Jaune clair */
            --accent-color: #FFB300; /* Jaune foncé */
            --text-color: #333;
            --light-color: #FFFFFF; /* Blanc */
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-color);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar {
            background-color: var(--light-color);
            color: var(--text-color);
            min-height: 100vh;
            padding-top: 20px;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-right: 1px solid rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: var(--text-color);
            padding: 12px 20px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
            border-radius: 5px;
        }
        .sidebar .nav-link:hover {
            background-color: var(--secondary-color);
            color: var(--text-color);
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            color: white;
        }
        .nav-section {
            margin-top: 15px;
            border-top: 1px solid rgba(0,0,0,0.1);
            padding-top: 15px;
        }
        .nav-section-title {
            color: var(--accent-color);
            padding: 8px 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .container-fluid {
            flex: 1;
            display: flex;
            flex-direction: column;
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
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.15);
            transition: all 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(255, 193, 7, 0.25);
        }
        .card-body {
            padding: 25px;
        }
        .card-title {
            color: var(--primary-color);
            font-weight: 600;
        }
        .btn-primary {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: var(--secondary-color);
            color: var(--text-color);
        }
        .btn-danger {
            border-radius: 50px;
            padding: 8px 16px;
        }
        .header h1 {
            font-weight: 700;
            color: var(--primary-color);
        }
        .lead {
            color: var(--text-color);
        }
        .mobile-toggle {
            display: none;
            background-color: transparent;
            color: var(--accent-color);
            border: none;
            padding: 10px;
            font-size: 1.5rem;
            cursor: pointer;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1001;
        }
        .hotel-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
        }
        .circuit-card {
            border-left: 4px solid var(--primary-color);
        }
        .circuit-date {
            font-size: 0.9rem;
            color: #666;
        }
        .circuit-guide {
            font-size: 0.9rem;
            color: #666;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 80%;
                height: 100%;
                z-index: 1000;
            }
            .sidebar.active {
                left: 0;
            }
            .content {
                padding: 15px;
            }
            .header h1 {
                font-size: 1.5rem;
            }
            .mobile-toggle {
                display: block;
            }
            main {
                margin-top: 60px;
            }
        }
    </style>
</head>
<body>
    <button class="mobile-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar" id="sidebar">
                <div class="position-sticky">
                    <div class="text-center mb-4">
                        <img src="img/hotel-logo.png" alt="Logo Hôtel" width="60" height="60">
                        <h4 class="mt-2 text-dark"><?php echo $hotel_data['hotel_name']; ?></h4>
                        <span class="badge bg-primary"><?php echo $hotel_data['hotel_category']; ?></span>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'accueil' ? 'active' : ''; ?>" href="index.php">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'profil' ? 'active' : ''; ?>" href="index.php?page=profil">
                                <i class="fas fa-hotel me-2"></i> Profil de l'hôtel
                            </a>
                        </li>
                        
                        <!-- Section Circuits -->
                        <div class="nav-section">
                            <p class="nav-section-title"><i class="fas fa-route me-2"></i> Circuits Touristiques</p>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $page == 'mes_circuits' ? 'active' : ''; ?>" href="index.php?page=mes_circuits">
                                    <i class="fas fa-map-marked-alt me-2"></i> Mes circuits
                                </a>
                            </li>
                           
                        </div>
                        
                        <!-- Section Paramètres -->
                        <div class="nav-section">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $page == 'parametres' ? 'active' : ''; ?>" href="index.php?page=parametres">
                                    <i class="fas fa-cog me-2"></i> Paramètres
                                </a>
                            </li>
                        </div>
                    </ul>
                    <div class="mt-5 text-center">
                        <a href="index.php?page=logout" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1>Espace Prestataire</h1>
                        <img src="./img/hotel-icon.png" alt="Icône Hôtel" width="60" height="60">
                    </div>
                    <p class="lead">Gérez votre participation aux circuits touristiques et vos informations</p>
                </div>

                <div class="content">
                    <?php if ($page == 'accueil'): ?>
                        <!-- Profil rapide -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            
                                            <div class="col-md-10">
                                                <h3><?php echo $hotel_data['hotel_name']; ?></h3>
                                                <p><i class="fas fa-map-marker-alt me-2"></i> <?php echo $hotel_data['address'].', '.$hotel_data['city']; ?></p>
                                                <p><i class="fas fa-star me-2"></i> <?php echo $hotel_data['hotel_category']; ?></p>
                                                <p><i class="fas fa-phone me-2"></i> <?php echo $hotel_data['contact_phone']; ?></p>
                                                <div class="mt-3">
                                                    <a href="index.php?page=profil" class="btn btn-primary"><i class="fas fa-edit me-2"></i> Modifier le profil</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       
                        
                        <!-- Actions rapides -->
                        <h4 class="mb-3">Actions rapides</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-edit fa-3x mb-3 text-primary"></i>
                                        <h5 class="card-title">Modifier profil</h5>
                                        <p class="card-text">Mettez à jour les informations de votre hôtel</p>
                                        <a href="index.php?page=profil" class="btn btn-primary">Modifier</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-map-marked-alt fa-3x mb-3 text-primary"></i>
                                        <h5 class="card-title">Voir tous les circuits</h5>
                                        <p class="card-text">Consultez tous les circuits auxquels vous participez</p>
                                        <a href="index.php?page=mes_circuits" class="btn btn-primary">Consulter</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-lock fa-3x mb-3 text-primary"></i>
                                        <h5 class="card-title">Mot de passe</h5>
                                        <p class="card-text">Modifiez votre mot de passe d'accès</p>
                                        <a href="index.php?page=parametres" class="btn btn-primary">Modifier</a>
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

    <!-- Ajout de jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Ajout de SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.4.8/sweetalert2.all.min.js"></script>
    <script>
        // Script pour le toggle du sidebar en mobile
        document.addEventListener('DOMContentLoaded', function() {
           
            
            // Confirmation de déconnexion avec SweetAlert2
            // Sélecteur plus spécifique pour cibler uniquement le bouton de déconnexion
    $('.btn-danger').on('click', function(e) {
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
    
    // Code pour le toggle du sidebar (gardé tel quel)
    $('#sidebarToggle').on('click', function() {
        $('#sidebar').toggleClass('active');
    });
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#sidebar').length && !$(e.target).closest('#sidebarToggle').length) {
            $('#sidebar').removeClass('active');
        }
    });
        });
    </script>
</body>
</html>