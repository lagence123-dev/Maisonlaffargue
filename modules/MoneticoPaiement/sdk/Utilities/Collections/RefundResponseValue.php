<?php

namespace MoneticoPaiement\Sdk\Utilities\Collections;

/**
 * Class RefundResponseValue
 * @package MoneticoPaiement\Sdk\Utilities\Collections;
 * TAKE THE documentation technique page 60 for more informations
 */
abstract class RefundResponseValue
{
    const DEFAULT_LANGUAGE = 'fr';
    /*
     * Format
     * [[
     *      'cdr' => [
     *              'fr'=>[
         *              'desc' => X ,
         *              'rmk' => Y
     *              ],
     *              'en' => [
     *                  'desc' => X ,
     *                   'rmk' => Y
     *              ]
     *      ]
     *   ]
     * ]
     */
    const RESPONSE = [
        "0" => [
            'fr' => [
                'desc' => "La demande de recrédit a été prise en compte",
                'rmk' => ""
            ],
        ],
        "-1" => [
            'fr' => [
                'desc' => "La demande de recrédit n’a pas été prise en compte",
                'rmk' => ""
            ],
        ],
        "-30" => [
            'fr' => [
                'desc' => "Les paramètres servant à identifier le site commerçant ne sont pas corrects",
                'rmk' => "Vérifier les paramètres societe, TPE et lgue. Vérifier également que l'adresse IP du serveur est autorisée à faire des remboursements sur le contrat TPE utilisé pour le paiement"
            ],
        ],
        "-31" => [
            'fr' => [
                'desc' => "La signature MAC est invalide",
            ],
        ],
        "-32" => [
            'fr' => [
                'desc' => "Votre TPE n’est pas autorisé à effectuer des recrédits",
                'rmk' => "Contacter l’assistance technique"
            ],
        ],
        "-33" => [
            'fr' => [
                'desc' => "La date de recrédit dépasse le délai autorisé (+/- 24h)",
                'rmk' => "Vérifier le paramètre date"
            ],
        ],
        "-34" => [
            'fr' => [
                'desc' => "Le montant à recréditer est incorrect",
                'rmk' => "Vérifier le paramètre date"
            ],
        ],
        "-35" => [
            'fr' => [
                'desc' => "Les montants transmis ne sont pas en phase avec ceux du serveur bancaire",
                'rmk' => "Vérifier les champs montant_recredit et montant_possible"
            ],
        ],
        "-36" => [
            'fr' => [
                'desc' => "Le nombre maximum de recrédits pour votre TPE a été atteint",
            ],
        ],
        "-37" => [
            'fr' => [
                'desc' => "La commande n’existe pas",
                'rmk' => "Vérifier que les champs permettant d’identifier la commande sont corrects"
            ],
        ],
        "-38" => [
            'fr' => [
                'desc' => "La commande n’a pas encore été payée, aucun recrédit ne peut être effectué",
            ],
        ],
        "-39" => [
            'fr' => [
                'desc' => "Une demande d’autorisation a déjà été délivrée pour cette commande",
            ],
        ],
        "-40" => [
            'fr' => [
                'desc' => "Le montant à recréditer est incorrect",
            ],
        ],
        "-41" => [
            'fr' => [
                'desc' => "Problème technique",
                'rmk' => "Réitérer la demande",
            ],
        ],
        "-42" => [
            'fr' => [
                'desc' => "La devise transmise ne correspond pas à la devise de la commande",
                'rmk' => "Vérifier le paramètre devise",
            ],
        ],
        "-43" => [
            'fr' => [
                'desc' => "Un ou plusieurs paramètres ne respectent pas le format requis",
                'rmk' => "Vérifier la longueur des champs et le format des dates",
            ],
        ],
        "-44" => [
            'fr' => [
                'desc' => "Une autre transaction est en cours de traitement sur la même référence ; cela peut être un autre traitement que recredit_paiement",
                'rmk' => "Réitérer la demande",
            ],
        ],
    ];

    public function getValue($cdr, $language = 'fr')
    {
        return isset($this::RESPONSE[$cdr][$language]) ? $this::RESPONSE[$cdr][$language] : $this::RESPONSE[$cdr][$this::DEFAULT_LANGUAGE];
    }

}