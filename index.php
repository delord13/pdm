<?php
// index.php
///////////////////////////////////////////////////////////////////////////////
//	application gestionPDM
// février 2020
///////////////////////////////////////////////////////////////////////////////


	session_start();
	include('inc/init.inc.php');
	
	unset($_SESSION['quiAfficher']);

	$GLOBALS['titrePage'] = "Gestion du Panier";

	
// il faudra ajouter un contrôle d'accès par $_SESSION['accesAutorise'] et donc une fonction afficherLogin() et une fonction controlerLogin
	
	
	if (isset($_POST['newAction'])) {
		// agir selon POST newAcion
		switch ($_POST['action']) {
			case "comptes" :
				header("Location: comptes.php");
				exit;
				break;
			case "contrats" :
				header("Location: contrats.php");
				exit;
				break;
			case "distributions" :
				header("Location: distributions.php");
				exit;
				break;
			case "cheques" :
				header("Location: cheques.php");
				exit;
				break;
			case "courriels" :
				header("Location: courriels.php");
				exit;
				break;
			case "newsletter" :
				header("Location: newsletter.php");
				exit;
				break;
			case "bascule" :
				header("Location: bascule.php");
				exit;
				break;
			case "moulinette" :
				header("Location: moulinette.php");
				break;
			case "deconnexion" :
				header("Location: deconnexion.php");
				exit;
				break;
			case "quitter" :
				die('Au revoir !');
				exit;
				break;
		}
	}
	else {
		afficherChoixAction('');
	}
	
	
	
	function afficherChoixAction($message) {
		
?>
<!DOCTYPE html>
<html lang="fr-fr">
<?php	
		include("inc/headHTML.inc.php");
?>
	<body style="font-family: sans-serif; font-size:small; padding: 0px;"  >
	
		<div id="haut">
		<?php
			include("inc/divEnTete.inc.php");
		?>
<?php
	if ($message!='') {
		echo("<p>".$message."</p>");
	}
?>

		</div>
		<br>
		
		<div id="content" style=" overflow:auto;" >
		<hr>
		<br>
		<form method="POST" action="index.php" id="formAction" >
			<input type="hidden" name="newAction" id="newAction" value="traiterAction">
			<input type="hidden" name="action" id="action" value="comptes">

		<table border="0" style="width: 80%; margin: auto;">
			<tbody>
				<tr style="height: 50px; background-color: #ffffff;">
					<td style="text-align: center;">
						<button name="bt_comptes" style="width: 450px; height: 55px; text-align: center;" onClick="document.getElementById('action').value='comptes';document.getElementById('formAction').submit();">
							<b>Comptes</b>
						</button>
						<br>
					</td>
				</tr>
				<tr style="height: 50px; background-color: #ffffff">
					<td style="text-align: center;">
						<button name="bt_distributions" style="width: 450px; height: 55px; text-align: center;" onClick="document.getElementById('action').value='distributions';document.getElementById('formAction').submit();">
							<b>Distributions</b>
						</button>
						<br>
					</td>
				</tr>
				<tr style="height: 50px; background-color: #ffffff">
					<td style="text-align: center;">
						<button name="bt_cheques" style="width: 450px; height: 55px; text-align: center;" onClick="document.getElementById('action').value='cheques';document.getElementById('formAction').submit();">
							<b>Chèques</b>
						</button>
						<br>
					</td>
				</tr>
				<tr style="height: 50px; background-color: #ffffff">
					<td style="text-align: center;">
						<button name="bt_courriels" style="width: 450px; height: 55px; text-align: center;" onClick="document.getElementById('action').value='courriels';document.getElementById('formAction').submit();">
							<b>Courriels</b>
						</button>
						<br>
					</td>
				</tr>
				<tr style="height: 50px; background-color: #ffffff">
					<td style="text-align: center;">
						<button name="bt_newsletter" style="width: 450px; height: 55px; text-align: center;" onClick="document.getElementById('action').value='newsletter';document.getElementById('formAction').submit();">
							<b>Newsletter</b>
						</button>
						<br>
					</td>
				</tr>
				<tr style="height: 50px; background-color: #ffffff">
					<td style="text-align: center;">
						<button name="bt_bascule" style="width: 450px; height: 55px; text-align: center;" onClick="document.getElementById('action').value='bascule';document.getElementById('formAction').submit();">
							<b>Bascule annuelle</b>
						</button>
						<br>
					</td>
				</tr>
				<tr style="height: 50px; background-color: #ffffff">
					<td style="text-align: center;">
						<button name="bt_bascule" style="width: 450px; height: 55px; text-align: center;" onClick="document.getElementById('action').value='moulinette';document.getElementById('formAction').submit();">
							<b>Importer les données de l'ancienne application</b>
						</button>
						<br>
					</td>
				</tr>
				<tr style="height: 50px; background-color: #ffffff">
					<td style="text-align: center;">
						<button name="bt_deconnexion" style="width: 450px; height: 55px; text-align: center;" onClick="document.getElementById('action').value='deconnexion';document.getElementById('formAction').submit();">
							<b>Déconnexion</b>
						</button>
						<br>
					</td>
				</tr>
				<tr style="height: 50px; background-color: #ffffff">
					<td style="text-align: center;">
						<button name="bt_quitter" style="width: 450px; height: 55px; text-align: center;" onClick="document.getElementById('action').value='quitter';document.getElementById('formAction').submit();">
							<b>Quitter</b>
						</button>
						<br>
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		<br>
		<hr>
		</div>
		
		<div id="bas">
		</div>
	</body>
		<script>
		
			$(document).ready(
				function(){
					// redimmensionner
					redim();
				}
			);
		</script>
</html>
<?php
	} // fin afficherChoixAction()
?>
