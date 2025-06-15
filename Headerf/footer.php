<footer class="footer">
    <div class="footer-content">
        <div class="footer-links">
            <a href="index.php?page=home">Accueil</a>
            <a href="index.php?page=home#destinations">Destinations</a>
            <a href="index.php?page=home#attractions">Attractions</a>
            <a href="index.php?page=home#contact">Contact</a>
        </div>
        
        <div class="social-icons">
            <a href="https://www.facebook.com/TourismeBenin" class="social-icon" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-facebook"></i>
            </a>
            <a href="https://x.com/TourismeBenin" class="social-icon" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-twitter"></i>
            </a>
            <a href="https://www.instagram.com/tourisme_benin" class="social-icon" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-instagram"></i>
            </a>
            <a href="https://www.youtube.com/TourismeBenin" class="social-icon" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-youtube"></i>
            </a>
        </div>
        
        <p class="footer-text">&copy; 2025 Tourisme Bénin. Tous droits réservés.</p>
        
        <div class="flag-bar"></div>
    </div>
</footer>

<style>
.footer {
    background-color: #1a4d2e; /* Vert foncé évoquant la nature béninoise */
    padding: 30px 20px;
    text-align: center;
    color: #fff;
    margin-top: 40px;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
}

.footer-links {
    margin-bottom: 20px;
}

.footer-links a {
    color: #fff;
    text-decoration: none;
    margin: 0 15px;
    font-weight: 600;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: #f8c537; /* Jaune ocre, rappelant le soleil africain */
    text-decoration: none;
}

.social-icons {
    margin-bottom: 20px;
}

.social-icon {
    font-size: 1.8em;
    color: #fff;
    margin: 0 12px;
    transition: all 0.3s;
}

.social-icon:hover {
    transform: scale(1.2);
    color: #f8c537; /* Même couleur que pour les liens */
}

.footer-text {
    margin-bottom: 15px;
    font-size: 0.95em;
    color: rgba(255, 255, 255, 0.85);
}

.flag-bar {
    height: 6px;
    background: linear-gradient(to right, #008751 33%, #fcd116 33%, #fcd116 66%, #ce1126 66%); /* Couleurs du drapeau béninois */
    width: 100%;
    border-radius: 3px;
}

@media (max-width: 768px) {
    .footer-links a {
        display: block;
        margin: 10px 0;
    }
    
    .social-icons {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .social-icon {
        margin: 10px;
    }
}
</style>