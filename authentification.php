<?php
// authentification.php
///////////////////////////////////////////////////////////////////////////////
//	application gestionPDM
// février 2020
///////////////////////////////////////////////////////////////////////////////

	session_start();
	
	if (isset($_COOKIE['adminPDM'])) header('Location: index.php');

	if (isset($_POST['newAction'])) {
		if ($_POST['newAction']=="validerLogin") {
			if ($_POST['login']=='admin' && $_POST['password']=='7rutabaga') {
				$_SESSION['accesAutorise'] = TRUE;
				if (isset($_POST['rester'])) {
					if ($_SERVER['SERVER_NAME']=='localhost') $chemin = '/pdm/';
					else $chemin = '/';
					setcookie ('adminPDM', 'oui', time()+60*60*24*30, $chemin); // valable un mois
				}
				header('Location: index.php');
			}
			else {
				afficherLogin("L'identifiant et/ou le mot de passe n'ont pas été reconnus.");
			}
		}
	}
	else {
		afficherLogin('');
	}


	function afficherLogin($message) {
		$GLOBALS['titrePage'] = "Gestion du panier : Identification";
?>
<!DOCTYPE html>
<html lang="fr-fr">
<?php	
		include("inc/headHTML.inc.php");
?>
	<body style="font-family: sans-serif; font-size:small; padding:5px;"   onLoad="document.getElementById('login').focus();"" >
		<?php
			include("inc/divEnTete.inc.php");
		?>
		<hr>
		<br>
			
<?php
	if ($message!='') {
		echo("<p>".$message."</p>");
	}
?>
		<br>
		<form method="POST" action="authentification.php" id="formLogin" >
			<input type="hidden" name="newAction" id="newAction" value="validerLogin">
			
		<table border="0" style="width: 50%; margin: auto;">
			<tbody>
				<tr style="height: 50px;">
					<td style="text-align: right; width: 50%;">
						identifiant : 
					</td>
					<td style="text-align: left; width: 50%;">
						<input required="required" name="login" id="login" size="30px" type="text">  
					</td>
				</tr>
				
				<tr style="height: 50px;">
					<td style="text-align: right;">
						mot de passe : 
					</td>
					<td style="text-align: left;">
						<input required="required" name="password" size="30px" type="password">  
					</td>
				</tr>

				<tr style="height: 50px;">
					<td style="text-align: right;">
						rester identifié sur cet ordinateur : 
					</td>
					<td style="text-align: left;">
						<input name="rester" value="oui" type="checkbox">  
					</td>
				</tr>

				
				<tr style="height: 50px;">
					<td colspan=2 style="text-align: center;">
						<input name="valider" value="Valider" formmethod="post" type="submit">
					</td>
				</tr>
			</tbody>
		</table>
		</form>
		<br><br>
		<hr>
		</form>
	</body>
</html>
<?php
	} // fin afficherLogin
	

?>