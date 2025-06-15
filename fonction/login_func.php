<?php
    /*session_start();
    if (isset($_SESSION['admin'])) {
        header("Location:Admin/index.php");
        exit;
    }
    if (isset($_POST['valider'])) {
        // code...
        $email=$_POST['username'];
        $password=$_POST['password'];

        $r='SELECT * FROM admins WHERE Email=:email';
        $re=$db->prepare($r);
        $re->bindValue(':email',$email);
        $re->execute();

        $row=$re->fetch();

        if (!$row) {
            // code...
            echo "aucun utilisateur présent ";
            
        }else{
            
            if ($row['Email']==$email && $row['Password']==$password) {
            // code...
            $_SESSION['admin']=$email;
            header('Location:Admin/index.php');
            }else{
                echo '<script>alert("Vous n\'êtes pas autorisé à effectuer cette action.");';
                echo 'window.location.href = "index.php?page=login";</script>';
                exit;
            }
        }
    }*/

 /* session_start();
// Redirection si déjà connecté
if (isset($_SESSION['admin'])) {
    header("Location: Admin/index.php");
    exit;
}

// Connexion à la base de données (assurez-vous que $db est bien défini)
require_once 'db.php'; // ajustez selon votre projet

if (isset($_POST['valider'])) {
    $email = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Préparation de la requête
    $r = 'SELECT * FROM admins WHERE Email = :email';
    $re = $db->prepare($r);
    $re->bindValue(':email', $email);
    $re->execute();

    $row = $re->fetch();

    if (!$row) {
        // Aucun utilisateur trouvé
        $_SESSION['error'] = "Aucun utilisateur présent avec cet email.";
        header("Location: index.php?page=login");
        exit;
    } else {
        // Vérification du mot de passe
        if ($row['Password'] === $password) { // Remplacer par password_verify() si hash
            $_SESSION['admin'] = $email;
            header("Location: Admin/index.php");
            exit;
        } else {
            $_SESSION['error'] = "Mot de passe incorrect.";
            header("Location: index.php?page=login");
            exit;
        }
    }
} */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['admin'])) {
    header("Location: Admin/index.php");
    exit;
}

if (isset($_POST['valider'])) {
    $email = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $db->prepare("SELECT * FROM admins WHERE Email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $admin = $stmt->fetch();

    if (!$admin) {
        $_SESSION['error'] = "Aucun utilisateur trouvé.";
        header("Location: index.php?page=login");
        exit;
    }

    if ($admin['Password'] === $password) {
        $_SESSION['admin'] = $email;
        header("Location: Admin/index.php");
        exit;
    } else {
        $_SESSION['error'] = "Mot de passe incorrect.";
        header("Location: index.php?page=login");
        exit;
    }
}


?>



