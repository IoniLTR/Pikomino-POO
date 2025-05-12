<?php

include_once("Joueur.php");

class JoueurAgressif extends Joueur
{
    public function analyserLesDes($nombreDesRelances, $valeursDes)
    {
        // Récupère les valeurs déjà conservées
        $valeursConservees = array_column(array_filter($this->des, fn($d) => $d['conserve']), 'valeur');

        // Compte les occurrences de chaque valeur dans les dés lancés
        $compte = array_count_values($valeursDes);

        // Priorise les vers (valeur 6), ensuite les grosses valeurs 5, 4...
        $priorite = [6, 5, 4, 3, 2, 1];

        foreach ($priorite as $val) {
            if (in_array($val, $valeursDes) && !in_array($val, $valeursConservees)) {
                return [
                    'valeur' => $val,
                    'continuer' => true // Continue tant que possible pour risquer le max
                ];
            }
        }

        // Si rien à conserver, ou que toutes les valeurs ont déjà été prises
        return [
            'valeur' => -1,
            'continuer' => false
        ];
    }
}
?>