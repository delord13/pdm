<?php 
// go.php

$tabDate[1]['date'] = "2020-03-01";
$tabDate[2]['date'] = "2020-03-08";
$tabDate[1]['semaine'] = "A";
$tabDate[2]['semaine'] = "B";

function semaineA($tab) {
	return $tab['semaine']=="A";
}

function semaineB($tab) {
	return $tab['semaine']=="B";
}

echo "A :\n";
print_r(array_filter($tabDate, "semaineA"));
echo "B :\n";
print_r(array_filter($tabDate, "semaineB"));
?>