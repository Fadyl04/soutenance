<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Guide Touristique - Adjarra</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Fichier CSS spécifique pour le formulaire d'inscription des guides */
.guide-form :root {
    --primary-color: #ffc107; /* Jaune au lieu de bleu */
    --secondary-color: #6c757d;
    --accent-color: #ffc107; /* Jaune au lieu de vert */
    --light-bg: #f8f9fa;
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.container {
    max-width: 1200px;
}

.form-container {
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 30px;
    margin-top: 40px;
    margin-bottom: 40px;
}

.header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
}

.header h1 {
    color: #4caf50; /* Vert au lieu de jaune */
    font-weight: 700;
}

.header p {
    color: var(--secondary-color);
}

.form-section {
    margin-bottom: 30px;
    padding: 20px;
    border-radius: 10px;
    background-color: var(--light-bg);
}

.section-title {
    color: #4caf50; /* Vert au lieu de jaune */
    margin-bottom: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.section-title i {
    margin-right: 10px;
    color: #4caf50; /* Vert au lieu de jaune */
}

.form-label {
    font-weight: 500;
    color: #333;
}

.required::after {
    content: " *";
    color: red;
}

.form-check-label {
    margin-left: 5px;
}

.submit-btn {
    background-color: #4caf50; /* Vert au lieu de jaune */
    border: none;
    padding: 12px 30px;
    border-radius: 30px;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-top: 20px;
    color: white; /* Texte blanc pour contraste sur fond vert */
}

.submit-btn:hover {
    background-color: #3e8e41; /* Vert foncé au survol */
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.preview-images {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.img-preview {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 5px;
}

.status-info {
    font-style: italic;
    color: var(--secondary-color);
}

.success-message, .error-message {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.form-floating > .form-control {
    padding-top: 1.625rem;
    padding-bottom: 0.625rem;
}

/* Styles pour les boutons et éléments interactifs */
.btn-primary {
    background-color: #4caf50;
    border-color: #4caf50;
    color: white;
}

.btn-primary:hover {
    background-color: #3e8e41;
    border-color: #3e8e41;
}

.form-check-input:checked {
    background-color: #4caf50;
    border-color: #4caf50;
}

.file-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100px;
    height: 100px;
    background-color: #f5f5f5;
    border-radius: 5px;
    margin-right: 10px;
}

.file-icon i {
    font-size: 2rem;
    color: #6c757d;
}

.file-icon p {
    font-size: 0.8rem;
    margin-top: 5px;
    text-align: center;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    max-width: 90px;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="header">
                <h1><i class="fas fa-user-tie"></i> Inscription des Guides Touristiques</h1>
                <p>Remplissez le formulaire ci-dessous pour vous enregistrer en tant qu'Organisateur</p>
            </div>
            
            <?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Fadyl111');
define('DB_NAME', 'adjarra');

// Classes
class Database {
    private $connection;
    
    public function __construct() {
        try {
            $this->connection = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Erreur de connexion à la base de données: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function closeConnection() {
        $this->connection = null;
    }
    
    public function ensureTableExists($tableName, $schema) {
        $check_table = $this->connection->query("SHOW TABLES LIKE '$tableName'");
        if($check_table->rowCount() == 0) {
            $this->connection->exec($schema);
        }
    }
}

class FileUploader {
    public static function uploadSingleFile($file, $uploadDir) {
        if($file['error'] == 0) {
            // Créer le répertoire s'il n'existe pas
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $tempFile = $file['tmp_name'];
            $fileName = time() . '_' . $file['name'];
            $targetFile = $uploadDir . $fileName;
            
            if(move_uploaded_file($tempFile, $targetFile)) {
                return $targetFile;
            }
        }
        return null;
    }
    
    public static function uploadMultipleFiles($files, $uploadDir) {
        $uploadedFiles = [];
        
        if(!empty($files['name'][0])) {
            // Créer le répertoire s'il n'existe pas
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $totalFiles = count($files['name']);
            
            for($i = 0; $i < $totalFiles; $i++) {
                if($files['error'][$i] == 0) {
                    $tempFile = $files['tmp_name'][$i];
                    $fileName = time() . '_' . $files['name'][$i];
                    $targetFile = $uploadDir . $fileName;
                    
                    if(move_uploaded_file($tempFile, $targetFile)) {
                        $uploadedFiles[] = $targetFile;
                    }
                }
            }
        }
        
        return !empty($uploadedFiles) ? json_encode($uploadedFiles) : null;
    }
}

class GuideManager {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
        $this->ensureGuidesTableExists();
    }
    
    private function ensureGuidesTableExists() {
        $schema = "CREATE TABLE guides (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            birth_date DATE,
            gender ENUM('homme', 'femme', 'autre') NOT NULL,
            address VARCHAR(255) NOT NULL,
            city VARCHAR(100) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            email VARCHAR(255) NOT NULL,
            id_number VARCHAR(50),
            license_number VARCHAR(50),
            experience_years INT(2) NOT NULL,
            education VARCHAR(255),
            languages VARCHAR(255),
            specializations VARCHAR(255),
            regions VARCHAR(255),
            daily_rate DECIMAL(10,2),
            biography TEXT,
            profile_photo VARCHAR(255),
            documents TEXT,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->db->ensureTableExists('guides', $schema);
    }
    
    public function registerGuide($data, $files) {
        try {
            $conn = $this->db->getConnection();
            
            // Traitement de la photo de profil
            $profile_photo = null;
            if(isset($files['profile_photo']) && $files['profile_photo']['error'] == 0) {
                $profile_photo = FileUploader::uploadSingleFile($files['profile_photo'], "uploads/guides/");
            }
            
            // Traitement des documents
            $documents_json = null;
            if(isset($files['documents']) && !empty($files['documents']['name'][0])) {
                $documents_json = FileUploader::uploadMultipleFiles($files['documents'], "uploads/guides/documents/");
            }
            
            // Génération automatique du nom d'utilisateur et mot de passe
            $username = strtolower(substr($data['first_name'], 0, 1) . $data['last_name']) . rand(10, 99);
            $password_hash = password_hash('password123', PASSWORD_DEFAULT); // Mot de passe temporaire
            
            // Préparer la requête SQL
            $sql = "INSERT INTO guides (
                first_name, last_name, birth_date, gender, address, city, phone, email, id_number, 
                license_number, experience_years, education, languages, specializations, regions, 
                daily_rate, biography, profile_photo, documents, username, password, status
            ) VALUES (
                :first_name, :last_name, :birth_date, :gender, :address, :city, :phone, :email, :id_number, 
                :license_number, :experience_years, :education, :languages, :specializations, :regions, 
                :daily_rate, :biography, :profile_photo, :documents, :username, :password, :status
            )";
            
            $stmt = $conn->prepare($sql);
            
            // Associer les paramètres
            $stmt->bindParam(':first_name', $data['first_name']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':birth_date', $data['birth_date']);
            $stmt->bindParam(':gender', $data['gender']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':city', $data['city']);
            $stmt->bindParam(':phone', $data['phone']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':id_number', $data['id_number']);
            $stmt->bindParam(':license_number', $data['license_number']);
            $stmt->bindParam(':experience_years', $data['experience_years']);
            $stmt->bindParam(':education', $data['education']);
            
            // Traitement des tableaux
            $languages = isset($data['languages']) ? implode(',', $data['languages']) : null;
            $specializations = isset($data['specializations']) ? implode(',', $data['specializations']) : null;
            $regions = isset($data['regions']) ? implode(',', $data['regions']) : null;
            
            $stmt->bindParam(':languages', $languages);
            $stmt->bindParam(':specializations', $specializations);
            $stmt->bindParam(':regions', $regions);
            $stmt->bindParam(':daily_rate', $data['daily_rate']);
            $stmt->bindParam(':biography', $data['biography']);
            $stmt->bindParam(':profile_photo', $profile_photo);
            $stmt->bindParam(':documents', $documents_json);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password_hash);
            
            $status = 'pending';
            $stmt->bindParam(':status', $status);
            
            $stmt->execute();
            
            return true;
        } catch(PDOException $e) {
            throw new Exception("Erreur lors de l'enregistrement: " . $e->getMessage());
        }
    }
}

// Script principal
// Initialiser les variables
$success_message = "";
$error_message = "";

// Traitement du formulaire lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Initialiser la base de données
        $database = new Database();
        
        // Initialiser le gestionnaire de guides
        $guideManager = new GuideManager($database);
        
        // Récupérer les données du formulaire
        $data = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
            'gender' => $_POST['gender'],
            'address' => $_POST['address'],
            'city' => $_POST['city'],
            'phone' => $_POST['phone'],
            'email' => $_POST['email'],
            'id_number' => !empty($_POST['id_number']) ? $_POST['id_number'] : null,
            'license_number' => !empty($_POST['license_number']) ? $_POST['license_number'] : null,
            'experience_years' => $_POST['experience_years'],
            'education' => !empty($_POST['education']) ? $_POST['education'] : null,
            'languages' => isset($_POST['languages']) ? $_POST['languages'] : [],
            'specializations' => isset($_POST['specializations']) ? $_POST['specializations'] : [],
            'regions' => isset($_POST['regions']) ? $_POST['regions'] : [],
            'daily_rate' => !empty($_POST['daily_rate']) ? $_POST['daily_rate'] : null,
            'biography' => !empty($_POST['biography']) ? $_POST['biography'] : null
        ];
        
        // Enregistrer le guide
        if ($guideManager->registerGuide($data, $_FILES)) {
            $success_message = "Félicitations ! Votre profil de guide touristique a été enregistré avec succès. Votre demande est en cours d'examen. Un administrateur vous enverra vos identifiants de connexion par email une fois votre inscription validée.";
        }
        
        // Fermer la connexion à la base de données
        $database->closeConnection();
        
    } catch(Exception $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}
?>
            
            <?php if(!empty($success_message)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
            <?php endif; ?>
            
            <?php if(!empty($error_message)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <!-- Informations personnelles -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-user"></i> Informations personnelles</h3>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Prénom" required>
                                <label for="first_name" class="required">Prénom</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Nom" required>
                                <label for="last_name" class="required">Nom</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" id="birth_date" name="birth_date">
                                <label for="birth_date">Date de naissance</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select class="form-control" id="gender" name="gender" required>
                                    <option value="">Sélectionnez</option>
                                    <option value="homme">Homme</option>
                                    <option value="femme">Femme</option>
                                    <option value="autre">Autre</option>
                                </select>
                                <label for="gender" class="required">Genre</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="address" name="address" placeholder="Adresse" required>
                        <label for="address" class="required">Adresse</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="city" name="city" placeholder="Ville" required>
                        <label for="city" class="required">Ville</label>
                    </div>
                </div>
                
                <!-- Informations de contact -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-address-card"></i> Informations de contact</h3>
                    
                    <div class="form-floating mb-3">
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Téléphone" required>
                        <label for="phone" class="required">Téléphone</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                        <label for="email" class="required">Email</label>
                    </div>
                </div>
                
                <!-- Informations professionnelles -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-briefcase"></i> Informations professionnelles</h3>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="id_number" name="id_number" placeholder="Numéro d'identité">
                                <label for="id_number">Numéro d'identité (CNI, Passeport)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="license_number" name="license_number" placeholder="Numéro de licence">
                                <label for="license_number">Numéro de licence de guide (si disponible)</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="experience_years" name="experience_years" placeholder="Années d'expérience" min="0" required>
                                <label for="experience_years" class="required">Années d'expérience</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="education" name="education" placeholder="Formation">
                                <label for="education">Formation / Diplôme</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Langues parlées</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="lang_french" name="languages[]" value="Français">
                                    <label class="form-check-label" for="lang_french">Français</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="lang_english" name="languages[]" value="Anglais">
                                    <label class="form-check-label" for="lang_english">Anglais</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="lang_fon" name="languages[]" value="Fon">
                                    <label class="form-check-label" for="lang_fon">Fon</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="lang_yoruba" name="languages[]" value="Yoruba">
                                    <label class="form-check-label" for="lang_yoruba">Yoruba</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="lang_spanish" name="languages[]" value="Espagnol">
                                    <label class="form-check-label" for="lang_spanish">Espagnol</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="lang_other" name="languages[]" value="Autre">
                                    <label class="form-check-label" for="lang_other">Autre</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Spécialisations</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="spec_history" name="specializations[]" value="Histoire">
                                    <label class="form-check-label" for="spec_history">Histoire</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="spec_culture" name="specializations[]" value="Culture">
                                    <label class="form-check-label" for="spec_culture">Culture</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="spec_nature" name="specializations[]" value="Nature">
                                    <label class="form-check-label" for="spec_nature">Nature</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="spec_gastronomy" name="specializations[]" value="Gastronomie">
                                    <label class="form-check-label" for="spec_gastronomy">Gastronomie</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="spec_adventure" name="specializations[]" value="Aventure">
                                    <label class="form-check-label" for="spec_adventure">Aventure</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="spec_religion" name="specializations[]" value="Religion">
                                    <label class="form-check-label" for="spec_religion">Religion</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Régions couvertes</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="region_adjarra" name="regions[]" value="Adjarra">
                                    <label class="form-check-label" for="region_adjarra">Adjarra</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="region_cotonou" name="regions[]" value="Cotonou">
                                    <label class="form-check-label" for="region_cotonou">Cotonou</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="region_porto" name="regions[]" value="Porto-Novo">
                                    <label class="form-check-label" for="region_porto">Porto-Novo</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="region_ouidah" name="regions[]" value="Ouidah">
                                    <label class="form-check-label" for="region_ouidah">Ouidah</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="region_abomey" name="regions[]" value="Abomey">
                                    <label class="form-check-label" for="region_abomey">Abomey</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="region_other" name="regions[]" value="Autre">
                                    <label class="form-check-label" for="region_other">Autre</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="daily_rate" name="daily_rate" placeholder="Tarif journalier" step="0.01">
                        <label for="daily_rate">Tarif journalier (FCFA)</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="biography" name="biography" placeholder="Biographie" style="height: 150px"></textarea>
                        <label for="biography">Biographie / Présentation</label>
                        <div class="form-text">Décrivez votre expérience, vos compétences et ce qui vous passionne dans le métier de guide.</div>
                    </div>
                </div>
                
                <!-- Photo et Documents -->
<div class="form-section">
    <h3 class="section-title"><i class="fas fa-image"></i> Photo et Documents</h3>
    
    <div class="mb-3">
        <label for="profile_photo" class="form-label">Photo de profil</label>
        <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
        <div class="form-text">Format recommandé : JPG ou PNG, taille maximale : 2 Mo</div>
        <div class="preview-images mt-2" id="photoPreview"></div>
    </div>
    
    <div class="mb-3">
        <label for="documents" class="form-label">Documents (Diplômes, Certifications, CV, etc.)</label>
        <input type="file" class="form-control" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx">
        <div class="form-text">Formats acceptés : PDF, DOC, DOCX. Taille maximale : 5 Mo par fichier</div>
        <div class="preview-files mt-2" id="docsPreview"></div>
    </div>
</div>

<!-- Termes et conditions -->
<div class="form-section">
    <h3 class="section-title"><i class="fas fa-file-contract"></i> Termes et conditions</h3>
    
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
            <label class="form-check-label required" for="terms">
                J'accepte les termes et conditions d'utilisation
            </label>
        </div>
    </div>
    
    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
            <label class="form-check-label required" for="privacy">
                J'accepte que mes informations personnelles soient traitées conformément à la politique de confidentialité
            </label>
        </div>
    </div>
    
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Après validation de votre inscription par un administrateur, vous recevrez vos identifiants de connexion par email.
    </div>
</div>

<div class="text-center">
    <button type="submit" class="submit-btn" name="soumettre">
        <i class="fas fa-paper-plane"></i> Soumettre ma candidature
    </button>
</div>
</form>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Prévisualisation de la photo de profil
    document.getElementById('profile_photo').addEventListener('change', function(e) {
        const photoPreview = document.getElementById('photoPreview');
        photoPreview.innerHTML = '';
        
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-preview';
                photoPreview.appendChild(img);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Prévisualisation des documents
    document.getElementById('documents').addEventListener('change', function(e) {
        const docsPreview = document.getElementById('docsPreview');
        docsPreview.innerHTML = '';
        
        if (this.files && this.files.length > 0) {
            for (let i = 0; i < this.files.length; i++) {
                const file = this.files[i];
                const fileDiv = document.createElement('div');
                fileDiv.className = 'file-icon';
                
                const icon = document.createElement('i');
                
                // Déterminer l'icône en fonction du type de fichier
                if (file.type.includes('pdf')) {
                    icon.className = 'fas fa-file-pdf';
                } else if (file.type.includes('word') || file.name.endsWith('.doc') || file.name.endsWith('.docx')) {
                    icon.className = 'fas fa-file-word';
                } else {
                    icon.className = 'fas fa-file';
                }
                
                const fileName = document.createElement('p');
                fileName.textContent = file.name.length > 15 ? file.name.substring(0, 12) + '...' : file.name;
                
                fileDiv.appendChild(icon);
                fileDiv.appendChild(fileName);
                docsPreview.appendChild(fileDiv);
            }
        }
    });
</script>
</body>
</html>