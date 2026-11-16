<?php
/**
 *   Copyright since 2009 ohmyweb!
 *
 *   @author    ohmyweb <contact@ohmyweb.fr>
 *   @copyright since 2009 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
 */

namespace DwfProductExtraFields\Form\Type;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'dwfproductextrafields_image';
    }

    public function getParent()
    {
        return FileType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'locales' => [],
            'form_theme' => '@Modules/dwfproductextrafields/views/templates/admin/field_image.html.twig',
            'data_class' => null,
        ]);
    }
}
