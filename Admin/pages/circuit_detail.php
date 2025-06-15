<?php
include 'database/db.php';
// Vérifier que l'administrateur est connecté
if(!isset($_SESSION['admin'])) {
    header("Location:../index.php?page=login");
    exit();
}

// Vérifier que l'ID du circuit est fourni
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location:index.php?page=circuits");
    exit();
}

$circuit_id = intval($_GET['id']);

// Récupérer les détails du circuit
try {
    $stmt = $db->prepare("SELECT c.*, 
                          h.hotel_name, h.address AS hotel_address, h.city AS hotel_city, h.hotel_category,
                          CONCAT(g.first_name, ' ', g.last_name) AS guide_name, g.email AS guide_email, g.phone AS guide_phone, g.specializations AS guide_speciality,
                          CONCAT(t.first_name, ' ', t.last_name) AS transporter_name, t.email AS transporter_email, t.phone AS transporter_phone, t.vehicle_type
                          FROM circuits c 
                          LEFT JOIN hotels h ON c.hotel_id = h.id 
                          LEFT JOIN guides g ON c.guide_id = g.id 
                          LEFT JOIN transporters t ON c.transporter_id = t.id 
                          WHERE c.id = :circuit_id");
    $stmt->bindParam(':circuit_id', $circuit_id);
    $stmt->execute();
    $circuit = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$circuit) {
        echo "<div class='alert alert-danger'>Ce circuit n'existe pas.</div>";
        exit();
    }
    
    // Récupérer les sites touristiques associés au circuit
    $stmt = $db->prepare("SELECT s.* FROM sites_touristiques s 
                          INNER JOIN circuit_sites cs ON s.id = cs.site_id 
                          WHERE cs.circuit_id = :circuit_id 
                          ORDER BY s.nom");
    $stmt->bindParam(':circuit_id', $circuit_id);
    $stmt->execute();
    $sites = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les réservations pour ce circuit s'il y en a
try {
    $stmt = $db->prepare("SELECT COUNT(*) as total_bookings, 
                      SUM(CASE WHEN type_forfait = 'economy' THEN 1 ELSE 0 END) as economy_bookings,
                      SUM(CASE WHEN type_forfait = 'comfort' THEN 1 ELSE 0 END) as comfort_bookings,
                      SUM(CASE WHEN type_forfait = 'premium' THEN 1 ELSE 0 END) as premium_bookings
                      FROM reservations 
                      WHERE circuit_id = :circuit_id");
    $stmt->bindParam(':circuit_id', $circuit_id);
    $stmt->execute();
    $bookings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // If table doesn't exist, set defaults
    if (strpos($e->getMessage(), "Table 'adjarra.reservations' doesn't exist") !== false) {
        $bookings = [
            'total_bookings' => 0,
            'economy_bookings' => 0,
            'comfort_bookings' => 0,
            'premium_bookings' => 0
        ];
    } else {
        // For other database errors, display the error
        echo "<div class='alert alert-danger'>Erreur lors de la récupération des réservations: " . $e->getMessage() . "</div>";
        exit();
    }
}
    
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des détails du circuit: " . $e->getMessage() . "</div>";
    exit();
}

// Calculer les places restantes
$places_taken = isset($bookings['total_bookings']) ? $bookings['total_bookings'] : 0;
$places_remaining = $circuit['max_participants'] - $places_taken;
$occupancy_rate = $circuit['max_participants'] > 0 ? ($places_taken / $circuit['max_participants']) * 100 : 0;

// Calculer la durée du séjour
$start_date = new DateTime($circuit['start_date']);
$end_date = new DateTime($circuit['end_date']);
$interval = $start_date->diff($end_date);
$days_difference = $interval->days + 1; // +1 pour inclure le jour de début
?>

<div class="container-fluid">
    <div class="header">
        <h2><i class="fas fa-route me-2"></i> Détails du Circuit</h2>
        <p class="lead"><?php echo htmlspecialchars($circuit['name']); ?></p>
    </div>
    
    <!-- Boutons d'actions -->
    <div class="mb-4">
        <a href="index.php?page=circuits" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Retour à la liste
        </a>
        <a href="index.php?page=circuits&action=edit&id=<?php echo $circuit_id; ?>" class="btn btn-primary ms-2">
            <i class="fas fa-edit me-2"></i> Modifier ce circuit
        </a>
        <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#deleteCircuitModal">
            <i class="fas fa-trash me-2"></i> Supprimer
        </button>
    </div>
    
    <div class="row">
        <!-- Informations générales -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informations générales</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h4><?php echo htmlspecialchars($circuit['name']); ?></h4>
                            <p class="text-muted">
                                <i class="fas fa-calendar-alt me-2"></i> Du <?php echo date('d/m/Y', strtotime($circuit['start_date'])); ?> au <?php echo date('d/m/Y', strtotime($circuit['end_date'])); ?>
                                <span class="ms-3"><i class="fas fa-clock me-2"></i> <?php echo $days_difference; ?> jours</span>
                            </p>
                            <div class="mb-3">
                                <h5>Description</h5>
                                <p><?php echo nl2br(htmlspecialchars($circuit['description'])); ?></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Statut</h6>
                                    <p>
                                        <?php if($circuit['status'] == 'active'): ?>
                                            <span class="badge bg-success">Actif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactif</span>
                                        <?php endif; ?>
                                    </p>
                                    
                                    <h6 class="card-title">Capacité</h6>
                                    <p class="mb-1">
                                        <i class="fas fa-users me-1"></i> <?php echo $circuit['max_participants']; ?> participants max.
                                    </p>
                                    
                                    <?php if(isset($bookings['total_bookings'])): ?>
                                    <div class="progress mb-2" style="height: 20px;">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                             style="width: <?php echo $occupancy_rate; ?>%;" 
                                             aria-valuenow="<?php echo $occupancy_rate; ?>" aria-valuemin="0" aria-valuemax="100">
                                            <?php echo round($occupancy_rate); ?>%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo $places_taken; ?> places réservées, 
                                        <?php echo $places_remaining; ?> restantes
                                    </small>
                                    <?php else: ?>
                                    <p class="text-muted">Aucune réservation</p>
                                    <?php endif; ?>
                                    
                                    <h6 class="card-title mt-3">Création</h6>
                                    <p class="text-muted">
                                        <i class="fas fa-calendar me-1"></i> <?php echo date('d/m/Y', strtotime($circuit['creation_date'])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tarifs -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Tarifs</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Économique</h6>
                                            <h4><?php echo number_format($circuit['price_economy'], 2, ',', ' '); ?> €</h4>
                                            <div class="mt-2">
                                                <small class="text-muted">Inclus:</small>
                                                <ul class="small">
                                                    <li>Hébergement standard</li>
                                                    <li>Transport collectif</li>
                                                    <li>Guide pour visites principales</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Confort</h6>
                                            <h4><?php echo number_format($circuit['price_comfort'], 2, ',', ' '); ?> €</h4>
                                            <div class="mt-2">
                                                <small class="text-muted">Inclus:</small>
                                                <ul class="small">
                                                    <li>Hébergement 3-4 étoiles</li>
                                                    <li>Transport semi-privé</li>
                                                    <li>Guide pour toutes les visites</li>
                                                    <li>Petits déjeuners inclus</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">Premium</h6>
                                            <h4><?php echo number_format($circuit['price_premium'], 2, ',', ' '); ?> €</h4>
                                            <div class="mt-2">
                                                <small class="text-muted">Inclus:</small>
                                                <ul class="small">
                                                    <li>Hébergement 4-5 étoiles</li>
                                                    <li>Transport privé</li>
                                                    <li>Guide personnel dédié</li>
                                                    <li>Tous les repas inclus</li>
                                                    <li>Activités exclusives</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sites touristiques -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-landmark me-2"></i> Sites touristiques (<?php echo count($sites); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($sites)): ?>
                        <div class="alert alert-info">Aucun site touristique n'est associé à ce circuit.</div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach($sites as $site): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title"><?php echo htmlspecialchars($site['nom']); ?></h6>
                                            <p class="mb-1">
                                                <i class="fas fa-map-marker-alt me-1 text-danger"></i> 
                                                <?php echo htmlspecialchars($site['localisation']); ?>
                                            </p>
                                            <p class="mb-2">
                                                <span class="badge bg-secondary"><?php echo htmlspecialchars($site['categorie']); ?></span>
                                            </p>
                                            <p class="small text-muted mb-0">
                                                <?php 
                                                    $description = $site['description'] ?? 'Pas de description disponible';
                                                    echo strlen($description) > 100 ? substr(htmlspecialchars($description), 0, 100) . '...' : htmlspecialchars($description);
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Prestataires et statistiques -->
        <div class="col-lg-4">
            <!-- Prestataires -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i> Prestataires</h5>
                </div>
                <div class="card-body">
                    <!-- Guide -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Guide</h6>
                        <?php if(!empty($circuit['guide_name'])): ?>
                            <p class="mb-1"><strong><?php echo htmlspecialchars($circuit['guide_name']); ?></strong></p>
                            <p class="mb-1">
                                <i class="fas fa-phone me-1"></i> 
                                <?php echo htmlspecialchars($circuit['guide_phone'] ?? 'Non renseigné'); ?>
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-envelope me-1"></i> 
                                <?php echo htmlspecialchars($circuit['guide_email'] ?? 'Non renseigné'); ?>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-star me-1"></i> 
                                <?php echo htmlspecialchars($circuit['guide_speciality'] ?? 'Pas de spécialité'); ?>
                            </p>
                        <?php else: ?>
                            <p class="text-muted">Aucun guide sélectionné</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Hébergement -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Hébergement</h6>
                        <?php if(!empty($circuit['hotel_name'])): ?>
                            <p class="mb-1"><strong><?php echo htmlspecialchars($circuit['hotel_name']); ?></strong></p>
                            <p class="mb-1">
                                <i class="fas fa-map-marker-alt me-1"></i> 
                                <?php echo htmlspecialchars($circuit['hotel_address'] ?? 'Adresse non renseignée'); ?>, 
                                <?php echo htmlspecialchars($circuit['hotel_city'] ?? ''); ?>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-star me-1"></i> 
                                <?php 
                                    $stars = '';
                                    for($i = 0; $i < $circuit['hotel_category']; $i++) {
                                        $stars .= '⭐';
                                    }
                                    echo $stars ? $stars : 'Non classé';
                                ?>
                            </p>
                        <?php else: ?>
                            <p class="text-muted">Aucun hébergement sélectionné</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Transporteur -->
                    <div>
                        <h6 class="border-bottom pb-2">Transporteur</h6>
                        <?php if(!empty($circuit['transporter_name'])): ?>
                            <p class="mb-1"><strong><?php echo htmlspecialchars($circuit['transporter_name']); ?></strong></p>
                            <p class="mb-1">
                                <i class="fas fa-phone me-1"></i> 
                                <?php echo htmlspecialchars($circuit['transporter_phone'] ?? 'Non renseigné'); ?>
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-envelope me-1"></i> 
                                <?php echo htmlspecialchars($circuit['transporter_email'] ?? 'Non renseigné'); ?>
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-bus me-1"></i> 
                                <?php echo htmlspecialchars($circuit['vehicle_type'] ?? 'Non renseigné'); ?>
                            </p>
                        <?php else: ?>
                            <p class="text-muted">Aucun transporteur sélectionné</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Statistiques des réservations -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> Statistiques des réservations</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($bookings['total_bookings']) && $bookings['total_bookings'] > 0): ?>
                        <div class="text-center mb-3">
                            <h3><?php echo $bookings['total_bookings']; ?> réservation(s)</h3>
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: <?php echo $occupancy_rate; ?>%;" 
                                     aria-valuenow="<?php echo $occupancy_rate; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?php echo round($occupancy_rate); ?>%
                                </div>
                            </div>
                            <p class="text-muted">
                                <?php echo $places_taken; ?> places occupées sur <?php echo $circuit['max_participants']; ?>
                            </p>
                        </div>
                        
                        <h6>Répartition par panel</h6>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Économique</span>
                                <span><?php echo $bookings['economy_bookings']; ?></span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-secondary" role="progressbar" 
                                     style="width: <?php echo ($bookings['economy_bookings'] / $bookings['total_bookings']) * 100; ?>%" 
                                     aria-valuenow="<?php echo $bookings['economy_bookings']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $bookings['total_bookings']; ?>"></div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Confort</span>
                                <span><?php echo $bookings['comfort_bookings']; ?></span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                     style="width: <?php echo ($bookings['comfort_bookings'] / $bookings['total_bookings']) * 100; ?>%" 
                                     aria-valuenow="<?php echo $bookings['comfort_bookings']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $bookings['total_bookings']; ?>"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Premium</span>
                                <span><?php echo $bookings['premium_bookings']; ?></span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-dark" role="progressbar" 
                                     style="width: <?php echo ($bookings['premium_bookings'] / $bookings['total_bookings']) * 100; ?>%" 
                                     aria-valuenow="<?php echo $bookings['premium_bookings']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $bookings['total_bookings']; ?>"></div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="index.php?page=reservations&circuit_id=<?php echo $circuit_id; ?>" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-list me-1"></i> Voir toutes les réservations
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6>Pas encore de réservation</h6>
                            <p class="text-muted">Aucune réservation n'a été enregistrée pour ce circuit.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Actions supplémentaires -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i> Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="index.php?page=circuits&action=edit&id=<?php echo $circuit_id; ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Modifier ce circuit
                        </a>
                        
                        <button type="button" class="btn btn-success" id="duplicateCircuitBtn">
                            <i class="fas fa-copy me-2"></i> Dupliquer ce circuit
                        </button>
                        
                        <button type="button" class="btn btn-info" id="exportCircuitBtn">
                            <i class="fas fa-file-export me-2"></i> Exporter les détails (PDF)
                        </button>
                        
                        <?php if($circuit['status'] == 'active'): ?>
                            <button type="button" class="btn btn-warning" id="deactivateCircuitBtn">
                                <i class="fas fa-pause-circle me-2"></i> Désactiver temporairement
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-success" id="activateCircuitBtn">
                                <i class="fas fa-play-circle me-2"></i> Activer le circuit
                            </button>
                        <?php endif; ?>
                        
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCircuitModal">
                            <i class="fas fa-trash me-2"></i> Supprimer définitivement
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteCircuitModal" tabindex="-1" aria-labelledby="deleteCircuitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCircuitModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le circuit <strong><?php echo htmlspecialchars($circuit['name']); ?></strong> ?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Cette action est irréversible et supprimera toutes les données associées à ce circuit.</p>
                
                <?php if(isset($bookings['total_bookings']) && $bookings['total_bookings'] > 0): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i> Attention : Ce circuit a déjà <?php echo $bookings['total_bookings']; ?> réservation(s). La suppression annulera toutes ces réservations.
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="index.php?page=circuits&action=delete&id=<?php echo $circuit_id; ?>" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de duplication -->
<div class="modal fade" id="duplicateModal" tabindex="-1" aria-labelledby="duplicateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="duplicateModalLabel">Dupliquer le circuit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="duplicateForm" action="index.php?page=circuits&action=duplicate" method="post">
                    <input type="hidden" name="original_circuit_id" value="<?php echo $circuit_id; ?>">
                    
                    <div class="mb-3">
                        <label for="new_circuit_name" class="form-label">Nom du nouveau circuit</label>
                        <input type="text" class="form-control" id="new_circuit_name" name="new_circuit_name" 
                               value="<?php echo htmlspecialchars($circuit['name']); ?> (copie)" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_start_date" class="form-label">Nouvelle date de début</label>
                            <input type="date" class="form-control" id="new_start_date" name="new_start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_end_date" class="form-label">Nouvelle date de fin</label>
                            <input type="date" class="form-control" id="new_end_date" name="new_end_date" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="copy_prestataires" name="copy_prestataires" checked>
                            <label class="form-check-label" for="copy_prestataires">
                                Copier les prestataires (guide, hébergement, transporteur)
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="copy_prices" name="copy_prices" checked>
                            <label class="form-check-label" for="copy_prices">
                                Copier les tarifs des différents panels
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="duplicateForm" class="btn btn-success">Dupliquer</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du bouton Dupliquer
    const duplicateBtn = document.getElementById('duplicateCircuitBtn');
    if(duplicateBtn) {
        duplicateBtn.addEventListener('click', function() {
            // Calculer la date de demain pour la date minimale
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            // Formater la date au format YYYY-MM-DD
            const tomorrowFormatted = tomorrow.toISOString().split('T')[0];
            
            // Définir la date minimale pour les champs de date
            document.getElementById('new_start_date').min = tomorrowFormatted;
            document.getElementById('new_end_date').min = tomorrowFormatted;
            
            // Préremplir avec des dates par défaut (ex: 1 mois plus tard)
            const oneMonthLater = new Date();
            oneMonthLater.setMonth(oneMonthLater.getMonth() + 1);
            const oneMonthLaterFormatted = oneMonthLater.toISOString().split('T')[0];
            
            const oneMonthAndWeekLater = new Date(oneMonthLater);
            oneMonthAndWeekLater.setDate(oneMonthAndWeekLater.getDate() + 7);
            const oneMonthAndWeekLaterFormatted = oneMonthAndWeekLater.toISOString().split('T')[0];
            
            document.getElementById('new_start_date').value = oneMonthLaterFormatted;
            document.getElementById('new_end_date').value = oneMonthAndWeekLaterFormatted;
            
            // Afficher le modal
            const duplicateModal = new bootstrap.Modal(document.getElementById('duplicateModal'));
            duplicateModal.show();
        });
    }
    
    // Gestion du bouton Exporter
    const exportBtn = document.getElementById('exportCircuitBtn');
    if(exportBtn) {
        exportBtn.addEventListener('click', function() {
            window.location.href = 'index.php?page=circuits&action=export&id=' + <?php echo $circuit_id; ?>;
        });
    }
    
    // Gestion du bouton Activer/Désactiver
    const activateBtn = document.getElementById('activateCircuitBtn');
    const deactivateBtn = document.getElementById('deactivateCircuitBtn');
    
    if(activateBtn) {
        activateBtn.addEventListener('click', function() {
            if(confirm('Voulez-vous activer ce circuit ?')) {
                window.location.href = 'index.php?page=circuits&action=toggle_status&id=' + <?php echo $circuit_id; ?> + '&status=active';
            }
        });
    }
    
    if(deactivateBtn) {
        deactivateBtn.addEventListener('click', function() {
            if(confirm('Voulez-vous désactiver temporairement ce circuit ?')) {
                window.location.href = 'index.php?page=circuits&action=toggle_status&id=' + <?php echo $circuit_id; ?> + '&status=inactive';
            }
        });
    }
});

            </script>