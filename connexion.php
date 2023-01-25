<?php

require_once('inc/init.php');
$pagetitle = 'Connexion';

// Deconnexion
if( isset($_GET['action']) && $_GET['action'] == 'disconnect' ){
    unset($_SESSION['user']);
    // session_destroy();
    $_SESSION['message'] = 'Vous êtes deconnecté(e)';
    redirect('connexion.php');
}


// Si je suis connecté, aucun intérêt à être sur le formulaire de connexion
if (isConnected()) {
    redirect('compte.php');
}


if (!empty($_POST)) {

    $erreurs = array();

    if (empty($_POST['login']) || empty($_POST['mdp'])) {
        $erreurs[] = "Merci de remplir les deux champs";
    } else {

        $user = getUserByLogin($_POST['login']);
        //var_dump($user);
        if (!$user) {
            $erreurs[] = 'Erreur sur les identifiants';
        } elseif (!password_verify($_POST['mdp'], $user['mdp'])) {
            $erreurs[] = 'Erreur sur les identifiants';
        } else {
            // Tout est ok
            $_SESSION['user'] = $user;
            
            if(isset($_GET['redirect']) && $_GET['redirect'] == 'panier'){
                redirect(URL.'panier.php');
                // redirect(URL.'panier.php?action=valider');
            }
            else{
                redirect(URL);
            }
        }
    }
}



require_once('inc/header.php');
?>
<div class="container">
    <h1><i class="fas fa-power-off"></i>Se connecter</h1>

    <?php if (!empty($_SESSION['message'])) : ?>
        <div class="succes"><?php echo $_SESSION['message'] ?></div>
    <?php
        unset($_SESSION['message']); // destruction de variable
    endif; ?>

    <?php if (!empty($erreurs)) : ?>
        <div class="erreur"><?php echo implode('<br>', $erreurs) ?></div>
    <?php endif; ?>


    <form method="post" class="contact-form">
        <input type="text" id="login" name="login" placeholder="Votre login">
        <input type="password" id="mdp" name="mdp" placeholder="Votre mot de passe">
        <!-- <input type="submit" value="Se connecter"> -->
        <button type="submit">Se connecter</button>
    </form>
    <p>
        Pas encore de compte ? Vous pouvez en créer un en <a href="inscription.php" class="lien">cliquant ici</a>
    </p>

</div>
<?php
require_once('inc/footer.php');
