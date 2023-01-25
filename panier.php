<?php

require_once('inc/init.php');

if (isset($_GET['action']) && $_GET['action'] == 'del' && !empty($_GET['id_film']) && is_numeric($_GET['id_film'])) {

    retraitPanier($_GET['id_film']);
    redirect($_SERVER['PHP_SELF']);
}

if (isset($_GET['action']) && $_GET['action'] == 'vider') {
    viderPanier();
    redirect($_SERVER['PHP_SELF']);
}

if (isset($_GET['action']) && $_GET['action'] == 'valider') {

    if( !isConnected() ){
        $_SESSION['message'] = 'Pour valider votre commande, merci de vous <a href="'.URL.'connexion.php?redirect=panier">connecter</a>';
    }
    else{
        // Valider le panier
        // Insertion dans commandes
        safeSQL(" INSERT INTO commande VALUES (NULL,:id_client,:total,NOW()) ",array(
            'id_client' => $_SESSION['user']['id_client'],
            'total' => str_replace(',','.',montantPanier())
        ));

        // On récupère l'id de la commande que l'on vient d'insérer
        $id_commande = $pdo->lastInsertId();
        // Insertion dans details_commandes

        foreach($_SESSION['panier']['id_films'] as $index => $value){

            safeSQL("INSERT INTO details_commande VALUES (NULL,:id_commande,:id_film,:prix)",array(
                'id_commande' => $id_commande,
                'id_film' => $value,
                'prix' => $_SESSION['panier']['prix'][$index]
            ));
        }
        // on vide le panier après insertion
        viderPanier();
        $_SESSION['message'] = "Votre commande a été validée, elle a reçu le numéro $id_commande";
        redirect(URL);
    }

}





$pagetitle = 'Panier';
require_once('inc/header.php');
?>
<div class="container">
    <h1><i class="fas fa-shopping-cart"></i> Panier</h1>

    <?php if (!empty($_SESSION['message'])) : ?>
        <div class="succes"><?php echo $_SESSION['message'] ?></div>
    <?php
        unset($_SESSION['message']); // destruction de variable
    endif; ?>

    <?php if (empty($_SESSION['panier'])) : ?>
        <p>Votre panier est vide <i class="far fa-frown"></i></p>
    <?php else : ?>
        <table id="panier">
            <thead>
                <tr>
                    <th class="width-20">Affiche</th>
                    <th>Titre</th>
                    <th>Prix</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($_SESSION['panier']['id_films'] as $id_film) :
                    $statement = safeSQL("SELECT * FROM films WHERE id_film=:id_film", array(
                        'id_film' => $id_film
                    ));
                    $movie = $statement->fetch();
                ?>

                    <tr>
                        <td><img src="<?php echo 'affiche/' . $movie['affiche'] ?>" alt="<?php echo $movie['titre'] ?>"></td>
                        <td>
                            <?php echo $movie['titre'] ?>
                            <a href="?action=del&id_film=<?php echo $movie['id_film'] ?>" class="confirmdel"><i class="fas fa-trash"></i></a>
                        </td>
                        <td><?php echo number_format($movie['prix'], 2, ',', ' ') ?> €</td>
                    </tr>

                <?php
                endforeach;
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total </td>
                    <td><?php echo montantPanier() ?>€</td>
                </tr>
            </tfoot>
        </table>
        <div class="enligne">
            <a href="?action=vider" class="bouton confirmvide">Vider le panier</a>
            <a href="?action=valider" class="bouton">Valider</a>
       
        </div>
    <?php endif; ?>
</div>
<?php
require_once('inc/footer.php');
