<?php

namespace MoneticoPaiement\Sdk\Collections;

/**
 * Class PaymentCaptureResponseValue
 * @package MoneticoPaiement\Sdk\Collections
 * TAKE THE documentation technique page 46 for more informations
 */
abstract class PaymentCaptureResponseValue
{
    // default language if current language is not found
    const DEFAULT_LANGUAGE = 'fr';
    /*
     * Format
     * [
     *    [
     *      'cdr' => [
     *          'lib' => [
     *              'fr'=>[
         *              'desc' => X ,
         *              'rmk' => Y
     *              ],
     *              'en' => [
     *                  'desc' => X ,
     *                   'rmk' => Y
     *              ]
     *          ]
     *      ]
     *   ]
     * ]
     */
    const RESPONSE = [
        '1' => [
            'paiement accepte' => [
                'fr' => [
                    'desc' => 'La mise en recouvrement a été effectuée avec succès',
                    'rmk' => ''
                ]
            ],
            'commande annulee' => [
                'fr' => [
                    'desc' => 'La demande d’annulation a été prise en compte et la commande a été annulée',
                ]
            ],
            'recurrence stoppee' => [
                'fr' => [
                    'desc' => 'La demande d’annulation définitive du renouvellement a été prise en compte',
                    'rmk' => 'Uniquement en Paiement Récurrent'
                ]
            ],
        ],
        '0' => [
            'commande non authentifiee' => [
                'fr' => [
                    'desc' => 'La référence ne correspond pas à une commande',
                    'rmk' => 'Vérifier les paramètres référence et date_commande'
                ]
            ],
            'commande expiree' => [
                'fr' => [
                    'desc' => 'La date de commande dépasse le délai autorisé (+/- 24h)',
                    'rmk' => ''
                ]
            ],
            'commande grillee' => [
                'fr' => [
                    'desc' => 'Le nombre maximal de tentatives de fourniture de carte a été atteint (3 tentatives sont acceptées)',
                    'rmk' => 'La commande n’est plus acceptée par le serveur bancaire'
                ]
            ],
            'autorisation refusee' => [
                'fr' => [
                    'desc' => 'L’autorisation bancaire n’a pas été délivrée',
                    'rmk' => ''
                ]
            ],
            'annulation refusee' => [
                'fr' => [
                    'desc' => 'L’annulation de l’autorisation a été refusée',
                    'rmk' => 'L’annulation n’est pas effectuée'
                ]
            ],
            'la commande est deja annulee' => [
                'fr' => [
                    'desc' => 'La commande a été annulée lors d’une précédente capture',
                    'rmk' => 'Aucune requête ne sera acceptée sur cette commande'
                ]
            ],
            'paiement deja accepte' => [
                'fr' => [
                    'desc' => 'Une demande d’autorisation a déjà été délivrée pour cette commande',
                    'rmk' => ''
                ]
            ],
        ],
        '-1' => [
            'signature non valide' => [
                'fr' => [
                    'desc' => 'La signature MAC est invalide',
                ]
            ],
            'verification echouee (mode de paiement)' => [
                'fr' => [
                    'desc' => 'Le mode de paiement n’est pas compatible avec cette requête',
                    'rmk' => 'Par exemple : le paiement immédiat, car le recouvrement est fait automatiquement'
                ]
            ],
            'la demande ne peut aboutir' => [
                'fr' => [
                    'desc' => 'La demande de capture est formulée de manière incorrecte',
                    'rmk' => 'Vérifier les paramètres envoyés'
                ]
            ],
            'montant errone' => [
                'fr' => [
                    'desc' => 'Un des montants transmis est mal formaté',
                    'rmk' => 'Vérifier les 4 paramètres de montant'
                ]
            ],
            'commercant non identifie' => [
                'fr' => [
                    'desc' => 'Les paramètres servant à identifier le site commerçant ne sont pas corrects',
                    'rmk' => 'Vérifier les champs societe, lgue et TPE'
                ]
            ],
            'traitement en cours' => [
                'fr' => [
                    'desc' => 'La commande est en cours de traitement',
                ]
            ],
            'date erronee' => [
                'fr' => [
                    'desc' => 'La date ne respecte pas le format requis',
                    'rmk' => 'Vérifier le paramètre date'
                ]
            ],
            'autre traitement en cours' => [
                'fr' => [
                    'desc' => 'Une autre transaction est en cours de traitement sur la même référence',
                    'rmk' => 'Réitérer la demande'
                ]
            ],
            'indisponibilite temporaire du service' => [
                'fr' => [
                    'desc' => 'Service non disponible lors des opérations de maintenance en HNO',
                    'rmk' => 'Réitérer la demande en fin de maintenance'
                ]
            ],
            'probleme technique' => [
                'fr' => [
                    'desc' => 'Un problème technique est survenu',
                    'rmk' => 'Réitérer la demande'
                ]
            ],
        ],
    ];
}