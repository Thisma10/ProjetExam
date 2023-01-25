<?php

require_once('init.php'); // acces à la BDD + functions.php

$reponse = array();

$id_film = $_POST['id_film'];

switch($_POST['action']){

    case 'ajoutpanier':
        $statement = safeSQL("SELECT * FROM films WHERE id_film = :id_film",array(
            'id_film' => $id_film
        ));
        $film = $statement->fetch();

        ajoutPanier($id_film, $film['prix']);
        $reponse['libelle'] = 'Ajouté';
        break;

    case 'retraitpanier':
        retraitPanier($id_film);
        $reponse['libelle'] = 'Ajouter';
        break;
}

if(isset($_SESSION['panier'])){
    $reponse['summary'] = nbFilmsPanier() . ' article'.( (nbFilmsPanier() > 1 ) ? 's' : '' ).' ('.montantPanier().'€)';
}
else{
    $reponse['summary']  = '(vide)';
}


echo json_encode($reponse); // alimente datas de js