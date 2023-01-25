/* Constante de l'url du fichier php */
const URL_AJAX = 'inc/functions_ajax.php';


/* NAV */

document.querySelector("#burger").addEventListener("click", function () {
    document.querySelector("nav").classList.add("move");
})

document.querySelector("#close").addEventListener("click", function () {
    document.querySelector("nav").classList.remove("move");
})

/* TRAILER */

var btnTrailer = document.querySelectorAll(".trailer");

for (i = 0; i < btnTrailer.length; i++) {

    btnTrailer[i].addEventListener("click", function (event) {

        // Annule le comportement par defaul du lien
        event.preventDefault();

        // On récupère l'attribut "data-video"
        var dataV = this.getAttribute("data-video");

        // On affiche le fond noir et le bloc video

        document.querySelector("#black-bg").style.display = "block";
        document.querySelector("#video-container").style.display = "block";

        // On ajoute la video dans le bloc "video-container"

        document.querySelector("#video-container").innerHTML = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' + dataV + '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

    })
}

if (document.querySelector("#black-bg")) {

    document.querySelector("#black-bg").addEventListener("click", function () {
        // On cache le fond noir
        this.style.display = "none";

        // On cache le bloc video
        document.querySelector("#video-container").style.display = "none";

        // On vide le bloc video de son contenu précédent
        document.querySelector("#video-container").innerHTML = "";
    });
}

// ADD BUTTON

var btnAdd = document.querySelectorAll(".add");

for (i = 0; i < btnAdd.length; i++) {
    btnAdd[i].addEventListener("click", function (event) {

        // Annule le comportement par defaut
        event.preventDefault();

        let $thisbutton = this;

        // Ajoute ou enlève la class "focus"
        $thisbutton.classList.toggle("focus");
        
        
        // Communication avec php pour ajouter/retirer le film dans le panier
        let id_film = $thisbutton.getAttribute('data-id');
        let form = new FormData();
        form.append('id_film',id_film);


        // On vérifie si l'élément contient la class "focus"
        if ($thisbutton.classList.contains("focus")) {           
            form.append('action','ajoutpanier');       
        } else {
            form.append('action','retraitpanier');
        }

        let params = {
            method : 'POST',
            body : form
        };

        // AJAX
        // on lance le fichier avec les paramètres définis juste au dessus
        fetch(URL_AJAX, params)
            .then(function(reponsePHP){
               return reponsePHP.json() ;
            })
            .then(function(datas){
                console.log(datas);
                $thisbutton.innerHTML = datas.libelle;
                document.getElementById('cartdetails').innerHTML = datas.summary;
            })
            .catch(function(error){
                console.log(error);
            });    


    })
}


if (document.getElementById('longueur')) {

    document.getElementById('mdp').addEventListener('input', function () {

        let mdp = document.getElementById('mdp').value;
        if (mdp != '') {

            if (mdp.length >= 8 && mdp.length <= 15) {
                document.getElementById('longueur').style.color = 'lime';
            }
            else {
                document.getElementById('longueur').style.color = 'orange';
            }

            let pattern_minuscule = new RegExp('^(?=.*[a-z])[\\w\\!\\@\\-\\#\\*]{1,}$');
            
            if(pattern_minuscule.test(mdp)){
                document.getElementById('minuscule').style.color = 'lime';
            }
            else{
                document.getElementById('minuscule').style.color = 'orange';
            }

            let pattern_majuscule = new RegExp('^(?=.*[A-Z])[\\w\\!\\@\\-\\#\\*]{1,}$');
            
            if(pattern_majuscule.test(mdp)){
                document.getElementById('majuscule').style.color = 'lime';
            }
            else{
                document.getElementById('majuscule').style.color = 'orange';
            }

            let pattern_chiffre = new RegExp('^(?=.*[0-9])[\\w\\!\\@\\-\\#\\*]{1,}$');
            
            if(pattern_chiffre.test(mdp)){
                document.getElementById('chiffre').style.color = 'lime';
            }
            else{
                document.getElementById('chiffre').style.color = 'orange';
            }

            let pattern_special = new RegExp('^(?=.*[\\_\\!\\@\\-\\#\\*])[\\w\\!\\@\\-\\#\\*]{1,}$');

            if(pattern_special.test(mdp)){
                document.getElementById('special').style.color = 'lime';
            }
            else{
                document.getElementById('special').style.color = 'orange';
            }            

        }

    });

}

if(document.querySelectorAll('.confirmdel')){

    let listeliens = document.querySelectorAll('.confirmdel');

    for(let i=0; i < listeliens.length; i++){
        
        listeliens[i].onclick = function(){
            return(confirm('Etes vous sûr(e) de vouloir supprimer ce film ?'));
        };
    }
}


if(document.querySelectorAll('.confirmvide')){

    let listeliens = document.querySelectorAll('.confirmvide');

    for(let i=0; i < listeliens.length; i++){
        
        listeliens[i].onclick = function(){
            return(confirm('Etes vous sûr(e) de vouloir vider votre panier ?'));
        };
    }
}


if(document.getElementById('affiche')){

    document.getElementById('affiche').addEventListener('change',function(event){

        // On récupère la liste des fichiers (indexés en commençant par 0)
        let fics = event.target.files;    
        // Objet de lecture de fichier
        let reader = new FileReader();
        // Je lis le contenu en mode données Web (DataURL) et le transforme en base64
        reader.readAsDataURL(fics[0]);
        // Quand le reader a fini de lire, je mets le résultat de la lecture en tant que source de mon image
        reader.onload = function(e){
            console.log(e);
            document.getElementById('preview').setAttribute('src',e.target.result);
        }

    });

}