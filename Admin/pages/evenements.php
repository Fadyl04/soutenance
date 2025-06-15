<?php
include 'database/db.php';
// Protection contre l'accès direct
if (!defined('INCLUDED_FROM_INDEX')) {
    define('INCLUDED_FROM_INDEX', true);
}

// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier si la table events existe et la créer si nécessaire
function checkAndCreateEventsTable($db) {
    try {
        // Vérifier si la table existe
        $stmt = $db->query("SHOW TABLES LIKE 'events'");
        $tableExists = $stmt->rowCount() > 0;
        
        if (!$tableExists) {
            // Créer la table events
            $createTableSQL = "CREATE TABLE events (
                id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                picture_event VARCHAR(111) NOT NULL,
                label_event VARCHAR(255) NOT NULL,
                description_event TEXT NOT NULL,
                start_date DATETIME NOT NULL,
                end_date DATETIME NOT NULL,
                localisation VARCHAR(255) NOT NULL,
                amount_event DOUBLE NOT NULL,
                number_available_event DOUBLE NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
            
            $db->exec($createTableSQL);
            return "<div class='alert alert-info'>La table 'events' a été créée avec succès.</div>";
        }
        return "";
    } catch (PDOException $e) {
        return "<div class='alert alert-danger'>Erreur lors de la vérification/création de la table: " . $e->getMessage() . "</div>";
    }
}

// Vérifier et créer la table si nécessaire
echo checkAndCreateEventsTable($db);

// Traitement des actions
if (isset($_POST['ajouter_event'])) {
    try {
        // Récupération des données du formulaire
        $label_event = htmlspecialchars($_POST['label_event']);
        $description_event = htmlspecialchars($_POST['description_event']);
        $localisation = htmlspecialchars($_POST['localisation']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $amount_event = floatval($_POST['amount_event']);
        $number_available_event = floatval($_POST['number_available_event']);

        // Validation des données
        if ($amount_event < 0) {
            echo "<div class='alert alert-warning'>Le prix ne peut pas être négatif. Il a été défini à 0.</div>";
            $amount_event = 0;
        }

        if ($number_available_event < 0) {
            echo "<div class='alert alert-warning'>Le nombre de places ne peut pas être négatif. Il a été défini à 0.</div>";
            $number_available_event = 0;
        }

        // Traitement de l'image
        $picture_event = '';
        if (isset($_FILES['picture_event']) && $_FILES['picture_event']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['picture_event']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                
                // Chemin absolu pour éviter les problèmes de chemins relatifs
                $root_path = $_SERVER['DOCUMENT_ROOT'];
                $upload_dir = $root_path . '/Golden/uploads/events/';
                
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
                
                if (move_uploaded_file($_FILES['picture_event']['tmp_name'], $full_path)) {
                    $picture_event = $new_filename;
                } else {
                    $error = error_get_last();
                    echo "<div class='alert alert-warning'>Problème lors du téléchargement de l'image: " . ($error ? $error['message'] : 'Erreur inconnue') . "</div>";
                    echo "<div class='alert alert-info'>Tentative de déplacement de " . $_FILES['picture_event']['tmp_name'] . " vers " . $full_path . "</div>";
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
            - Label: $label_event<br>
            - Start date: $start_date<br>
            - End date: $end_date<br>
            - Localisation: $localisation<br>
            - Amount: $amount_event<br>
            - Places: $number_available_event<br>
            - Image: $picture_event
        </div>";

        // Insertion dans la base de données avec PDO
        $stmt = $db->prepare("INSERT INTO events (label_event, description_event, start_date, end_date, localisation, amount_event, number_available_event, picture_event) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $result = $stmt->execute([
            $label_event, 
            $description_event, 
            $start_date, 
            $end_date, 
            $localisation, 
            $amount_event, 
            $number_available_event, 
            $picture_event
        ]);
        
        if ($result) {
            echo "<div class='alert alert-success'>L'événement a été ajouté avec succès (ID: " . $db->lastInsertId() . ").</div>";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "<div class='alert alert-danger'>Erreur lors de l'ajout de l'événement: " . $errorInfo[2] . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Exception: " . $e->getMessage() . "</div>";
    }
}

// Traitement de la suppression
if (isset($_POST['delete_event']) && isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
    try {
        $id = $_POST['delete_id'];
        
        // Récupérer le nom de l'image avant de supprimer
        $query = $db->prepare("SELECT picture_event FROM events WHERE id = ?");
        $query->execute([$id]);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Supprimer l'image si elle existe
            if (!empty($row['picture_event'])) {
                $root_path = $_SERVER['DOCUMENT_ROOT'];
                $image_path = $root_path . '/Golden/uploads/events/' . $row['picture_event'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }
        
        // Supprimer l'enregistrement de la base de données
        $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            echo "<div class='alert alert-success'>L'événement a été supprimé avec succès.</div>";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "<div class='alert alert-danger'>Erreur lors de la suppression de l'événement: " . $errorInfo[2] . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Exception: " . $e->getMessage() . "</div>";
    }
}

// Traitement de la modification
if (isset($_POST['modifier_event']) && isset($_POST['id'])) {
    try {
        $id = $_POST['id'];
        $label_event = htmlspecialchars($_POST['label_event']);
        $description_event = htmlspecialchars($_POST['description_event']);
        $localisation = htmlspecialchars($_POST['localisation']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $amount_event = floatval($_POST['amount_event']);
        $number_available_event = floatval($_POST['number_available_event']);
        
        // Validation des données
        if ($amount_event < 0) {
            echo "<div class='alert alert-warning'>Le prix ne peut pas être négatif. Il a été défini à 0.</div>";
            $amount_event = 0;
        }

        if ($number_available_event < 0) {
            echo "<div class='alert alert-warning'>Le nombre de places ne peut pas être négatif. Il a été défini à 0.</div>";
            $number_available_event = 0;
        }
        
        // Vérifier si une nouvelle image est téléchargée
        if (isset($_FILES['picture_event']) && $_FILES['picture_event']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['picture_event']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                // Récupérer l'ancienne image pour la supprimer
                $query = $db->prepare("SELECT picture_event FROM events WHERE id = ?");
                $query->execute([$id]);
                $row = $query->fetch(PDO::FETCH_ASSOC);
                
                if ($row) {
                    if (!empty($row['picture_event'])) {
                        $root_path = $_SERVER['DOCUMENT_ROOT'];
                        $old_image_path = $root_path . '/Golden/uploads/events/' . $row['picture_event'];
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                }
                
                // Télécharger la nouvelle image
                $new_filename = uniqid() . '.' . $ext;
                $root_path = $_SERVER['DOCUMENT_ROOT'];
                $upload_dir = $root_path . '/Golden/uploads/events/';
                
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
                
                if (move_uploaded_file($_FILES['picture_event']['tmp_name'], $full_path)) {
                    // Mettre à jour avec la nouvelle image
                    $stmt = $db->prepare("UPDATE events SET label_event = ?, description_event = ?, start_date = ?, end_date = ?, localisation = ?, amount_event = ?, number_available_event = ?, picture_event = ? WHERE id = ?");
                    $result = $stmt->execute([
                        $label_event, 
                        $description_event, 
                        $start_date, 
                        $end_date, 
                        $localisation, 
                        $amount_event, 
                        $number_available_event, 
                        $new_filename, 
                        $id
                    ]);
                } else {
                    $error = error_get_last();
                    echo "<div class='alert alert-warning'>Problème lors du téléchargement de l'image: " . ($error ? $error['message'] : 'Erreur inconnue') . "</div>";
                    echo "<div class='alert alert-info'>Tentative de déplacement de " . $_FILES['picture_event']['tmp_name'] . " vers " . $full_path . "</div>";
                }
            }
        } else {
            // Mettre à jour sans changer l'image
            $stmt = $db->prepare("UPDATE events SET label_event = ?, description_event = ?, start_date = ?, end_date = ?, localisation = ?, amount_event = ?, number_available_event = ? WHERE id = ?");
            $result = $stmt->execute([
                $label_event, 
                $description_event, 
                $start_date, 
                $end_date, 
                $localisation, 
                $amount_event, 
                $number_available_event, 
                $id
            ]);
        }
        
        if (isset($result) && $result) {
            echo "<div class='alert alert-success'>L'événement a été mis à jour avec succès.</div>";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "<div class='alert alert-danger'>Erreur lors de la mise à jour de l'événement: " . $errorInfo[2] . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Exception: " . $e->getMessage() . "</div>";
    }
}

// Si on demande d'éditer un événement
$event_to_edit = null;
if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $query = $db->prepare("SELECT * FROM events WHERE id = ?");
    $query->execute([$edit_id]);
    $event_to_edit = $query->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">
                        <?php echo $event_to_edit ? 'Modifier un événement' : 'Ajouter un nouvel événement'; ?>
                    </h2>
                    
                    <form method="post" enctype="multipart/form-data">
                        <?php if ($event_to_edit): ?>
                            <input type="hidden" name="id" value="<?php echo $event_to_edit['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="label_event" class="form-label">Nom de l'événement</label>
                                    <input type="text" class="form-control" id="label_event" name="label_event" required 
                                           value="<?php echo $event_to_edit ? $event_to_edit['label_event'] : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="localisation" class="form-label">Localisation</label>
                                    <input type="text" class="form-control" id="localisation" name="localisation" required
                                           value="<?php echo $event_to_edit ? $event_to_edit['localisation'] : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Date de début</label>
                                    <input type="datetime-local" class="form-control" id="start_date" name="start_date" required
                                           value="<?php echo $event_to_edit ? date('Y-m-d\TH:i', strtotime($event_to_edit['start_date'])) : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Date de fin</label>
                                    <input type="datetime-local" class="form-control" id="end_date" name="end_date" required
                                           value="<?php echo $event_to_edit ? date('Y-m-d\TH:i', strtotime($event_to_edit['end_date'])) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount_event" class="form-label">Prix</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" class="form-control" id="amount_event" name="amount_event" required
                                               value="<?php echo $event_to_edit ? $event_to_edit['amount_event'] : '0.00'; ?>">
                                        <span class="input-group-text">€</span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="number_available_event" class="form-label">Nombre de places disponibles</label>
                                    <input type="number" min="0" class="form-control" id="number_available_event" name="number_available_event" required
                                           value="<?php echo $event_to_edit ? $event_to_edit['number_available_event'] : '0'; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description_event" class="form-label">Description</label>
                                    <textarea class="form-control" id="description_event" name="description_event" rows="5" required><?php echo $event_to_edit ? $event_to_edit['description_event'] : ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="picture_event" class="form-label">Image de l'événement</label>
                                    <?php if ($event_to_edit && !empty($event_to_edit['picture_event'])): ?>
                                        <div class="mb-2">
                                            <img src="<?php echo '/Golden/uploads/events/' . $event_to_edit['picture_event']; ?>" alt="<?php echo $event_to_edit['label_event']; ?>" class="img-thumbnail" style="max-height: 150px;">
                                            <p class="text-muted small">Téléchargez une nouvelle image pour remplacer l'actuelle</p>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="picture_event" name="picture_event" <?php echo $event_to_edit ? '' : 'required'; ?>>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="index.php?page=events" class="btn btn-secondary me-2">Annuler</a>
                            <button type="submit" name="<?php echo $event_to_edit ? 'modifier_event' : 'ajouter_event'; ?>" class="btn btn-primary">
                                <?php echo $event_to_edit ? 'Modifier' : 'Ajouter'; ?>
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
                    <h2 class="card-title mb-4">Liste des événements</h2>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Nom</th>
                                    <th>Dates</th>
                                    <th>Localisation</th>
                                    <th>Prix</th>
                                    <th>Places disponibles</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    // Récupération de tous les événements
                                    $query = $db->query("SELECT * FROM events ORDER BY start_date DESC");
                                    if (!$query) {
                                        $errorInfo = $db->errorInfo();
                                        echo "<tr><td colspan='8' class='text-center text-danger'>Erreur lors de la récupération des événements: " . $errorInfo[2] . "</td></tr>";
                                    } else {
                                        $events = $query->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if (count($events) > 0) {
                                            foreach ($events as $row) {
                                                $start_date = date('d/m/Y H:i', strtotime($row['start_date']));
                                                $end_date = date('d/m/Y H:i', strtotime($row['end_date']));
                                                ?>
                                                <tr>
                                                    <td><?php echo $row['id']; ?></td>
                                                    <td>
                                                        <?php if (!empty($row['picture_event'])): ?>
                                                            <img src="<?php echo '/Golden/uploads/events/' . $row['picture_event']; ?>" alt="<?php echo $row['label_event']; ?>" class="img-thumbnail" style="max-height: 50px;">
                                                        <?php else: ?>
                                                            <span class="text-muted">Aucune image</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo $row['label_event']; ?></td>
                                                    <td>
                                                        <strong>Début:</strong> <?php echo $start_date; ?><br>
                                                        <strong>Fin:</strong> <?php echo $end_date; ?>
                                                    </td>
                                                    <td><?php echo $row['localisation']; ?></td>
                                                    <td><?php echo number_format($row['amount_event'], 2, ',', ' '); ?> €</td>
                                                    <td><?php echo $row['number_available_event']; ?></td>
                                                    <td>
                                                        <a href="index.php?page=events&edit_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </a>
                                                        <form method="post" style="display: inline;">
                                                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                                            <button type="submit" name="delete_event" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement?');">
                                                                <i class="fas fa-trash"></i> Supprimer
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='8' class='text-center'>Aucun événement trouvé.</td></tr>";
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
            .create(document.querySelector('#description_event'))
            .catch(error => {
                console.error(error);
            });
    }
});
</script>