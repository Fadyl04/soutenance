<?php
include 'database/db.php';

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['prestataire'])) {
    header("Location:../connexion.php");
    exit;
}
// Récupérer les données du guide depuis la base de données
$guide_id = $_SESSION['prestataire_id'];
$query = "SELECT * FROM guides WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $guide_id, PDO::PARAM_INT);
$stmt->execute();
$guide = $stmt->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = htmlspecialchars($_POST['first_name']);
    $last_name = htmlspecialchars($_POST['last_name']);
    $birth_date = htmlspecialchars($_POST['birth_date']);
    $gender = htmlspecialchars($_POST['gender']);
    $address = htmlspecialchars($_POST['address']);
    $city = htmlspecialchars($_POST['city']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $id_number = htmlspecialchars($_POST['id_number']);
    $license_number = htmlspecialchars($_POST['license_number']);
    $experience_years = htmlspecialchars($_POST['experience_years']);
    $education = htmlspecialchars($_POST['education']);
    $languages = htmlspecialchars($_POST['languages']);
    $specializations = htmlspecialchars($_POST['specializations']);
    $regions = htmlspecialchars($_POST['regions']);
    $daily_rate = htmlspecialchars($_POST['daily_rate']);
    $biography = htmlspecialchars($_POST['biography']);
    
    // Traitement de la photo de profil
    $profile_photo = $guide['profile_photo']; // Conserver l'ancienne photo par défaut
    
    if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === 0) {
        $targetDir = "uploads/guides/";
        if(!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['profile_photo']['name']);
        $targetPath = $targetDir . $fileName;
        
        if(move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetPath)) {
            $profile_photo = $targetPath;
        }
    }
    
    // Traitement des documents
    $documents = $guide['documents']; // Conserver les anciens documents par défaut
    
    if(isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
        $targetDir = "uploads/guides/documents/";
        if(!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $docArray = [];
        if(!empty($guide['documents'])) {
            $docArray = json_decode($guide['documents'], true);
        }
        
        $fileCount = count($_FILES['documents']['name']);
        
        for($i = 0; $i < $fileCount; $i++) {
            if($_FILES['documents']['error'][$i] === 0) {
                $fileName = time() . '_' . basename($_FILES['documents']['name'][$i]);
                $targetPath = $targetDir . $fileName;
                
                if(move_uploaded_file($_FILES['documents']['tmp_name'][$i], $targetPath)) {
                    $docArray[] = [
                        'path' => $targetPath,
                        'name' => $_FILES['documents']['name'][$i]
                    ];
                }
            }
        }
        
        $documents = json_encode($docArray);
    }
    
    // Mise à jour dans la base de données
    $updateQuery = "UPDATE guides SET 
                    first_name = ?, 
                    last_name = ?, 
                    birth_date = ?, 
                    gender = ?, 
                    address = ?, 
                    city = ?, 
                    phone = ?, 
                    email = ?, 
                    id_number = ?, 
                    license_number = ?, 
                    experience_years = ?, 
                    education = ?, 
                    languages = ?, 
                    specializations = ?, 
                    regions = ?, 
                    daily_rate = ?, 
                    biography = ?, 
                    profile_photo = ? 
                    WHERE id = ?";
                    
    $stmt = $db->prepare($updateQuery);
    $stmt->execute([
        $first_name, 
        $last_name, 
        $birth_date, 
        $gender, 
        $address, 
        $city, 
        $phone, 
        $email, 
        $id_number, 
        $license_number, 
        $experience_years, 
        $education, 
        $languages, 
        $specializations, 
        $regions, 
        $daily_rate, 
        $biography, 
        $profile_photo, 
        $guide_id
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
                    <h4><i class="fas fa-user-tie me-2"></i>Profil du guide</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <?php 
                                    // Afficher la photo de profil ou une image par défaut
                                    $profile_img = !empty($guide['profile_photo']) ? $guide['profile_photo'] : 'img/profile-default.jpg';
                                    ?>
                                    <img src="<?= $profile_img ?>" alt="Photo de profil" class="img-fluid rounded-circle" style="width: 180px; height: 180px; object-fit: cover;">
                                    <div class="mt-3">
                                        <label for="profile_photo" class="btn btn-outline-primary">
                                            <i class="fas fa-camera me-2"></i>Changer la photo
                                        </label>
                                        <input type="file" id="profile_photo" name="profile_photo" class="d-none" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">Prénom</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($guide['first_name']) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Nom</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($guide['last_name']) ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="birth_date" class="form-label">Date de naissance</label>
                                        <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?= $guide['birth_date'] ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="gender" class="form-label">Genre</label>
                                        <select class="form-control" id="gender" name="gender" required>
                                            <option value="homme" <?= $guide['gender'] == 'homme' ? 'selected' : '' ?>>Homme</option>
                                            <option value="femme" <?= $guide['gender'] == 'femme' ? 'selected' : '' ?>>Femme</option>
                                            <option value="autre" <?= $guide['gender'] == 'autre' ? 'selected' : '' ?>>Autre</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Téléphone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($guide['phone']) ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($guide['email']) ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Informations personnelles</h5>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="address" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($guide['address']) ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?= htmlspecialchars($guide['city']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Informations professionnelles</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="id_number" class="form-label">Numéro d'identité</label>
                                <input type="text" class="form-control" id="id_number" name="id_number" value="<?= htmlspecialchars($guide['id_number']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="license_number" class="form-label">Numéro de licence</label>
                                <input type="text" class="form-control" id="license_number" name="license_number" value="<?= htmlspecialchars($guide['license_number']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="experience_years" class="form-label">Années d'expérience</label>
                                <input type="number" class="form-control" id="experience_years" name="experience_years" value="<?= htmlspecialchars($guide['experience_years']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="daily_rate" class="form-label">Tarif journalier (€)</label>
                                <input type="number" step="0.01" class="form-control" id="daily_rate" name="daily_rate" value="<?= htmlspecialchars($guide['daily_rate']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="education" class="form-label">Formation</label>
                                <input type="text" class="form-control" id="education" name="education" value="<?= htmlspecialchars($guide['education']) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="languages" class="form-label">Langues parlées</label>
                                <input type="text" class="form-control" id="languages" name="languages" value="<?= htmlspecialchars($guide['languages']) ?>" placeholder="Ex: Français, Anglais, Espagnol">
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="specializations" class="form-label">Spécialisations</label>
                                <textarea class="form-control" id="specializations" name="specializations" rows="3" placeholder="Ex: Histoire, Architecture, Gastronomie"><?= htmlspecialchars($guide['specializations']) ?></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="regions" class="form-label">Régions couvertes</label>
                                <textarea class="form-control" id="regions" name="regions" rows="3" placeholder="Ex: Paris, Normandie, Provence"><?= htmlspecialchars($guide['regions']) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12 mb-3">
                                <label for="biography" class="form-label">Biographie</label>
                                <textarea class="form-control" id="biography" name="biography" rows="5"><?= htmlspecialchars($guide['biography']) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Documents</h5>
                                <div class="mb-3">
                                    <label for="documents" class="form-label">Ajouter des documents (CV, certificats, diplômes...)</label>
                                    <input type="file" class="form-control" id="documents" name="documents[]" multiple>
                                </div>
                            </div>
                        </div>
                        
                        <?php if(!empty($guide['documents'])): ?>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h6>Documents actuels</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nom du document</th>
                                                <th width="120">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $documents = json_decode($guide['documents'], true);
                                            if(is_array($documents) && count($documents) > 0):
                                                foreach($documents as $index => $doc):
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($doc['name']) ?></td>
                                                <td>
                                                    <a href="<?= $doc['path'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger remove-doc" data-index="<?= $index ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php 
                                                endforeach;
                                            else:
                                            ?>
                                            <tr>
                                                <td colspan="2" class="text-center">Aucun document téléchargé</td>
                                            </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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
// Prévisualisation de la photo de profil avant upload
document.getElementById('profile_photo').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.querySelector('img.rounded-circle');
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Prévisualisation des documents avant upload
document.getElementById('documents').addEventListener('change', function(event) {
    const files = event.target.files;
    
    if (files.length > 0) {
        alert(`${files.length} document(s) sélectionné(s). Enregistrez les modifications pour les télécharger.`);
    }
});

// Gestion de la suppression des documents (à compléter avec AJAX ou un traitement supplémentaire)
document.querySelectorAll('.remove-doc').forEach(button => {
    button.addEventListener('click', function() {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
            // Implémentation à faire : AJAX pour supprimer le document
            const docIndex = this.getAttribute('data-index');
            // Exemple : supprimer visuellement
            this.closest('tr').remove();
        }
    });
});
</script>