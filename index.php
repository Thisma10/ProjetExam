<?php
require_once('inc/init.php');


$statement = safeSQL("SELECT * FROM films ORDER BY id_film DESC");
$movies = $statement->fetchAll();

$pagetitle = 'Accueil';
require_once('inc/header.php');
?>
<div id="black-bg"></div>
<div id="video-container"></div>
<div class="container">
    <aside>
        <div class="tw">
            <a href=""><i class="fab fa-twitter"></i></a>
        </div>

        <div class="fb">
            <a href=""><i class="fab fa-facebook-f"></i></a>
        </div>

        <div class="ig">
            <a href=""><i class="fab fa-instagram"></i></a>
        </div>

        <div class="gp">
            <a href=""><i class="fab fa-google-plus-g"></i></a>
        </div>

        <div class="pt">
            <a href=""><i class="fab fa-pinterest"></i></a>
        </div>

        <div class="sh">
            <a href=""><i class="fas fa-share-alt"></i></a>
        </div>
    </aside>

    <h1><i class="fas fa-video"></i>Nos films disponibles à la location</h1>

    <div class="movies-grid">

        <?php if (empty($movies)) : ?>
            <div class="erreur">Pas encore de films à la location</div>
            <?php
        else :
            foreach ($movies as $movie) :

            ?>
                <article>
                    <div class="image" style="background-image: url('affiche/<?php echo $movie['affiche'] ?>');"></div> <!-- /data:image/png;base64, pour comprendre les images en base 64, mais cela nous empeche de les ajouter depuis la pages -->
                    <h3><?php echo $movie['titre'] ?></h3>
                    <p><?php echo $movie['synopsis'] ?></p>
                    <a class="add <?php 

                     if(isset($_SESSION['panier'])
                     && in_array($movie['id_film'],$_SESSION['panier']['id_films'])) 
                     echo "focus";

                     ?>" href="" data-id="<?php echo $movie['id_film'] ?>">
                     
                    <?php 
                    echo
                    (isset($_SESSION['panier'])
                    && in_array($movie['id_film'],$_SESSION['panier']['id_films'])) ?
                    'Ajouté' : 'Ajouter' ;
                    ?>

                    </a>
                    <a class="trailer" data-video="<?php echo $movie['code_yt'] ?>" href="">Bande annonce</a>
                </article>
        <?php
            endforeach;
        endif; ?>

    </div>
</div>
<?php

require_once('inc/footer.php');
