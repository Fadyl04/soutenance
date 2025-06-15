<?php
// Démarrer la session en début de fichier
session_start();

include 'database/db.php';

$pages = scandir('pages/');

if (isset($_GET['page']) && !empty($_GET['page']) && in_array($_GET['page'].'.php',$pages) ) {
    # code...
    $page=$_GET['page'];
}else {
    $page='home';
}

if ($page!='dashborad'  ) {


$pages_function = scandir('fonction/');

if (in_array($page.'_func.php',  $pages_function)) {
    include 'fonction/'.$page.'_func.php';
}
?>





<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourisme Bénin </title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="shortcut icon" href="img/io.jpg" type="image/x-icon">

   <?php
   include 'autre/link.php';
   ?>

    
</head>
<body>
    
    <?php
    include 'Headerf/header.php';
    ?>
    
    <div class="contenue">
        <?php
        include 'pages/'.$page.'.php';
    ?>




   
     <footer>
        <?php
        include 'Headerf/footer.php';
        ?>

     </footer>

     

   <?php
   include 'autre/lienpied.php';
   ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
  const percentage = document.querySelector('.loader-percentage');
  const loaderBar = document.querySelector('.loader-bar');
  const totalSteps = 100;
  const totalDuration = 30000; // 30 secondes
  let currentStep = 0;

  function updateLoader() {
    currentStep++;
    const progress = currentStep;

    // Mettre à jour le texte du pourcentage
    percentage.textContent = `${progress}%`;

    // Mettre à jour la largeur de la barre de progression
    loaderBar.style.width = `${progress}%`;

    if (currentStep < totalSteps) {
      setTimeout(updateLoader, totalDuration / totalSteps);
    } else {
      // Attendre un peu avant de masquer le préchargeur
      setTimeout(() => {
        document.getElementById('preloader').style.display = 'none';
      }, 1000);
    }
  }

  updateLoader();
});
</script>

<script src="//code.tidio.co/xox5pkwx1utlughbbw2m88thnszyxq49.js" async></script>


</body>
</html>
<?php
		}
		else{
			include 'pages/'.$page.'.php';


		}
?>	
