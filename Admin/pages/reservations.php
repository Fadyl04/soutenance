<?php
include 'database/db.php';
// Vérifier que l'administrateur est connecté
if(!isset($_SESSION['admin'])){
    header("Location:../index.php?page=login");
    exit();
}

// Récupérer la liste des circuits avec leurs capacités
try {
    $stmt = $db->query("SELECT id, name AS nom, max_participants FROM circuits ORDER BY name");
    $circuits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des circuits: " . $e->getMessage() . "</div>";
    $circuits = [];
}

// Filtrage par circuit spécifique
$circuit_filter = isset($_GET['circuit_id']) ? intval($_GET['circuit_id']) : 0;

// Filtrage par statut
$statut_filter = isset($_GET['statut']) ? $_GET['statut'] : 'all';

// Construction de la requête SQL selon les filtres
$sql = "SELECT r.*, c.name as circuit_nom, c.max_participants 
        FROM reservations r 
        INNER JOIN circuits c ON r.circuit_id = c.id 
        WHERE 1=1";

$params = [];

if ($circuit_filter > 0) {
    $sql .= " AND r.circuit_id = :circuit_id";
    $params[':circuit_id'] = $circuit_filter;
}

if ($statut_filter != 'all') {
    $sql .= " AND r.statut = :statut";
    $params[':statut'] = $statut_filter;
}

$sql .= " ORDER BY r.date_reservation DESC";

// Exécuter la requête
try {
    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des réservations: " . $e->getMessage() . "</div>";
    $reservations = [];
}

// Calculer les statistiques par circuit
$stats_by_circuit = [];
foreach ($circuits as $circuit) {
    $circuit_id = $circuit['id'];
    
    // Compter les réservations confirmées pour ce circuit
    try {
        $stmt = $db->prepare("SELECT SUM(nombre_voyageurs) as total_voyageurs FROM reservations WHERE circuit_id = :circuit_id AND statut = 'confirmee'");
        $stmt->bindParam(':circuit_id', $circuit_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_voyageurs = $result['total_voyageurs'] ?: 0;
    } catch(PDOException $e) {
        $total_voyageurs = 0;
    }
    
    // Calculer les places restantes
    $places_restantes = $circuit['max_participants'] - $total_voyageurs;
    $places_restantes = max(0, $places_restantes); // S'assurer que c'est au moins 0
    
    $stats_by_circuit[$circuit_id] = [
        'nom' => $circuit['nom'],
        'capacite_max' => $circuit['max_participants'],
        'total_voyageurs' => $total_voyageurs,
        'places_restantes' => $places_restantes,
        'taux_remplissage' => $circuit['max_participants'] > 0 ? round(($total_voyageurs / $circuit['max_participants']) * 100) : 0
    ];
}

// Grouper les réservations par circuit
$reservations_by_circuit = [];
foreach ($reservations as $reservation) {
    $circuit_id = $reservation['circuit_id'];
    if (!isset($reservations_by_circuit[$circuit_id])) {
        $reservations_by_circuit[$circuit_id] = [];
    }
    $reservations_by_circuit[$circuit_id][] = $reservation;
}
?>

<div class="container-fluid">
    <div class="header">
        <h2><i class="fas fa-route me-2"></i> Gestion des Réservations par Circuit</h2>
        <p class="lead">Visualisez les réservations confirmées et le taux de remplissage des circuits</p>
    </div>
    
    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Filtrer les réservations</h5>
            <form action="" method="GET" class="row">
                <input type="hidden" name="page" value="circuit_reservations">
                <div class="col-md-5">
                    <label for="circuit_id" class="form-label">Circuit</label>
                    <select name="circuit_id" id="circuit_id" class="form-select">
                        <option value="0">Tous les circuits</option>
                        <?php foreach ($circuits as $circuit): ?>
                            <option value="<?php echo $circuit['id']; ?>" <?php echo ($circuit_filter == $circuit['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($circuit['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="statut" class="form-label">Statut</label>
                    <select name="statut" id="statut" class="form-select">
                        <option value="all" <?php echo ($statut_filter == 'all') ? 'selected' : ''; ?>>Tous les statuts</option>
                        <option value="confirmee" <?php echo ($statut_filter == 'confirmee') ? 'selected' : ''; ?>>Confirmée</option>
                        <option value="en_attente" <?php echo ($statut_filter == 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                        <option value="annulee" <?php echo ($statut_filter == 'annulee') ? 'selected' : ''; ?>>Annulée</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Tableau récapitulatif des circuits -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">État de remplissage des circuits</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Circuit</th>
                            <th>Capacité max</th>
                            <th>Voyageurs confirmés</th>
                            <th>Places restantes</th>
                            <th>Taux de remplissage</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats_by_circuit as $circuit_id => $stats): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stats['nom']); ?></td>
                                <td><?php echo htmlspecialchars($stats['capacite_max']); ?> personnes</td>
                                <td><?php echo htmlspecialchars($stats['total_voyageurs']); ?> personnes</td>
                                <td>
                                    <?php if ($stats['places_restantes'] <= 5 && $stats['places_restantes'] > 0): ?>
                                        <span class="text-warning fw-bold"><?php echo htmlspecialchars($stats['places_restantes']); ?> places</span>
                                    <?php elseif ($stats['places_restantes'] == 0): ?>
                                        <span class="text-danger fw-bold">COMPLET</span>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($stats['places_restantes']); ?> places
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar <?php echo ($stats['taux_remplissage'] >= 90) ? 'bg-danger' : (($stats['taux_remplissage'] >= 70) ? 'bg-warning' : 'bg-success'); ?>" 
                                             role="progressbar" 
                                             style="width: <?php echo $stats['taux_remplissage']; ?>%" 
                                             aria-valuenow="<?php echo $stats['taux_remplissage']; ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?php echo $stats['taux_remplissage']; ?>%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="?page=reservations&circuit_id=<?php echo $circuit_id; ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Voir les réservations
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Liste des réservations filtrées -->
    <?php if ($circuit_filter > 0 || $statut_filter != 'all'): ?>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    Liste des réservations 
                    <?php if ($circuit_filter > 0): ?>
                        pour <?php echo htmlspecialchars($stats_by_circuit[$circuit_filter]['nom']); ?>
                    <?php endif; ?>
                    <?php if ($statut_filter != 'all'): ?>
                        (Statut: <?php echo $statut_filter == 'confirmee' ? 'Confirmées' : ($statut_filter == 'en_attente' ? 'En attente' : 'Annulées'); ?>)
                    <?php endif; ?>
                </h5>
                
                <?php if (empty($reservations)): ?>
                    <div class="alert alert-info">Aucune réservation trouvée avec les filtres actuels.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Contact</th>
                                    <th>Circuit</th>
                                    <th>Voyageurs</th>
                                    <th>Date du voyage</th>
                                    <th>Forfait</th>
                                    <th>Prix</th>
                                    <th>Statut</th>
                                    <th>Réservé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reservation['nom'] . ' ' . $reservation['prenom']); ?></td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($reservation['email']); ?>"><?php echo htmlspecialchars($reservation['email']); ?></a><br>
                                            <a href="tel:<?php echo htmlspecialchars($reservation['telephone']); ?>"><?php echo htmlspecialchars($reservation['telephone']); ?></a>
                                        </td>
                                        <td><?php echo htmlspecialchars($reservation['circuit_nom']); ?></td>
                                        <td><?php echo htmlspecialchars($reservation['nombre_voyageurs']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($reservation['date_voyage'])); ?></td>
                                        <td>
                                            <?php 
                                                switch($reservation['type_forfait']) {
                                                    case 'economy': echo '<span class="badge bg-secondary">Économique</span>'; break;
                                                    case 'comfort': echo '<span class="badge bg-primary">Confort</span>'; break;
                                                    case 'premium': echo '<span class="badge bg-warning text-dark">Premium</span>'; break;
                                                    default: echo htmlspecialchars($reservation['type_forfait']);
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo number_format($reservation['prix_total'], 2, ',', ' '); ?> €</td>
                                        <td>
                                            <?php 
                                                switch($reservation['statut']) {
                                                    case 'confirmee': echo '<span class="badge bg-success">Confirmée</span>'; break;
                                                    case 'en_attente': echo '<span class="badge bg-warning text-dark">En attente</span>'; break;
                                                    case 'annulee': echo '<span class="badge bg-danger">Annulée</span>'; break;
                                                    default: echo htmlspecialchars($reservation['statut']);
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_reservation'])); ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal<?php echo $reservation['id']; ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($reservation['statut'] == 'en_attente'): ?>
                                                    <a href="?page=reservations&action=confirm&id=<?php echo $reservation['id']; ?>" class="btn btn-success btn-sm confirm-reservation" title="Confirmer">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <a href="?page=reservations&action=cancel&id=<?php echo $reservation['id']; ?>" class="btn btn-danger btn-sm cancel-reservation" title="Annuler">
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                <?php endif; ?>
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
    <?php endif; ?>
</div>

<!-- Modals pour les détails des réservations -->
<?php foreach ($reservations as $reservation): ?>
    <div class="modal fade" id="detailModal<?php echo $reservation['id']; ?>" tabindex="-1" aria-labelledby="detailModalLabel<?php echo $reservation['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="modal-title" id="detailModalLabel<?php echo $reservation['id']; ?>">
                        Détails de la réservation #<?php echo $reservation['id']; ?>
                    </h5>
                    <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Informations du client -->
                        <div class="col-md-6">
                            <h6><i class="fas fa-user me-2"></i> Informations du client</h6>
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th>Nom complet</th>
                                    <td><?php echo htmlspecialchars($reservation['prenom'] . ' ' . $reservation['nom']); ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><a href="mailto:<?php echo htmlspecialchars($reservation['email']); ?>"><?php echo htmlspecialchars($reservation['email']); ?></a></td>
                                </tr>
                                <tr>
                                    <th>Téléphone</th>
                                    <td><a href="tel:<?php echo htmlspecialchars($reservation['telephone']); ?>"><?php echo htmlspecialchars($reservation['telephone']); ?></a></td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Détails de la réservation -->
                        <div class="col-md-6">
                            <h6><i class="fas fa-clipboard-list me-2"></i> Détails du voyage</h6>
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th>Circuit</th>
                                    <td><?php echo htmlspecialchars($reservation['circuit_nom']); ?></td>
                                </tr>
                                <tr>
                                    <th>Date du voyage</th>
                                    <td><?php echo date('d/m/Y', strtotime($reservation['date_voyage'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Nombre de voyageurs</th>
                                    <td><?php echo htmlspecialchars($reservation['nombre_voyageurs']); ?></td>
                                </tr>
                                <tr>
                                    <th>Type de forfait</th>
                                    <td>
                                        <?php 
                                            switch($reservation['type_forfait']) {
                                                case 'economy': echo 'Économique'; break;
                                                case 'comfort': echo 'Confort'; break;
                                                case 'premium': echo 'Premium'; break;
                                                default: echo htmlspecialchars($reservation['type_forfait']);
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Informations de paiement -->
                        <div class="col-md-6 mt-3">
                            <h6><i class="fas fa-credit-card me-2"></i> Informations de paiement</h6>
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th>Prix total</th>
                                    <td><?php echo number_format($reservation['prix_total'], 2, ',', ' '); ?> €</td>
                                </tr>
                                <tr>
                                    <th>Méthode de paiement</th>
                                    <td><?php echo htmlspecialchars($reservation['methode_paiement']); ?></td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <?php 
                                            switch($reservation['statut']) {
                                                case 'confirmee': echo '<span class="badge bg-success">Confirmée</span>'; break;
                                                case 'en_attente': echo '<span class="badge bg-warning text-dark">En attente</span>'; break;
                                                case 'annulee': echo '<span class="badge bg-danger">Annulée</span>'; break;
                                                default: echo htmlspecialchars($reservation['statut']);
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date de réservation</th>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reservation['date_reservation'])); ?></td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Demandes spéciales -->
                        <?php if(!empty($reservation['demandes_speciales'])): ?>
                            <div class="col-md-6 mt-3">
                                <h6><i class="fas fa-comment-alt me-2"></i> Demandes spéciales</h6>
                                <div class="card">
                                    <div class="card-body">
                                        <?php echo nl2br(htmlspecialchars($reservation['demandes_speciales'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if ($reservation['statut'] == 'en_attente'): ?>
                        <a href="?page=reservations&action=confirm&id=<?php echo $reservation['id']; ?>" class="btn btn-success confirm-reservation">
                            <i class="fas fa-check me-2"></i> Confirmer
                        </a>
                        <a href="?page=reservations&action=cancel&id=<?php echo $reservation['id']; ?>" class="btn btn-danger cancel-reservation">
                            <i class="fas fa-times me-2"></i> Annuler
                        </a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    // Confirmation pour l'approbation d'une réservation
    document.querySelectorAll('.confirm-reservation').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            Swal.fire({
                title: 'Confirmer la réservation',
                text: 'Voulez-vous confirmer cette réservation? Le client recevra une notification par email.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#008751',
                cancelButtonColor: '#e74c3c',
                confirmButtonText: 'Oui, confirmer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
    
    // Confirmation pour l'annulation d'une réservation
    document.querySelectorAll('.cancel-reservation').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            Swal.fire({
                title: 'Annuler la réservation',
                text: 'Voulez-vous vraiment annuler cette réservation? Le client recevra une notification par email.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, annuler',
                cancelButtonText: 'Ne pas annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
</script>