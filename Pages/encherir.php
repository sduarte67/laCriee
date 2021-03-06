<html>
<head>
    <meta charset="utf-8"/>

    <link rel="stylesheet" href="style.css"/>
    <!--[if lt IE 9]>
    <script src="http://github.com/aFarkas/html5shiv/blob/master/dist/html5shiv.js"></script>
    <![endif]-->
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=10">
    <![endif]-->
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <![endif]-->
    <title>Enchère</title>
    <link rel="stylesheet" href="../Libs/jquery-ui.css">
    <link rel="stylesheet" href="../Libs/bootstrap.min.css">
    <script src="../Libs/jquery-3.1.1.js"></script>
    <script src="../Libs/jquery-ui.js"></script>

</head>
<body class="img-criee">

<a class="button" href="logout.php">Déconnection</a>

<form method="post" action="encherirTraitement.php">
    <h1>Enchère</h1>
    <?php
    session_start();

    $bdd = new PDO('mysql:host=localhost;dbname=lacriee;charset=utf8', 'root', '');


    try {
        if ($bdd != null) {
            if ($_SESSION['status'] == 'acheteur' || $_SESSION['status'] == 'crieur') {


                if ($_SESSION['previous_location'] == 'monCompte') {

                    $enchereSelectionnee = $_POST['enchereSelectionnee'];


                    $separation = explode("?", $enchereSelectionnee);
                    $idLot = $separation[0];
                    $datePeche = $separation[1];
                    $idBateau = $separation[2];

                    $_SESSION['idlot'] = $idLot;
                    $_SESSION['datePeche'] = $datePeche;
                    $_SESSION['idBateau'] = $idBateau;
                }

                $idLot = $_SESSION['idlot'];
                $datePeche = $_SESSION['datePeche'];
                $idBateau = $_SESSION['idBateau'];


                $requete = "Select prixEnchere,prixPlancher,nomImg, prixDepart,specification,immatriculationBateau,nomScient, codeEsp, libelleQual,tare, poidsBrutLot, libellePres, nomComm,nomBateau from lot l,espece,bac,qualite,taille, presentation, peche, bateau where l.idEsp=espece.idEsp and l.datePeche=peche.datePeche and l.idBateau=peche.idBateau and peche.idBateau=bateau.idBateau and l.idTaille=taille.idTaille and l.idPres=presentation.idpres and l.idQual=qualite.idQual and l.idBac=bac.idBac and l.datePeche='" . $_SESSION['datePeche'] . "' and l.idBateau=" . $_SESSION['idBateau'] . " and l.idLot=" . $_SESSION['idlot'];


                $resultat = $bdd->prepare($requete);
                $resultat->execute();


                $donnees = $resultat->fetch();

                echo '<div class="encherePanel">
<div class="container-enchere">
<div class="row">
<div class="col-lg-9 col-sm-9 col-md-9">
    <table id="table-description-enchere">
        <tr><td><b>Nom Commun: </b>' . $donnees['nomComm'] . '</td><td><b>Nom scientifique:</b> ' . $donnees['nomScient'] . '</td><td><b>Code espèce: </b>' . $donnees['codeEsp'] . '</td></tr>
        <tr><td><b>Nom du bateau:</b> ' . $donnees['nomBateau'] . '</td><td><b>Immatriculation du bateau:</b> ' . $donnees['immatriculationBateau'] . '</td><td></td></tr>
        <tr><td><b>Spécification: </b>' . $donnees['specification'] . '</td><td><b>Qualité:</b> ' . $donnees['libelleQual'] . '</td><td></td></tr>

';
                $poidsNet = $donnees['poidsBrutLot'] - $donnees['tare'];
                $prixDepart = $donnees['prixDepart'];
                $prixPlacher = $donnees['prixPlancher'];
                $prixEnchere = $donnees['prixEnchere'];

                echo '
        <tr><td><b>Poids Brut: </b>' . $donnees['poidsBrutLot'] . 'kg</td><td><b>Tare:</b> ' . $donnees['tare'] . 'kg</td><td><b>Poids net: </b>' . $poidsNet . 'kg</td></tr>
        <tr><td><b>Prix de départ: </b>' . $donnees['prixDepart'] . '€</td><td><b>Prix plancher:</b> ' . $donnees['prixPlancher'] . '€</td><td></td></tr>
    </table>
</div>
<div class="col-lg-3 col-sm-3 col-md-3"><img class="img-poisson" src="../Images/Poissons/' . $donnees['nomImg'] . '.jpg"></div>
</div>
</div>
';

                if ($_SESSION['previous_location'] == 'monCompte') {
                    $montantActu = $prixDepart;
                } else if ($_SESSION['previous_location'] == 'traitement') {
                    $montantActu = $prixEnchere;
                }

                if ($_SESSION['status'] == 'acheteur') {


                    echo '<div>
<div class="container-enchere2">
<div class="row">
    <div class="col-md-6 col-sm-6 col-lg-6" style="border-right: solid 1px #ccc">
        <label>Montant actuel:</label>
        <input id="montantActuel" type="text" value="' . $montantActu . '" readonly/>€
        <a class="button-enchere" style="margin-left: 86%;" >Stop</a>
    </div>
    <div class="col-md-6 col-sm-6 col-lg-6">
    <form>
        <label>Enchère:</label></br>
        ';
                    if ($_SESSION['previous_location'] == 'monCompte') {
                        $enchereMin = intval($prixDepart) + 1;
                    } else if ($_SESSION['previous_location'] == 'traitement') {
                        $enchereMin = intval($prixEnchere) + 1;
                    }

                    echo '
        <input type="number" min="' . $enchereMin . '" name="montantActu" required/>   

            </br>
        <input type="submit" value="Enchérir" style="margin-left: 85%;"/>
     </form>
    </div>
</div>

</div>
</div>
</div>';


                } elseif ($_SESSION['status'] == 'crieur') {

                    echo '
 <div class="container-enchere2">
    <div class="row">
        <div class="col-md-6 col-sm-6 col-lg-6" style="border-right: solid 1px #ccc">

        <label>Montant actuel:</label></br>
        <p id="montantActuel">
                <input id="montantActuel" type="text" value="' . $montantActu . '" readonly/>€
</p>
         </div>
</div>
</div>
</div>';
                }
            } else {

                header('Location: login.html?connection=0');
                exit();
            }

        }

    } catch
    (PDOException $e) {
        die('Erreur: ' . $e->getMessage());
    }
    ?>
    <a class="button" href="monCompte.php">Annuler</a>
    <a class="button" href="monCompte.php">Valider</a>
</body>
<footer>
    Criée Poulgoazec, 29780 Plouhinec - +33 (0)2 98 70 77 19
</footer>
</html>
