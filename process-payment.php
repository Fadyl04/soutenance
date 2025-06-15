<?php
// Connexion à la base de données
require_once 'database/db.php';

// Vérification de l'ID de l'événement
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$event_id = $_GET['id'];

// Récupération des détails de l'événement
$query = $db->prepare("SELECT * FROM events WHERE id = ?");
$query->execute([$event_id]);
$event = $query->fetch(PDO::FETCH_ASSOC);

// Redirection si l'événement n'existe pas
if (!$event) {
    header('Location: index.php');
    exit();
}

// Vérification de la disponibilité des billets
if ($event['number_available_event'] <= 0) {
    // Redirection avec message d'erreur si plus de billets disponibles
    header('Location: detail-event.php?id=' . $event_id . '&error=no_tickets');
    exit();
}

// Configuration de FedaPay avec votre clé API (à remplacer par vos propres clés)
$fedapay_public_key = 'pk_sandbox_xxxxxxxxxxxxxxxxxxxxxxxxx'; // À remplacer par votre clé publique FedaPay
$fedapay_env = 'sandbox'; // Utilisez 'live' pour la production

// Titre de la page
$page_title = 'Réservation pour ' . $event['label_event'] . ' - Événements au Bénin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #d4a017; /* Jaune plus sombre */
            --secondary-color: #b8870b; /* Teinte plus sombre du jaune */
            --accent-color: #e6b800; /* Jaune vif pour accentuation */
            --dark-color: #4a4112; /* Brun-jaune foncé */
            --light-color: #f8f5e6; /* Jaune très clair presque blanc */
            --text-color: #3a3100; /* Couleur de texte foncée */
            --background-color: #f7f3dc; /* Fond légèrement teinté de jaune */
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-color);
            background-color: var(--background-color);
        }
        
        .breadcrumbs {
            padding: 30px 0;
            background-color: #f0e9c0; /* Jaune très pâle */
            border-bottom: 1px solid #e6d580;
        }
        
        .breadcrumbs h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .breadcrumbs ol {
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 14px;
        }
        
        .breadcrumbs ol li + li {
            padding-left: 10px;
        }
        
        .breadcrumbs ol li + li::before {
            display: inline-block;
            padding-right: 10px;
            color: #88750d;
            content: "/";
        }
        
        .payment-section {
            padding: 60px 0;
        }
        
        .event-summary {
            background-color: #f4edc3;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(150, 120, 0, 0.1);
        }
        
        .payment-form {
            background-color: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(150, 120, 0, 0.1);
        }
        
        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
        }
        
        .btn-primary:hover {
            background-color: #b8870b !important;
            border-color: #b8870b !important;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(212, 160, 23, 0.25);
        }
        
        .payment-method {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #e6d580;
            border-radius: 10px;
            background-color: #f8f5e6;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .payment-method:hover, .payment-method.active {
            background-color: #f0e9c0;
            border-color: var(--primary-color);
        }
        
        .payment-method img {
            height: 40px;
            margin-right: 15px;
        }
        
        .payment-method-icon {
            font-size: 24px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary-color);
        }
        
        .ticket-info {
            padding: 15px;
            background-color: #f0e9c0;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 4px solid var(--primary-color);
        }
    </style>
</head>
<body>

<main id="main">
    <!-- Breadcrumbs -->
    <section class="breadcrumbs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <ol>
                    <li><a href="index.php" style="color: var(--primary-color); text-decoration: none;">Accueil</a></li>
                    <li><a href="index.php?page=events" style="color: var(--primary-color); text-decoration: none;">Événements</a></li>
                    <li><a href="detail-event.php?id=<?php echo $event_id; ?>" style="color: var(--primary-color); text-decoration: none;"><?php echo $event['label_event']; ?></a></li>
                    <li style="color: #6c6012;">Réservation</li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Payment Section -->
    <section class="payment-section">
        <div class="container">
            <?php if(isset($_GET['error']) && $_GET['error'] == 'no_tickets'): ?>
            <div class="alert alert-danger mb-4" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i> Désolé, il n'y a plus de billets disponibles pour cet événement.
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-lg-7">
                    <h2 class="mb-4" style="color: var(--dark-color);">Finaliser votre réservation</h2>
                    
                    <div class="payment-form">
                        <form id="payment-form" method="POST" action="confirm-booking.php">
                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                            
                            <h4 class="mb-4" style="color: var(--dark-color);">Informations personnelles</h4>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="firstname" class="form-label">Prénom</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="lastname" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                                <div class="col-12">
                                    <label for="quantity" class="form-label">Nombre de billets</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?php echo $event['number_available_event']; ?>" value="1" required>
                                    <small class="text-muted">Maximum disponible: <?php echo $event['number_available_event']; ?> billets</small>
                                </div>
                            </div>
                            
                            <h4 class="mb-4" style="color: var(--dark-color);">Méthode de paiement</h4>
                            
                            <div id="payment-methods">
                                <div class="payment-method active" data-method="mobile_money">
                                    <div class="payment-method-icon">
                                        <i class="bi bi-phone"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Mobile Money</h5>
                                        <p class="mb-0 text-muted small">MTN Mobile Money, Moov Money, etc.</p>
                                    </div>
                                </div>
                                
                                <div class="payment-method" data-method="card">
                                    <div class="payment-method-icon">
                                        <i class="bi bi-credit-card"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Carte bancaire</h5>
                                        <p class="mb-0 text-muted small">Visa, Mastercard</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4 mt-4" id="mobile-money-form">
                                <div class="mb-3">
                                    <label for="mobile-number" class="form-label">Numéro Mobile Money</label>
                                    <input type="tel" class="form-control" id="mobile-number" name="mobile_number" placeholder="Ex: 97123456">
                                    <small class="text-muted">Entrez le numéro qui recevra la demande de paiement</small>
                                </div>
                                <div class="mb-3">
                                    <label for="operator" class="form-label">Opérateur</label>
                                    <select class="form-select" id="operator" name="operator">
                                        <option value="mtn">MTN Mobile Money</option>
                                        <option value="moov">Moov Money</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-4 mt-4 d-none" id="card-form">
                                <div class="mb-3">
                                    <label for="card-holder" class="form-label">Nom du titulaire</label>
                                    <input type="text" class="form-control" id="card-holder" name="card_holder">
                                </div>
                                <div class="mb-3">
                                    <label for="card-number" class="form-label">Numéro de carte</label>
                                    <input type="text" class="form-control" id="card-number" name="card_number" placeholder="XXXX XXXX XXXX XXXX">
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="expiry-date" class="form-label">Date d'expiration</label>
                                        <input type="text" class="form-control" id="expiry-date" name="expiry_date" placeholder="MM/YY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" value="" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        J'accepte les conditions générales de vente
                                    </label>
                                </div>

                                <button type="button" id="pay-button" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-lock me-2"></i> Payer <span id="total-amount"><?php echo number_format($event['amount_event'], 0, ',', ' '); ?></span> FCFA
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="event-summary">
                        <h3 class="mb-4" style="color: var(--dark-color);">Résumé de la commande</h3>
                        
                        <div class="d-flex align-items-center mb-4">
                            <?php if(!empty($event['picture_event'])): ?>
                                <img src="uploads/events/<?php echo $event['picture_event']; ?>" alt="<?php echo $event['label_event']; ?>" class="img-fluid rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                            <?php else: ?>
                                <img src="/api/placeholder/80/80" alt="<?php echo $event['label_event']; ?>" class="img-fluid rounded me-3">
                            <?php endif; ?>
                            <div>
                                <h5 class="mb-1"><?php echo $event['label_event']; ?></h5>
                                <p class="mb-0 text-muted">
                                    <i class="bi bi-geo-alt"></i> <?php echo $event['localisation']; ?>
                                </p>
                                <p class="mb-0 text-muted">
                                    <i class="bi bi-calendar"></i> <?php echo (new DateTime($event['start_date']))->format('d/m/Y à H:i'); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="ticket-info mb-4">
                            <h5 class="mb-3" style="color: var(--dark-color);"><i class="bi bi-ticket-perforated me-2"></i>Détails du billet</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Prix unitaire:</span>
                                <span><?php echo number_format($event['amount_event'], 0, ',', ' '); ?> FCFA</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Quantité:</span>
                                <span id="ticket-quantity">1</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span id="total-price"><?php echo number_format($event['amount_event'], 0, ',', ' '); ?> FCFA</span>
                            </div>
                        </div>
                        
                        <div class="mt-4 bg-white p-3 rounded">
                            <h5 class="mb-3" style="color: var(--dark-color);"><i class="bi bi-shield-check me-2"></i>Paiement sécurisé</h5>
                            <p class="small text-muted mb-0">Vos informations de paiement sont sécurisées. Les données sont chiffrées et transmises en toute sécurité via notre prestataire de paiement.</p>
                        </div>
                        
                        <div class="mt-4 d-flex justify-content-center">
                            <a href="detail-event.php?id=<?php echo $event_id; ?>" class="btn btn-outline-dark">
                                <i class="bi bi-arrow-left me-2"></i> Retour à l'événement
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.fedapay.com/checkout.js?v=1.1.7"></script>
<script>
// Initialisation de FedaPay
FedaPay.init({
    public_key: '<?php echo $fedapay_public_key; ?>',
    environment: '<?php echo $fedapay_env; ?>',
    transaction: {
        amount: <?php echo $event['amount_event']; ?>,
        description: 'Réservation pour <?php echo addslashes($event['label_event']); ?>',
        callback_url: 'https://votresite.com/payment-success.php'
    }
});

// Gestion du changement de méthode de paiement
document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', function() {
        // Suppression de la classe active sur tous les éléments
        document.querySelectorAll('.payment-method').forEach(m => {
            m.classList.remove('active');
        });
        
        // Ajout de la classe active sur l'élément cliqué
        this.classList.add('active');
        
        // Affiche le formulaire correspondant
        const selectedMethod = this.getAttribute('data-method');
        if (selectedMethod === 'mobile_money') {
            document.getElementById('mobile-money-form').classList.remove('d-none');
            document.getElementById('card-form').classList.add('d-none');
        } else if (selectedMethod === 'card') {
            document.getElementById('mobile-money-form').classList.add('d-none');
            document.getElementById('card-form').classList.remove('d-none');
        }
    });
});

// Calcul du prix total lors du changement de quantité
document.getElementById('quantity').addEventListener('change', function() {
    const quantity = parseInt(this.value);
    const unitPrice = <?php echo $event['amount_event']; ?>;
    const totalPrice = quantity * unitPrice;
    
    document.getElementById('ticket-quantity').textContent = quantity;
    document.getElementById('total-price').textContent = totalPrice.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('total-amount').textContent = totalPrice.toLocaleString('fr-FR');
    
    // Mise à jour du montant dans FedaPay
    FedaPay.setTransactionAmount(totalPrice);
});

// Gestion du bouton de paiement
document.getElementById('pay-button').addEventListener('click', function() {
    // Validation du formulaire
    const form = document.getElementById('payment-form');
    if (form.checkValidity()) {
        // Collecte des données du formulaire
        const formData = {
            firstname: document.getElementById('firstname').value,
            lastname: document.getElementById('lastname').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            quantity: document.getElementById('quantity').value,
        };
        
        // Si mobile money est sélectionné
        if (document.querySelector('.payment-method.active').getAttribute('data-method') === 'mobile_money') {
            formData.mobile_number = document.getElementById('mobile-number').value;
            formData.operator = document.getElementById('operator').value;
            
            // Ouvrir la fenêtre de paiement FedaPay
            FedaPay.mobileMoneyCheckout({
                phone_number: {
                    number: formData.mobile_number,
                    country: 'BJ' // Code pays pour le Bénin
                }
            }).then(response => {
                // Redirection vers la page de confirmation après paiement réussi
                window.location.href = 'confirm-booking.php?event_id=' + <?php echo $event_id; ?> + '&transaction_id=' + response.transaction.id;
            }).catch(error => {
                console.error('Erreur de paiement:', error);
                alert('Une erreur est survenue lors du paiement. Veuillez réessayer.');
            });
        }
        // Si carte bancaire est sélectionnée
        else if (document.querySelector('.payment-method.active').getAttribute('data-method') === 'card') {
            // Ouvrir la fenêtre de paiement FedaPay pour carte bancaire
            FedaPay.creditCardCheckout().then(response => {
                // Redirection vers la page de confirmation après paiement réussi
                window.location.href = 'confirm-booking.php?event_id=' + <?php echo $event_id; ?> + '&transaction_id=' + response.transaction.id;
            }).catch(error => {
                console.error('Erreur de paiement:', error);
                alert('Une erreur est survenue lors du paiement. Veuillez réessayer.');
            });
        }
    } else {
        // Affichage des messages de validation
        form.reportValidity();
    }
});
</script>
</body>
</html>