<?php
include 'database/db.php';
// Protection contre l'accès direct
if (!defined('INCLUDED_FROM_INDEX')) {
    define('INCLUDED_FROM_INDEX', true);
}

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier si la table sites_touristiques existe et la créer si nécessaire
function checkAndCreateSitesTouristiquesTable($db) {
    try {
        // Vérifier si la table existe
        $stmt = $db->query("SHOW TABLES LIKE 'sites_touristiques'");
        $tableExists = $stmt->rowCount() > 0;
        
        if (!$tableExists) {
            // Créer la table sites_touristiques avec la même structure
            $createTableSQL = "CREATE TABLE sites_touristiques (
                id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                localisation VARCHAR(255) NOT NULL,
                horaires VARCHAR(255) NULL DEFAULT NULL,
                categorie VARCHAR(50) NOT NULL,
                image VARCHAR(255) NULL DEFAULT NULL,
                date_ajout TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
            
            $db->exec($createTableSQL);
            return "<div class='alert alert-info'>La table 'sites_touristiques' a été créée avec succès.</div>";
        }
        return "";
    } catch (PDOException $e) {
        return "<div class='alert alert-danger'>Erreur lors de la vérification/création de la table: " . $e->getMessage() . "</div>";
    }
}

// Vérifier et créer la table si nécessaire
echo checkAndCreateSitesTouristiquesTable($db);

// Traitement des actions
if (isset($_POST['ajouter_site'])) {
    try {
        // Récupération des données du formulaire
        $nom = htmlspecialchars($_POST['nom']);
        $description = htmlspecialchars($_POST['description']);
        $localisation = htmlspecialchars($_POST['localisation']);
        $horaires = htmlspecialchars($_POST['horaires']);
        $categorie = htmlspecialchars($_POST['categorie']);

        // Traitement de l'image
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                
                // Chemin absolu pour éviter les problèmes de chemins relatifs
                $root_path = $_SERVER['DOCUMENT_ROOT'];
                $upload_dir = $root_path . '/Golden/uploads/sites_touristiques/';
                
                // Vérifiez si le répertoire existe, sinon essayez de le créer
                if (!is_dir($upload_dir)) {
                    // Créer des répertoires récursivement avec permissions 0755
                    if (!mkdir($upload_dir, 0755, true)) {
                        echo "<div class='alert alert-warning'>Impossible de créer le répertoire de téléchargement. Vérifiez les permissions: " . $upload_dir . "</div>";
                    }
                }
                
                // Vérifiez les permissions du répertoire
                if (!is_writable($upload_dir)) {
                    echo "<div class='alert alert-warning'>Le répertoire de téléchargement n'est pas accessible en écriture: " . $upload_dir . "</div>";
                }
                
                // Chemin complet du fichier
                $full_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $full_path)) {
                    $image = $new_filename;
                } else {
                    $error = error_get_last();
                    echo "<div class='alert alert-warning'>Problème lors du téléchargement de l'image: " . ($error ? $error['message'] : 'Erreur inconnue') . "</div>";
                    echo "<div class='alert alert-info'>Tentative de déplacement de " . $_FILES['image']['tmp_name'] . " vers " . $full_path . "</div>";
                }
            }
        }

        // Vérifier la connexion à la base de données
        if (!$db) {
            throw new Exception("La connexion à la base de données n'est pas établie");
        }

        // Débogage - afficher les valeurs avant insertion
        echo "<div class='alert alert-info'>
            Tentative d'insertion avec les valeurs suivantes:<br>
            - Nom: $nom<br>
            - Localisation: $localisation<br>
            - Horaires: $horaires<br>
            - Catégorie: $categorie<br>
            - Image: $image
        </div>";

        // Insertion dans la base de données avec PDO
        $stmt = $db->prepare("INSERT INTO sites_touristiques (nom, description, localisation, horaires, categorie, image) VALUES (?, ?, ?, ?, ?, ?)");
        
        $result = $stmt->execute([
            $nom, 
            $description, 
            $localisation, 
            $horaires, 
            $categorie, 
            $image
        ]);
        
        if ($result) {
            echo "<div class='alert alert-success'>Le site touristique a été ajouté avec succès (ID: " . $db->lastInsertId() . ").</div>";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "<div class='alert alert-danger'>Erreur lors de l'ajout du site touristique: " . $errorInfo[2] . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Exception: " . $e->getMessage() . "</div>";
    }
}

// Traitement de la suppression
if (isset($_POST['delete_site']) && isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
    try {
        $id = $_POST['delete_id'];
        
        // Récupérer le nom de l'image avant de supprimer
        $query = $db->prepare("SELECT image FROM sites_touristiques WHERE id = ?");
        $query->execute([$id]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Supprimer l'image si elle existe
            if (!empty($row['image'])) {
                $root_path = $_SERVER['DOCUMENT_ROOT'];
                $image_path = $root_path . '/Golden/uploads/sites_touristiques/' . $row['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        
        // Supprimer l'enregistrement de la base de données
        $stmt = $db->prepare("DELETE FROM sites_touristiques WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            echo "<div class='alert alert-success'>Le site touristique a été supprimé avec succès.</div>";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "<div class='alert alert-danger'>Erreur lors de la suppression du site touristique: " . $errorInfo[2] . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Exception: " . $e->getMessage() . "</div>";
    }
}

// Traitement de la modification
if (isset($_POST['modifier_site']) && isset($_POST['id'])) {
    try {
        $id = $_POST['id'];
        $nom = htmlspecialchars($_POST['nom']);
        $description = htmlspecialchars($_POST['description']);
        $localisation = htmlspecialchars($_POST['localisation']);
        $horaires = htmlspecialchars($_POST['horaires']);
        $categorie = htmlspecialchars($_POST['categorie']);
        
        // Vérifier si une nouvelle image est téléchargée
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                // Récupérer l'ancienne image pour la supprimer
                $query = $db->prepare("SELECT image FROM sites_touristiques WHERE id = ?");
                $query->execute([$id]);
                $row = $query->fetch(PDO::FETCH_ASSOC);
                
                if ($row) {
                    if (!empty($row['image'])) {
                        $root_path = $_SERVER['DOCUMENT_ROOT'];
                        $old_image_path = $root_path . '/Golden/uploads/sites_touristiques/' . $row['image'];
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                }
                
                // Télécharger la nouvelle image
                $new_filename = uniqid() . '.' . $ext;
                $root_path = $_SERVER['DOCUMENT_ROOT'];
                $upload_dir = $root_path . '/Golden/uploads/sites_touristiques/';
                
                // Vérifiez si le répertoire existe, sinon essayez de le créer
                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0755, true)) {
                        echo "<div class='alert alert-warning'>Impossible de créer le répertoire de téléchargement. Vérifiez les permissions: " . $upload_dir . "</div>";
                    }
                }
                
                // Vérifiez les permissions du répertoire
                if (!is_writable($upload_dir)) {
                    echo "<div class='alert alert-warning'>Le répertoire de téléchargement n'est pas accessible en écriture: " . $upload_dir . "</div>";
                }
                
                $full_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $full_path)) {
                    // Mettre à jour avec la nouvelle image
                    $stmt = $db->prepare("UPDATE sites_touristiques SET nom = ?, description = ?, localisation = ?, horaires = ?, categorie = ?, image = ? WHERE id = ?");
                    $result = $stmt->execute([
                        $nom, 
                        $description, 
                        $localisation, 
                        $horaires, 
                        $categorie, 
                        $new_filename, 
                        $id
                    ]);
                } else {
                    $error = error_get_last();
                    echo "<div class='alert alert-warning'>Problème lors du téléchargement de l'image: " . ($error ? $error['message'] : 'Erreur inconnue') . "</div>";
                    echo "<div class='alert alert-info'>Tentative de déplacement de " . $_FILES['image']['tmp_name'] . " vers " . $full_path . "</div>";
                }
            }
        } else {
            // Mettre à jour sans changer l'image
            $stmt = $db->prepare("UPDATE sites_touristiques SET nom = ?, description = ?, localisation = ?, horaires = ?, categorie = ? WHERE id = ?");
            $result = $stmt->execute([
                $nom, 
                $description, 
                $localisation, 
                $horaires, 
                $categorie, 
                $id
            ]);
        }
        
        if (isset($result) && $result) {
            echo "<div class='alert alert-success'>Le site touristique a été mis à jour avec succès.</div>";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "<div class='alert alert-danger'>Erreur lors de la mise à jour du site touristique: " . $errorInfo[2] . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Exception: " . $e->getMessage() . "</div>";
    }
}

// Si on demande d'éditer un site touristique
$site_to_edit = null;
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $query = $db->prepare("SELECT * FROM sites_touristiques WHERE id = ?");
    $query->execute([$edit_id]);
    $site_to_edit = $query->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">
                        <?php echo $site_to_edit ? 'Modifier un site touristique' : 'Ajouter un nouveau site touristique'; ?>
                    </h2>
                    
                    <form method="post" enctype="multipart/form-data">
                        <?php if ($site_to_edit): ?>
                            <input type="hidden" name="id" value="<?php echo $site_to_edit['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom du site touristique</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required 
                                           value="<?php echo $site_to_edit ? $site_to_edit['nom'] : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="localisation" class="form-label">Localisation</label>
                                    <input type="text" class="form-control" id="localisation" name="localisation" required
                                           value="<?php echo $site_to_edit ? $site_to_edit['localisation'] : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="horaires" class="form-label">Horaires</label>
                                    <input type="text" class="form-control" id="horaires" name="horaires"
                                           value="<?php echo $site_to_edit ? $site_to_edit['horaires'] : ''; ?>">
                                    <small class="form-text text-muted">Format suggéré: Lun-Ven 9h-18h / Sam-Dim 10h-17h</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categorie" class="form-label">Catégorie</label>
                                    <select class="form-select" id="categorie" name="categorie" required>
                                        <option value="">Choisir une catégorie</option>
                                        <option value="Monument" <?php echo ($site_to_edit && $site_to_edit['categorie'] == 'Monument') ? 'selected' : ''; ?>>Monument</option>
                                        <option value="Musée" <?php echo ($site_to_edit && $site_to_edit['categorie'] == 'Musée') ? 'selected' : ''; ?>>Musée</option>
                                        <option value="Parc" <?php echo ($site_to_edit && $site_to_edit['categorie'] == 'Parc') ? 'selected' : ''; ?>>Parc</option>
                                        <option value="Plage" <?php echo ($site_to_edit && $site_to_edit['categorie'] == 'Plage') ? 'selected' : ''; ?>>Plage</option>
                                        <option value="Site historique" <?php echo ($site_to_edit && $site_to_edit['categorie'] == 'Site historique') ? 'selected' : ''; ?>>Site historique</option>
                                        <option value="Site naturel" <?php echo ($site_to_edit && $site_to_edit['categorie'] == 'Site naturel') ? 'selected' : ''; ?>>Site naturel</option>
                                        <option value="Autre" <?php echo ($site_to_edit && $site_to_edit['categorie'] == 'Autre') ? 'selected' : ''; ?>>Autre</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $site_to_edit ? $site_to_edit['description'] : ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image du site touristique</label>
                                    <?php if ($site_to_edit && !empty($site_to_edit['image'])): ?>
                                        <div class="mb-2">
                                            <img src="<?php echo '/Golden/uploads/sites_touristiques/' . $site_to_edit['image']; ?>" alt="<?php echo $site_to_edit['nom']; ?>" class="img-thumbnail" style="max-height: 150px;">
                                            <p class="text-muted small">Téléchargez une nouvelle image pour remplacer l'actuelle</p>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="image" name="image" <?php echo $site_to_edit && !empty($site_to_edit['image']) ? '' : 'required'; ?>>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="index.php?page=sites_touristiques" class="btn btn-secondary me-2">Annuler</a>
                            <button type="submit" name="<?php echo $site_to_edit ? 'modifier_site' : 'ajouter_site'; ?>" class="btn btn-primary">
                                <?php echo $site_to_edit ? 'Modifier' : 'Ajouter'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Liste des sites touristiques</h2>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Localisation</th>
                                    <th>Horaires</th>
                                    <th>Date d'ajout</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    // Récupération de tous les sites touristiques
                                    $query = $db->query("SELECT * FROM sites_touristiques ORDER BY date_ajout DESC");
                                    if (!$query) {
                                        $errorInfo = $db->errorInfo();
                                        echo "<tr><td colspan='8' class='text-center text-danger'>Erreur lors de la récupération des sites touristiques: " . $errorInfo[2] . "</td></tr>";
                                    } else {
                                        $sites = $query->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if (count($sites) > 0) {
                                            foreach ($sites as $row) {
                                                $date_ajout = date('d/m/Y H:i', strtotime($row['date_ajout']));
                                                ?>
                                                <tr>
                                                    <td><?php echo $row['id']; ?></td>
                                                    <td>
                                                        <?php if (!empty($row['image'])): ?>
                                                            <img src="<?php echo '/Golden/uploads/sites_touristiques/' . $row['image']; ?>" alt="<?php echo $row['nom']; ?>" class="img-thumbnail" style="max-height: 50px;">
                                                        <?php else: ?>
                                                            <span class="text-muted">Aucune image</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo $row['nom']; ?></td>
                                                    <td><span class="badge bg-primary"><?php echo $row['categorie']; ?></span></td>
                                                    <td><?php echo $row['localisation']; ?></td>
                                                    <td><?php echo $row['horaires'] ? $row['horaires'] : '<span class="text-muted">Non spécifié</span>'; ?></td>
                                                    <td><?php echo $date_ajout; ?></td>
                                                    <td>
                                                        <a href="index.php?page=sites_touristiques&edit_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </a>
                                                        <form method="post" style="display: inline;">
                                                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                                            <button type="submit" name="delete_site" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce site touristique?');">
                                                                <i class="fas fa-trash"></i> Supprimer
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='8' class='text-center'>Aucun site touristique trouvé.</td></tr>";
                                        }
                                    }
                                } catch (Exception $e) {
                                    echo "<tr><td colspan='8' class='text-center text-danger'>Exception: " . $e->getMessage() . "</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Intégration de l'éditeur WYSIWYG pour la description si disponible
    if (typeof ClassicEditor !== 'undefined') {
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    }
});
</script>