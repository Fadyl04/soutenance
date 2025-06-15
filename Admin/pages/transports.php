<?php
include 'database/db.php';
// Vérifier que l'administrateur est connecté
if(!isset($_SESSION['admin'])){
    header("Location:../index.php?page=login");
    exit();
}

// Traitement des actions (confirmation ou suppression)
if(isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $transporter_id = intval($_GET['id']);
    
    if($action === 'confirm') {
        try {
            $stmt = $db->prepare("UPDATE transporters SET status = 'approved' WHERE id = :id");
            $stmt->bindParam(':id', $transporter_id);
            if($stmt->execute()) {
                $stmt = $db->prepare("SELECT first_name, last_name, email, username, password FROM transporters WHERE id = :id");
                $stmt->bindParam(':id', $transporter_id);
                $stmt->execute();
                $transporter_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo "<script>
                    $(document).ready(function() {
                        $('#sendEmailModal" . $transporter_id . "').modal('show');
                    });
                </script>";
            }
        } catch(PDOException $e) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Erreur lors de la confirmation: " . addslashes($e->getMessage()) . "',
                    confirmButtonColor: '#008751'
                });
            </script>";
        }
    } elseif($action === 'send_email') {
        if(isset($_POST['email_message']) && isset($_POST['transporter_id'])) {
            $email_message = $_POST['email_message'];
            $transporter_id = intval($_POST['transporter_id']);
            
            try {
                $stmt = $db->prepare("SELECT first_name, last_name, email, username, password FROM transporters WHERE id = :id");
                $stmt->bindParam(':id', $transporter_id);
                $stmt->execute();
                $transporter_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $to = $transporter_data['email'];
                $subject = "Confirmation d'inscription - " . $transporter_data['first_name'] . " " . $transporter_data['last_name'];
                
                $message = str_replace(
                    ['[PRENOM]', '[NOM]', '[USERNAME]', '[PASSWORD]'],
                    [$transporter_data['first_name'], $transporter_data['last_name'], $transporter_data['username'], $transporter_data['password']],
                    $email_message
                );
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: contact@votre-site.com";
                
                if(mail($to, $subject, $message, $headers)) {
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Email envoyé',
                            text: 'Email envoyé à " . addslashes($transporter_data['email']) . "',
                            confirmButtonColor: '#008751'
                        });
                    </script>";
                }
            } catch(Exception $e) {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Erreur: " . addslashes($e->getMessage()) . "',
                        confirmButtonColor: '#008751'
                    });
                </script>";
            }
        }
    } elseif($action === 'delete') {
        try {
            $stmt = $db->prepare("SELECT profile_photo, vehicle_photos, documents FROM transporters WHERE id = :id");
            $stmt->bindParam(':id', $transporter_id);
            $stmt->execute();
            $transporter_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Suppression des fichiers
            $files = array_merge(
                [$transporter_data['profile_photo']],
                json_decode($transporter_data['vehicle_photos'], true) ?: [],
                json_decode($transporter_data['documents'], true) ?: []
            );
            
            foreach($files as $file) {
                if(file_exists($file)) {
                    unlink($file);
                }
            }
            
            $stmt = $db->prepare("DELETE FROM transporters WHERE id = :id");
            $stmt->bindParam(':id', $transporter_id);
            $stmt->execute();
            
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Supprimé',
                    text: 'Transporteur supprimé avec succès',
                    confirmButtonColor: '#008751'
                });
            </script>";
            
        } catch(PDOException $e) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Erreur suppression: " . addslashes($e->getMessage()) . "',
                    confirmButtonColor: '#008751'
                });
            </script>";
        }
    }
}

// Récupération des transporteurs
try {
    $stmt = $db->query("SELECT * FROM transporters ORDER BY 
                      CASE WHEN status = 'pending' THEN 1 ELSE 2 END, 
                      created_at DESC");
    $transporters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur: " . $e->getMessage() . "</div>";
    $transporters = [];
}
?>

<div class="container-fluid">
    <div class="header">
        <h2><i class="fas fa-truck me-2"></i> Gestion des Transporteurs</h2>
        <p class="lead">Validez ou supprimez les inscriptions des transporteurs</p>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <select id="statusFilter" class="form-select">
                        <option value="all">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="approved">Validés</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
                </div>
            </div>
        </div>
    </div>

    <!-- Compteurs -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning">
                <div class="card-body">
                    <h5>En attente</h5>
                    <h2 id="pendingCount"><?= count(array_filter($transporters, fn($t) => $t['status'] === 'pending')) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Validés</h5>
                    <h2 id="approvedCount"><?= count(array_filter($transporters, fn($t) => $t['status'] === 'approved')) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total</h5>
                    <h2 id="totalCount"><?= count($transporters) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Véhicule</th>
                            <th>Expérience</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($transporters as $t): ?>
                        <tr data-status="<?= $t['status'] ?>">
                            <td><?= $t['id'] ?></td>
                            <td><?= $t['first_name'] . ' ' . $t['last_name'] ?></td>
                            <td><?= $t['vehicle_type'] ?> (<?= $t['passenger_capacity'] ?> places)</td>
                            <td><?= $t['experience_years'] ?> ans</td>
                            <td>
                                <?php if($t['status'] === 'pending'): ?>
                                    <span class="badge bg-warning">En attente</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Validé</span>
                                <?php endif; ?>
                            </td>
                            <td>
                            <div class="btn-group" role="group">
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?= $t['id'] ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <?php if($t['status'] == 'pending'): ?>
                                                <a href="index.php?page=transports&action=confirm&id=<?php echo $t['id']; ?>" 
                                                   class="btn btn-success btn-sm confirm-btn" title="Approuver">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php elseif($t['status'] == 'approved'): ?>
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sendEmailModal<?php echo $t['id']; ?>" title="Envoyer email">
                                                    <i class="fas fa-envelope"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <a href="index.php?page=transports&action=delete&id=<?php echo $t['id']; ?>" 
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
        </div>
    </div>

    <!-- Modals -->
    <?php foreach($transporters as $t): ?>
    <div class="modal fade" id="viewModal<?= $t['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><?= $t['first_name'] . ' ' . $t['last_name'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php if($t['profile_photo']): ?>
                            <img src="<?= $t['profile_photo'] ?>" class="img-fluid rounded mb-3">
                            <?php endif; ?>
                            
                            <h6>Informations personnelles</h6>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Email:</strong> <?= $t['email'] ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Téléphone:</strong> <?= $t['phone'] ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Ville:</strong> <?= $t['city'] ?>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-md-8">
                            <h6>Détails du véhicule</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Type:</strong> <?= $t['vehicle_type'] ?></p>
                                    <p><strong>Modèle:</strong> <?= $t['vehicle_model'] ?></p>
                                    <p><strong>Immatriculation:</strong> <?= $t['plate_number'] ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Année:</strong> <?= $t['vehicle_year'] ?></p>
                                    <p><strong>Capacité:</strong> <?= $t['passenger_capacity'] ?> personnes</p>
                                    <p><strong>Tarif:</strong> <?= $t['rate_per_km'] ?>€/km</p>
                                </div>
                            </div>
                            
                            <h6 class="mt-4">Autres informations</h6>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Langues:</strong> <?= $t['languages'] ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Services:</strong> <?= $t['services'] ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Zones d'intervention:</strong> <?= $t['regions'] ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Email -->
    <div class="modal fade" id="sendEmailModal<?= $t['id'] ?>">
        <div class="modal-dialog">
            <div class="modal-content">
            <form action="https://formsubmit.co/<?php echo htmlspecialchars($t['email']); ?>" method="post">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Envoyer les identifiants</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="transporter_id" value="<?= $t['id'] ?>">
                        <div class="mb-3">
                            <label>Message:</label>
                            <textarea name="email_message" class="form-control" rows="8">
Bonjour <?= $t['first_name'] ?>,

Votre compte transporteur a été approuvé.

Identifiants de connexion :
Nom d'utilisateur : <?= $t['username'] ?> 
Mot de passe : password123

Cordialement,
L'équipe de gestion
                            </textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
    // Confirmation pour l'approbation d'un transporteur
    document.querySelectorAll('.confirm-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            Swal.fire({
                title: 'Confirmer l\'approbation',
                text: 'Voulez-vous vraiment approuver ce transporteur? Il sera visible sur le site public après cette action.',
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
    
    // Confirmation pour la suppression d'un transporteur
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            Swal.fire({
                title: 'Confirmer la suppression',
                text: 'Voulez-vous vraiment supprimer ce transporteur? Cette action est irréversible.',
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
    
    // Filtrage des transporteurs par statut
    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value;
        const rows = document.querySelectorAll('tr[data-status]');
        
        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            if(status === 'all' || status === rowStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        updateCounters();
    });
    
    // Recherche de transporteur
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('tr[data-status]');
        
        rows.forEach(row => {
            const transporterName = row.children[1].textContent.toLowerCase();
            const vehicleType = row.children[2].textContent.toLowerCase();
            
            if(transporterName.includes(searchText) || vehicleType.includes(searchText)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        updateCounters();
    });
    
    // Mise à jour des compteurs
    function updateCounters() {
        const rows = document.querySelectorAll('tr[data-status]');
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