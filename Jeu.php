<?php
include_once("JoueurBasique.php");

class Jeu
{
    /*
    Pikomino est un jeu de société créé par Reiner Knizia. C'est un jeu de dés et de stratégie pour 2 à 7 joueurs. 
    Le but du jeu est de marquer le plus de points en collectant des tuiles Pikomino, qui sont représentées par des vers de terre, 
    tout en évitant de perdre des points en perdant des tuiles.

    Règles du jeu :
    Objectif : Les joueurs essaient de collecter le maximum de tuiles, qui ont des valeurs différentes, pour marquer des points.

    Tour de jeu : À chaque tour, un joueur lance les dés et choisit de garder certains dés, en fonction de leur valeur. 
    Les joueurs peuvent relancer les dés restants, mais ils risquent de perdre leur tour s'ils ne peuvent pas ajouter de nouveaux dés à leur collection actuelle.

    Collecte de tuiles : Les joueurs collectent des tuiles en fonction de la valeur totale de leurs dés. 
    Ils peuvent prendre une tuile de la pile centrale ou voler une tuile à un autre joueur.

    Fin de la partie : La partie se termine lorsque toutes les tuiles ont été collectées ou qu'aucun joueur ne peut plus collecter de tuiles.

    Gagnant : Le joueur avec le plus de points, représentés par les vers de terre sur les tuiles, à la fin de la partie est déclaré gagnant.
    */

    // Propriétés privées pour gérer l'état du jeu
    private $joueurs; // Liste des joueurs
    private $tuiles; // Tuiles disponibles dans le jeu
    private $etatJeu; // État actuel du jeu
    private $historique; // Historique des actions du jeu
    private $maxRelances = 10; // Limite maximale des relances de dés pour éviter les boucles infinies

    // Constructeur de la classe Jeu
    public function __construct(Array $joueurs) 
    {
        $this->joueurs = $joueurs; // Initialisation de la liste des joueurs
        $this->initialiserTuiles(); // Initialisation des tuiles
        $this->etatJeu = [/*'tuiles' => [], */'joueurs' => []]; // État initial du jeu
        $this->historique = []; // Initialisation de l'historique
    }
    
    // Fonction privée pour initialiser les tuiles avec des valeurs spécifiques
    private function initialiserTuiles()
    {
        $this->tuiles = [
            21 => ['vers' => 1, 'active' => true],
            22 => ['vers' => 1, 'active' => true],
            23 => ['vers' => 1, 'active' => true],
            24 => ['vers' => 1, 'active' => true],
            25 => ['vers' => 2, 'active' => true],
            26 => ['vers' => 2, 'active' => true],
            27 => ['vers' => 2, 'active' => true],
            28 => ['vers' => 2, 'active' => true],
            29 => ['vers' => 3, 'active' => true],
            30 => ['vers' => 3, 'active' => true],
            31 => ['vers' => 3, 'active' => true],
            32 => ['vers' => 3, 'active' => true],
            33 => ['vers' => 4, 'active' => true],
            34 => ['vers' => 4, 'active' => true],
            35 => ['vers' => 4, 'active' => true],
            36 => ['vers' => 4, 'active' => true],
        ];
    }

    // Fonction publique pour lancer le jeu
    public function jouer()
    {
        //$this->exporterEtatJeu(); // Exporter l'état initial du jeu
        while (count($this->getTuilesLibres()) > 0) { // Tant qu'il y a des tuiles libres 
            foreach ($this->joueurs as $joueur) { // Pour chaque joueur
            	if(count($this->getTuilesLibres())>0){
                echo "<br>Tour de {$joueur->getNom()}\n";
                $this->jouerTour($joueur); // Jouer un tour pour le joueur courant
                //$this->exporterEtatJeu(); // Exporter l'état du jeu après chaque tour
                }
            }
        }
        $this->exporterEtatJeu(); // Exporter l'état final du jeu
    }

    // Fonction publique pour exporter l'état du jeu dans un fichier JSON
    public function exporterEtatJeu()
    {
        //$this->etatJeu['tuiles'] = $this->tuiles;
        //$this->etatJeu['tuilesLibres'] = $this->getTuilesLibres();
        //$this->etatJeu['tuilesJoueurs'] = $this->getTuilesJoueur();
        foreach ($this->joueurs as $joueur) {
            $this->etatJeu['joueurs'][$joueur->getNom()] = [
                'score' => $joueur->calculerScoreTuiles(),
                'tuiles' => $joueur->getTuiles(),
            ];
        }
        echo "<br> Score final (nombre de vers picorés) : ";
        $note=[];
        $n=0;
        foreach ($this->joueurs as $joueur) { // Pour chaque joueur
            echo "<br>{$joueur->getNom()} : {$joueur->calculerScoreTuiles()}";
            $note[$n]=["nom"=>$joueur->getNom(),"note"=>$joueur->calculerScoreTuiles()];
            $n++;

        }
        file_put_contents('etatJeu.json', json_encode(['historique' => $this->historique, 'FinPartie' => $this->etatJeu, 'Notes' => $note]));
    }

    // Fonction publique pour gérer un tour de jeu d'un joueur
    public function jouerTour($joueur)
    {
    	$echec=false;
        $actions = []; // Liste des actions du tour

        do {
            $nombreDesRelances = $joueur->lancerDes(); //on lance les dés qui n'ont pas été conservé (donc tous au départ) et on récupère le nombre de dés relancés
            $valeursDes = $this->obtenirValeursDes($joueur->getDes()); //valeurs des dés relancés
            $desConserves = $this->obtenirValeursDesConserves($joueur->getDes()); //valeurs des dés conservés

            // Debug: Affichage des valeurs des dés
            echo "<br>Joueur: {$joueur->getNom()}, Dés lancés: " . implode(',', $valeursDes) . ", Dés conservés: " . implode(',', $desConserves) . "\n";
            
            $resultat = $joueur->analyserLesDes($nombreDesRelances, $valeursDes);//$resultat contient 2 cases : la "valeur" et s'il souhaite "continuer"
            echo "valeur conservée : {$resultat['valeur']} ";
            if (in_array($resultat['valeur'], $valeursDes) && $joueur->dispo($resultat['valeur']) ){ //si la valeur annoncée a été tirée et si elle n'a pas déjà été conservée
                $joueur->conserverDe($resultat['valeur']);
            
                $actions[] = [
                    //'joueur' => $joueur->getNom(),
                    'action' => 'conserve_de',
                    'valConservee' => $resultat['valeur'],
                    'desLances' => $valeursDes,
                    'desConserves' => $desConserves
                ];
                echo "score : {$joueur->calculerScoreIntermédiaire()} ";
            } else {
                $echec=true; //on n'a pas trouvé de dés à retenir.
                //rendre sa tuile et retourner la plus grosse tuile dispo
               /* $derniereTuile = $joueur->retirerDerniereTuile();//le joueur perd sa tuile supérieure
                if ($derniereTuile != null) { //null s'il n'y a pas de tuile à rendre
                    //on rend la derniere tuile
                    $this->revaliderTuile($derniereTuile['numero']);
                    $actions[] = [
                        //'joueur' => $joueur->getNom(),
                        'action' => 'rend_tuile',
                        'numero' => $derniereTuile['numero'],
                        'origine' => $joueur->getNom()
                    ];
                    echo "<br> {$joueur->getNom()} rend sa tuile {$derniereTuile['numero']} et retourne la tuile {$derniereTuile['numero']}";
                }
                    */
                
            }
        } while ($resultat['continuer'] && !$echec); //while ($resultat['continuer'] && !empty($this->obtenirValeursDes($joueur->getDes())) && $iterations < $maxIterations);


       	$reponse = $this->picorer($joueur); //retourne le numero de la tuile picorée ou 0 s'il n'y a rien à picorer
       	$numTuile=$reponse["tuile"];
       	if ($numTuile == 0) { //si on ne picore pas de tuile
       		$derniereTuile = $joueur->retirerDerniereTuile();//le joueur perd sa tuile supérieure
       		if ($derniereTuile != null) { //null s'il n'y a pas de tuile à rendre
       		 	//on rend la derniere tuile
               		$this->revaliderTuile($derniereTuile['numero']);
			$actions[] = [
			    //'joueur' => $joueur->getNom(),
			    'action' => 'rend_tuile',
			    'numero' => $derniereTuile['numero'],
			    'origine' => $joueur->getNom(),
                'TuileRetournee' => $this->trouverTuileMax()
			];
            echo "<br> {$joueur->getNom()} rend sa tuile {$derniereTuile['numero']}";
		    }

            echo "<br> La tuile {$this->trouverTuileMax()} devient inaccessible ";
            	$this->invaliderTuile($this->trouverTuileMax()); //on retourne la plus grosse tuile dispo

        } else { //si une tuile est picorée
            $actions[] = [
                //'joueur' => $joueur->getNom(),
                'action' => 'picore_tuile',
                'numero' => $numTuile,
		        'origine' => $reponse['origine'],
                'tuilesPicorees' => $joueur->getTuiles()
            ];
            echo "<br> {$joueur->getNom()} prend la tuile {$numTuile} depuis {$reponse['origine']}, il a ces tuiles : ".implode(',', $joueur->getNumTuilesPicorees());
        
            //le joueur a déjà récupéré la tuile dans la fonction picorer
        }
        $joueur->reinitialiserDes(); //remets tous les dés en statut "non conservé" pour un nouveau tour

        $this->historique[] = [
            'manche' => count($this->historique) + 1,
            'joueur' => $joueur->getNom(),
            'actions' => $actions,
            'tuilesDispo' => $this->getTuilesLibres(),
            'tuilesDuCentre'=> $this->getTuilesCentre()
        ];
        echo "<br>tuiles du centre : "; foreach($this->getTuilesCentre() as $k => $tuile) echo "<br>$k:".$tuile['active'];   
    }


    // Fonction privée pour trouver la tuile avec la valeur maximale
    private function trouverTuileMax()
    {
        $maxTuile = null;
        foreach ($this->tuiles as $numero => $tuile) {
            if ($tuile['active'] && ($maxTuile === null || $numero > $maxTuile)) {
                $maxTuile = $numero;
            }
        }
        return $maxTuile;
    }

    // Fonction publique pour gérer la collecte de tuiles par un joueur
    public function picorer($joueurDemandeur)
    {
        $score = $joueurDemandeur->calculerScore(); //si on récupère 0 ça veut dire qu'il n'y avait pas de ver dans les dés conservés.
        // Vérification des tuiles des autres joueurs
        foreach ($this->joueurs as $joueur) {
            if ($joueur !== $joueurDemandeur) {
                $derniereTuile = $joueur->getDerniereTuile();
                if ($derniereTuile && $derniereTuile['numero'] == $score) {
                    $joueur->retirerDerniereTuile();
                    $joueurDemandeur->ajouterTuile($derniereTuile['numero'], $derniereTuile['vers']);
                    return ["origine"=>$joueur->getNom(),"tuile"=>$derniereTuile['numero']];
                }
            }
        }
        
        // Vérification des tuiles du joueur demandeur
  //      $derniereTuileJoueurDemandeur = $joueurDemandeur->getDerniereTuile();
  //      if ($derniereTuileJoueurDemandeur && $derniereTuileJoueurDemandeur['numero'] == $score) {
  //          return $derniereTuileJoueurDemandeur['numero'];
  //      }

        // Vérification des tuiles libres
        if($score>20){
		$tuilesLibres = $this->getTuilesLibres(); //tuiles libres est une matrice avec le numero de la tuile en ligne et un tableau associatif 2cases "vers" et "active" en colonne
		
		krsort($tuilesLibres);//tableau trié par ordre décroissant des valeurs des tuiles
		foreach ($tuilesLibres as $numero => $tuile) {//$tuile est le tableau associatif avec vers et active
		    if ($numero <= $score) {
		        $this->invaliderTuile($numero); 
		        $joueurDemandeur->ajouterTuile($numero, $tuile['vers']);
                   	return ["origine"=>"centreTable","tuile"=>$numero];
		    }
		}
        }
        return ["origine"=>null,"tuile"=>0];//on n'a pas réussi à picorer de tuiles chez les autres ou au centre de la table
    }

    // Fonction publique pour obtenir les tuiles libres
    public function getTuilesLibres()
    {
        //récupère une copie du tableau $this->tuiles enleve les tuiles inactives et retourne le tableau    
        $tuilesLib = array_filter($this->tuiles, function($tuile) {
            return $tuile['active'];
        });
        return $tuilesLib;

    }    
    
    public function getTuilesCentre()
    {//retourne la liste des tuiles (actives ou non) moins celles des joueurs
        $tuilesCentre = $this->tuiles;
        foreach ($this->joueurs as $joueur) {
            $tuilesJoueur = $joueur->getNumTuilesPicorees();
            foreach ($tuilesJoueur as $tuile) {
                unset($tuilesCentre[$tuile]);
            }
        }
        return $tuilesCentre;
    }

    // Fonction publique pour obtenir les tuiles de chaque joueur
    public function getTuilesJoueur()
    {
        $tuilesJoueur = [];
        
        foreach ($this->joueurs as $joueur) {
            $tuilesJoueur[$joueur->getNom()] = $joueur->getTuiles();
        }
        
        return $tuilesJoueur;
    }

    // Fonction publique pour obtenir les valeurs des dés qui ne sont pas encore conservés
    public function obtenirValeursDes($des)
    {
        $valeursDes = [];
        $i=0;
        foreach ($des as $de) {
            if (!$de['conserve']) {
                $valeursDes[$i] = $de['valeur'];
                $i++;
            }
        }
        
        return $valeursDes;
    }

    // Fonction publique pour obtenir les valeurs des dés qui sont conservés
    public function obtenirValeursDesConserves($des)
    {
        $valeursDesConserves = [];
        $i=0;
        foreach ($des as $de) {
            if ($de['conserve']) {
                $valeursDesConserves[$i] = $de['valeur'];
                $i++;
            }
        }
        
        return $valeursDesConserves;
    }

    // Fonction publique pour invalider une tuile (la rendre inactive)
    public function invaliderTuile($numTuile)
    {
        if (isset($this->tuiles[$numTuile]) && $this->tuiles[$numTuile]['active']) {
            $this->tuiles[$numTuile]['active'] = false;
        }
    }

    // Fonction publique pour revalider une tuile (la rendre active)
    public function revaliderTuile($numTuile)
    {
        if (isset($this->tuiles[$numTuile]) && !$this->tuiles[$numTuile]['active']) {
            $this->tuiles[$numTuile]['active'] = true;
        }
    }
}
?>

