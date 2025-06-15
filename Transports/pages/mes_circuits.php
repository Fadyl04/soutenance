<?php
// Pas besoin de démarrer la session ou d'inclure db.php, déjà fait dans index.php
include 'database/db.php';
// Récupérer l'ID de l'hôtel connecté de la même manière que dans index.php
$hotel_id = $_SESSION['prestataire_id'];

// Récupérer les informations de l'hôtel connecté
try {
    $stmt = $db->prepare("SELECT id, hotel_name, city, hotel_category FROM hotels WHERE id = :hotel_id");
    $stmt->bindParam(':hotel_id', $hotel_id);
    $stmt->execute();
    $hotel_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if hotel_info is false (no rows returned)
    if($hotel_info === false) {
        $hotel_info = [
            'hotel_name' => 'Information non disponible',
            'city' => 'Information non disponible',
            'hotel_category' => 0
        ];
        echo "<div class='alert alert-warning'>Informations de l'hôtel non trouvées.</div>";
    }
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des informations de l'hôtel: " . $e->getMessage() . "</div>";
    $hotel_info = [
        'hotel_name' => 'Information non disponible',
        'city' => 'Information non disponible',
        'hotel_category' => 0
    ];
}

// Le reste du code reste identique à partir d'ici...
// Requête pour récupérer les circuits affectés à cet hôtel
try {
    $query = "SELECT c.*, 
              CONCAT(g.first_name, ' ', g.last_name) AS guide_name, 
              CONCAT(t.first_name, ' ', t.last_name) AS transporter_name 
              FROM circuits c 
              LEFT JOIN guides g ON c.guide_id = g.id 
              LEFT JOIN transporters t ON c.transporter_id = t.id
              WHERE c.hotel_id = :hotel_id
              ORDER BY c.start_date";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':hotel_id', $hotel_id);
    $stmt->execute();
    $hotel_circuits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If no circuits found, set as empty array
    if($hotel_circuits === false) {
        $hotel_circuits = [];
    }
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des circuits: " . $e->getMessage() . "</div>";
    $hotel_circuits = [];
}
// Initialiser les compteurs à zéro avant de les utiliser
$upcoming_circuits = 0;
$ongoing_circuits = 0;
$past_circuits = 0;
$today = date('Y-m-d');

// Ensuite, effectuer le comptage comme vous le faites déjà
foreach($hotel_circuits as $circuit) {
    if($circuit['start_date'] > $today) {
        $upcoming_circuits++;
    } elseif($circuit['end_date'] < $today) {
        $past_circuits++;
    } else {
        $ongoing_circuits++;
    }
}

// Reste de votre code...
?>

<div class="container-fluid">
    <div class="header">
        <h2><i class="fas fa-route me-2"></i> Mes Circuits</h2>
        <p class="lead">Visualisez tous les circuits affectés à votre hôtel</p>
    </div>
    
    <!-- Informations de l'hôtel -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        <?php echo htmlspecialchars($hotel_info['hotel_name'] ?? 'Information non disponible'); ?>
                        <?php 
                            $stars = '';
                            $category = isset($hotel_info['hotel_category']) ? intval($hotel_info['hotel_category']) : 0;
                            for($i = 0; $i < $category; $i++) {
                                $stars .= '⭐';
                            }
                            echo $stars;
                        ?>
                    </h4>
                    <p class="card-text"><?php echo htmlspecialchars($hotel_info['city'] ?? 'Information non disponible'); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-calendar-alt me-2"></i> Circuits à venir</h5>
                    <h3 class="card-text"><?php echo $upcoming_circuits; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-play-circle me-2"></i> Circuits en cours</h5>
                    <h3 class="card-text"><?php echo $ongoing_circuits; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-history me-2"></i> Circuits passés</h5>
                    <h3 class="card-text"><?php echo $past_circuits; ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Liste des circuits -->
    <div class="card">
        <div class="card-header bg-light">
            <h4 class="card-title mb-0">
                <i class="fas fa-list me-2"></i> Vos circuits 
                <span class="badge bg-primary"><?php echo count($hotel_circuits); ?></span>
            </h4>
        </div>
        <div class="card-body">
            <?php if(empty($hotel_circuits)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Aucun circuit n'est actuellement affecté à votre hôtel.
                </div>
            <?php else: ?>
                <!-- Filtres -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="searchCircuit" placeholder="Rechercher un circuit...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="btn-group float-end" role="group">
                            <button type="button" class="btn btn-outline-primary filter-btn active" data-filter="all">Tous</button>
                            <button type="button" class="btn btn-outline-primary filter-btn" data-filter="upcoming">À venir</button>
                            <button type="button" class="btn btn-outline-primary filter-btn" data-filter="ongoing">En cours</button>
                            <button type="button" class="btn btn-outline-primary filter-btn" data-filter="past">Passés</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="hotelCircuitsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Circuit</th>
                                <th>Dates</th>
                                <th>Durée</th>
                                <th>Guide</th>
                                <th>Transporteur</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($hotel_circuits as $circuit): 
                                // Déterminer le statut du circuit
                                $status = '';
                                $status_class = '';
                                if($circuit['start_date'] > $today) {
                                    $status = 'À venir';
                                    $status_class = 'bg-primary';
                                    $filter_class = 'upcoming';
                                } elseif($circuit['end_date'] < $today) {
                                    $status = 'Terminé';
                                    $status_class = 'bg-secondary';
                                    $filter_class = 'past';
                                } else {
                                    $status = 'En cours';
                                    $status_class = 'bg-success';
                                    $filter_class = 'ongoing';
                                }
                            ?>
                                <tr class="circuit-row <?php echo $filter_class; ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($circuit['name']); ?></strong>
                                        <?php if(!empty($circuit['description'])): ?>
                                            <div class="small text-muted"><?php echo substr(htmlspecialchars($circuit['description']), 0, 50) . '...'; ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        Du <?php echo date('d/m/Y', strtotime($circuit['start_date'])); ?>
                                        <br>au <?php echo date('d/m/Y', strtotime($circuit['end_date'])); ?>
                                    </td>
                                    <td><?php echo $circuit['duration']; ?> jours</td>
                                    <td>
                                        <?php if(!empty($circuit['guide_name'])): ?>
                                            <?php echo htmlspecialchars($circuit['guide_name']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Non assigné</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if(!empty($circuit['transporter_name'])): ?>
                                            <?php echo htmlspecialchars($circuit['transporter_name']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Non assigné</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $status_class; ?>"><?php echo $status; ?></span>
                                    </td>
                                    <td>
                                        <a href="index.php?page=circuit_detail&id=<?php echo $circuit['id']; ?>" class="btn btn-sm btn-info" title="Voir les détails">
                                            <i class="fas fa-eye"></i> Détails
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonctionnalité de recherche
    const searchInput = document.getElementById('searchCircuit');
    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#hotelCircuitsTable tbody tr');
            
            rows.forEach(row => {
                const circuitName = row.querySelector('td:first-child').textContent.toLowerCase();
                const guideName = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const transporterName = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                
                if(circuitName.includes(searchTerm) || guideName.includes(searchTerm) || transporterName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Fonctionnalité de filtrage par statut
    const filterButtons = document.querySelectorAll('.filter-btn');
    if(filterButtons.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Retirer la classe active de tous les boutons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Ajouter la classe active au bouton cliqué
                this.classList.add('active');
                
                const filter = this.getAttribute('data-filter');
                const rows = document.querySelectorAll('#hotelCircuitsTable tbody tr');
                
                rows.forEach(row => {
                    if(filter === 'all' || row.classList.contains(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    }
});
</script>