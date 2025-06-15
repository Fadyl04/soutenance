<?php
include 'database/db.php';
// Vérifier que l'administrateur est connecté
if(!isset($_SESSION['admin'])){
    header("Location:../index.php?page=login");
    exit();
}

// Créer la table circuits si elle n'existe pas
try {
    $db->exec("CREATE TABLE IF NOT EXISTS circuits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        price_economy DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        price_comfort DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        price_premium DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
        max_participants INT NOT NULL DEFAULT 10,
        duration INT NOT NULL DEFAULT 1,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        guide_id INT,
        hotel_id INT,
        transporter_id INT,
        creation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('active', 'inactive') DEFAULT 'active'
    )");
    
    $db->exec("CREATE TABLE IF NOT EXISTS circuit_sites (
        circuit_id INT,
        site_id INT,
        PRIMARY KEY (circuit_id, site_id)
    )");
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la création des tables: " . $e->getMessage() . "</div>";
}

// Traitement des actions (création, modification, suppression de circuit)
if(isset($_GET['action'])) {
    $action = $_GET['action'];
    
    // SUPPRESSION D'UN CIRCUIT
    if($action === 'delete' && isset($_GET['id'])) {
        $circuit_id = intval($_GET['id']);
        try {
            // Supprimer le circuit
            $stmt = $db->prepare("DELETE FROM circuits WHERE id = :id");
            $stmt->bindParam(':id', $circuit_id);
            if($stmt->execute()) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Circuit supprimé',
                        text: 'Le circuit a été supprimé avec succès.',
                        confirmButtonColor: '#008751'
                    });
                </script>";
            }
        } catch(PDOException $e) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de la suppression du circuit: " . addslashes($e->getMessage()) . "',
                    confirmButtonColor: '#008751'
                });
            </script>";
        }
    }
    
    // TRAITEMENT DU FORMULAIRE DE CRÉATION/MODIFICATION
    if(($action === 'create' || $action === 'edit') && isset($_POST['submit_circuit'])) {
        $circuit_name = $_POST['circuit_name'];
        $description = $_POST['description'];
        $price_economy = $_POST['price_economy'];
        $price_comfort = $_POST['price_comfort'];
        $price_premium = $_POST['price_premium'];
        $max_participants = $_POST['max_participants'];
        $duration = $_POST['duration'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        
        // Traitement des prestataires sélectionnés
        $guide_id = isset($_POST['guide_id']) ? intval($_POST['guide_id']) : null;
        $hotel_id = isset($_POST['hotel_id']) ? intval($_POST['hotel_id']) : null;
        $transporter_id = isset($_POST['transporter_id']) ? intval($_POST['transporter_id']) : null;
        
        // Traitement des sites touristiques sélectionnés
        $selected_sites = isset($_POST['tourist_sites']) ? $_POST['tourist_sites'] : [];
        
        try {
            $db->beginTransaction();
            
            if($action === 'create') {
                // Insertion du nouveau circuit
                $stmt = $db->prepare("INSERT INTO circuits (name, description, price_economy, price_comfort, price_premium, 
                                    max_participants, duration, start_date, end_date, guide_id, hotel_id, transporter_id, 
                                    creation_date, status) 
                                    VALUES (:name, :description, :price_economy, :price_comfort, :price_premium, 
                                    :max_participants, :duration, :start_date, :end_date, :guide_id, :hotel_id, :transporter_id, 
                                    NOW(), 'active')");
                
                // Exécution de la requête
                $stmt->execute([
                    ':name' => $circuit_name,
                    ':description' => $description,
                    ':price_economy' => $price_economy,
                    ':price_comfort' => $price_comfort,
                    ':price_premium' => $price_premium,
                    ':max_participants' => $max_participants,
                    ':duration' => $duration,
                    ':start_date' => $start_date,
                    ':end_date' => $end_date,
                    ':guide_id' => $guide_id,
                    ':hotel_id' => $hotel_id,
                    ':transporter_id' => $transporter_id
                ]);
                
                $circuit_id = $db->lastInsertId();
                
                // Relation avec les sites touristiques
                foreach($selected_sites as $site_id) {
                    $stmt = $db->prepare("INSERT INTO circuit_sites (circuit_id, site_id) VALUES (:circuit_id, :site_id)");
                    $stmt->execute([':circuit_id' => $circuit_id, ':site_id' => intval($site_id)]);
                }
                
                $message = "Le circuit a été créé avec succès.";
            } else {
                // Modification d'un circuit existant
                $circuit_id = intval($_POST['circuit_id']);
                
                $stmt = $db->prepare("UPDATE circuits SET 
                                    name = :name, 
                                    description = :description, 
                                    price_economy = :price_economy, 
                                    price_comfort = :price_comfort, 
                                    price_premium = :price_premium, 
                                    max_participants = :max_participants, 
                                    duration = :duration, 
                                    start_date = :start_date, 
                                    end_date = :end_date, 
                                    guide_id = :guide_id, 
                                    hotel_id = :hotel_id, 
                                    transporter_id = :transporter_id 
                                    WHERE id = :id");
                
                // Exécution de la requête
                $stmt->execute([
                    ':name' => $circuit_name,
                    ':description' => $description,
                    ':price_economy' => $price_economy,
                    ':price_comfort' => $price_comfort,
                    ':price_premium' => $price_premium,
                    ':max_participants' => $max_participants,
                    ':duration' => $duration,
                    ':start_date' => $start_date,
                    ':end_date' => $end_date,
                    ':guide_id' => $guide_id,
                    ':hotel_id' => $hotel_id,
                    ':transporter_id' => $transporter_id,
                    ':id' => $circuit_id
                ]);
                
                // Supprimer les anciennes relations avec les sites touristiques
                $stmt = $db->prepare("DELETE FROM circuit_sites WHERE circuit_id = :circuit_id");
                $stmt->execute([':circuit_id' => $circuit_id]);
                
                // Créer les nouvelles relations
                foreach($selected_sites as $site_id) {
                    $stmt = $db->prepare("INSERT INTO circuit_sites (circuit_id, site_id) VALUES (:circuit_id, :site_id)");
                    $stmt->execute([':circuit_id' => $circuit_id, ':site_id' => intval($site_id)]);
                }
                
                $message = "Le circuit a été mis à jour avec succès.";
            }
            
            $db->commit();
            
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: '" . $message . "',
                    confirmButtonColor: '#008751'
                }).then(() => {
                    window.location = 'index.php?page=circuits';
                });
            </script>";
            
        } catch(PDOException $e) {
            $db->rollBack();
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
}

// Récupérer tous les circuits
try {
    $stmt = $db->query("SELECT c.*, 
                        h.hotel_name, 
                        CONCAT(g.first_name, ' ', g.last_name) AS guide_name, 
                        CONCAT(t.first_name, ' ', t.last_name) AS transporter_name 
                        FROM circuits c 
                        LEFT JOIN hotels h ON c.hotel_id = h.id 
                        LEFT JOIN guides g ON c.guide_id = g.id 
                        LEFT JOIN transporters t ON c.transporter_id = t.id 
                        ORDER BY c.creation_date DESC");
    $circuits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des circuits: " . $e->getMessage() . "</div>";
    $circuits = [];
}

// Récupérer la liste des guides pour le formulaire
try {
    $stmt = $db->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name, specializations AS speciality FROM guides WHERE status = 'pending' OR status = 'approved' ORDER BY first_name, last_name");
    $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des guides: " . $e->getMessage() . "</div>";
    $guides = [];
}

// Récupérer la liste des hôtels pour le formulaire
try {
    $stmt = $db->query("SELECT id, hotel_name, city, hotel_category FROM hotels WHERE status = 'approved' ORDER BY hotel_name");
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des hôtels: " . $e->getMessage() . "</div>";
    $hotels = [];
}

// Récupérer la liste des transporteurs pour le formulaire
try {
    $stmt = $db->query("SELECT id, CONCAT(first_name, ' ', last_name) AS company_name, vehicle_type FROM transporters WHERE status = 'pending' OR status = 'approved' ORDER BY first_name, last_name");
    $transporters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des transporteurs: " . $e->getMessage() . "</div>";
    $transporters = [];
}

// Récupérer la liste des sites touristiques pour le formulaire
try {
    $stmt = $db->query("SELECT id, nom AS name, localisation AS location, categorie AS type FROM sites_touristiques ORDER BY nom");
    $tourist_sites = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des sites touristiques: " . $e->getMessage() . "</div>";
    $tourist_sites = [];
}

// Si on est en mode édition, récupérer les détails du circuit
$edit_mode = false;
$current_circuit = [];
$circuit_sites = [];

if(isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_mode = true;
    $circuit_id = intval($_GET['id']);
    
    // Récupérer les détails du circuit
    try {
        $stmt = $db->prepare("SELECT * FROM circuits WHERE id = :id");
        $stmt->bindParam(':id', $circuit_id);
        $stmt->execute();
        $current_circuit = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Récupérer les sites touristiques associés
        $stmt = $db->prepare("SELECT site_id FROM circuit_sites WHERE circuit_id = :circuit_id");
        $stmt->bindParam(':circuit_id', $circuit_id);
        $stmt->execute();
        $sites_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($sites_results as $site) {
            $circuit_sites[] = $site['site_id'];
        }
    } catch(PDOException $e) {
        echo "<div class='alert alert-danger'>Erreur lors de la récupération des détails du circuit: " . $e->getMessage() . "</div>";
    }
}
?>

<!-- Le reste du code HTML reste inchangé -->

<div class="container-fluid">
    <div class="header">
        <h2><i class="fas fa-route me-2"></i> Gestion des Circuits Touristiques</h2>
        <p class="lead">Créez, modifiez et gérez les circuits touristiques</p>
    </div>
    
    <?php if(isset($_GET['action']) && ($_GET['action'] === 'create' || $_GET['action'] === 'edit')): ?>
        <!-- Formulaire de création/modification de circuit -->
        <div class="card mb-4">
            <div class="card-body">
                <h3 class="card-title mb-4">
                    <?php echo $edit_mode ? 'Modifier le circuit' : 'Créer un nouveau circuit'; ?>
                </h3>
                
                <form method="post" action="index.php?page=circuits&action=<?php echo $edit_mode ? 'edit' : 'create'; ?>">
                    <?php if($edit_mode): ?>
                        <input type="hidden" name="circuit_id" value="<?php echo $current_circuit['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <!-- Informations générales -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informations générales</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="circuit_name" class="form-label">Nom du circuit</label>
                                        <input type="text" class="form-control" id="circuit_name" name="circuit_name" required
                                            value="<?php echo $edit_mode ? htmlspecialchars($current_circuit['name']) : ''; ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="5" required><?php 
                                            echo $edit_mode ? htmlspecialchars($current_circuit['description']) : ''; 
                                        ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="duration" class="form-label">Durée (jours)</label>
                                            <input type="number" class="form-control" id="duration" name="duration" min="1" required
                                                value="<?php echo $edit_mode ? htmlspecialchars($current_circuit['duration']) : '1'; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="max_participants" class="form-label">Nombre max. de participants</label>
                                            <input type="number" class="form-control" id="max_participants" name="max_participants" min="1" required
                                                value="<?php echo $edit_mode ? htmlspecialchars($current_circuit['max_participants']) : '10'; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="start_date" class="form-label">Date de début</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" required
                                                value="<?php echo $edit_mode ? htmlspecialchars($current_circuit['start_date']) : ''; ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="end_date" class="form-label">Date de fin</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" required
                                                value="<?php echo $edit_mode ? htmlspecialchars($current_circuit['end_date']) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tarifs par panel -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Tarifs par panel</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="price_economy" class="form-label">Panel Économique</label>
                                            <div class="input-group">
                                                <span class="input-group-text">€</span>
                                                <input type="number" class="form-control" id="price_economy" name="price_economy" min="0" step="0.01" required
                                                    value="<?php echo $edit_mode ? htmlspecialchars($current_circuit['price_economy']) : '0.00'; ?>">
                                            </div>
                                            <small class="text-muted">Services basiques</small>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="price_comfort" class="form-label">Panel Confort</label>
                                            <div class="input-group">
                                                <span class="input-group-text">€</span>
                                                <input type="number" class="form-control" id="price_comfort" name="price_comfort" min="0" step="0.01" required
                                                    value="<?php echo $edit_mode ? htmlspecialchars($current_circuit['price_comfort']) : '0.00'; ?>">
                                            </div>
                                            <small class="text-muted">Services améliorés</small>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="price_premium" class="form-label">Panel Premium</label>
                                            <div class="input-group">
                                                <span class="input-group-text">€</span>
                                                <input type="number" class="form-control" id="price_premium" name="price_premium" min="0" step="0.01" required
                                                    value="<?php echo $edit_mode ? htmlspecialchars($current_circuit['price_premium']) : '0.00'; ?>">
                                            </div>
                                            <small class="text-muted">Services haut de gamme</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <h6 class="mb-3">Description des panels :</h6>
                                        <div class="accordion" id="panelsAccordion">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingEconomy">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEconomy" aria-expanded="false" aria-controls="collapseEconomy">
                                                        Panel Économique
                                                    </button>
                                                </h2>
                                                <div id="collapseEconomy" class="accordion-collapse collapse" aria-labelledby="headingEconomy" data-bs-parent="#panelsAccordion">
                                                    <div class="accordion-body">
                                                        <ul>
                                                            <li>Hébergement standard</li>
                                                            <li>Transport collectif</li>
                                                            <li>Guide pour les visites principales</li>
                                                            <li>Repas non inclus</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingComfort">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseComfort" aria-expanded="false" aria-controls="collapseComfort">
                                                        Panel Confort
                                                    </button>
                                                </h2>
                                                <div id="collapseComfort" class="accordion-collapse collapse" aria-labelledby="headingComfort" data-bs-parent="#panelsAccordion">
                                                    <div class="accordion-body">
                                                        <ul>
                                                            <li>Hébergement 3-4 étoiles</li>
                                                            <li>Transport semi-privé</li>
                                                            <li>Guide pour toutes les visites</li>
                                                            <li>Petits déjeuners inclus</li>
                                                            <li>Une activité exclusive</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingPremium">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePremium" aria-expanded="false" aria-controls="collapsePremium">
                                                        Panel Premium
                                                    </button>
                                                </h2>
                                                <div id="collapsePremium" class="accordion-collapse collapse" aria-labelledby="headingPremium" data-bs-parent="#panelsAccordion">
                                                    <div class="accordion-body">
                                                        <ul>
                                                            <li>Hébergement 4-5 étoiles</li>
                                                            <li>Transport privé</li>
                                                            <li>Guide personnel dédié</li>
                                                            <li>Tous les repas inclus</li>
                                                            <li>Activités exclusives</li>
                                                            <li>Transferts aéroport</li>
                                                            <li>Service de concierge</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sites touristiques -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-landmark me-2"></i> Sites touristiques</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Sélectionnez les sites touristiques</label>
                                        <div class="row">
                                            <?php foreach($tourist_sites as $site): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="tourist_sites[]" 
                                                            value="<?php echo $site['id']; ?>" id="site<?php echo $site['id']; ?>"
                                                            <?php echo $edit_mode && in_array($site['id'], $circuit_sites) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="site<?php echo $site['id']; ?>">
                                                            <?php echo htmlspecialchars($site['name']); ?> 
                                                            <small class="text-muted">(<?php echo htmlspecialchars($site['location']); ?>)</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Prestataires -->
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="fas fa-users me-2"></i> Prestataires</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="guide_id" class="form-label">Guide</label>
                                        <select class="form-select" id="guide_id" name="guide_id">
                                            <option value="">-- Sélectionner un guide --</option>
                                            <?php foreach($guides as $guide): ?>
                                                <option value="<?php echo $guide['id']; ?>" 
                                                    <?php echo $edit_mode && $current_circuit['guide_id'] == $guide['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($guide['name']); ?> 
                                                    <small>(<?php echo htmlspecialchars($guide['speciality']); ?>)</small>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="hotel_id" class="form-label">Hébergement</label>
                                        <select class="form-select" id="hotel_id" name="hotel_id">
                                            <option value="">-- Sélectionner un hébergement --</option>
                                            <?php foreach($hotels as $hotel): ?>
                                                <option value="<?php echo $hotel['id']; ?>" 
                                                    <?php echo $edit_mode && $current_circuit['hotel_id'] == $hotel['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($hotel['hotel_name']); ?> 
                                                    (<?php echo htmlspecialchars($hotel['city']); ?>) 
                                                    <?php 
                                                        $stars = '';
                                                        for($i = 0; $i < $hotel['hotel_category']; $i++) {
                                                            $stars .= '⭐';
                                                        }
                                                        echo $stars;
                                                    ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="transporter_id" class="form-label">Transporteur</label>
                                        <select class="form-select" id="transporter_id" name="transporter_id">
                                            <option value="">-- Sélectionner un transporteur --</option>
                                            <?php foreach($transporters as $transporter): ?>
                                                <option value="<?php echo $transporter['id']; ?>" 
                                                    <?php echo $edit_mode && $current_circuit['transporter_id'] == $transporter['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($transporter['company_name']); ?> 
                                                    (<?php echo htmlspecialchars($transporter['vehicle_type']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="index.php?page=circuits" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-2"></i> Retour
                        </a>
                        <button type="submit" name="submit_circuit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> <?php echo $edit_mode ? 'Mettre à jour' : 'Créer le circuit'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Liste des circuits -->
        <div class="mb-4">
            <a href="index.php?page=circuits&action=create" class="btn btn-success">
                <i class="fas fa-plus me-2"></i> Créer un nouveau circuit
            </a>
        </div>
        
        <!-- Filtres pour les circuits -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Filtrer les circuits</h5>
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" id="searchCircuit" class="form-control" placeholder="Rechercher un circuit...">
                    </div>
                    <div class="col-md-4">
                        <select id="locationFilter" class="form-select">
                            <option value="all">Toutes les régions</option>
                            <?php 
                                $regions = [];
                                foreach($tourist_sites as $site) {
                                    if(!in_array($site['location'], $regions)) {
                                        $regions[] = $site['location'];
                                        echo '<option value="' . htmlspecialchars($site['location']) . '">' . htmlspecialchars($site['location']) . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="durationFilter" class="form-select">
                            <option value="all">Toutes les durées</option>
                            <option value="1-3">1-3 jours</option>
                            <option value="4-7">4-7 jours</option>
                            <option value="8+">8+ jours</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Compteurs -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-route me-2"></i> Total circuits</h5>
                        <h3 class="card-text"><?php echo count($circuits); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-calendar-alt me-2"></i> Circuits actifs</h5>
                        <h3 class="card-text">
                            <?php 
                                $active_count = 0;
                                foreach($circuits as $circuit) {
                                    if($circuit['status'] == 'active') $active_count++;
                                }
                                echo $active_count;
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-map-marker-alt me-2"></i> Sites touristiques</h5>
                        <h3 class="card-text"><?php echo count($tourist_sites); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-users me-2"></i> Prestataires</h5>
                        <h3 class="card-text"><?php echo count($guides) + count($hotels) + count($transporters); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liste des circuits -->
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-4">Liste des circuits</h3>
                
                <?php if(empty($circuits)): ?>
                    <div class="alert alert-info">
                        Aucun circuit n'a été créé pour le moment. Cliquez sur "Créer un nouveau circuit" pour commencer.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover" id="circuitsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Durée</th>
                                    <th>Dates</th>
                                    <th>Prix (F)</th>
                                    <th>Capacité</th>
                                    <th>Prestataires</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($circuits as $circuit): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($circuit['name']); ?></strong>
                                        </td>
                                        <td><?php echo $circuit['duration']; ?> jours</td>
                                        <td>
                                            Du <?php echo date('d/m/Y', strtotime($circuit['start_date'])); ?>
                                            <br>au <?php echo date('d/m/Y', strtotime($circuit['end_date'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">Eco: <?php echo number_format($circuit['price_economy'], 2, ',', ' '); ?> €</span>
                                            <br>
                                            <span class="badge bg-primary">Confort: <?php echo number_format($circuit['price_comfort'], 2, ',', ' '); ?> €</span>
                                            <br>
                                            <span class="badge bg-dark">Premium: <?php echo number_format($circuit['price_premium'], 2, ',', ' '); ?> €</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-users me-1"></i> <?php echo $circuit['max_participants']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if(!empty($circuit['guide_name'])): ?>
                                                <div><i class="fas fa-user-tie me-1"></i> <?php echo htmlspecialchars($circuit['guide_name']); ?></div>
                                            <?php endif; ?>
                                            
                                            <?php if(!empty($circuit['hotel_name'])): ?>
                                                <div><i class="fas fa-hotel me-1"></i> <?php echo htmlspecialchars($circuit['hotel_name']); ?></div>
                                            <?php endif; ?>
                                            
                                            <?php if(!empty($circuit['transporter_name'])): ?>
                                                <div><i class="fas fa-bus me-1"></i> <?php echo htmlspecialchars($circuit['transporter_name']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="index.php?page=circuit_detail&id=<?php echo $circuit['id']; ?>" class="btn btn-sm btn-info" title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="index.php?page=circuits&action=edit&id=<?php echo $circuit['id']; ?>" class="btn btn-sm btn-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-circuit" data-id="<?php echo $circuit['id']; ?>" data-name="<?php echo htmlspecialchars($circuit['name']); ?>" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteCircuitModal" tabindex="-1" aria-labelledby="deleteCircuitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCircuitModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le circuit <strong id="circuitNameToDelete"></strong> ?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Cette action est irréversible et supprimera toutes les données associées à ce circuit.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="confirmDeleteCircuit" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Validation du formulaire
    document.addEventListener('DOMContentLoaded', function() {
        // Validation des dates
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if(startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                endDateInput.min = startDateInput.value;
                if(endDateInput.value && endDateInput.value < startDateInput.value) {
                    endDateInput.value = startDateInput.value;
                }
            });
            
            // Initialiser la date minimale pour la date de fin
            if(startDateInput.value) {
                endDateInput.min = startDateInput.value;
            }
        }
        
        // Validation des prix
        const priceInputs = document.querySelectorAll('input[type="number"][name^="price_"]');
        priceInputs.forEach(input => {
            input.addEventListener('change', function() {
                if(parseFloat(this.value) < 0) {
                    this.value = 0;
                }
            });
        });
        
        // Modal de suppression
        const deleteButtons = document.querySelectorAll('.delete-circuit');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const circuitId = this.getAttribute('data-id');
                const circuitName = this.getAttribute('data-name');
                
                document.getElementById('circuitNameToDelete').textContent = circuitName;
                document.getElementById('confirmDeleteCircuit').href = 'index.php?page=circuits&action=delete&id=' + circuitId;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteCircuitModal'));
                deleteModal.show();
            });
        });
        
        // Filtrage des circuits
        const searchInput = document.getElementById('searchCircuit');
        const locationFilter = document.getElementById('locationFilter');
        const durationFilter = document.getElementById('durationFilter');
        const circuitsTable = document.getElementById('circuitsTable');
        
        if(searchInput && circuitsTable) {
            function filterCircuits() {
                const searchTerm = searchInput.value.toLowerCase();
                const locationValue = locationFilter.value;
                const durationValue = durationFilter.value;
                
                const rows = circuitsTable.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const circuitName = row.querySelector('td:first-child').textContent.toLowerCase();
                    const circuitDuration = parseInt(row.querySelector('td:nth-child(2)').textContent);
                    
                    let showRow = circuitName.includes(searchTerm);
                    
                    // Filtrage par durée
                    if(durationValue !== 'all') {
                        if(durationValue === '1-3' && (circuitDuration < 1 || circuitDuration > 3)) {
                            showRow = false;
                        } else if(durationValue === '4-7' && (circuitDuration < 4 || circuitDuration > 7)) {
                            showRow = false;
                        } else if(durationValue === '8+' && circuitDuration < 8) {
                            showRow = false;
                        }
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
            }
            
            searchInput.addEventListener('input', filterCircuits);
            if(locationFilter) locationFilter.addEventListener('change', filterCircuits);
            if(durationFilter) durationFilter.addEventListener('change', filterCircuits);
        }
    });
</script>