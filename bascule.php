<?php
// bascule.php
///////////////////////////////////////////////////////////////////////////////
//	application gestionPDM
// février 2020
///////////////////////////////////////////////////////////////////////////////
/*
bascule annuelle
	date 1ère distribution
	nombre de semaines
=>
	ajout des id de contrat
	création de la liste des distributions
	RGPDP : virer toutes les infos sauf cette année et l'année précédente
*/

	session_start();
	include('inc/init.inc.php');
	

	$GLOBALS['titrePage'] = "Bascule";
	
die('En travaux !');	
	
	
// fonction à modifier et à utiliser poiur générer les distributions de l'année dans la table distribution
	function tableDistribution($idAnnee) { // les dates de distribution avec attributs sans Noël Jour de l'An où nombreUnites = 0
		// date 1ère et nb de semaines dans PDM_annee
		$sql = "SELECT * FROM PDM_annee WHERE idAnnee=$idAnnee";
		$res = mysqli_query($GLOBALS['lkId'],$sql);
		$annee = mysqli_fetch_assoc($res);
		$datePremiereDistribution = $annee['datePremiereDistribution'];

 		$distribution[0]['date'] = $annee['datePremiereDistribution'];
		$attributs = attributsDate($distribution[0]['date']);
		$distribution[0]['nombreUnites'] = $attributs['nombreUnites'];
		$distribution[0]['semaine'] = $attributs['semaine'];
		$distribution[0]['semestre'] = $attributs['semestre'];

		$j = 0;
		$datePrecedente = $distribution[0]['date'];
		for ($i=1; $i<$annee['nombreSemaines']; $i++) {
		// 1 heure de plus à cause de l'heure d'hiver
			$laDate = date('Y-m-d',strtotime($datePrecedente) + (7*25*3600)) ;
         $attributs = attributsDate($laDate);
         if ($attributs['nombreUnites']!=0) {
            $j++;
            $distribution[$j]['date'] = $laDate;
            $distribution[$j]['nombreUnites'] = $attributs['nombreUnites'];
            $distribution[$j]['semaine'] = $attributs['semaine'];
            $distribution[$j]['semestre'] = $attributs['semestre'];
            
         }
         $datePrecedente = $laDate;
		}

//var_dump($distribution); die();
      return $distribution;
	}

?>
