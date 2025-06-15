<?php
include 'database/db.php';

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['prestataire'])) {
    header("Location:../connexion.php");
    exit;
}

// Récupérer les données de l'utilisateur depuis la base de données
$prestataire_id = $_SESSION['prestataire_id'];
$query = "SELECT username FROM hotels WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $prestataire_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Variables pour stocker les messages
$success_message = '';
$error_message = '';

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_credentials'])) {
    $username = htmlspecialchars($_POST['username']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Vérifier que le nom d'utilisateur n'est pas déjà utilisé (sauf par l'utilisateur actuel)
    $check_query = "SELECT id FROM users WHERE username = ? AND id != ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$username, $prestataire_id]);
    
    if ($check_stmt->rowCount() > 0) {
        $error_message = "Ce nom d'utilisateur est déjà utilisé. Veuillez en choisir un autre.";
    } else {
        // Vérifier le mot de passe actuel
        $verify_query = "SELECT password FROM users WHERE id = ?";
        $verify_stmt = $db->prepare($verify_query);
        $verify_stmt->execute([$prestataire_id]);
        $current_hash = $verify_stmt->fetchColumn();
        
        if (password_verify($current_password, $current_hash)) {
            // Modification du nom d'utilisateur uniquement
            if (empty($new_password)) {
                $update_query = "UPDATE users SET username = ? WHERE id = ?";
                $update_stmt = $db->prepare($update_query);
                $result = $update_stmt->execute([$username, $prestataire_id]);
                
                if ($result) {
                    $success_message = "Nom d'utilisateur mis à jour avec succès!";
                    $_SESSION['prestataire'] = $username; // Mettre à jour la session
                } else {
                    $error_message = "Une erreur est survenue lors de la mise à jour du nom d'utilisateur.";
                }
            } 
            // Modification du nom d'utilisateur et du mot de passe
            else {
                // Vérifier que les deux mots de passe correspondent
                if ($new_password === $confirm_password) {
                    // Hacher le nouveau mot de passe
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    
                    $update_query = "UPDATE users SET username = ?, password = ? WHERE id = ?";
                    $update_stmt = $db->prepare($update_query);
                    $result = $update_stmt->execute([$username, $password_hash, $prestataire_id]);
                    
                    if ($result) {
                        $success_message = "Identifiants mis à jour avec succès!";
                        $_SESSION['prestataire'] = $username; // Mettre à jour la session
                    } else {
                        $error_message = "Une erreur est survenue lors de la mise à jour des identifiants.";
                    }
                } else {
                    $error_message = "Les nouveaux mots de passe ne correspondent pas.";
                }
            }
        } else {
            $error_message = "Mot de passe actuel incorrect.";
        }
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-user-cog me-2"></i>Paramètres du compte</h4>
                </div>
                <div class="card-body">
                    <?php if(!empty($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $success_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Informations de connexion</h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                                <div class="invalid-feedback">
                                    Veuillez entrer un nom d'utilisateur.
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Modification du mot de passe</h5>
                                <p class="text-muted small">Laissez vide si vous ne souhaitez pas changer votre mot de passe.</p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="current_password" class="form-label">Mot de passe actuel <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Veuillez entrer votre mot de passe actuel.
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3"></div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="new_password" class="form-label">Nouveau mot de passe</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    8 caractères minimum, avec majuscules, minuscules et chiffres.
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" name="update_credentials" class="btn btn-primary px-5">
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
// Validation du formulaire
(function() {
    'use strict';
    
    // Fetch all forms to apply validation
    var forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            // Vérifier si le mot de passe actuel est renseigné
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            // Si nouveau mot de passe renseigné, vérifier qu'il correspond à la confirmation
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword && newPassword !== confirmPassword) {
                event.preventDefault();
                document.getElementById('confirm_password').setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                document.getElementById('confirm_password').setCustomValidity('');
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = 'password';
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
})();
</script>