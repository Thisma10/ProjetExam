<?php

require_once('inc/init.php');
$pagetitle = 'Mes films';

$statement  = safeSQL("
SELECT com.id_commande, com.total, com.date_commande, det.id_film, det.prix, films.titre
FROM commande com
INNER JOIN details_commande det ON det.id_commande = com.id_commande
INNER JOIN films ON det.id_film = films.id_film
WHERE com.id_client = :id_client
ORDER BY com.date_commande DESC
", array(
    'id_client' => $_SESSION['user']['id_client']
));
$locations = $statement->fetchAll();

require_once('inc/header.php');
?>
<div class="container">
    <h1><i class="fas fa-film"></i>Mes films</h1>

    <?php if (!empty($_SESSION['message'])) : ?>
        <div class="succes"><?php echo $_SESSION['message'] ?></div>
    <?php
        unset($_SESSION['message']); // destruction de variable
    endif; ?>

    <?php if (!empty($locations)) : ?>

        <table id="orders">
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Nom du film</th>
                    <th>Statut</th>
                    <th>Prix</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($locations as $location) : ?>

                    <tr>
                        <td>
                            Commande n°<?php echo $location['id_commande'] ?>
                            en date du <?php
                                        echo strftime('%A %d %B %Y à %H:%M', strtotime($location['date_commande']));
                                        /* 2021-12-24 12:31:25 */
                                        ?>
                        </td>
                        <td><?php echo $location['titre'] ?></td>

                        <?php
                        $maintenant = time();
                        $datelocation = strtotime($location['date_commande']);
                        $interval = 48 * 3600; // 48h en secondes
                        if ($maintenant - $datelocation > $interval) :
                        ?>
                            <td class="erreur">Indisponible</td>
                        <?php
                        else :
                        ?>
                            <td class="succes">Disponible</td>
                        <?php
                        endif;
                        ?>

                        <td><?php echo $location['prix'] ?>€</td>
                    </tr>

                <?php endforeach; ?>


            </tbody>
        </table>

    <?php else : ?>
        <p> Vous n'avez pas encore loué de films. <a href="<?php echo URL ?>">Consulter notre catalogue</a>
        </p>
    <?php endif; ?>


</div>
<?php

require_once('inc/footer.php');
