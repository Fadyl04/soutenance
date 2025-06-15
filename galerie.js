function viewAlbum(albumId) {
    // Utiliser Fetch API pour récupérer les images de l'album
    fetch(`get_album_images.php?album_id=${albumId}`)
        .then(response => response.json())
        .then(images => {
            const albumImagesContainer = document.getElementById('albumImagesContainer');
            const albumTitle = document.getElementById('albumModalTitle');
            
            // Effacer les images précédentes
            albumImagesContainer.innerHTML = '';
            
            // Mettre à jour le titre de l'album
            albumTitle.textContent = document.querySelector(`[data-album-id="${albumId}"] .card-title`).textContent;
            
            // Ajouter les images
            images.forEach(image => {
                const col = document.createElement('div');
                col.className = 'col-md-4 col-sm-6 mb-4';
                
                const img = document.createElement('img');
                img.src = `uploads/albums/${image.image_path}`;
                img.className = 'img-fluid rounded';
                img.alt = 'Image d\'album';
                
                col.appendChild(img);
                albumImagesContainer.appendChild(col);
            });
            
            // Afficher le modal
            const albumModal = new bootstrap.Modal(document.getElementById('albumModal'));
            albumModal.show();
        })
        .catch(error => {
            console.error('Erreur lors du chargement des images:', error);
            alert('Impossible de charger les images de l\'album');
        });
}