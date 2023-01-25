<?php

require_once('../inc/init.php');

// Si l'utilisateur n'est PAS admin, il est redirigé vers connexion
if (!isAdmin()) {
    redirect('/connexion.php');
}

// Suppression
if (
    isset($_GET['action']) &&
    $_GET['action'] == 'del'
    && !empty($_GET['id_film'])
    && is_numeric($_GET['id_film'])
) {

    $statement = safeSQL("SELECT * FROM films WHERE id_film=:id_film", array(
        'id_film' => $_GET['id_film']
    ));
    if ($statement->rowCount() ==  1) {
        // le film existe
        $film = $statement->fetch();
        $affiche = $film['affiche'];

        $chemin = $_SERVER['DOCUMENT_ROOT'] . URL . 'affiche/';
        //        c:/wamp64/www             /marvelphp/  affiches/
        //        http://localhost
        $fichier = $chemin . $affiche;
        if (!empty($affiche) && file_exists($fichier)) {
            // suppression du fichier
            unlink($fichier);
        }
        // Suppression en BDD
        safeSQL("DELETE FROM films WHERE id_film=:id_film", array(
            'id_film' => $_GET['id_film']
        ));
        $_SESSION['message'] = 'Le film ' . $film['titre'] . ' a été supprimé';
        redirect($_SERVER['PHP_SELF']);
    } else {
        $erreurs = array("Le film n'existe pas");
    }
}

// Chargement du film à éditer
if (isset($_GET['action']) && $_GET['action'] == 'edit' && !empty($_GET['id_film']) && is_numeric($_GET['id_film'])) {

    $statement = safeSQL("SELECT * FROM films WHERE id_film=:id_film", array(
        'id_film' => $_GET['id_film']
    ));
    if ($statement->rowCount() == 1) {
        $currentmovie = $statement->fetch();
    } else {
        redirect($_SERVER['PHP_SELF']);
    }
}



// Traitement de l'insertion d'un film ou la mise à jour d'un film existant
if (!empty($_POST)) {

    // var_dump($_POST);
    // var_dump($_FILES);

    $erreurs = array();

    if (empty($_POST['titre'])) {
        $erreurs[] = 'Merci de saisir un titre';
    }
    if (empty($_POST['annee'])) {
        $erreurs[] = 'Merci de saisir une année';
    } else {
        $pattern = '#^[0-9]{4}$#';
        if (!preg_match($pattern, $_POST['annee']) || $_POST['annee'] < 1901 || $_POST['annee'] > date('Y')) {
            $erreurs[] = 'Date incorrecte';
        }
    }


    if (empty($_POST['realisateur'])) {
        $erreurs[] = 'Merci de saisir un réalisateur';
    }

    if (empty($_POST['duree'])) {
        $erreurs[] = 'Merci de saisir une durée';
    } else {
        if ($_POST['duree'] < 1) {
            $erreurs[] = 'La durée doit être égale ou supérieure à 1 minute';
        }
    }

    if (empty($_POST['prix'])) {
        $erreurs[] = 'Merci de saisir un prix';
    } else {
        if ($_POST['prix'] < 0) {
            $erreurs[] = 'Le prix ne peut pas être négatif';
        }
    }

    if (empty($_POST['code_yt'])) {
        $erreurs[] = 'Merci de saisir le code Youtube de la bande annonce';
    }
    if (empty($_POST['synopsis'])) {
        $erreurs[] = 'Merci de saisir un synopsis';
    }

    // Ni fichier choisi ni affiche actuelle renseignée
    if (empty($_FILES['affiche']['name']) && empty($_POST['affiche'])) {
        $erreurs[] = 'Merci de choisir une affiche';
    } else {

        // on vérifie les extensions si j'ai un fichier joint
        if ( !empty($_FILES['affiche']['name']) ) {

            $types_autorises = array('image/jpeg', 'image/png');
            if (!in_array($_FILES['affiche']['type'], $types_autorises)) {
                $erreurs[]  = 'Format de fichier incorrect. Merci de choisir une image JPEG ou PNG';
            }

        }
    }


    // TEST FINAL
    if (empty($erreurs)) {
        //  tout est bon

        // Si nouveau fichier on l'upload
        if ( !empty($_FILES['affiche']['name']) ) {
            // Copie physique de l'image dans le répertoire affiches
            $nomfichier = uniqid() . '_' . $_FILES['affiche']['name'];
            $chemin = $_SERVER['DOCUMENT_ROOT']."../affiche/"; 
            move_uploaded_file($_FILES['affiche']['tmp_name'], $chemin . $nomfichier);

            // suppression de l'ancienne affiche en cas d'édition
            if(isset($_POST['affiche'])){
                unlink($chemin . $_POST['affiche']);
            }
            //  Insertion en BDD

            $_POST['affiche'] = $nomfichier;
        }

        if (isset($currentmovie)) {

            $_POST['id_film'] = $_GET['id_film'];
            safeSQL("UPDATE films
            SET titre = :titre,
            prix = :prix,
            duree = :duree,
            synopsis = :synopsis,
            annee = :annee,
            realisateur = :realisateur,
            code_yt = :code_yt,
            affiche = :affiche
            WHERE id_film = :id_film
            ",$_POST);
            $_SESSION['message'] = 'Film mis à jour avec succès';

        } else {
            safeSQL("INSERT INTO films VALUES (NULL, :titre,:prix,:duree,:synopsis,:annee,:realisateur,:code_yt,:affiche)", $_POST);
            $_SESSION['message'] = 'Film ajouté avec succès';
        }

        redirect($_SERVER['PHP_SELF']);
    }
}




$statement = safeSQL("SELECT * FROM films ORDER BY titre");
$movies = $statement->fetchAll();

$pagetitle = 'Gestion des films';
require_once('../inc/header.php');
?>
<div class="container">
    <h1> <i class="fas fa-video"></i> Gestion des films</h1>

    <?php if (!empty($_SESSION['message'])) : ?>
        <div class="succes"><?php echo $_SESSION['message'] ?></div>
    <?php
        unset($_SESSION['message']); // destruction de variable
    endif; ?>

    <?php if (!empty($erreurs)) : ?>
        <div class="erreur"><?php echo implode('<br>', $erreurs) ?></div>
    <?php endif; ?>


    <?php if (
        isset($_GET['action']) &&
        ($_GET['action'] == 'addmovie' || $_GET['action'] == 'edit')
    ) :
    ?>

        <a href="<?php echo $_SERVER['PHP_SELF'] ?>" class="bouton">Retour à la liste des films</a>

        <form method="post" class="contact-form" enctype="multipart/form-data">

            <div id="formmovies">
                <div class="width-50">
                    <input type="text" id="titre" name="titre" placeholder="Titre du film" value="<?php echo $_POST['titre'] ?? $currentmovie['titre'] ?? '' ?>">
                    <input type="number" id="annee" name="annee" placeholder="Année" min="1901" max="<?php echo date('Y'); ?>" value="<?php echo $_POST['annee'] ?? $currentmovie['annee'] ?? date('Y') ?>">
                    <input type="text" id="realisateur" name="realisateur" placeholder="Réalisateur" value="<?php echo $_POST['realisateur'] ?? $currentmovie['realisateur'] ?? '' ?>">
                    <input type="number" id="duree" name="duree" placeholder="Durée" min="1" value="<?php echo $_POST['duree'] ?? $currentmovie['duree'] ?? '' ?>">
                    <input type="number" id="prix" name="prix" step="0.01" placeholder="Prix" min="0" value="<?php echo $_POST['prix']  ?? $currentmovie['prix'] ?? '' ?>">

                    <input type="text" id="code_yt" name="code_yt" placeholder="Code Youtube Bande annonce" value="<?php echo $_POST['code_yt']  ?? $currentmovie['code_yt'] ?? '' ?>">
                    <textarea id="synopsis" name="synopsis" placeholder="Synopsis"><?php echo $_POST['synopsis']  ?? $currentmovie['synopsis'] ?? '' ?></textarea>
                </div>
                <div class="width-50">
                    <label for="affiche" id="choixaffiche">
                        <img src="<?php echo (isset($currentmovie)) ? '../affiche/' . $currentmovie['affiche'] : '../images/placeholder-450x600.png'; ?>" id="preview">
                    </label>
                    <input type="file" id="affiche" name="affiche" accept="image/jpeg,image/png">
                    <?php if (isset($currentmovie)) : ?>
                        <input type="hidden" name="affiche" value="<?php echo $currentmovie['affiche'] ?>">
                    <?php endif; ?>

                </div>
            </div>
            <button type="submit">Enregistrer</button>


        </form>

    <?php else : ?>

        <a href="?action=addmovie" class="bouton">Ajouter un film</a>

        <table id="movies">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Titre</th>
                    <th class="width-20">Affiche</th>
                    <th>Prix</th>
                    <th>Durée</th>
                    <th>Année</th>
                    <th><i class="far fa-edit"></i></th>
                    <th><i class="fas fa-trash"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($movies)) :
                    foreach ($movies as $movie) :
                ?>
                        <tr>
                            <td><?php echo $movie['id_film'] ?></td>
                            <td><?php echo $movie['titre'] ?></td>
                            <td> <img src="../affiche/<?php echo $movie['affiche'] ?>" alt="<?php echo $movie['titre'] ?>"></td>
                            <td><?php echo number_format($movie['prix'], 2, ',', ' ');
                                // number_format(nombre à formater,nb de décimales, séparateur décimales, séparateur milliers)                        
                                ?> €</td>
                            <td><?php echo $movie['duree'] ?> min</td>
                            <td><?php echo $movie['annee'] ?></td>
                            <td>
                                <a href="?action=edit&id_film=<?php echo $movie['id_film'] ?>"><i class="far fa-edit"></i></a>
                            </td>
                            <td>
                                <a href="?action=del&id_film=<?php echo $movie['id_film'] ?>" class="confirmdel"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php
                    endforeach;
                else : ?>
                    <tr>
                        <td colspan="8">Pas encore de film enregistré</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    <?php endif; ?>


</div>

<?php
require_once('../inc/footer.php');
