<?php

namespace MoneticoPaiement\Sdk\Utilities;

trait LogoWithAdditionalCards
{
    public function createLogoWithAdditionalCards($additional_cards, $checkout_logo_url, $new_logo_url)
    {
        $image_cb = imagecreatefrompng($checkout_logo_url);
        if (!$image_cb) {
            return false;
        }
        $image_cb_width = imagesx($image_cb);
        $image_cb_height = imagesy($image_cb);
        $additional_cards_images = [];
        $new_image_width = $image_cb_width;
        $new_image_height = $image_cb_height;
        $image_has_been_saved = false;

        foreach ($additional_cards as $additional_card) {
            $additional_cards_image = imagecreatefromjpeg(dirname(__FILE__) . "/../Assets/Img/" . mb_strtolower($additional_card) . ".jpg");
            if ($additional_cards_image) {
                $new_image_width += imagesx($additional_cards_image);
                $additional_cards_images[] = $additional_cards_image;
            } else {
                return false;
            }
        }

        $new_image = imagecreate($new_image_width, $new_image_height);
        if ($new_image) {
            $image_cb_has_been_copy = imagecopy($new_image, $image_cb, 0, 0, 0, 0, $image_cb_width, $image_cb_height);

            $image_position_x = 0;
            if ($image_cb_has_been_copy) {
                foreach ($additional_cards_images as $additional_cards_image) {
                    $image_position_x += imagesx($additional_cards_image);
                    if (!imagecopy($new_image, $additional_cards_image, $image_cb_width, 0, 0, 0, $image_position_x, imagesy($additional_cards_image))) {
                        return false;
                    }
                    $image_cb_width += imagesx($additional_cards_image);
                }
            } else {
                return false;
            }

            $image_has_been_saved = imagepng($new_image, $new_logo_url);
            imagedestroy($new_image);
        }

        return $image_has_been_saved && file_exists($new_logo_url);
    }
}