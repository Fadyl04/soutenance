<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Transporteur - Adjarra</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Fichier CSS spécifique pour le formulaire d'inscription des transporteurs */
.transport-form :root {
    --primary-color: #3498db; /* Bleu au lieu de vert */
    --secondary-color: #6c757d;
    --accent-color: #3498db; /* Bleu au lieu de jaune */
    --light-bg: #f8f9fa;
}

.transport-form body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.transport-form .container {
    max-width: 1200px;
}

.transport-form .form-container {
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 30px;
    margin-top: 40px;
    margin-bottom: 40px;
}

.transport-form .header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e0e0e0;
}

.transport-form .header h1 {
    color: #3498db; /* Bleu au lieu de vert */
    font-weight: 700;
}

.transport-form .header p {
    color: var(--secondary-color);
}

.transport-form .form-section {
    margin-bottom: 30px;
    padding: 20px;
    border-radius: 10px;
    background-color: var(--light-bg);
}

.transport-form .section-title {
    color: #3498db; /* Bleu au lieu de vert */
    margin-bottom: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.transport-form .section-title i {
    margin-right: 10px;
    color: #3498db; /* Bleu au lieu de vert */
}

.transport-form .form-label {
    font-weight: 500;
    color: #333;
}

.transport-form .required::after {
    content: " *";
    color: red;
}

.transport-form .form-check-label {
    margin-left: 5px;
}

.transport-form .submit-btn {
    background-color: #3498db; /* Bleu au lieu de vert */
    border: none;
    padding: 12px 30px;
    border-radius: 30px;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-top: 20px;
    color: white; /* Texte blanc pour contraste sur fond bleu */
}

.transport-form .submit-btn:hover {
    background-color: #2980b9; /* Bleu foncé au survol */
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.transport-form .preview-images {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.transport-form .img-preview {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 5px;
}

.transport-form .status-info {
    font-style: italic;
    color: var(--secondary-color);
}

.transport-form .success-message, .transport-form .error-message {
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.transport-form .success-message {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.transport-form .error-message {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.transport-form .form-floating > .form-control {
    padding-top: 1.625rem;
    padding-bottom: 0.625rem;
}

/* Styles pour les boutons et éléments interactifs */
.transport-form .btn-primary {
    background-color: #3498db;
    border-color: #3498db;
    color: white;
}

.transport-form .btn-primary:hover {
    background-color: #2980b9;
    border-color: #2980b9;
}

.transport-form .form-check-input:checked {
    background-color: #3498db;
    border-color: #3498db;
}
    </style>
</head> <br> <br> <br> <br>
<body>
    <div class="transport-form">
        <div class="form-container">
            <div class="header">
                <h1><i class="fas fa-car"></i> Inscription des Transporteurs</h1>
                <p>Remplissez le formulaire ci-dessous pour vous enregistrer en tant que transporteur</p>
            </div>
            
            <?php
// Variables pour la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "adjarra";

// Initialiser les variables
$success_message = "";
$error_message = "";

// Traitement du formulaire lors de la soumission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Créer une connexion à la base de données
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // Configurer PDO pour qu'il lance des exceptions en cas d'erreur
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Récupérer les données du formulaire
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $birth_date = !empty($_POST['birth_date']) ? $_POST['birth_date'] : null;
        $gender = $_POST['gender'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $id_number = !empty($_POST['id_number']) ? $_POST['id_number'] : null;
        $driver_license = !empty($_POST['driver_license']) ? $_POST['driver_license'] : null;
        $experience_years = $_POST['experience_years'];
        $vehicle_type = isset($_POST['vehicle_type']) ? implode(',', $_POST['vehicle_type']) : '';
        $vehicle_model = !empty($_POST['vehicle_model']) ? $_POST['vehicle_model'] : null;
        $vehicle_year = !empty($_POST['vehicle_year']) ? $_POST['vehicle_year'] : null;
        $plate_number = !empty($_POST['plate_number']) ? $_POST['plate_number'] : null;
        $passenger_capacity = !empty($_POST['passenger_capacity']) ? $_POST['passenger_capacity'] : null;
        $languages = isset($_POST['languages']) ? implode(',', $_POST['languages']) : '';
        $services = isset($_POST['services']) ? implode(',', $_POST['services']) : '';
        $regions = isset($_POST['regions']) ? implode(',', $_POST['regions']) : '';
        $rate_per_km = !empty($_POST['rate_per_km']) ? $_POST['rate_per_km'] : null;
        $biography = !empty($_POST['biography']) ? $_POST['biography'] : null;
        
        // Génération automatique du nom d'utilisateur et mot de passe
        $username = strtolower(substr($first_name, 0, 1) . $last_name) . rand(10, 99);
        $password_hash = password_hash('password123', PASSWORD_DEFAULT); // Mot de passe temporaire
        $status = 'pending'; // Statut par défaut
        
        // Traitement de la photo de profil
        $profile_photo = null;
        if(isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
            $upload_dir = "uploads/transporters/";
            
            // Créer le répertoire s'il n'existe pas
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $temp_file = $_FILES['profile_photo']['tmp_name'];
            $file_name = time() . '_' . $_FILES['profile_photo']['name'];
            $target_file = $upload_dir . $file_name;
            
            if(move_uploaded_file($temp_file, $target_file)) {
                $profile_photo = $target_file;
            }
        }
        
        // Traitement des photos du véhicule
        $vehicle_photos_json = null;
        if(isset($_FILES['vehicle_photos']) && !empty($_FILES['vehicle_photos']['name'][0])) {
            $upload_dir = "uploads/transporters/vehicles/";
            
            // Créer le répertoire s'il n'existe pas
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $vehicle_photos = [];
            $total_files = count($_FILES['vehicle_photos']['name']);
            
            for($i = 0; $i < $total_files; $i++) {
                if($_FILES['vehicle_photos']['error'][$i] == 0) {
                    $temp_file = $_FILES['vehicle_photos']['tmp_name'][$i];
                    $file_name = time() . '_' . $_FILES['vehicle_photos']['name'][$i];
                    $target_file = $upload_dir . $file_name;
                    
                    if(move_uploaded_file($temp_file, $target_file)) {
                        $vehicle_photos[] = $target_file;
                    }
                }
            }
            
            if(!empty($vehicle_photos)) {
                $vehicle_photos_json = json_encode($vehicle_photos);
            }
        }
        
        // Traitement des documents
        $documents_json = null;
        if(isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
            $upload_dir = "uploads/transporters/documents/";
            
            // Créer le répertoire s'il n'existe pas
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $documents = [];
            $total_files = count($_FILES['documents']['name']);
            
            for($i = 0; $i < $total_files; $i++) {
                if($_FILES['documents']['error'][$i] == 0) {
                    $temp_file = $_FILES['documents']['tmp_name'][$i];
                    $file_name = time() . '_' . $_FILES['documents']['name'][$i];
                    $target_file = $upload_dir . $file_name;
                    
                    if(move_uploaded_file($temp_file, $target_file)) {
                        $documents[] = $target_file;
                    }
                }
            }
            
            if(!empty($documents)) {
                $documents_json = json_encode($documents);
            }
        }
        
        // Vérifier si la table existe
        $check_table = $conn->query("SHOW TABLES LIKE 'transporters'");
        if($check_table->rowCount() == 0) {
            // La table n'existe pas, créons-la
            $create_table = "CREATE TABLE transporters (
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
                driver_license VARCHAR(50),
                experience_years INT(2) NOT NULL,
                vehicle_type VARCHAR(255),
                vehicle_model VARCHAR(100),
                vehicle_year INT(4),
                plate_number VARCHAR(50),
                passenger_capacity INT(2),
                languages VARCHAR(255),
                services VARCHAR(255),
                regions VARCHAR(255),
                rate_per_km DECIMAL(10,2),
                biography TEXT,
                profile_photo VARCHAR(255),
                vehicle_photos TEXT,
                documents TEXT,
                username VARCHAR(50) NOT NULL,
                password VARCHAR(255) NOT NULL,
                status VARCHAR(20) NOT NULL DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $conn->exec($create_table);
        }
        
        // Préparer la requête SQL
        $sql = "INSERT INTO transporters (first_name, last_name, birth_date, gender, address, city, phone, email, id_number, 
                driver_license, experience_years, vehicle_type, vehicle_model, vehicle_year, plate_number, passenger_capacity, 
                languages, services, regions, rate_per_km, biography, profile_photo, vehicle_photos, documents, username, password, status) 
                VALUES (:first_name, :last_name, :birth_date, :gender, :address, :city, :phone, :email, :id_number, 
                :driver_license, :experience_years, :vehicle_type, :vehicle_model, :vehicle_year, :plate_number, :passenger_capacity, 
                :languages, :services, :regions, :rate_per_km, :biography, :profile_photo, :vehicle_photos, :documents, :username, :password, :status)";
        
        $stmt = $conn->prepare($sql);
        
        // Associer les paramètres et exécuter la requête
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':birth_date', $birth_date);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_number', $id_number);
        $stmt->bindParam(':driver_license', $driver_license);
        $stmt->bindParam(':experience_years', $experience_years);
        $stmt->bindParam(':vehicle_type', $vehicle_type);
        $stmt->bindParam(':vehicle_model', $vehicle_model);
        $stmt->bindParam(':vehicle_year', $vehicle_year);
        $stmt->bindParam(':plate_number', $plate_number);
        $stmt->bindParam(':passenger_capacity', $passenger_capacity);
        $stmt->bindParam(':languages', $languages);
        $stmt->bindParam(':services', $services);
        $stmt->bindParam(':regions', $regions);
        $stmt->bindParam(':rate_per_km', $rate_per_km);
        $stmt->bindParam(':biography', $biography);
        $stmt->bindParam(':profile_photo', $profile_photo);
        $stmt->bindParam(':vehicle_photos', $vehicle_photos_json);
        $stmt->bindParam(':documents', $documents_json);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':status', $status);
        
        $stmt->execute();
        
        $success_message = "Félicitations ! Votre profil de transporteur a été enregistré avec succès. Votre demande est en cours d'examen. Un administrateur vous enverra vos identifiants de connexion par email une fois votre inscription validée.";
        
    } catch(PDOException $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
    
    $conn = null; // Fermer la connexion
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
                
                <!-- Permis et Documents -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-id-card"></i> Permis et Documents</h3>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="id_number" name="id_number" placeholder="Numéro d'identité">
                                <label for="id_number">Numéro d'identité (CNI, Passeport)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="driver_license" name="driver_license" placeholder="Numéro de permis de conduire" required>
                                <label for="driver_license" class="required">Numéro de permis de conduire</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="experience_years" name="experience_years" placeholder="Années d'expérience" min="0" required>
                        <label for="experience_years" class="required">Années d'expérience en conduite</label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="documents" class="form-label required">Documents d'identité et permis</label>
                        <input type="file" class="form-control" id="documents" name="documents[]" multiple required>
                        <div class="form-text">Téléchargez des copies de votre pièce d'identité, permis de conduire et autres documents pertinents</div>
                    </div>
                </div>
                
                <!-- Informations sur le véhicule -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-car"></i> Informations sur le véhicule</h3>
                    
                    <div class="mb-3">
                        <label class="form-label required">Type de véhicule</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="vehicle_type_sedan" name="vehicle_type[]" value="Berline">
                                    <label class="form-check-label" for="vehicle_type_sedan">Berline</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="vehicle_type_suv" name="vehicle_type[]" value="SUV">
                                    <label class="form-check-label" for="vehicle_type_suv">SUV</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="vehicle_type_van" name="vehicle_type[]" value="Minibus">
                                    <label class="form-check-label" for="vehicle_type_van">Minibus</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="vehicle_type_bus" name="vehicle_type[]" value="Bus">
                                    <label class="form-check-label" for="vehicle_type_bus">Bus</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="vehicle_type_taxi" name="vehicle_type[]" value="Taxi">
                                    <label class="form-check-label" for="vehicle_type_taxi">Taxi</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="vehicle_type_other" name="vehicle_type[]" value="Autre">
                                    <label class="form-check-label" for="vehicle_type_other">Autre</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="vehicle_model" name="vehicle_model" placeholder="Modèle du véhicule" required>
                                <label for="vehicle_model" class="required">Marque et modèle du véhicule</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="vehicle_year" name="vehicle_year" placeholder="Année du véhicule" min="1970" max="2025" required>
                                <label for="vehicle_year" class="required">Année du véhicule</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="plate_number" name="plate_number" placeholder="Numéro d'immatriculation" required>
                                <label for="plate_number" class="required">Numéro d'immatriculation</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="passenger_capacity" name="passenger_capacity" placeholder="Capacité de passagers" min="1" max="50" required>
                                <label for="passenger_capacity" class="required">Capacité de passagers</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="vehicle_photos" class="form-label required">Photos du véhicule</label>
                        <input type="file" class="form-control" id="vehicle_photos" name="vehicle_photos[]" multiple required>
                        <div class="form-text">Téléchargez au moins 3 photos de votre véhicule (extérieur et intérieur)</div>
                    </div>
                </div>
                
                <!-- Services proposés -->
                <div class="form-section">
                    <h3 class="section-title"><i class="fas fa-concierge-bell"></i> Services proposés</h3>
                    
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
                                    <input class="form-check-input" type="checkbox" id="lang_other" name="languages[]" value="Autre">
                                    <label class="form-check-label" for="lang_other">Autre</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Types de services</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="service_airport" name="services[]" value="Transfert aéroport">
                                    <label class="form-check-label" for="service_airport">Transfert aéroport</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="service_city" name="services[]" value="Transport en ville">
                                    <label class="form-check-label" for="service_city">Transport en ville</label>
                                </div>
                            </div>
                            <div class="col-md-4">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="service_city" name="services[]" value="Transport en ville">
            <label class="form-check-label" for="service_city">Transport en ville</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="service_intercity" name="services[]" value="Transport inter-villes">
            <label class="form-check-label" for="service_intercity">Transport inter-villes</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="service_tours" name="services[]" value="Visites touristiques">
            <label class="form-check-label" for="service_tours">Visites touristiques</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="service_events" name="services[]" value="Événements spéciaux">
            <label class="form-check-label" for="service_events">Événements spéciaux</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="service_business" name="services[]" value="Transport d'affaires">
            <label class="form-check-label" for="service_business">Transport d'affaires</label>
        </div>
    </div>
</div>
</div>

<div class="mb-3">
    <label class="form-label">Régions desservies</label>
    <div class="row">
        <div class="col-md-4">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="region_adjarra" name="regions[]" value="Adjarra">
                <label class="form-check-label" for="region_adjarra">Adjarra</label>
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
                <input class="form-check-input" type="checkbox" id="region_cotonou" name="regions[]" value="Cotonou">
                <label class="form-check-label" for="region_cotonou">Cotonou</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="region_parakou" name="regions[]" value="Parakou">
                <label class="form-check-label" for="region_parakou">Parakou</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="region_natitingou" name="regions[]" value="Natitingou">
                <label class="form-check-label" for="region_natitingou">Natitingou</label>
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
    <input type="number" class="form-control" id="rate_per_km" name="rate_per_km" placeholder="Tarif par kilomètre" step="0.01" min="0">
    <label for="rate_per_km">Tarif par kilomètre (FCFA)</label>
</div>
</div>

<!-- Profil et photo -->
<div class="form-section">
<h3 class="section-title"><i class="fas fa-image"></i> Photo de profil et biographie</h3>

<div class="mb-3">
    <label for="profile_photo" class="form-label">Photo de profil</label>
    <input type="file" class="form-control" id="profile_photo" name="profile_photo">
    <div class="form-text">Téléchargez une photo professionnelle pour votre profil</div>
</div>

<div class="mb-3">
    <label for="biography" class="form-label">Biographie</label>
    <textarea class="form-control" id="biography" name="biography" rows="4" placeholder="Parlez-nous un peu de vous et de votre expérience en tant que transporteur..."></textarea>
</div>
</div>

<!-- Conditions générales -->
<div class="form-section">
<h3 class="section-title"><i class="fas fa-file-contract"></i> Conditions générales</h3>

<div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
    <label class="form-check-label required" for="terms">J'accepte les conditions générales d'utilisation et je certifie que toutes les informations fournies sont véridiques et exactes.</label>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" class="form-check-input" id="data_consent" name="data_consent" required>
    <label class="form-check-label required" for="data_consent">J'accepte que mes données personnelles soient collectées et traitées dans le cadre de ce service.</label>
</div>
</div>

<!-- Informations supplémentaires et note -->
<div class="alert alert-info">
<h5><i class="fas fa-info-circle"></i> Note importante</h5>
<p>Après soumission de votre demande, notre équipe examinera votre profil. Le processus de validation peut prendre jusqu'à 48 heures ouvrables. Une fois votre inscription validée, vos identifiants de connexion vous seront envoyés par email.</p>
<p class="status-info"><strong>Statut initial :</strong> En attente de validation</p>
</div>

<div class="text-center">
<button type="submit" class="btn submit-btn"><i class="fas fa-paper-plane"></i> Soumettre ma demande</button>
</div>
</form>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
// Prévisualisation des photos du véhicule
document.getElementById('vehicle_photos').addEventListener('change', function(e) {
    const previewContainer = document.createElement('div');
    previewContainer.className = 'preview-images mt-3';
    
    const files = e.target.files;
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (!file.type.match('image')) continue;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-preview';
                previewContainer.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
        
        // Supprimer l'ancien conteneur de prévisualisation s'il existe
        const oldPreview = this.nextElementSibling.nextElementSibling;
        if (oldPreview && oldPreview.className === 'preview-images mt-3') {
            oldPreview.remove();
        }
        
        this.parentNode.appendChild(previewContainer);
    }
});

// Prévisualisation de la photo de profil
document.getElementById('profile_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file && file.type.match('image')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Supprimer l'ancienne prévisualisation s'il existe
            const oldPreview = document.querySelector('.profile-preview');
            if (oldPreview) {
                oldPreview.remove();
            }
            
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-preview profile-preview mt-2';
            document.getElementById('profile_photo').parentNode.appendChild(img);
        }
        reader.readAsDataURL(file);
    }
});

// Validation côté client pour s'assurer qu'au moins un type de véhicule est sélectionné
document.querySelector('form').addEventListener('submit', function(e) {
    const vehicleTypes = document.querySelectorAll('input[name="vehicle_type[]"]:checked');
    if (vehicleTypes.length === 0) {
        e.preventDefault();
        alert('Veuillez sélectionner au moins un type de véhicule.');
    }
});
</script>
</body>
</html>