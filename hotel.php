<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Hôtel - Adjarra</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Styles CSS conservés identiques à votre fichier original -->
    <style>
        /* Fichier CSS spécifique pour le formulaire d'inscription d'hôtels */
.hotel-form :root {
    --primary-color: #ffc107; /* Jaune au lieu de bleu */
    --secondary-color: #6c757d;
    --accent-color: #ffc107; /* Jaune au lieu de vert */
    --light-bg: #f8f9fa;
}

.hotel-form body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.hotel-form .container {
    max-width: 1200px;
}

.hotel-form .form-container {
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 30px;
    margin-top: 40px;
    margin-bottom: 40px;
}

.hotel-form .header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
}

.hotel-form .header h1 {
    color: #ffc107; /* Jaune au lieu de bleu */
    font-weight: 700;
}

.hotel-form .header p {
    color: var(--secondary-color);
}

.hotel-form .form-section {
    margin-bottom: 30px;
    padding: 20px;
    border-radius: 10px;
    background-color: var(--light-bg);
}

.hotel-form .section-title {
    color: #ffc107; /* Jaune au lieu de bleu */
    margin-bottom: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.hotel-form .section-title i {
    margin-right: 10px;
    color: #ffc107; /* Jaune au lieu de vert */
}

.hotel-form .form-label {
    font-weight: 500;
    color: #333;
}

.hotel-form .required::after {
    content: " *";
    color: red;
}

.hotel-form .form-check-label {
    margin-left: 5px;
}

.hotel-form .submit-btn {
    background-color: #ffc107; /* Jaune au lieu de vert */
    border: none;
    padding: 12px 30px;
    border-radius: 30px;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-top: 20px;
    color: #212529; /* Texte foncé pour contraste sur fond jaune */
}

.hotel-form .submit-btn:hover {
    background-color: #e0a800; /* Jaune foncé au survol */
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.hotel-form .preview-images {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.hotel-form .img-preview {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 5px;
}

.hotel-form .status-info {
    font-style: italic;
    color: var(--secondary-color);
}

.hotel-form .success-message, .hotel-form .error-message {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.hotel-form .success-message {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.hotel-form .error-message {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.hotel-form .form-floating > .form-control {
    padding-top: 1.625rem;
    padding-bottom: 0.625rem;
}

/* Styles pour les boutons et éléments interactifs */
.hotel-form .btn-primary {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.hotel-form .btn-primary:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

.hotel-form .form-check-input:checked {
    background-color: #ffc107;
    border-color: #ffc107;
}
    </style>
</head>
<body>
    <div class="hotel-form">
        <div class="form-container">
            <div class="header">
                <h1><i class="fas fa-hotel"></i> Inscription des Hôtels</h1>
                <p>Remplissez le formulaire ci-dessous pour enregistrer votre établissement</p>
            </div>
            
            <?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
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

class HotelManager {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
        $this->ensureHotelsTableExists();
    }
    
    private function ensureHotelsTableExists() {
        $schema = "CREATE TABLE hotels (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            hotel_name VARCHAR(255) NOT NULL,
            creation_year INT(11),
            address VARCHAR(255) NOT NULL,
            city VARCHAR(100) NOT NULL,
            region VARCHAR(100),
            contact_name VARCHAR(100) NOT NULL,
            contact_phone VARCHAR(50) NOT NULL,
            contact_email VARCHAR(100) NOT NULL,
            website VARCHAR(255),
            hotel_category VARCHAR(50),
            room_count INT(11) NOT NULL,
            has_wifi TINYINT(1) DEFAULT 0,
            has_parking TINYINT(1) DEFAULT 0,
            has_pool TINYINT(1) DEFAULT 0,
            has_restaurant TINYINT(1) DEFAULT 0,
            has_ac TINYINT(1) DEFAULT 0,
            has_conference TINYINT(1) DEFAULT 0,
            price_range VARCHAR(50) NOT NULL,
            check_in_time TIME,
            check_out_time TIME,
            description TEXT,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            photo_files TEXT
        )";
        
        $this->db->ensureTableExists('hotels', $schema);
    }
    
    public function registerHotel($data, $files) {
        try {
            $conn = $this->db->getConnection();
            
            // Traitement des photos
            $photo_files_json = null;
            // Correction ici: utiliser 'photos' au lieu de 'photo_files' pour correspondre au nom du champ dans le formulaire
            if(isset($files['photos']) && !empty($files['photos']['name'][0])) {
                $photo_files_json = FileUploader::uploadMultipleFiles($files['photos'], "uploads/hotels/photos/");
            }
            
            // Génération automatique du nom d'utilisateur et mot de passe
            $username = strtolower(str_replace(' ', '', $data['hotel_name'])) . rand(10, 99);
            $password_hash = password_hash('password123', PASSWORD_DEFAULT); // Mot de passe temporaire
            
            // Préparer la requête SQL
            $sql = "INSERT INTO hotels (
                hotel_name, creation_year, address, city, region, contact_name, contact_phone,
                contact_email, website, hotel_category, room_count, has_wifi, has_parking,
                has_pool, has_restaurant, has_ac, has_conference, price_range,
                check_in_time, check_out_time, description, username, password, status, photo_files
            ) VALUES (
                :hotel_name, :creation_year, :address, :city, :region, :contact_name, :contact_phone,
                :contact_email, :website, :hotel_category, :room_count, :has_wifi, :has_parking,
                :has_pool, :has_restaurant, :has_ac, :has_conference, :price_range,
                :check_in_time, :check_out_time, :description, :username, :password, :status, :photo_files
            )";
            
            $stmt = $conn->prepare($sql);
            
            // Associer les paramètres
            $stmt->bindParam(':hotel_name', $data['hotel_name']);
            $stmt->bindParam(':creation_year', $data['creation_year']);
            $stmt->bindParam(':address', $data['address']);
            $stmt->bindParam(':city', $data['city']);
            $stmt->bindParam(':region', $data['region']);
            $stmt->bindParam(':contact_name', $data['contact_name']);
            $stmt->bindParam(':contact_phone', $data['contact_phone']);
            $stmt->bindParam(':contact_email', $data['contact_email']);
            $stmt->bindParam(':website', $data['website']);
            $stmt->bindParam(':hotel_category', $data['hotel_category']);
            $stmt->bindParam(':room_count', $data['room_count']);
            
            // Conversion des checkbox en valeurs binaires
            $has_wifi = isset($data['has_wifi']) ? 1 : 0;
            $has_parking = isset($data['has_parking']) ? 1 : 0;
            $has_pool = isset($data['has_pool']) ? 1 : 0;
            $has_restaurant = isset($data['has_restaurant']) ? 1 : 0;
            $has_ac = isset($data['has_ac']) ? 1 : 0;
            $has_conference = isset($data['has_conference']) ? 1 : 0;
            
            $stmt->bindParam(':has_wifi', $has_wifi, PDO::PARAM_INT);
            $stmt->bindParam(':has_parking', $has_parking, PDO::PARAM_INT);
            $stmt->bindParam(':has_pool', $has_pool, PDO::PARAM_INT);
            $stmt->bindParam(':has_restaurant', $has_restaurant, PDO::PARAM_INT);
            $stmt->bindParam(':has_ac', $has_ac, PDO::PARAM_INT);
            $stmt->bindParam(':has_conference', $has_conference, PDO::PARAM_INT);
            
            $stmt->bindParam(':price_range', $data['price_range']);
            $stmt->bindParam(':check_in_time', $data['check_in_time']);
            $stmt->bindParam(':check_out_time', $data['check_out_time']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password_hash);
            
            $status = 'pending';
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':photo_files', $photo_files_json);
            
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
        
        // Initialiser le gestionnaire d'hôtels
        $hotelManager = new HotelManager($database);
        
        // Récupérer les données du formulaire
        $data = [
            'hotel_name' => $_POST['hotel_name'],
            'creation_year' => !empty($_POST['creation_year']) ? $_POST['creation_year'] : null,
            'address' => $_POST['address'],
            'city' => $_POST['city'],
            'region' => !empty($_POST['region']) ? $_POST['region'] : null,
            'contact_name' => $_POST['contact_name'],
            'contact_phone' => $_POST['contact_phone'],
            'contact_email' => $_POST['contact_email'],
            'website' => !empty($_POST['website']) ? $_POST['website'] : null,
            'hotel_category' => !empty($_POST['hotel_category']) ? $_POST['hotel_category'] : null,
            'room_count' => $_POST['room_count'],
            'has_wifi' => isset($_POST['has_wifi']) ? $_POST['has_wifi'] : 0,
            'has_parking' => isset($_POST['has_parking']) ? $_POST['has_parking'] : 0,
            'has_pool' => isset($_POST['has_pool']) ? $_POST['has_pool'] : 0,
            'has_restaurant' => isset($_POST['has_restaurant']) ? $_POST['has_restaurant'] : 0,
            'has_ac' => isset($_POST['has_ac']) ? $_POST['has_ac'] : 0,
            'has_conference' => isset($_POST['has_conference']) ? $_POST['has_conference'] : 0,
            'price_range' => $_POST['price_range'],
            'check_in_time' => !empty($_POST['check_in_time']) ? $_POST['check_in_time'] : null,
            'check_out_time' => !empty($_POST['check_out_time']) ? $_POST['check_out_time'] : null,
            'description' => !empty($_POST['description']) ? $_POST['description'] : null
        ];
        
        // Enregistrer l'hôtel
        if ($hotelManager->registerHotel($data, $_FILES)) {
            $success_message = "Félicitations ! Votre hôtel a été enregistré avec succès. Votre demande est en cours d'examen. Un administrateur vous enverra vos identifiants de connexion par email une fois votre inscription validée.";
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
                <!-- Informations de l'hôtel -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-info-circle"></i> Informations générales</h3>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="hotel_name" name="hotel_name" placeholder="Nom de l'hôtel" required>
                                <label for="hotel_name" class="required">Nom de l'hôtel</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="creation_year" name="creation_year" placeholder="Année de création" min="1800" max="<?php echo date('Y'); ?>">
                                <label for="creation_year">Année de création</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="address" name="address" placeholder="Adresse complète" required>
                        <label for="address" class="required">Adresse complète</label>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="city" name="city" placeholder="Ville" required>
                                <label for="city" class="required">Ville</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="region" name="region" placeholder="Région/Province">
                                <label for="region">Région/Province</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <select class="form-control" id="hotel_category" name="hotel_category">
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="1 étoile">1 étoile</option>
                            <option value="2 étoiles">2 étoiles</option>
                            <option value="3 étoiles">3 étoiles</option>
                            <option value="4 étoiles">4 étoiles</option>
                            <option value="5 étoiles">5 étoiles</option>
                            <option value="Non classé">Non classé</option>
                        </select>
                        <label for="hotel_category">Catégorie de l'hôtel</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="room_count" name="room_count" placeholder="Nombre de chambres" min="1" required>
                        <label for="room_count" class="required">Nombre de chambres</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <select class="form-control" id="price_range" name="price_range" required>
                            <option value="">Sélectionnez une gamme de prix</option>
                            <option value="Économique">Économique</option>
                            <option value="Intermédiaire">Intermédiaire</option>
                            <option value="Supérieur">Supérieur</option>
                            <option value="Luxe">Luxe</option>
                        </select>
                        <label for="price_range" class="required">Gamme de prix</label>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="time" class="form-control" id="check_in_time" name="check_in_time">
                                <label for="check_in_time">Heure d'arrivée</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="time" class="form-control" id="check_out_time" name="check_out_time">
                                <label for="check_out_time">Heure de départ</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="description" name="description" placeholder="Description de l'hôtel" style="height: 120px"></textarea>
                        <label for="description">Description de l'hôtel</label>
                    </div>
                </div>

                <!-- Équipements et Services -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-concierge-bell"></i> Équipements et Services</h3>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_wifi" name="has_wifi">
                                <label class="form-check-label" for="has_wifi"><i class="fas fa-wifi"></i> Wi-Fi gratuit</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_parking" name="has_parking">
                                <label class="form-check-label" for="has_parking"><i class="fas fa-parking"></i> Parking</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_pool" name="has_pool">
                                <label class="form-check-label" for="has_pool"><i class="fas fa-swimming-pool"></i> Piscine</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_restaurant" name="has_restaurant">
                                <label class="form-check-label" for="has_restaurant"><i class="fas fa-utensils"></i> Restaurant</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_ac" name="has_ac">
                                <label class="form-check-label" for="has_ac"><i class="fas fa-snowflake"></i> Climatisation</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="has_conference" name="has_conference">
                                <label class="form-check-label" for="has_conference"><i class="fas fa-chalkboard"></i> Salle de conférence</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-address-card"></i> Informations de contact</h3>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="Nom du contact" required>
                        <label for="contact_name" class="required">Nom du contact</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="tel" class="form-control" id="contact_phone" name="contact_phone" placeholder="Téléphone" required>
                        <label for="contact_phone" class="required">Téléphone</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="contact_email" name="contact_email" placeholder="Email" required>
                        <label for="contact_email" class="required">Email</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="url" class="form-control" id="website" name="website" placeholder="Site web">
                        <label for="website">Site web (avec http:// ou https://)</label>
                    </div>
                </div>

                <!-- Photos -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-images"></i> Photos de l'hôtel</h3>
                    <div class="mb-3">
                        <label for="photos" class="form-label">Télécharger des photos (maximum 5)</label>
                        <input type="file" class="form-control" id="photos" name="photos[]" accept="image/*" multiple>
                        <div class="form-text">Formats acceptés: JPG, PNG, GIF. Taille maximale: 5MB par image.</div>
                        <div class="preview-images" id="imagePreview"></div>
                    </div>
                </div>

                <!-- Note d'information sur le processus d'inscription -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-info-circle"></i> Informations importantes</h3>
                    <div class="status-info">
                        <p><i class="fas fa-check-circle"></i> Votre inscription sera examinée par notre équipe avant d'être approuvée.</p>
                        <p><i class="fas fa-envelope"></i> Vos identifiants de connexion seront générés automatiquement et envoyés à l'adresse email fournie une fois votre inscription validée.</p>
                        <p><i class="fas fa-lock"></i> Pour des raisons de sécurité, nous vous recommanderons de changer votre mot de passe lors de votre première connexion.</p>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary submit-btn">
                        <i class="fas fa-paper-plane"></i> Soumettre l'inscription
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Prévisualisation des images
        document.getElementById('photos').addEventListener('change', function(event) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            const files = event.target.files;
            
            if (files.length > 5) {
                alert('Vous pouvez télécharger un maximum de 5 images.');
                this.value = '';
                return;
            }
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                if (file.size > 5 * 1024 * 1024) {
                    alert('La taille du fichier ' + file.name + ' dépasse 5MB.');
                    continue;
                }
                
                if (!file.type.match('image.*')) {
                    continue;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.classList.add('img-preview');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                
                reader.readAsDataURL(file);
            }
        });
        
        // Suppression de la validation incorrecte du formulaire
        // Le code problématique a été retiré ici
    </script>
</body>
</html>