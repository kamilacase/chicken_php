<?php

include_once 'app/start_inc.php';

if (isset($_GET['deconnexion'])) {
    session_destroy();
    header('Location: .');
    exit();
}

$manager = new PersonnagesManager($db);

if (isset($_SESSION['perso'])) { // Si la session perso existe, on restaure l'objet.
    $perso = $_SESSION['perso'];
}

if (isset($_POST['creer']) && isset($_POST['nom'])) { // Si on a voulu créer un personnage.
  $perso = new Personnage(['nom' => $_POST['nom']]); // On crée un nouveau personnage.
  
  if (!$perso->nomValide()) {
      $message = 'Le nom choisi est invalide.';
      unset($perso);
  } elseif ($manager->exists($perso->nom())) {
      $message = 'Le nom du personnage est déjà pris.';
      unset($perso);
  } else {
      $manager->add($perso);
  }
} elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) { // Si on a voulu utiliser un personnage.
  if ($manager->exists($_POST['nom'])) { // Si celui-ci existe.
    $perso = $manager->get($_POST['nom']);
  } else {
      $message = 'Ce personnage n\'existe pas !'; // S'il n'existe pas, on affichera ce message.
  }
} elseif (isset($_GET['frapper'])) { // Si on a cliqué sur un personnage pour le frapper.
    if (!isset($perso)) {
        $message = 'Merci de créer un personnage ou de vous identifier.';
    } else {
        if (!$manager->exists((int) $_GET['frapper'])) {
            $message = 'Le personnage que vous voulez frapper n\'existe pas !';
        } else {
            $persoAFrapper = $manager->get((int) $_GET['frapper']);
      
            $retour = $perso->frapper($persoAFrapper); // On stocke dans $retour les éventuelles erreurs ou messages que renvoie la méthode frapper.
      
            switch ($retour) {
    
                case Personnage::CEST_MOI:
                    $message = 'Mais... pourquoi voulez-vous vous frapper ???';
                break;

                case Personnage::PERSONNAGE_FRAPPE:
                    $message = 'Le personnage a bien été frappé !';
                    $manager->update($perso);
                    $manager->update($persoAFrapper);
                break;

                case Personnage::PERSONNAGE_TUE:
                    $message = 'Vous avez tué ce personnage !';
                    $manager->update($perso);
                    $manager->delete($persoAFrapper);
                break;
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>TP : Mini jeu de combat</title>
    <meta charset="utf-8" />

    <link rel="stylesheet" type="text/css" href="style.css">

</head>

<body>
 <audio autoplay>
  <source src="music.mp3" type="audio/ogg">
</audio> 
    <h1>Chicken Phantasy Fighter<br /> <img class="lutin" src="Leprechaun_the_simpsons.png" alt="lutin" style="width: 150px; height:auto;">
</h1>
    
    <p id="uno">Nombre de poules créés :
        <?= $manager->count() ?>
    </p>
    <?php
if (isset($message)) { // On a un message à afficher ?
  echo '<p>', $message, '</p>'; // Si oui, on l'affiche.
}

if (isset($perso)) { // Si on utilise un personnage (nouveau ou pas). ?>
    <p id="deco"><a href="?deconnexion=1">Menu</a></p>

    <fieldset>
        <legend>Mes informations à moi que j'ai :</legend>
        <p class="details">
            Mon petit nom tout moisi ===>>
            <?= htmlspecialchars($perso->nom()) ?><br />
            Dégâts que moi j'ai encaissé ----->
            <?php echo $perso->degats() ?><br />
            Exp que je galère à obtenir ----->
            <?php echo $perso->xp() ?><br />
            Level que j'ai réussi à avoir ----->
            <?php echo $perso->lev() ?><br />
            Les vies qu'on m'a donné a moi ----->
            <?php echo $perso->life() ?>
    </fieldset>

    <fieldset>
        <legend>Qui frapper dans sa mouille?</legend>
        <p class="details">
            <?php
$persos = $manager->getList($perso->nom());

    if (empty($persos)) {
        echo 'Personne à frapper !';
    } else {
        foreach ($persos as $unPerso) {
            echo '<a class="victim" href="?frapper=', $unPerso->id(), '" >', htmlspecialchars($unPerso->nom()), '</a> (dégâts : ', $unPerso->degats(), ')<br />';
            ?>
             <h2>Comment je lui tatane sa face :<img src="44550-full.png" style="width:150px; height:auto;"></h2>
                <div class="container">
                <div class="skills degats" style="width:<?php echo$unPerso->degats()?>%"></div>
                </div>
                <?php
}
} ?>

            

        </p>
    </fieldset>
   
    <?php
} else {

    if ($manager->count() > 0) {

      ?>

        <div id="list">
    <h2>Liste des personnages:</h2>
    <ul>
        <?php
        $allPersonnages = $manager->getList();
        foreach ($allPersonnages as $personnage) {
        echo "<li>{$personnage->nom()}</li>";
}

?>
    </ul>
</div>
    <?php

    }

    ?>
    <form class="create" action="" method="post">
        <p>
            Nom : <input class="btn" type="text" name="nom" maxlength="50" />
            <input class="btn" type="submit" value="Créer cette poule" name="creer" />
            <input class="btn" type="submit" value="Utiliser cette poule" name="utiliser" />
        </p>
    </form>
    <?php
}
?>
</body>

</html>
<?php
if (isset($perso)) { // Si on a créé un personnage, on le stocke dans une variable session afin d'économiser une requête SQL.
    $_SESSION['perso'] = $perso;
}