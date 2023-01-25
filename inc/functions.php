<?php

function isConnected() : bool{
    return isset($_SESSION['user']); // on prévoit de renseigner $_SESSION['user'] en cas de connexion réalisée avec succés
}

function isAdmin() : bool {
    return (isConnected() && $_SESSION['user']['droits'] == 1);
}

function redirect($destination){
    header('location:' . $destination);
    exit();
}

function safeSQL($requete, $params = array()){
   
    // Assainissement (sanitize)
    if(!empty($params)){
        foreach($params as $index => $value){
            $params[$index] = htmlspecialchars(trim($value));
            // htmlspecialchars() - neutralise le html et en fait du texte
            // trim() - retire les espaces avant et après
        }
    }

    global $pdo; // accès à la variable globale $pdo
    $statement = $pdo->prepare($requete);
    $statement->execute($params);

    return $statement;
}

function getUserByLogin($login){
    $statement = safeSQL("SELECT * FROM clients WHERE login=:login",array('login' => $login));
    if( $statement->rowCount() > 0 ) return $statement->fetch();
    else return false;
}


// --- fonctions liées au panier

function creationPanier(){
    if(!isset($_SESSION['panier'])){
        $_SESSION['panier'] = array();
        $_SESSION['panier']['id_films'] = array();
        $_SESSION['panier']['prix'] = array();
    }
}

function ajoutPanier($id_film,$prix){
    
    creationPanier();

    // on ajoute le film que s'il n'existe pas déjà dans le panier
    if( !in_array($id_film, $_SESSION['panier']['id_films']) ){
        $_SESSION['panier']['id_films'][] = $id_film;
        $_SESSION['panier']['prix'][] = $prix;
    }
}

function retraitPanier($id_film){

    $position_film = array_search($id_film,$_SESSION['panier']['id_films']);
    
    // comparaison stricte
    if( $position_film !== false ){
        /*
        On supprime une entrée, et on réorganise les index du tableau
        */
        array_splice($_SESSION['panier']['id_films'],$position_film,1);
        array_splice($_SESSION['panier']['prix'],$position_film,1);
        /*
            unset($_SESSION['panier']['id_films'][$position_film]);
            unset($_SESSION['panier']['prix'][$position_film]);
        */
    }
    // Si, après avoir supprimé un film du panier, j'ai un tableau vide, cela revient à vider le panier
    if(empty($_SESSION['panier']['id_films'])){
        viderPanier();
    }

}

function nbFilmsPanier(){
    return count($_SESSION['panier']['id_films']);
}

function montantPanier(){
    return number_format(array_sum($_SESSION['panier']['prix']), 2, ',', ' ');
}

function viderPanier(){
    unset($_SESSION['panier']);
}
