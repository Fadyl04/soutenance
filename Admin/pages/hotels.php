<?php
include 'database/db.php';
// Vérifier que l'administrateur est connecté
if(!isset($_SESSION['admin'])){
    header("Location:../index.php?page=login");
    exit();
}

// Traitement des actions (confirmation ou suppression d'hôtel)
if(isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $hotel_id = intval($_GET['id']);
    
    if($action === 'confirm') {
        // Mettre à jour le statut de l'hôtel à "approved"
        try {
            $stmt = $db->prepare("UPDATE hotels SET status = 'approved' WHERE id = :id");
            $stmt->bindParam(':id', $hotel_id);
            if($stmt->execute()) {
                // Récupérer les infos de l'hôtel pour l'email
                $stmt = $db->prepare("SELECT hotel_name, contact_name, contact_email, username, password FROM hotels WHERE id = :id");
                $stmt->bindParam(':id', $hotel_id);
                $stmt->execute();
                $hotel_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Afficher le modal pour envoyer l'email
                echo "<script>
                    $(document).ready(function() {
                        $('#sendEmailModal" . $hotel_id . "').modal('show');
                    });
                </script>";
            }
        } catch(PDOException $e) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de la confirmation de l\'hôtel: " . addslashes($e->getMessage()) . "',
                    confirmButtonColor: '#008751'
                });
            </script>";
        }
    } elseif($action === 'send_email') {
        // Traitement de l'envoi d'email après confirmation
        if(isset($_POST['email_message']) && isset($_POST['hotel_id'])) {
            $email_message = $_POST['email_message'];
            $hotel_id = intval($_POST['hotel_id']);
            
            try {
                // Récupérer les infos de l'hôtel pour l'email
                $stmt = $db->prepare("SELECT hotel_name, contact_name, contact_email, username, password FROM hotels WHERE id = :id");
                $stmt->bindParam(':id', $hotel_id);
                $stmt->execute();
                $hotel_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(!$hotel_data) {
                    throw new Exception("Données de l'hôtel introuvables.");
                }
                
                // Préparer et envoyer l'email
                $to = $hotel_data['contact_email'];
                $subject = "Confirmation d'inscription - " . $hotel_data['hotel_name'];
                
                // Message final avec les informations d'authentification
                $message = str_replace(
                    ['[NOM_HOTEL]', '[NOM_CONTACT]', '[USERNAME]', '[PASSWORD]'],
                    [$hotel_data['hotel_name'], $hotel_data['contact_name'], $hotel_data['username'], $hotel_data['password']],
                    $email_message
                );
                
                // En-têtes appropriés pour un e-mail HTML
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: votre-email@domaine.com" . "\r\n"; // Remplacez par votre adresse email valide
                
                // Tentative d'envoi de l'e-mail
                $mail_sent = mail($to, $subject, $message, $headers);
                
                if($mail_sent) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Email envoyé',
                            text: 'L\'email de confirmation a été envoyé avec succès à " . addslashes($hotel_data['contact_email']) . "',
                            confirmButtonColor: '#008751'
                        });
                    </script>";
                } else {
                    // Récupérer les erreurs PHP potentielles
                    $error = error_get_last();
                    $error_message = isset($error) ? $error['message'] : 'Raison inconnue';
                    
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur d\'envoi',
                            text: 'L\'envoi de l\'email a échoué. Erreur: " . addslashes($error_message) . "',
                            confirmButtonColor: '#008751'
                        });
                    </script>";
                }
            } catch(Exception $e) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Une erreur est survenue: " . addslashes($e->getMessage()) . "',
                        confirmButtonColor: '#008751'
                    });
                </script>";
            }
        }
    } elseif($action === 'delete') {
        // Supprimer l'hôtel de la base de données
        try {
            // D'abord récupérer les informations sur les photos pour les supprimer du serveur
            $stmt = $db->prepare("SELECT photo_files FROM hotels WHERE id = :id");
            $stmt->bindParam(':id', $hotel_id);
            $stmt->execute();
            $hotel_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Supprimer les fichiers photos si existants
            if($hotel_data && !empty($hotel_data['photo_files'])) {
                $photo_files = json_decode($hotel_data['photo_files'], true);
                if(is_array($photo_files)) {
                    foreach($photo_files as $file) {
                        if(file_exists($file)) {
                            unlink($file);
                        }
                    }
                }
            }
            
            // Ensuite supprimer l'enregistrement de la base de données
            $stmt = $db->prepare("DELETE FROM hotels WHERE id = :id");
            $stmt->bindParam(':id', $hotel_id);
            if($stmt->execute()) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Hôtel supprimé',
                        text: 'L\'hôtel a été supprimé avec succès.',
                        confirmButtonColor: '#008751'
                    });
                </script>";
            }
        } catch(PDOException $e) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de la suppression de l\'hôtel: " . addslashes($e->getMessage()) . "',
                    confirmButtonColor: '#008751'
                });
            </script>";
        }
    }
}

// Récupérer tous les hôtels de la base de données, triés par statut (pending en premier)
try {
    $stmt = $db->query("SELECT * FROM hotels ORDER BY 
                         CASE 
                            WHEN status = 'pending' THEN 1
                            WHEN status = 'approved' THEN 2
                            ELSE 3
                         END, 
            registration_date DESC");
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des hôtels: " . $e->getMessage() . "</div>";
    $hotels = [];
}
?>

<div class="container-fluid">
    <div class="header">
        <h2><i class="fas fa-hotel me-2"></i> Gestion des Hébergements</h2>
        <p class="lead">Approuvez ou supprimez les demandes d'enregistrement d'hôtels</p>
    </div>
    
    <!-- Filtres pour les hôtels -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Filtrer les hôtels</h5>
            <div class="row">
                <div class="col-md-6">
                    <select id="statusFilter" class="form-select mb-3">
                        <option value="all">Tous les statuts</option>
                        <option value="pending">En attente d'approbation</option>
                        <option value="approved">Approuvés</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" id="searchHotel" class="form-control" placeholder="Rechercher un hôtel...">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Compteurs d'hôtels -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-clock me-2"></i> En attente</h5>
                    <h2 id="pendingCount">
                        <?php 
                            $pending_count = 0;
                            foreach($hotels as $hotel) {
                                if($hotel['status'] == 'pending') $pending_count++;
                            }
                            echo $pending_count;
                        ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-check-circle me-2"></i> Approuvés</h5>
                    <h2 id="approvedCount">
                        <?php 
                            $approved_count = 0;
                            foreach($hotels as $hotel) {
                                if($hotel['status'] == 'approved') $approved_count++;
                            }
                            echo $approved_count;
                        ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-hotel me-2"></i> Total</h5>
                    <h2 id="totalCount"><?php echo count($hotels); ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Liste des hôtels -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des hôtels</h5>
            
            <?php if(empty($hotels)): ?>
                <div class="alert alert-info">Aucun hôtel enregistré pour le moment.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom de l'hôtel</th>
                                <th>Catégorie</th>
                                <th>Ville</th>
                                <th>Contact</th>
                                <th>Statut</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="hotelsList">
                            <?php foreach($hotels as $hotel): ?>
                                <tr class="hotel-row" data-status="<?php echo htmlspecialchars($hotel['status']); ?>">
                                    <td><?php echo htmlspecialchars($hotel['id']); ?></td>
                                    <td><?php echo htmlspecialchars($hotel['hotel_name']); ?></td>
                                    <td>
                                        <?php 
                                            $category = '';
                                            switch($hotel['hotel_category']) {
                                                case '1': $category = '⭐'; break;
                                                case '2': $category = '⭐⭐'; break;
                                                case '3': $category = '⭐⭐⭐'; break;
                                                case '4': $category = '⭐⭐⭐⭐'; break;
                                                case '5': $category = '⭐⭐⭐⭐⭐'; break;
                                                default: $category = '-';
                                            }
                                            echo $category;
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($hotel['city']); ?></td>
                                    <td>
                                        <a href="tel:<?php echo htmlspecialchars($hotel['contact_phone']); ?>">
                                            <?php echo htmlspecialchars($hotel['contact_phone']); ?>
                                        </a>
                                        <br>
                                        <a href="mailto:<?php echo htmlspecialchars($hotel['contact_email']); ?>">
                                            <?php echo htmlspecialchars($hotel['contact_email']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php if($hotel['status'] == 'pending'): ?>
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        <?php elseif($hotel['status'] == 'approved'): ?>
                                            <span class="badge bg-success">Approuvé</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($hotel['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            if(isset($hotel['registration_date'])) {
                                                echo date('d/m/Y H:i', strtotime($hotel['registration_date']));
                                            } else {
                                                echo '-';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewHotelModal<?php echo $hotel['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <?php if($hotel['status'] == 'pending'): ?>
                                                <a href="index.php?page=hotels&action=confirm&id=<?php echo $hotel['id']; ?>" 
                                                   class="btn btn-success btn-sm confirm-btn" title="Approuver">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php elseif($hotel['status'] == 'approved'): ?>
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sendEmailModal<?php echo $hotel['id']; ?>" title="Envoyer email">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <a href="index.php?page=hotels&action=delete&id=<?php echo $hotel['id']; ?>" 
                                               class="btn btn-danger btn-sm delete-btn" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Modals pour visualiser les détails de chaque hôtel -->
    <?php foreach($hotels as $hotel): ?>
        <div class="modal fade" id="viewHotelModal<?php echo $hotel['id']; ?>" tabindex="-1" aria-labelledby="viewHotelModalLabel<?php echo $hotel['id']; ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                        <h5 class="modal-title" id="viewHotelModalLabel<?php echo $hotel['id']; ?>">
                            <?php echo htmlspecialchars($hotel['hotel_name']); ?>
                        </h5>
                        <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Photos de l'hôtel -->
                            <?php if(!empty($hotel['photo_files'])): ?>
                                <div class="col-md-12 mb-4">
                                    <h6><i class="fas fa-images me-2"></i> Photos</h6>
                                    <div class="row">
                                        <?php 
                                            $photo_files = json_decode($hotel['photo_files'], true);
                                            if(is_array($photo_files)):
                                                foreach($photo_files as $photo):
                                        ?>
                                            <div class="col-md-4 mb-3">
                                                <img src="<?php echo htmlspecialchars($photo); ?>" class="img-fluid rounded" alt="Photo de l'hôtel">
                                            </div>
                                        <?php 
                                                endforeach; 
                                            endif;
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Informations de base -->
                            <div class="col-md-6">
                                <h6><i class="fas fa-info-circle me-2"></i> Informations générales</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th>Catégorie</th>
                                        <td>
                                            <?php 
                                                $category = '';
                                                switch($hotel['hotel_category']) {
                                                    case '1': $category = '⭐'; break;
                                                    case '2': $category = '⭐⭐'; break;
                                                    case '3': $category = '⭐⭐⭐'; break;
                                                    case '4': $category = '⭐⭐⭐⭐'; break;
                                                    case '5': $category = '⭐⭐⭐⭐⭐'; break;
                                                    default: $category = '-';
                                                }
                                                echo $category;
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Année de création</th>
                                        <td><?php echo !empty($hotel['creation_year']) ? htmlspecialchars($hotel['creation_year']) : '-'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Nombre de chambres</th>
                                        <td><?php echo htmlspecialchars($hotel['room_count']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Gamme de prix</th>
                                        <td><?php echo htmlspecialchars($hotel['price_range']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Check-in</th>
                                        <td><?php echo !empty($hotel['check_in_time']) ? htmlspecialchars($hotel['check_in_time']) : '-'; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Check-out</th>
                                        <td><?php echo !empty($hotel['check_out_time']) ? htmlspecialchars($hotel['check_out_time']) : '-'; ?></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Coordonnées -->
                            <div class="col-md-6">
                                <h6><i class="fas fa-map-marker-alt me-2"></i> Emplacement et contact</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th>Adresse</th>
                                        <td><?php echo htmlspecialchars($hotel['address']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Ville</th>
                                        <td><?php echo htmlspecialchars($hotel['city']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Région</th>
                                        <td><?php echo htmlspecialchars($hotel['region']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Contact</th>
                                        <td><?php echo htmlspecialchars($hotel['contact_name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Téléphone</th>
                                        <td><a href="tel:<?php echo htmlspecialchars($hotel['contact_phone']); ?>"><?php echo htmlspecialchars($hotel['contact_phone']); ?></a></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><a href="mailto:<?php echo htmlspecialchars($hotel['contact_email']); ?>"><?php echo htmlspecialchars($hotel['contact_email']); ?></a></td>
                                    </tr>
                                    <tr>
                                        <th>Site web</th>
                                        <td>
                                            <?php if(!empty($hotel['website'])): ?>
                                                <a href="<?php echo htmlspecialchars($hotel['website']); ?>" target="_blank"><?php echo htmlspecialchars($hotel['website']); ?></a>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Services -->
                            <div class="col-md-12 mt-3">
                                <h6><i class="fas fa-concierge-bell me-2"></i> Services disponibles</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                WiFi
                                                <?php if($hotel['has_wifi']): ?>
                                                    <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i></span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger rounded-pill"><i class="fas fa-times"></i></span>
                                                <?php endif; ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Parking
                                                <?php if($hotel['has_parking']): ?>
                                                    <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i></span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger rounded-pill"><i class="fas fa-times"></i></span>
                                                <?php endif; ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Piscine
                                                <?php if($hotel['has_pool']): ?>
                                                    <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i></span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger rounded-pill"><i class="fas fa-times"></i></span>
                                                <?php endif; ?>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Restaurant
                                                <?php if($hotel['has_restaurant']): ?>
                                                    <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i></span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger rounded-pill"><i class="fas fa-times"></i></span>
                                                <?php endif; ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Climatisation
                                                <?php if($hotel['has_ac']): ?>
                                                    <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i></span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger rounded-pill"><i class="fas fa-times"></i></span>
                                                <?php endif; ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Salle de conférence
                                                <?php if($hotel['has_conference']): ?>
                                                    <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i></span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger rounded-pill"><i class="fas fa-times"></i></span>
                                                <?php endif; ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <?php if(!empty($hotel['description'])): ?>
                                <div class="col-md-12 mt-3">
                                    <h6><i class="fas fa-align-left me-2"></i> Description</h6>
                                    <div class="card">
                                        <div class="card-body">
                                            <?php echo nl2br(htmlspecialchars($hotel['description'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Informations de compte -->
                            <div class="col-md-12 mt-3">
                                <h6><i class="fas fa-user me-2"></i> Informations de compte</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th>Nom d'utilisateur</th>
                                        <td><?php echo htmlspecialchars($hotel['username']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Statut</th>
                                        <td>
                                            <?php if($hotel['status'] == 'pending'): ?>
                                                <span class="badge bg-warning text-dark">En attente</span>
                                            <?php elseif($hotel['status'] == 'approved'): ?>
                                                <span class="badge bg-success">Approuvé</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($hotel['status']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date d'enregistrement</th>
                                        <td>
                                            <?php 
                                                if(isset($hotel['creation_date'])) {
                                                    echo date('d/m/Y H:i', strtotime($hotel['creation_date']));
                                                } else {
                                                    echo '-';
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                        <?php if($hotel['status'] == 'pending'): ?>
                            <a href="index.php?page=hotels&action=confirm&id=<?php echo $hotel['id']; ?>" class="btn btn-success confirm-btn">
                                <i class="fas fa-check me-2"></i> Approuver
                            </a>
                        <?php elseif($hotel['status'] == 'approved'): ?>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendEmailModal<?php echo $hotel['id']; ?>">
                                <i class="fas fa-envelope me-2"></i> Envoyer Email de Bienvenue
                            </button>
                        <?php endif; ?>
                        <a href="index.php?page=hotels&action=delete&id=<?php echo $hotel['id']; ?>" class="btn btn-danger delete-btn">
                            <i class="fas fa-trash me-2"></i> Supprimer
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal pour envoyer un e-mail après approbation -->
<div class="modal fade" id="sendEmailModal<?php echo $hotel['id']; ?>" tabindex="-1" aria-labelledby="sendEmailModalLabel<?php echo $hotel['id']; ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                <h5 class="modal-title" id="sendEmailModalLabel<?php echo $hotel['id']; ?>">
                    Envoyer un e-mail de confirmation à <?php echo htmlspecialchars($hotel['hotel_name']); ?>
                </h5>
                <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="https://formsubmit.co/<?php echo htmlspecialchars($hotel['contact_email']); ?>" method="post">
    <div class="modal-body">
        <input type="hidden" name="_subject" value="Approbation de votre établissement sur notre plateforme">
        <input type="hidden" name="_captcha" value="false">
        <input type="hidden" name="hotel_id" value="<?php echo $hotel['id']; ?>">
        
        <!-- Redirection après envoi -->
        <input type="hidden" name="_next" value="https://votre-site.com/confirmation-envoi">
        
        <!-- Champs d'information visibles -->
        <div class="alert alert-info">
            <p><strong>Destinataire :</strong> <?php echo htmlspecialchars($hotel['contact_name']); ?> (<?php echo htmlspecialchars($hotel['contact_email']); ?>)</p>
            <p><strong>Nom d'utilisateur :</strong> <?php echo htmlspecialchars($hotel['username']); ?></p>
        </div>
        
        <!-- Message -->
        <div class="mb-3">
            <label for="emailMessage<?php echo $hotel['id']; ?>" class="form-label">Message :</label>
            <textarea class="form-control" id="emailMessage<?php echo $hotel['id']; ?>" name="message" rows="12" required>Cher/Chère <?php echo htmlspecialchars($hotel['contact_name']); ?>,
Félicitations ! Votre établissement "<?php echo htmlspecialchars($hotel['hotel_name']); ?>" a été approuvé sur notre plateforme.
Voici vos informations de connexion:
- Nom d'utilisateur: <?php echo htmlspecialchars($hotel['username']); ?> 
-Mot de passe: password123 

Nous vous invitons à vous connecter pour compléter votre profil et mettre à jour vos disponibilités.
N'hésitez pas à nous contacter si vous avez des questions.

Cordialement,
L'équipe d'administration</textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Annuler
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane me-2"></i> Envoyer l'e-mail
        </button>
    </div>
</form>
        </div>
    </div>
</div>
    <?php endforeach; ?>

<script>
    // Confirmation pour l'approbation d'un hôtel
    document.querySelectorAll('.confirm-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            Swal.fire({
                title: 'Confirmer l\'approbation',
                text: 'Voulez-vous vraiment approuver cet hôtel? Il sera visible sur le site public après cette action.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#008751',
                cancelButtonColor: '#e74c3c',
                confirmButtonText: 'Oui, approuver',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
    
    // Confirmation pour la suppression d'un hôtel
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            Swal.fire({
                title: 'Confirmer la suppression',
                text: 'Voulez-vous vraiment supprimer cet hôtel? Cette action est irréversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
    
    // Filtrage des hôtels par statut
    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value;
        const rows = document.querySelectorAll('.hotel-row');
        
        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            if(status === 'all' || status === rowStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour les compteurs
        updateCounters();
    });
    
    // Recherche d'hôtel
    document.getElementById('searchHotel').addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('.hotel-row');
        
        rows.forEach(row => {
            const hotelName = row.children[1].textContent.toLowerCase();
            const city = row.children[3].textContent.toLowerCase();
            
            if(hotelName.includes(searchText) || city.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mettre à jour les compteurs
        updateCounters();
    });
    
    // Fonction pour mettre à jour les compteurs
    function updateCounters() {
        const rows = document.querySelectorAll('.hotel-row');
        let pendingCount = 0;
        let approvedCount = 0;
        let totalVisible = 0;
        
        rows.forEach(row => {
            if(row.style.display !== 'none') {
                totalVisible++;
                const status = row.getAttribute('data-status');
                if(status === 'pending') pendingCount++;
                if(status === 'approved') approvedCount++;
            }
        });
        
        document.getElementById('pendingCount').textContent = pendingCount;
        document.getElementById('approvedCount').textContent = approvedCount;
        document.getElementById('totalCount').textContent = totalVisible;
    }
</script>