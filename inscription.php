<?php

require_once('inc/init.php');

// Si je suis connecté, aucun intérêt à être sur le formulaire d'inscription
if (isConnected()) {
    redirect('compte.php');
}

// Traitement du formulaire
if (!empty($_POST)) { // l'utilisateur a cliquer sur le bouton de soumission

    $erreurs = array();

    if (empty($_POST['login'])) {
        $erreurs[] = 'Merci de choisir un login';
    } else {
        if (getUserByLogin($_POST['login'])) {
            $erreurs[] = "Ce login est indisponible, merci d'en choisir un autre";
        }
    }

    if(empty($_POST['mdp'])){
        $erreurs[] = 'Merci de saisir un mot de passe';
    }
    else{
        /*
          (?=.*[a-z]) impose la présence d'au moins 1 minuscule
          (?=.*[\_\!\@\-\#\*]) impose 1 des caractères spéciaux
          [\w\!\@\-\#\*] caractères autorisés a-zA-Z0-9_ et !@-#*
          {8,15} Longueur de l'expression (entre 8 et 15)
        */

        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\_\!\@\-\#\*])[\w\!\@\-\#\*]{8,15}$/';

        if(!preg_match($pattern,$_POST['mdp'])){
            $erreurs[] = 'Votre mot de passe doit comporter 8 à 15 caractères et contenir au moins 1 minuscule, 1 majuscule, 1 chiffre et 1 caractère spécial (_!@-#*)';
        }

    }

    if(empty($_POST['email'])){
        $erreurs[] = 'Merci de saisir votre email';
    }
    else{
        if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
            $erreurs[] = 'Adresse email invalide';
        }
    }

    if(empty($_POST['nom'])){
        $erreurs[] = 'Merci de saisir votre nom';
    }
    if(empty($_POST['prenom'])){
        $erreurs[] = 'Merci de saisir votre prénom';
    }
    if(empty($_POST['date_de_naissance'])){
        $erreurs[] = 'Merci de saisir votre prénom';
    }
    else{
       $date_decoupee = explode('-',$_POST['date_de_naissance']);
       if(count($date_decoupee) != 3 ){
            $erreurs[]  = 'Date de naissance incorrecte';
       }
       else{
           list($annee,$mois,$jour) = $date_decoupee;
           if(!checkdate($mois,$jour,$annee)){
            $erreurs[]  = 'Date de naissance incorrecte';
           }
       }
    }

    if(empty($erreurs)){
        // Tout est OK

        $_POST['mdp'] = password_hash($_POST['mdp'],PASSWORD_DEFAULT);

        safeSQL("INSERT INTO clients VALUES (NULL,:nom,:prenom,:date_de_naissance,:email,:login,:mdp,0)",$_POST);
        $_SESSION['message'] = "Vous êtes bien inscrit sur notre site. Vous pouvez vous connecter";
        redirect("connexion.php");

    }



}


$pagetitle = 'Inscription';
require_once('inc/header.php');
?>
<div class="container">
    <h1><i class="fas fa-user-edit"></i>S'inscrire</h1>

    <?php if (!empty($erreurs)) : ?>
        <div class="erreur"><?php echo implode('<br>',$erreurs) ?></div>        
    <?php endif; ?>

    <form method="post" class="contact-form">
        <fieldset>
            <legend>Identifiants</legend>

            <input type="text" id="login" name="login" placeholder="Votre login *" value="<?php echo $_POST['login'] ?? '' ?>">

            <input type="password" id="mdp" name="mdp" placeholder="Votre mot de passe *">
            <div class="pattern">
                (
                    <span id="longueur">8 à 15 caractères</span>,
                    <span id="minuscule">au moins 1 minuscule</span>,
                    <span id="majuscule">au moins 1 majuscule</span>,
                    <span id="chiffre">au moins 1 chiffre</span>,
                    <span id="special">au moins 1 caractère spécial _!@-#*</span>
                )
            </div>

            <input type="email" id="email" name="email" placeholder="Votre courriel *" value="<?php echo $_POST['email'] ?? '' ?>">

        </fieldset>

        <fieldset>
            <legend>Informations personnelles</legend>
            <input type="text" id="nom" name="nom" placeholder="Votre nom *" value="<?php echo $_POST['nom'] ?? '' ?>">
            <input type="text" id="prenom" name="prenom" placeholder="Votre prénom *" value="<?php echo $_POST['prenom'] ?? '' ?>">
            <input type="date" id="date_de_naissance" name="date_de_naissance" value="<?php echo $_POST['date_de_naissance'] ?? '' ?>">
        </fieldset>

        <button type="submit">S'inscrire</button>
    </form>
</div>
<?php
require_once('inc/footer.php');
