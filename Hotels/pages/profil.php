<?php


include 'database/db.php';

// Rest of your code...
// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['prestataire'])) {
    header("Location:../connexion.php");
    exit;
}
// Récupérer les données de l'hôtel depuis la base de données
$hotel_id = $_SESSION['prestataire_id'];
$query = "SELECT * FROM hotels WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $hotel_id, PDO::PARAM_INT);
$stmt->execute();
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $hotel_name = htmlspecialchars($_POST['hotel_name']);
    $creation_year = htmlspecialchars($_POST['creation_year']);
    $address = htmlspecialchars($_POST['address']);
    $city = htmlspecialchars($_POST['city']);
    $region = htmlspecialchars($_POST['region']);
    $contact_name = htmlspecialchars($_POST['contact_name']);
    $contact_phone = htmlspecialchars($_POST['contact_phone']);
    $contact_email = htmlspecialchars($_POST['contact_email']);
    $website = htmlspecialchars($_POST['website']);
    $hotel_category = htmlspecialchars($_POST['hotel_category']);
    $room_count = htmlspecialchars($_POST['room_count']);
    $price_range = htmlspecialchars($_POST['price_range']);
    $description = htmlspecialchars($_POST['description']);
    
    // Gestion des checkboxes
    $has_wifi = isset($_POST['has_wifi']) ? 1 : 0;
    $has_parking = isset($_POST['has_parking']) ? 1 : 0;
    $has_pool = isset($_POST['has_pool']) ? 1 : 0;
    $has_restaurant = isset($_POST['has_restaurant']) ? 1 : 0;
    $has_ac = isset($_POST['has_ac']) ? 1 : 0;
    $has_conference = isset($_POST['has_conference']) ? 1 : 0;
    
    // Gestion des horaires
    $check_in_time = $_POST['check_in_time'];
    $check_out_time = $_POST['check_out_time'];
    
    // Traitement des photos
    $photo_files = $hotel['photo_files']; // Conserver les anciennes photos par défaut
    
    if(isset($_FILES['hotel_photos']) && !empty($_FILES['hotel_photos']['name'][0])) {
        $targetDir = "uploads/hotels/";
        if(!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $photoArray = [];
        if(!empty($hotel['photo_files'])) {
            $photoArray = json_decode($hotel['photo_files'], true);
        }
        
        $fileCount = count($_FILES['hotel_photos']['name']);
        
        for($i = 0; $i < $fileCount; $i++) {
            if($_FILES['hotel_photos']['error'][$i] === 0) {
                $fileName = time() . '_' . basename($_FILES['hotel_photos']['name'][$i]);
                $targetPath = $targetDir . $fileName;
                
                if(move_uploaded_file($_FILES['hotel_photos']['tmp_name'][$i], $targetPath)) {
                    $photoArray[] = $targetPath;
                }
            }
        }
        
        $photo_files = json_encode($photoArray);
    }
    
    // Mise à jour dans la base de données
    $updateQuery = "UPDATE hotels SET 
                    hotel_name = ?, 
                    creation_year = ?, 
                    address = ?, 
                    city = ?, 
                    region = ?, 
                    contact_name = ?, 
                    contact_phone = ?, 
                    contact_email = ?, 
                    website = ?, 
                    hotel_category = ?, 
                    room_count = ?, 
                    has_wifi = ?, 
                    has_parking = ?, 
                    has_pool = ?, 
                    has_restaurant = ?, 
                    has_ac = ?, 
                    has_conference = ?, 
                    price_range = ?, 
                    check_in_time = ?, 
                    check_out_time = ?, 
                    description = ?, 
                    photo_files = ? 
                    WHERE id = ?";
                    
    $stmt = $db->prepare($updateQuery);
    $stmt->execute([
        $hotel_name, 
        $creation_year, 
        $address, 
        $city, 
        $region, 
        $contact_name, 
        $contact_phone, 
        $contact_email, 
        $website, 
        $hotel_category, 
        $room_count, 
        $has_wifi, 
        $has_parking, 
        $has_pool, 
        $has_restaurant, 
        $has_ac, 
        $has_conference, 
        $price_range, 
        $check_in_time, 
        $check_out_time, 
        $description, 
        $photo_files, 
        $hotel_id
    ]);
    
    // Rediriger pour éviter la soumission du formulaire à nouveau
    echo "<script>
        alert('Profil mis à jour avec succès !');
        window.location.href = 'index.php?page=profil';
    </script>";
    exit;
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-hotel me-2"></i>Profil de l'hôtel</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <?php 
                                    // Afficher la première photo ou une image par défaut
                                    $hotel_photos = !empty($hotel['photo_files']) ? json_decode($hotel['photo_files'], true) : [];
                                    $main_photo = !empty($hotel_photos) ? $hotel_photos[0] : 'img/hotel-default.jpg';
                                    ?>
                                    <img src="<?= $main_photo ?>" alt="Photo de l'hôtel" class="img-fluid rounded" style="max-height: 250px;">
                                    <div class="mt-3">
                                        <label for="hotel_photos" class="btn btn-outline-primary">
                                            <i class="fas fa-upload me-2"></i>Ajouter des photos
                                        </label>
                                        <input type="file" id="hotel_photos" name="hotel_photos[]" class="d-none" multiple accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="hotel_name" class="form-label">Nom de l'hôtel</label>
                                        <input type="text" class="form-control" id="hotel_name" name="hotel_name" value="<?= htmlspecialchars($hotel['hotel_name']) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="creation_year" class="form-label">Année de création</label>
                                        <input type="number" class="form-control" id="creation_year" name="creation_year" value="<?= htmlspecialchars($hotel['creation_year']) ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="address" class="form-label">Adresse</label>
                                        <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($hotel['address']) ?>" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="city" class="form-label">Ville</label>
                                        <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($hotel['city']) ?>" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="region" class="form-label">Région</label>
                                        <input type="text" class="form-control" id="region" name="region" value="<?= htmlspecialchars($hotel['region']) ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="contact_name" class="form-label">Nom du contact</label>
                                        <input type="text" class="form-control" id="contact_name" name="contact_name" value="<?= htmlspecialchars($hotel['contact_name']) ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="contact_phone" class="form-label">Téléphone</label>
                                        <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?= htmlspecialchars($hotel['contact_phone']) ?>" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="contact_email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?= htmlspecialchars($hotel['contact_email']) ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Détails de l'hôtel</h5>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="website" class="form-label">Site web</label>
                                <input type="url" class="form-control" id="website" name="website" value="<?= htmlspecialchars($hotel['website']) ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="hotel_category" class="form-label">Catégorie</label>
                                <select class="form-control" id="hotel_category" name="hotel_category">
                                    <option value="">Sélectionner une catégorie</option>
                                    <option value="1 étoile" <?= $hotel['hotel_category'] == '1 étoile' ? 'selected' : '' ?>>1 étoile</option>
                                    <option value="2 étoiles" <?= $hotel['hotel_category'] == '2 étoiles' ? 'selected' : '' ?>>2 étoiles</option>
                                    <option value="3 étoiles" <?= $hotel['hotel_category'] == '3 étoiles' ? 'selected' : '' ?>>3 étoiles</option>
                                    <option value="4 étoiles" <?= $hotel['hotel_category'] == '4 étoiles' ? 'selected' : '' ?>>4 étoiles</option>
                                    <option value="5 étoiles" <?= $hotel['hotel_category'] == '5 étoiles' ? 'selected' : '' ?>>5 étoiles</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="room_count" class="form-label">Nombre de chambres</label>
                                <input type="number" class="form-control" id="room_count" name="room_count" value="<?= htmlspecialchars($hotel['room_count']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="price_range" class="form-label">Fourchette de prix</label>
                                <select class="form-control" id="price_range" name="price_range">
                                    <option value="économique" <?= $hotel['price_range'] == 'économique' ? 'selected' : '' ?>>Économique</option>
                                    <option value="moyen" <?= $hotel['price_range'] == 'moyen' ? 'selected' : '' ?>>Moyen</option>
                                    <option value="premium" <?= $hotel['price_range'] == 'premium' ? 'selected' : '' ?>>Premium</option>
                                    <option value="luxe" <?= $hotel['price_range'] == 'luxe' ? 'selected' : '' ?>>Luxe</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="check_in_time" class="form-label">Heure d'arrivée</label>
                                <input type="time" class="form-control" id="check_in_time" name="check_in_time" value="<?= $hotel['check_in_time'] ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="check_out_time" class="form-label">Heure de départ</label>
                                <input type="time" class="form-control" id="check_out_time" name="check_out_time" value="<?= $hotel['check_out_time'] ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Services disponibles</h5>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="has_wifi" name="has_wifi" <?= $hotel['has_wifi'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="has_wifi">
                                        <i class="fas fa-wifi me-1"></i> Wi-Fi
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="has_parking" name="has_parking" <?= $hotel['has_parking'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="has_parking">
                                        <i class="fas fa-parking me-1"></i> Parking
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="has_pool" name="has_pool" <?= $hotel['has_pool'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="has_pool">
                                        <i class="fas fa-swimming-pool me-1"></i> Piscine
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="has_restaurant" name="has_restaurant" <?= $hotel['has_restaurant'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="has_restaurant">
                                        <i class="fas fa-utensils me-1"></i> Restaurant
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="has_ac" name="has_ac" <?= $hotel['has_ac'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="has_ac">
                                        <i class="fas fa-wind me-1"></i> Climatisation
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="has_conference" name="has_conference" <?= $hotel['has_conference'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="has_conference">
                                        <i class="fas fa-users me-1"></i> Conférence
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($hotel['description']) ?></textarea>
                            </div>
                        </div>
                        
                        <?php if(!empty($hotel_photos) && count($hotel_photos) > 0): ?>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Photos actuelles</h5>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <?php foreach($hotel_photos as $index => $photo): ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card">
                                            <img src="<?= $photo ?>" alt="Photo de l'hôtel" class="card-img-top" style="height: 150px; object-fit: cover;">
                                            <div class="card-body p-2 text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-photo" data-index="<?= $index ?>">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" name="update_profile" class="btn btn-primary px-5">
                                    <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Prévisualisation des photos avant upload
document.getElementById('hotel_photos').addEventListener('change', function(event) {
    const files = event.target.files;
    
    if (files.length > 0) {
        alert(`${files.length} photo(s) sélectionnée(s). Enregistrez les modifications pour les télécharger.`);
    }
});

// Gestion de la suppression des photos (à compléter avec AJAX ou un traitement supplémentaire)
document.querySelectorAll('.remove-photo').forEach(button => {
    button.addEventListener('click', function() {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette photo ?')) {
            // Implémentation à faire : AJAX pour supprimer la photo
            const photoIndex = this.getAttribute('data-index');
            // Exemple : supprimer visuellement
            this.closest('.col-md-3').remove();
        }
    });
});
</script>