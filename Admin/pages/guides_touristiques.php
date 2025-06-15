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
    $guide_id = intval($_GET['id']);
    
    if($action === 'confirm') {
        try {
            $stmt = $db->prepare("UPDATE guides SET status = 'approved' WHERE id = :id");
            $stmt->bindParam(':id', $guide_id);
            if($stmt->execute()) {
                $stmt = $db->prepare("SELECT first_name, last_name, email, username, password FROM guides WHERE id = :id");
                $stmt->bindParam(':id', $guide_id);
                $stmt->execute();
                $guide_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo "<script>
                    $(document).ready(function() {
                        $('#sendEmailModal" . $guide_id . "').modal('show');
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
        if(isset($_POST['email_message']) && isset($_POST['guide_id'])) {
            $email_message = $_POST['email_message'];
            $guide_id = intval($_POST['guide_id']);
            
            try {
                $stmt = $db->prepare("SELECT first_name, last_name, email, username, password FROM guides WHERE id = :id");
                $stmt->bindParam(':id', $guide_id);
                $stmt->execute();
                $guide_data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $to = $guide_data['email'];
                $subject = "Confirmation d'inscription - " . $guide_data['first_name'] . " " . $guide_data['last_name'];
                
                $message = str_replace(
                    ['[PRENOM]', '[NOM]', '[USERNAME]', '[PASSWORD]'],
                    [$guide_data['first_name'], $guide_data['last_name'], $guide_data['username'], $guide_data['password']],
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
                            text: 'Email envoyé à " . addslashes($guide_data['email']) . "',
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
            $stmt = $db->prepare("SELECT profile_photo, documents FROM guides WHERE id = :id");
            $stmt->bindParam(':id', $guide_id);
            $stmt->execute();
            $guide_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Suppression des fichiers
            $files = array_merge(
                [$guide_data['profile_photo']],
                json_decode($guide_data['documents'], true) ?: []
            );
            
            foreach($files as $file) {
                if($file && file_exists($file)) {
                    unlink($file);
                }
            }
            
            $stmt = $db->prepare("DELETE FROM guides WHERE id = :id");
            $stmt->bindParam(':id', $guide_id);
            $stmt->execute();
            
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Supprimé',
                    text: 'Guide supprimé avec succès',
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

// Récupération des guides
try {
    $stmt = $db->query("SELECT * FROM guides ORDER BY 
                      CASE WHEN status = 'pending' THEN 1 ELSE 2 END, 
                      created_at DESC");
    $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur: " . $e->getMessage() . "</div>";
    $guides = [];
}
?>

<div class="container-fluid">
    <div class="header">
        <h2><i class="fas fa-user-tie me-2"></i> Gestion des Guides</h2>
        <p class="lead">Validez ou supprimez les inscriptions des guides touristiques</p>
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
                    <h2 id="pendingCount"><?= count(array_filter($guides, fn($g) => $g['status'] === 'pending')) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Validés</h5>
                    <h2 id="approvedCount"><?= count(array_filter($guides, fn($g) => $g['status'] === 'approved')) ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total</h5>
                    <h2 id="totalCount"><?= count($guides) ?></h2>
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
                            <th>Spécialisations</th>
                            <th>Expérience</th>
                            <th>Tarif</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($guides as $g): ?>
                        <tr data-status="<?= $g['status'] ?>">
                            <td><?= $g['id'] ?></td>
                            <td><?= $g['first_name'] . ' ' . $g['last_name'] ?></td>
                            <td><?= $g['specializations'] ?? 'Non spécifié' ?></td>
                            <td><?= $g['experience_years'] ?> ans</td>
                            <td><?= $g['daily_rate'] ? $g['daily_rate'] . '€/jour' : 'Non spécifié' ?></td>
                            <td>
                                <?php if($g['status'] === 'pending'): ?>
                                    <span class="badge bg-warning">En attente</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Validé</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal<?= $g['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <?php if($g['status'] == 'pending'): ?>
                                        <a href="index.php?page=guides_touristiques&action=confirm&id=<?php echo $g['id']; ?>" 
                                           class="btn btn-success btn-sm confirm-btn" title="Approuver">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php elseif($g['status'] == 'approved'): ?>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#sendEmailModal<?php echo $g['id']; ?>" title="Envoyer email">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <a href="index.php?page=guides_touristiques&action=delete&id=<?php echo $g['id']; ?>" 
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
    <?php foreach($guides as $g): ?>
    <div class="modal fade" id="viewModal<?= $g['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><?= $g['first_name'] . ' ' . $g['last_name'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php if($g['profile_photo']): ?>
                            <img src="<?= $g['profile_photo'] ?>" class="img-fluid rounded mb-3">
                            <?php endif; ?>
                            
                            <h6>Informations personnelles</h6>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Email:</strong> <?= $g['email'] ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Téléphone:</strong> <?= $g['phone'] ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Genre:</strong> <?= ucfirst($g['gender']) ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Date de naissance:</strong> <?= $g['birth_date'] ? date('d/m/Y', strtotime($g['birth_date'])) : 'Non spécifié' ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Ville:</strong> <?= $g['city'] ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Adresse:</strong> <?= $g['address'] ?>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="col-md-8">
                            <h6>Qualifications professionnelles</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>N° d'identité:</strong> <?= $g['id_number'] ?? 'Non spécifié' ?></p>
                                    <p><strong>N° de licence:</strong> <?= $g['license_number'] ?? 'Non spécifié' ?></p>
                                    <p><strong>Formation:</strong> <?= $g['education'] ?? 'Non spécifié' ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Expérience:</strong> <?= $g['experience_years'] ?> ans</p>
                                    <p><strong>Tarif journalier:</strong> <?= $g['daily_rate'] ? $g['daily_rate'] . '€' : 'Non spécifié' ?></p>
                                </div>
                            </div>
                            
                            <h6 class="mt-4">Spécialisations et zones d'intervention</h6>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <strong>Langues:</strong> <?= $g['languages'] ?? 'Non spécifié' ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Spécialisations:</strong> <?= $g['specializations'] ?? 'Non spécifié' ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Régions:</strong> <?= $g['regions'] ?? 'Non spécifié' ?>
                                </li>
                            </ul>
                            
                            <?php if($g['biography']): ?>
                            <h6 class="mt-4">Biographie</h6>
                            <div class="card">
                                <div class="card-body">
                                    <?= nl2br($g['biography']) ?>
                                </div>
                            </div>
                            <?php endif; ?>
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
    <div class="modal fade" id="sendEmailModal<?= $g['id'] ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="https://formsubmit.co/<?php echo htmlspecialchars($g['email']); ?>" method="post">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Envoyer les identifiants</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="guide_id" value="<?= $g['id'] ?>">
                        <div class="mb-3">
                            <label>Message:</label>
                            <textarea name="email_message" class="form-control" rows="8">
Bonjour <?= $g['first_name'] ?>,

Votre compte guide touristique a été approuvé.

Identifiants de connexion :
Nom d'utilisateur : <?= $g['username'] ?> 
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
    // Confirmation pour l'approbation d'un guide
    document.querySelectorAll('.confirm-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            Swal.fire({
                title: 'Confirmer l\'approbation',
                text: 'Voulez-vous vraiment approuver ce guide? Il sera visible sur le site public après cette action.',
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
    
    // Confirmation pour la suppression d'un guide
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            Swal.fire({
                title: 'Confirmer la suppression',
                text: 'Voulez-vous vraiment supprimer ce guide? Cette action est irréversible.',
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
    
    // Filtrage des guides par statut
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
    
    // Recherche de guide
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        const rows = document.querySelectorAll('tr[data-status]');
        
        rows.forEach(row => {
            const guideName = row.children[1].textContent.toLowerCase();
            const specializations = row.children[2].textContent.toLowerCase();
            
            if(guideName.includes(searchText) || specializations.includes(searchText)) {
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