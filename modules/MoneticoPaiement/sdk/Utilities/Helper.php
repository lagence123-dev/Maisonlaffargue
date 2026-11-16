<?php

namespace MoneticoPaiement\Sdk\Utilities;

class Helper
{
    /**
     * Concat amount and currency
     *
     * @param $amount
     * @param $currency
     *
     * @return string
     */
    public static function formatAmount($amount, $currency)
    {
        return $amount . $currency;
    }

    /**
     * Generate a ref order
     *
     * @param int $len
     *
     * @return string
     * @throws \Exception
     */
    public static function generateRefOrder($prefix = "MP", $len = 10)
    {
        $reference = "";
        $listAlpha = str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

        for ($s = 1; $s <= $len; $s++) {
            $reference .= mb_substr($listAlpha, random_int(0, 61), 1);
        }

        return $prefix . $reference;
    }

    /**
     * Get amount whithout currency
     *
     * @param string $amountWithCurrency
     *
     * @return float
     */
    public static function getAmountWithoutCurrency(string $amountWithCurrency): float
    {
        return floatval(mb_substr($amountWithCurrency, 0, mb_strlen($amountWithCurrency) - 3));
    }


    /**
     * Get Formating version
     *
     * @param $version
     *
     * @return string
     */
    public static function getFormatingVersion($version): string
    {
        $pos = mb_strpos($version, "-", 1);
        if ($pos !== false) {
            $version = mb_substr($version, 0, $pos);
        }
        return $version;
    }

    /**
     *  Month Shifter
     *
     * @param $date
     * @param $months
     *
     * @return mixed
     */
    public static function monthShifter($date, $months)
    {
        $dateA = clone $date;
        $dateB = clone $date;
        $plusMonths = clone $dateA->modify($months . ' Month');
        //it is checked whether the reverse modification gives the same
        if ($dateB != $dateA->modify($months * -1 . ' Month')) {
            //if not, the last day of last month is recovered
            $result = $plusMonths->modify('last day of last month');
        } else {
            //if that is the case, we are consistent, we can keep that date
            $result = $plusMonths;
        }
        return $result;
    }

    /**
     * On Windows this may return a date *prior* to the creation date of the file on the local filesystem.
     * On Unix this data may just not exist at all (note Florent: I do not know what the method will return then)...
     *
     * @return string|null The file creation datetime in 'Y-m-d H:i:s' format, or null
     */
    public static function getFileCreationDate(string $fileAbsolutePath): ?string
    {
        $creationTS = filectime($fileAbsolutePath); // this actually return the file's "inode change time", whatever that really means
        if (is_int($creationTS)) {
            return date('Y-m-d H:i:s', $creationTS);
        }

        return null;
    }

    /**
     * @return string|null The file last modification datetime in 'Y-m-d H:i:s' format, or null
     */
    public static function getFileModifDate(string $fileAbsolutePath): ?string
    {
        $creationTS = filemtime($fileAbsolutePath);
        if (is_int($creationTS)) {
            return date('Y-m-d H:i:s', $creationTS);
        }

        return null;
    }

    /**
     * Shows only the 4 first and 4 last characters of the passed value, separated by 8 stars.
     * Ie: abcd*********1234
     */
    public static function getObfuscatedMac(string $mac): string
    {
        return substr($mac, 0, 4) . '*********' . substr($mac, -4);
    }
}