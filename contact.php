<?php
require_once('inc/init.php');


$pagetitle = 'Contact';
require_once('inc/header.php');
?>
<div class="container">
    <h1><i class="fas fa-envelope-open"></i>Nous contacter</h1>

    <form action="" class="contact-form">
        <input type="text" id="firstname" placeholder="Votre prénom">
        <input type="text" id="lastname" placeholder="Votre nom">
        <input type="email" id="email" placeholder="Votre email">
        <textarea id="message" placeholder="Votre message"></textarea>
        <input type="submit" value="Envoyer">
    </form>
</div>
<?php

require_once('inc/footer.php');
