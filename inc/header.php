<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Site de location de films Marvel">
    <title>Marvel - <?php echo $pagetitle ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.google.com">
    <!-- <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="../css/style.css">
    
    
</head>

<body>
    <header>
        <div class="container">
            <a href="../index.php">
                <img src="../images/logo.png" id="logo" alt="Logo Marvel">
            </a>

            <nav>
                <i id="close" class="fas fa-times"></i>

                <?php if (isAdmin()) : ?>
                    <a href="/admin"><i class="fas fa-user-cog"></i>Admin</a>
                <?php endif; ?>

                <?php if (isConnected()) { ?>
                    <a href="/commandes.php"><i class="fas fa-film"></i>Mes films</a>
                    <a href="/compte.php"><i class="fas fa-user"></i>Compte (<?php echo $_SESSION['user']['login'] ?>)</a>
                <?php } ?>

                <a href="/contact.php"><i class="fas fa-envelope-open"></i>Contact</a>

                <?php if (!isConnected()) : ?>
                    <a href="/connexion.php"><i class="fas fa-power-off"></i>Se connecter</a>
                <?php
                else :
                ?>
                    <a href="/connexion.php?action=disconnect"><i class="fas fa-power-off"></i>Se déconnecter</a>
                <?php endif; ?>

                <a href="/panier.php"><i class="fas fa-shopping-cart"></i>
                    <span id="cartdetails">
                        <?php
                        echo (isset($_SESSION['panier'])) ? nbFilmsPanier() . ' article' . ((nbFilmsPanier() > 1) ? 's' : '') . ' (' . montantPanier() . '€)' : '(vide)';
                        ?>
                    </span>
                </a>

            </nav>

            <i id="burger" class="fas fa-bars"></i>
        </div>
    </header>

    <main>