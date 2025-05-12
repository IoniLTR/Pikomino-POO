<?php
include_once("Joueur.php");
/*
Explication de la stratégie :
Priorité aux vers : La méthode analyserLesDes commence par vérifier si la valeur 1 (qui représente les vers) est disponible parmi les dés relancés. Si oui, cette valeur est conservée.
Conservation de la valeur la plus élevée : Si la valeur 1 n'est pas disponible, la méthode conserve la valeur la plus élevée parmi les dés relancés.
Continuer le jeu : Pour cette implémentation de test, le joueur décide toujours de continuer à jouer tant qu'il peut conserver des dés. Cela peut être modifié pour une stratégie plus complexe selon les besoins.
*/

class JoueurBasique extends Joueur
{

	public function analyserLesDes($nombreDesRelances, $valeursDes)
	{
	//créer un tableau des valeurs triés par ordre décroissant
	// Initialisation du tableau trié
	    $tabTrie = [];
		$i=0; //pour contenir le nombre de dés ayant des valeurs conservables
	    // Parcours des valeurs des dés
	    foreach ($valeursDes as $valeur) 
	    	if($valeur==6 && $this->dispo($valeur)) {$tabTrie[$i]=6; $i++;}	    	
	    foreach ($valeursDes as $valeur) 
	    	if($valeur==5 && $this->dispo($valeur)) {$tabTrie[$i]=5; $i++;}
	    foreach ($valeursDes as $valeur) 
	    	if($valeur==4 && $this->dispo($valeur)) {$tabTrie[$i]=4; $i++;}
	    foreach ($valeursDes as $valeur) 
	    	if($valeur==3 && $this->dispo($valeur)) {$tabTrie[$i]=3; $i++;}
	    foreach ($valeursDes as $valeur) 
	    	if($valeur==2 && $this->dispo($valeur)) {$tabTrie[$i]=2; $i++;}
	    foreach ($valeursDes as $valeur) 
	    	if($valeur==1 && $this->dispo($valeur)) {$tabTrie[$i]=1; $i++;}
	
	//retourner la plus grosse valeur disponible
	if($this->nombreDispo()>3) $continuer=true; else $continuer=false; //si on 3 valeurs ou moins de dispo on seulement, on arrete
	foreach($tabTrie as $t){//
		return ['valeur' => $t, 'continuer' => $continuer];
	}
	return ['valeur' => -1, 'continuer' => $continuer];//si tabTrie est vide, donc qu'il n'y a pas de valeur conservable, on retourne -1
	
	}
	
}
?>


