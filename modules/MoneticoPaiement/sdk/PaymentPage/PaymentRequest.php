<?php
namespace MoneticoPaiement\Sdk\PaymentPage;

use MoneticoPaiement\Sdk\Utilities\Helper;
use MoneticoPaiement\Sdk\Utilities\HmacComputer;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext;

class PaymentRequest
{
    /**
     * @var string
     * Reference of the terminal
     */
    private $ept;

    /**
     * @var string
     *  Terminal version , default = 3.0
     */
    private $ept_version = "3.0";

    /**
     * @var string
     * Reference of the societe number
     */
    private $code_societe;
    /**
     * @var string
     * Etp mac used to seal fields
     */
    private $cle_mac;
    /**
     * @var string
     * Reference of the payment
     */
    private $reference;

    /**
     * @var \DateTime
     * Date of the payment
     */
    private $date;

    /**
     * @var float
     * Amount of the payment
     */
    private $amount;

    /**
     * @var string
     * Currency of the payment
     * Must be a string from the Currency class
     */
    private $currency;

    /**
     * @var string
     * Display language of the payment page
     * Must be a string from the Language class
     */
    private $langue;

    /**
     * @var OrderContext
     * Customer Order context
     */
    private $contexteCommande;

    /**
     * @var ?string
     * Free text associated with this payment.
     * Free text allows you to associate any text that you wish to see on your dashboard when viewing this payment.
     *
     * Careful : The content of this field may be blocked for security reasons.
     * Therefore, it is recommended to avoid special characters and line feeds.
     */
    private $texteLibre;


    /**
     * @var ?string
     * Client e-mail address
     */
    private $mail;

    /**
     * @var ?string
     * URL of the page that will be used by Monetico payment page to redirect the client if the payment has been done successfully
     */
    private $urlRetourOk;

    /**
     * @var ?string
     * URL of the page that will be used by Monetico payment page to redirect the client if the payment has failed
     */
    private $urlRetourErreur;

    /**
     * @var ?bool
     * Indicates if the merchant wish not to authenticate the client (optional parameter)
     */
    private $threeDSecureDebrayable;

    /**
     * @var ?string
     * Indicates the merchant wish about client authentication in 3DSecure V2 protocol (optional parameter)
     *
     * Must be a string from the ThreeDSecureChallengeValue class
     */
    private $threeDSecureChallenge;

    /**
     * @var ?string
     * Label that will appear on the card holder's bank statement (optional parameter)
     */
    private $libelleMonetique;

    /**
     * @var ?string
     * Location/city that will appear on the card holder's bank statement (optional parameter)
     */
    private $libelleMoneticoLocalite;

    /**
     * @var ?string[]
     * Indicates the alternate payment mode that the payment page must not display for this payment (optional parameter)
     *
     * Must contain string from the PaymentProtocol class
     */
    private $desactiveMoyenPaiement;

    /**
     * @var ?string
     * Alias of the client bank card to use (optional parameter) (requires subscription to the "express payment" option)
     */
    private $aliasCb;

    /**
     * @var ?bool
     * Forces the client to input a bank card (optional parameter) (requires subscription to the "express payment" option)
     */
    private $forceSaisieCb;

    /**
     * @var ?string
     * Indicates to the payment page that this payment has to be proceed using selected protocol.
     * The client will be immediately redirected to the partner protocol page and will not see the Monetico payment page itself.
     * (optional parameter) (requires subscription to a partner payment protocol)
     *
     * Must contain string from the PaymentProtocol class
     */
    private $protocole;
    /**
     * Required Fields
     */
    public $required_fields = ['ept'];

    public function getFormFields()
    {
        $formFields = $this->getFormFieldsWithoutMac();

        $hmacComputer = new HmacComputer();
        $seal = $hmacComputer->sealFields($formFields, $this->getCleMac());
        $formFields['MAC'] = $seal;
        return $formFields;
    }

    public function getFormFieldsWithoutMac()
    {

        $formFields = [];

        // Payment terminal
        $formFields["TPE"] = $this->getEpt();
        $formFields["societe"] = $this->getCodeSociete();
        $formFields["lgue"] = $this->getLangue();
        $formFields["version"] = $this->getEptVersion();

        // Payment informations
        $formFields["reference"] = $this->getReference();
        $formFields["date"] = $this->getDate()->format("d/m/Y:H:i:s");
        $formFields["montant"] = Helper::formatAmount($this->getAmount(), $this->getCurrency());
        $formFields["contexte_commande"] = base64_encode(json_encode($this->getContexteCommande()));


        // Optional parameters
        if (is_string($this->getTexteLibre())) {
            $formFields["texte-libre"] = $this->getTexteLibre();
        }

        if (is_string($this->getMail())) {
            $formFields["mail"] = $this->getMail();
        }

        if (is_string($this->getUrlRetourOk())) {
            $formFields["url_retour_ok"] = $this->getUrlRetourOk();
        }

        if (is_string($this->getUrlRetourErreur())) {
            $formFields["url_retour_err"] = $this->getUrlRetourErreur();
        }

        if (!is_null($this->getThreeDSecureDebrayable())) {
            $formFields["3dsdebrayable"] = $this->getThreeDSecureDebrayable() ? "1" : "0";
        }

        if (is_string($this->getThreeDSecureChallenge())) {
            $formFields["ThreeDSecureChallenge"] = $this->getThreeDSecureChallenge();
        }

        if (is_string($this->getLibelleMonetique())) {
            $formFields["libelleMonetique"] = $this->getLibelleMonetique();
        }

        if (is_string($this->getLibelleMoneticoLocalite())) {
            $formFields["libelleMonetiqueLocalite"] = $this->getLibelleMoneticoLocalite();
        }

        if ($this->getDesactiveMoyenPaiement() != null && !empty($this->getDesactiveMoyenPaiement())) {
            $formFields["desactivemoyenpaiement"] = implode(',', $this->getDesactiveMoyenPaiement());
        }

        if (is_string($this->getAliasCb())) {
            $formFields["aliascb"] = $this->getAliasCb();
        }

        if (!is_null($this->getForceSaisieCb())) {
            $formFields["forcesaisiecb"] = $this->getForceSaisieCb() ? "1" : "0";
        }

        if (is_string($this->getProtocole())) {
            $formFields["protocole"] = $this->getProtocole();
        }
        return $formFields;
    }


    /**
     * @return string
     */
    public function getEpt(): string
    {
        return $this->ept;
    }

    /**
     * @param string $ept     */
    public function setEPT(string $ept)
    {
        $this->ept = $ept;
    }

    /**
     * @return string
     */
    public function getEptVersion(): string
    {
        return $this->ept_version;
    }

    /**
     * @param string $ept_version
     */
    public function setEptVersion(string $ept_version)
    {
        $this->ept_version = $ept_version;
    }

    /**
     * @return string
     */
    public function getCodeSociete(): string
    {
        return $this->code_societe;
    }

    /**
     * @param string $code_societe
     */
    public function setCodeSociete(string $code_societe)
    {
        $this->code_societe = $code_societe;
    }
    /**
     * @return string
     */
    public function getCleMac(): string
    {
        return $this->cle_mac;
    }

    /**
     * @param string $cle_mac
     */
    public function setCleMac(string $cle_mac)
    {
        $this->cle_mac = $cle_mac;
    }
    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount)
    {
        $this->amount = round($amount,2);
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getLangue(): string
    {
        return $this->langue;
    }

    /**
     * @param string $langue
     */
    public function setLangue(string $langue)
    {
        $this->langue = $langue;
    }

    /**
     * @return OrderContext
     */
    public function getContexteCommande(): OrderContext
    {
        return $this->contexteCommande;
    }

    /**
     * @param OrderContext $contexteCommande
     */
    public function setContexteCommande(OrderContext $contexteCommande)
    {
        $this->contexteCommande = $contexteCommande;
    }

    /**
     * @return string|null
     */
    public function getTexteLibre()
    {
        return $this->texteLibre;
    }

    /**
     * @param string|null $texteLibre
     */
    public function setTexteLibre(string $texteLibre)
    {
        $this->texteLibre = $texteLibre;
    }

    /**
     * @return string|null
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param string|null $mail
     */
    public function setMail(string $mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return string|null
     */
    public function getUrlRetourOk()
    {
        return $this->urlRetourOk;
    }

    /**
     * @param string|null $urlRetourOk
     */
    public function setUrlRetourOk(string $urlRetourOk)
    {
        $this->urlRetourOk = $urlRetourOk;
    }

    /**
     * @return string|null
     */
    public function getUrlRetourErreur()
    {
        return $this->urlRetourErreur;
    }

    /**
     * @param string|null $urlRetourErreur
     */
    public function setUrlRetourErreur(string $urlRetourErreur)
    {
        $this->urlRetourErreur = $urlRetourErreur;
    }
    /**
     * @return bool|null
     */
    public function getThreeDSecureDebrayable()
    {
        return $this->threeDSecureDebrayable;
    }

    /**
     * @param bool|null $threeDSecureDebrayable
     */
    public function setThreeDSecureDebrayable(bool $threeDSecureDebrayable)
    {
        $this->threeDSecureDebrayable = $threeDSecureDebrayable;
    }

    /**
     * @return string|null
     */
    public function getThreeDSecureChallenge()
    {
        return $this->threeDSecureChallenge;
    }

    /**
     * @param string|null $threeDSecureChallenge
     */
    public function setThreeDSecureChallenge(string $threeDSecureChallenge)
    {
        $this->threeDSecureChallenge = $threeDSecureChallenge;
    }

    /**
     * @return string|null
     */
    public function getLibelleMonetique()
    {
        return $this->libelleMonetique;
    }

    /**
     * @param string|null $libelleMonetique
     */
    public function setLibelleMonetique(string $libelleMonetique)
    {
        $this->libelleMonetique = $libelleMonetique;
    }

    /**
     * @return string|null
     */
    public function getLibelleMoneticoLocalite()
    {
        return $this->libelleMoneticoLocalite;
    }

    /**
     * @param string|null $libelleMoneticoLocalite
     */
    public function setLibelleMoneticoLocalite(string $libelleMoneticoLocalite)
    {
        $this->libelleMoneticoLocalite = $libelleMoneticoLocalite;
    }

    /**
     * @return string[]|null
     */
    public function getDesactiveMoyenPaiement()
    {
        return $this->desactiveMoyenPaiement;
    }

    /**
     * @param string[]|null $desactiveMoyenPaiement
     */
    public function setDesactiveMoyenPaiement(array $desactiveMoyenPaiement)
    {
        $this->desactiveMoyenPaiement = $desactiveMoyenPaiement;
    }

    /**
     * @return string|null
     */
    public function getAliasCb()
    {
        return $this->aliasCb;
    }

    /**
     * @param string|null $aliasCb
     */
    public function setAliasCb(string $aliasCb)
    {
        $this->aliasCb = $aliasCb;
    }

    /**
     * @return bool|null
     */
    public function getForceSaisieCb()
    {
        return $this->forceSaisieCb;
    }

    /**
     * @param bool|null $forceSaisieCb
     */
    public function setForceSaisieCb(bool $forceSaisieCb)
    {
        $this->forceSaisieCb = $forceSaisieCb;
    }

    /**
     * @return string|null
     */
    public function getProtocole()
    {
        return $this->protocole;
    }

    /**
     * @param string|null $protocole
     */
    public function setProtocole(string $protocole)
    {
        $this->protocole = $protocole;
    }
}