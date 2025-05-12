<?php

abstract class Joueur
{
    protected $nom;
    protected $tuiles; //une tuile porte un "numero" et a des "vers"
    protected $des; 

    public function __construct($nom)
    {
        $this->nom = $nom;
        $this->tuiles = [];
        $this->des = array_fill(0, 8, ['valeur' => 0, 'conserve' => false]);
    }
    
    public abstract function analyserLesDes($nombreDesRelances, $valeursDes);
    
    public function getDerniereTuile()
    {
        $tuile = end($this->tuiles);
        if ($tuile !== false) {
            return ['numero' => $tuile['numero'], 'vers' => $tuile['vers']];
        }
        return null;
    }
    
    public function ajouterTuile($numero, $vers)
    {
        if (!is_null($numero) && !is_null($vers)) {
            $this->tuiles[] = ['numero' => $numero, 'vers' => $vers];
        }
    }
    
    public function conserverDe($val){
    	foreach($this->des as &$de)
    		if($de['valeur']==$val)
    			$de['conserve']=true;
    
    	//for($i=0;$i<count($this->des);$i++)
    	//	if ($this->des[$i]['valeur']==$val) 
    	//		$this->des[$i]['conserve']=true;
    }
    
    public function retirerDerniereTuile()
    {
        if (empty($this->tuiles)) {
            return null;
        }
        
        $derniereTuile = array_pop($this->tuiles);
        return ['numero' => $derniereTuile['numero'], 'vers' => $derniereTuile['vers']];
    }

    public function getNom()
    {
        return $this->nom;
    }
    
    public function getTuiles()
    {
        return $this->tuiles;
    }
    public function getNumTuilesPicorees(){
        	$numTuiles=[];
            $i=0;
        	foreach ($this->tuiles as $tuile) {
                $numTuiles[$i]= $tuile['numero'];
                $i++;
            }
            return $numTuiles;

        

    }

    public function getDes()
    {
        return $this->des;
    }
    
    public function lancerDes()
    {
        $compteur = 0; //nombre de dés relancés
        foreach ($this->des as &$de) { //dans le tableau des 8 dés, on relance ceux qui n'ont pas été conservé
         // Utilisation de la référence (&) pour modifier directement le tableau, sinon $de n'est qu'une copie et $this->des n'est pas mis à jour
            if (!$de['conserve']) {
                $de['valeur'] = rand(1, 6);
                $compteur++;
            }
        }
        return $compteur;
    }

    public function calculerScore()
    {
        $score = 0;
        $verPresent=false;
        foreach ($this->des as $de) {
            if ($de['conserve']) {
                if ($de['valeur'] == 6) { // valeur 6 est un ver et vaut 5 points
                    $score += 5;
                    $verPresent=true;
                } else {
                    $score += $de['valeur'];
                }
            }
        }
        if($verPresent) return $score;
        else return 0;
    }


    public function calculerScoreIntermédiaire() //on totalise les dés conservés même s'il n'y a pas de vers
    {
        $score = 0;
        foreach ($this->des as $de) {
            if ($de['conserve']) {
                if ($de['valeur'] == 6) { // valeur 6 est un ver et vaut 5 points
                    $score += 5;
                } else {
                    $score += $de['valeur'];
                }
            }
        }
        return $score;
    }
    
    
	//fonction qui dit si une valeur est dispo
	public function dispo($valeur){
		$valeursDesConserves = [];
        	$i=0;
        	foreach ($this->des as $de) 
		    if ($de['conserve']) {
		        $valeursDesConserves[$i] = $de['valeur'];
		        $i++;
		    }
		 
		foreach ($this->des as $de) {
		//echo "<br> des ".var_dump($de)." : ";
		    if ($de['valeur']==$valeur && !in_array($de['valeur'], $valeursDesConserves))  
		    	return true;
		    	}
		return false;
		
	}
	
	//retourne le nombre de valeurs dispo
	public function nombreDispo(){
	$nb=0; //nombre de valeurs conservées
	for ($i=1;$i<=6;$i++){	
		$trouve=false;
		foreach ($this->des as $de) 
		    if ($de['valeur']==$i && $de['conserve']) {$trouve=true;}
		if ($trouve) $nb++;
         }
         $n=6-$nb;
	return $n;
	}
    
    public function calculerScoreTuiles()
    {
        $score = 0;
        foreach ($this->tuiles as $tuile) {
            $score += $tuile['vers'];
        }
        return $score;
    }

    public function reinitialiserDes() //remet tous les dés en statut "non conservé"
    {
        foreach ($this->des as &$de) {
            $de['conserve'] = false;
        }
    }
}
?>

