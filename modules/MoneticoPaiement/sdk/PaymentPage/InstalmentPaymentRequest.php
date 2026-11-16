<?php

namespace MoneticoPaiement\Sdk\PaymentPage;

use MoneticoPaiement\Sdk\Utilities\Helper;

class InstalmentPaymentRequest extends PaymentRequest
{
    /**
     * @var int
     * Reference of the terminal
     */
    private $nbrEch;

    /**
     * @var array
     * Deadline date of N = {1;4}
     */
    private $dateEch;

    /**
     * @var array
     * Deadline amount of N = {1;4}
     */
    private $montantEch;


    public function getFormFieldsWithoutMac()
    {
        $formFields = parent::getFormFieldsWithoutMac();

        $nbr_ech = $this->getNbrEch();
        if ($nbr_ech) {
            $formFields['nbrech'] = $nbr_ech;

            $date_ech1 = $this->getDateEchN(1);
            $formFields['dateech1'] = $date_ech1->format("d/m/Y");
            $formFields['montantech1'] = Helper::formatAmount($this->getMontantEchN(1), $this->getCurrency());

            for ($i = 2; $i <= $nbr_ech; $i++) {
                $formFields['dateech' . $i] = Helper::monthShifter($date_ech1, ($i - 1))->format("d/m/Y");
                if ($this->getMontantEchN($i) > 0) {
                    $formFields['montantech' . $i] = Helper::formatAmount($this->getMontantEchN($i), $this->getCurrency());
                }
            }
        }

        return $formFields;
    }

    /**
     * @return int
     */
    public function getNbrEch()
    {
        return $this->nbrEch;
    }

    /**
     * @param int $nbrEch
     */
    public function setNbrEch(int $nbrEch): void
    {
        $this->nbrEch = $nbrEch;
    }

    /**
     * @return array
     */
    public function getDateEch(): array
    {
        return $this->dateEch;
    }

    /**
     * @param int $n
     *
     * @return \DateTime
     */
    public function getDateEchN(int $n): \DateTime
    {
        return $this->dateEch[$n];
    }

    /**
     * @param \DateTime $date
     * @param int       $n
     *
     * @return void
     */
    public function setDateEchN(\DateTime $date, int $n): void
    {
        $this->dateEch[$n] = $date;
    }

    /**
     * @return array
     */
    public function getMontantEch(): array
    {
        return $this->montantEch;
    }

    /**
     * @return float
     * @var int
     */
    public function getMontantEchN(int $n): float
    {
        return $this->montantEch[$n];
    }

    /**
     * @param float $amount
     * @param int   $n
     *
     * @return void
     */
    public function setMontantEchN(float $amount, int $n): void
    {
        $this->montantEch[$n] = $amount;
    }
}