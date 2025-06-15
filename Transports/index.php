<?php
session_start();
include '../database/db.php';

// Ajouter un code de débogage temporaire si nécessaire
// echo "<pre>"; print_r($_SESSION); echo "</pre>";

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

// Vérifier si l'utilisateur est connecté et est un guide
if(!isset($_SESSION['prestataire']) || $_SESSION['user_type'] !== 'guide'){
    header("Location: ../connexion.php");
    exit;
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

// Récupérer les informations du guide connecté
$guide_id = $_SESSION['prestataire_id'];
$query = "SELECT * FROM guides WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $guide_id, PDO::PARAM_INT);
$stmt->execute();
$guide_data = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Guide - <?php echo $guide_data['first_name'] . ' ' . $guide_data['last_name']; ?></title>
    <link rel="shortcut icon" href="../img/guide-icon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Ajout de SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.4.8/sweetalert2.min.css">
    <style>
        :root {
            --primary-color: #4CAF50; /* Vert */
            --secondary-color: #81C784; /* Vert clair */
            --accent-color: #388E3C; /* Vert foncé */
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
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.15);
            transition: all 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(76, 175, 80, 0.25);
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
        .guide-avatar {
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
        .badge-specialization {
            background-color: var(--secondary-color);
            color: var(--text-color);
            margin-right: 5px;
        }
        .language-badge {
            background-color: #e9ecef;
            color: var(--text-color);
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
            margin-right: 5px;
            display: inline-block;
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
                        <?php if(isset($guide_data['profile_photo']) && $guide_data['profile_photo']): ?>
                            <img src="<?php echo $guide_data['profile_photo']; ?>" alt="Photo de profil" class="guide-avatar mb-2">
                        <?php else: ?>
                            <img src="../img/default-guide.png" alt="Photo de profil par défaut" class="guide-avatar mb-2">
                        <?php endif; ?>
                        <h4 class="mt-2 text-dark"><?php echo $guide_data['first_name'] . ' ' . $guide_data['last_name']; ?></h4>
                        <span class="badge bg-primary"><?php echo isset($guide_data['experience_years']) ? $guide_data['experience_years'] : '0'; ?> ans d'expérience</span>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'accueil' ? 'active' : ''; ?>" href="index.php">
                                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $page == 'profil' ? 'active' : ''; ?>" href="index.php?page=profil">
                                <i class="fas fa-user me-2"></i> Mon Profil
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
                            <li class="nav-item">
                                <a class="nav-link <?php echo $page == 'calendrier' ? 'active' : ''; ?>" href="index.php?page=calendrier">
                                    <i class="fas fa-calendar-alt me-2"></i> Mon calendrier
                                </a>
                            </li>
                        </div>
                        
                        <!-- Section Documents -->
                        <div class="nav-section">
                            <p class="nav-section-title"><i class="fas fa-file-alt me-2"></i> Documents</p>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $page == 'documents' ? 'active' : ''; ?>" href="index.php?page=documents">
                                    <i class="fas fa-folder me-2"></i> Mes documents
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
                        <h1>Espace Guide Touristique</h1>
                        <img src="../img/guide-icon.png" alt="Icône Guide" width="60" height="60">
                    </div>
                    <p class="lead">Gérez vos circuits touristiques, votre planning et vos informations</p>
                </div>

                <div class="content">
                    <?php if ($page == 'accueil'): ?>
                        <!-- Profil rapide -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <?php if(isset($guide_data['profile_photo']) && $guide_data['profile_photo']): ?>
                                                    <img src="<?php echo $guide_data['profile_photo']; ?>" alt="Photo de profil" class="guide-avatar mb-2">
                                                <?php else: ?>
                                                    <img src="../img/default-guide.png" alt="Photo de profil par défaut" class="guide-avatar mb-2">
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-10">
                                                <h3><?php echo $guide_data['first_name'] . ' ' . $guide_data['last_name']; ?></h3>
                                                <p><i class="fas fa-map-marker-alt me-2"></i> <?php echo isset($guide_data['address']) ? $guide_data['address'] . ', ' . $guide_data['city'] : 'Adresse non spécifiée'; ?></p>
                                                <p><i class="fas fa-graduation-cap me-2"></i> <?php echo isset($guide_data['education']) ? $guide_data['education'] : 'Formation non spécifiée'; ?></p>
                                                <p><i class="fas fa-phone me-2"></i> <?php echo isset($guide_data['phone']) ? $guide_data['phone'] : 'Téléphone non spécifié'; ?></p>
                                                
                                                <div class="mt-2 mb-3">
                                                    <strong>Spécialisations:</strong>
                                                    <?php 
                                                    if(isset($guide_data['specializations']) && $guide_data['specializations']) {
                                                        $specs = explode(',', $guide_data['specializations']);
                                                        foreach($specs as $spec) {
                                                            echo '<span class="badge bg-secondary mx-1">' . trim($spec) . '</span>';
                                                        }
                                                    } else {
                                                        echo '<span class="text-muted">Aucune spécialisation</span>';
                                                    }
                                                    ?>
                                                </div>
                                                
                                                <div class="mt-2 mb-3">
                                                    <strong>Langues:</strong>
                                                    <?php 
                                                    if(isset($guide_data['languages']) && $guide_data['languages']) {
                                                        $langs = explode(',', $guide_data['languages']);
                                                        foreach($langs as $lang) {
                                                            echo '<span class="language-badge">' . trim($lang) . '</span>';
                                                        }
                                                    } else {
                                                        echo '<span class="text-muted">Aucune langue spécifiée</span>';
                                                    }
                                                    ?>
                                                </div>
                                                
                                                <div class="mt-3">
                                                    <a href="index.php?page=profil" class="btn btn-primary">
                                                        <i class="fas fa-edit me-2"></i>Modifier mon profil
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Statistiques rapides -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-route fa-3x text-primary mb-3"></i>
                                        <?php
                                        // Compter les circuits du guide
                                        $query = "SELECT COUNT(*) as total FROM circuits WHERE guide_id = ?";
                                        $stmt = $db->prepare($query);
                                        $stmt->bindParam(1, $guide_id, PDO::PARAM_INT);
                                        $stmt->execute();
                                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $total_circuits = $result['total'];
                                        ?>
                                        <h3 class="card-title"><?php echo $total_circuits; ?></h3>
                                        <p class="card-text">Circuits créés</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-check fa-3x text-success mb-3"></i>
                                        <?php
                                        // Compter les circuits programmés à venir
                                        $today = date('Y-m-d');
                                        $query = "SELECT COUNT(*) as total FROM circuit_dates 
                                                 WHERE guide_id = ? AND date >= ?";
                                        $stmt = $db->prepare($query);
                                        $stmt->bindParam(1, $guide_id, PDO::PARAM_INT);
                                        $stmt->bindParam(2, $today, PDO::PARAM_STR);
                                        $stmt->execute();
                                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $upcoming_circuits = $result['total'];
                                        ?>
                                        <h3 class="card-title"><?php echo $upcoming_circuits; ?></h3>
                                        <p class="card-text">Circuits programmés</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-star fa-3x text-warning mb-3"></i>
                                        <?php
                                        // Calculer la note moyenne du guide
                                        $query = "SELECT AVG(rating) as average FROM guide_reviews WHERE guide_id = ?";
                                        $stmt = $db->prepare($query);
                                        $stmt->bindParam(1, $guide_id, PDO::PARAM_INT);
                                        $stmt->execute();
                                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $average_rating = $result['average'] ? number_format($result['average'], 1) : 'N/A';
                                        ?>
                                        <h3 class="card-title"><?php echo $average_rating; ?></h3>
                                        <p class="card-text">Note moyenne</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Prochains circuits -->
                        <h2 class="mb-4"><i class="fas fa-calendar-day me-2"></i>Prochains circuits programmés</h2>
                        <div class="row">
                            <?php
                            // Récupérer les prochains circuits programmés du guide
                            $query = "SELECT cd.*, c.title, c.description, c.duration 
                                     FROM circuit_dates cd
                                     JOIN circuits c ON cd.circuit_id = c.id
                                     WHERE cd.guide_id = ? AND cd.date >= ?
                                     ORDER BY cd.date ASC
                                     LIMIT 3";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(1, $guide_id, PDO::PARAM_INT);
                            $stmt->bindParam(2, $today, PDO::PARAM_STR);
                            $stmt->execute();
                            $upcoming_tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if(count($upcoming_tours) > 0) {
                                foreach($upcoming_tours as $tour) {
                                    ?>
                                    <div class="col-md-4">
                                        <div class="card circuit-card">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo $tour['title']; ?></h5>
                                                <p class="circuit-date">
                                                    <i class="far fa-calendar-alt me-2"></i>
                                                    <?php echo date('d/m/Y', strtotime($tour['date'])); ?> 
                                                    <i class="far fa-clock ms-2 me-1"></i>
                                                    <?php echo date('H:i', strtotime($tour['start_time'])); ?>
                                                </p>
                                                <p class="card-text">
                                                    <?php echo substr($tour['description'], 0, 100) . '...'; ?>
                                                </p>
                                                <p>
                                                    <i class="fas fa-users me-2"></i>
                                                    <?php echo $tour['max_participants']; ?> participants max
                                                    <br>
                                                    <i class="fas fa-hourglass-half me-2"></i>
                                                    <?php echo $tour['duration']; ?> heures
                                                </p>
                                                <a href="index.php?page=details_circuit&id=<?php echo $tour['circuit_id']; ?>" class="btn btn-primary">
                                                    <i class="fas fa-info-circle me-2"></i>Détails
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                echo '<div class="col-12"><div class="alert alert-info">Aucun circuit programmé prochainement.</div></div>';
                            }
                            ?>
                        </div>
                        
                        <!-- Actions rapides -->
                        <h2 class="mt-5 mb-4"><i class="fas fa-bolt me-2"></i>Actions rapides</h2>
                        <div class="row">
                            <div class="col-md-3">
                                <a href="index.php?page=ajouter_circuit" class="card text-decoration-none">
                                    <div class="card-body text-center">
                                        <i class="fas fa-plus-circle fa-3x text-success mb-3"></i>
                                        <h5 class="card-title">Ajouter un circuit</h5>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="index.php?page=calendrier" class="card text-decoration-none">
                                    <div class="card-body text-center">
                                        <i class="fas fa-calendar-plus fa-3x text-primary mb-3"></i>
                                        <h5 class="card-title">Planifier une date</h5>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="index.php?page=documents" class="card text-decoration-none">
                                    <div class="card-body text-center">
                                        <i class="fas fa-file-upload fa-3x text-warning mb-3"></i>
                                        <h5 class="card-title">Téléverser un document</h5>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="index.php?page=profil" class="card text-decoration-none">
                                    <div class="card-body text-center">
                                        <i class="fas fa-id-card fa-3x text-info mb-3"></i>
                                        <h5 class="card-title">Mettre à jour le profil</h5>
                                    </div>
                                </a>
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
    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.4.8/sweetalert2.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 768 && 
                sidebar.classList.contains('active') && 
                !sidebar.contains(event.target) && 
                event.target !== sidebarToggle) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>