<?php


namespace MoneticoPaiement\Sdk\Collections;


class PaymentStateResponseValue
{
    // default language if current language is not found
    const DEFAULT_LANGUAGE = 'fr';

    const RESPONSE = [
        '-1' => [
            'fr' => [
                'desc' => 'Problème technique',
                'rmk' => 'Problème technique, il faut réitérer la demande',
            ],
        ],
        '-2' => [
            'fr' => [
                'desc' => 'Erreur dans les paramètres d’entrée',
                'rmk' => 'Les paramètres servant à identifier la commande site ne sont pas corrects, vérifier les champs version, société, date, référence, montant et TPE. Vérifier si un des champs n’est pas nul ou si la longueur d’un champ ne dépasse pas la taille maximale autorisée.',
            ],
        ],
        '-3' => [
            'fr' => [
                'desc' => 'Montant erroné',
                'rmk' => 'Le montant transmis est mal formaté ou égale à zéro',
            ],
        ],
        '-4' => [
            'fr' => [
                'desc' => 'Erreur demande d’autorisation',
                'rmk' => 'La date de validité de la carte bancaire n’est pas valide',
            ],
        ],
        '-5' => [
            'fr' => [
                'desc' => 'MAC erroné',
                'rmk' => 'Vérifier la valeur des paramètres d’entrée TPE, date, référence, montant et MAC. Vérifier en particulier le calcul du MAC et la valeur de la clé MAC.',
            ],
        ],
        '-6' => [
            'fr' => [
                'desc' => 'Date erronée',
                'rmk' => 'La date d’entrée est erronée. Vérifier que le format de la date est du type JJ/MM/AAAA.',
            ],
        ],
        '-7' => [
            'fr' => [
                'desc' => 'Transfert des données',
                'rmk' => 'Les données doivent être transmises avec la méthode POST.',
            ],
        ],
        '-8' => [
            'fr' => [
                'desc' => 'Sémaphore verrouillée',
                'rmk' => 'Le programme a déjà été lancé en parallèle sur le même paiement et est en cours d’exécution ou la page de paiement est en cours.',
            ],
        ],
        '-9' => [
            'fr' => [
                'desc' => 'Problème recherche commande',
                'rmk' => 'La commande ne peut pas être trouvée. Vérifier la valeur des paramètres d’entrée.',
            ],
        ],
        '-10' => [
            'fr' => [
                'desc' => 'Version erronée',
                'rmk' => 'La version transmise est mal formatée ou non renseignée.',
            ],
        ],
    ];
}