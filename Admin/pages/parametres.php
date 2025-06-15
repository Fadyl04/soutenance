<?php
include 'database/db.php';
// Vérifier que l'administrateur est connecté
if(!isset($_SESSION['admin'])){
    header("Location:../index.php?page=login");
    exit();
}

// Récupérer les informations de l'administrateur connecté
// Vérifier comment est stockée la session admin
if (is_array($_SESSION['admin']) && isset($_SESSION['admin'])) {
    // Si c'est un tableau avec une clé 'id'
    $admin_id = $_SESSION['admin'];
} else {
    // Si c'est directement l'ID
    $admin_id = $_SESSION['admin'];
}
$success_message = '';
$error_message = '';

// Traitement du formulaire de mise à jour
if(isset($_POST['update_admin'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    try {
        // Vérifier si l'email existe déjà pour un autre administrateur
        $check_email = $db->prepare("SELECT id FROM admins WHERE email = :email AND id != :id");
        $check_email->bindParam(':email', $email);
        $check_email->bindParam(':id', $admin_id);
        $check_email->execute();
        
        if($check_email->rowCount() > 0) {
            $error_message = "Cet email est déjà utilisé par un autre administrateur.";
        } else {
            // Récupérer le mot de passe actuel de l'administrateur
            $stmt = $db->prepare("SELECT password FROM admins WHERE id = :id");
            $stmt->bindParam(':id', $admin_id);
            $stmt->execute();
            $admin_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Vérifier que le mot de passe actuel est correct
            if(!password_verify($current_password, $admin_data['password'])) {
                $error_message = "Le mot de passe actuel est incorrect.";
            } else {
                // Si un nouveau mot de passe est fourni, le valider
                if(!empty($new_password)) {
                    if(strlen($new_password) < 8) {
                        $error_message = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
                    } elseif($new_password !== $confirm_password) {
                        $error_message = "Les nouveaux mots de passe ne correspondent pas.";
                    } else {
                        // Hasher le nouveau mot de passe
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        
                        // Mise à jour de l'email et du mot de passe
                        $update = $db->prepare("UPDATE admins SET email = :email, password = :password WHERE id = :id");
                        $update->bindParam(':email', $email);
                        $update->bindParam(':password', $hashed_password);
                        $update->bindParam(':id', $admin_id);
                        
                        if($update->execute()) {
                            // Mettre à jour la session avec le nouvel email
                            $_SESSION['admin']['email'] = $email;
                            $success_message = "Vos informations ont été mises à jour avec succès.";
                        } else {
                            $error_message = "Une erreur est survenue lors de la mise à jour.";
                        }
                    }
                } else {
                    // Mise à jour de l'email uniquement
                    $update = $db->prepare("UPDATE admins SET email = :email WHERE id = :id");
                    $update->bindParam(':email', $email);
                    $update->bindParam(':id', $admin_id);
                    
                    if($update->execute()) {
                        // Mettre à jour la session avec le nouvel email
                        $_SESSION['admin']['email'] = $email;
                        $success_message = "Votre email a été mis à jour avec succès.";
                    } else {
                        $error_message = "Une erreur est survenue lors de la mise à jour.";
                    }
                }
            }
        }
    } catch(PDOException $e) {
        $error_message = "Erreur de base de données: " . $e->getMessage();
    }
}

/// Récupérer les informations actuelles de l'administrateur
try {
    $stmt = $db->prepare("SELECT ID, Email  FROM admins ");
   
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Vérifier si un résultat a été trouvé
    if ($admin === false) {
        // Aucun administrateur trouvé avec cet ID
        $error_message = "Administrateur introuvable dans la base de données.";
        $admin = ['id' => $admin_id, 'email' => '']; // Valeurs par défaut
    }
} catch(PDOException $e) {
    $error_message = "Erreur lors de la récupération des informations: " . $e->getMessage();
    $admin = ['id' => $admin_id, 'email' => '']; // Valeurs par défaut
}
?>

<div class="container-fluid">
    <div class="header">
        <h2><i class="fas fa-cogs me-2"></i> Paramètres du compte</h2>
        <p class="lead">Modifiez vos informations de connexion</p>
    </div>
    
    <?php if(!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if(!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="mb-0"><i class="fas fa-user-cog me-2"></i> Informations du compte</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="" id="updateAdminForm">
                        <div class="mb-4">
                            <label for="admin_id" class="form-label">ID d'administrateur</label>
                            <input type="text" class="form-control" id="admin_id" value="<?php echo $admin['ID']; ?>" readonly>
                            <small class="text-muted">L'identifiant de votre compte (non modifiable)</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="form-label">Adresse email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($admin['Email']); ?>" required>
                            <small class="text-muted">Cette adresse sera utilisée pour vous connecter et recevoir des notifications</small>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6 class="mb-3"><i class="fas fa-lock me-2"></i> Modifier le mot de passe</h6>
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de passe actuel</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="current_password" name="current_password">
                                <span class="input-group-text toggle-password" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <small class="text-muted">Requis pour toute modification</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password" name="new_password">
                                <span class="input-group-text toggle-password" data-target="new_password">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <small class="text-muted">Laissez vide pour conserver votre mot de passe actuel</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                <span class="input-group-text toggle-password" data-target="confirm_password">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" name="update_admin">
                                <i class="fas fa-save me-2"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Carte pour les informations de sécurité -->
            <div class="card shadow mt-4">
                <div class="card-header" style="background-color: var(--primary-color); color: white;">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i> Sécurité du compte</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i> Conseils de sécurité</h6>
                        <ul>
                            <li>Utilisez un mot de passe fort avec au moins 8 caractères incluant des lettres majuscules, minuscules, des chiffres et des caractères spéciaux.</li>
                            <li>Changez votre mot de passe régulièrement.</li>
                            <li>Ne partagez jamais vos identifiants avec d'autres personnes.</li>
                            <li>Déconnectez-vous toujours lorsque vous avez terminé votre session.</li>
                        </ul>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-clock me-2"></i> Dernière connexion: <strong>Aujourd'hui à <?php echo date('H:i'); ?></strong></span>
                        <a href="index.php?page=logout" class="btn btn-danger btn-sm">
                            <i class="fas fa-power-off me-2"></i> Se déconnecter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fonction pour afficher/masquer les mots de passe
    document.querySelectorAll('.toggle-password').forEach(element => {
        element.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const targetIcon = this.querySelector('i');
            
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                targetIcon.classList.remove('fa-eye');
                targetIcon.classList.add('fa-eye-slash');
            } else {
                targetInput.type = 'password';
                targetIcon.classList.remove('fa-eye-slash');
                targetIcon.classList.add('fa-eye');
            }
        });
    });
    
    // Validation du formulaire
    document.getElementById('updateAdminForm').addEventListener('submit', function(e) {
        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        // Vérifier si le mot de passe actuel est saisi
        if (currentPassword === '') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Attention',
                text: 'Veuillez saisir votre mot de passe actuel pour confirmer les modifications.',
                confirmButtonColor: '#008751'
            });
            return;
        }
        
        // Vérifier que les mots de passe correspondent
        if (newPassword !== '' && newPassword !== confirmPassword) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Les nouveaux mots de passe ne correspondent pas.',
                confirmButtonColor: '#008751'
            });
            return;
        }
        
        // Vérifier la longueur du mot de passe
        if (newPassword !== '' && newPassword.length < 8) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Mot de passe faible',
                text: 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
                confirmButtonColor: '#008751'
            });
            return;
        }
        
        // Confirmation des modifications
        e.preventDefault();
        Swal.fire({
            title: 'Confirmer les modifications',
            text: 'Êtes-vous sûr de vouloir mettre à jour vos informations ?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#008751',
            cancelButtonColor: '#e74c3c',
            confirmButtonText: 'Oui, mettre à jour',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>