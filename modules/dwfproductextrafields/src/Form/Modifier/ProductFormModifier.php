<?php
/**
 *   Copyright since 2009 ohmyweb!
 *
 *   @author    ohmyweb <contact@ohmyweb.fr>
 *   @copyright since 2009 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
 */

declare(strict_types=1);

namespace DwfProductExtraFields\Form\Modifier;

if (!defined('_PS_VERSION_')) {
    exit;
}

use DwfProductExtraFields\Form\Type\DwfProductExtraFieldsTabType;
use PrestaShopBundle\Form\FormBuilderModifier;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductFormModifier
{
    /**
     * @var FormBuilderModifier
     */
    private $formBuilderModifier;

    /**
     * @param FormBuilderModifier $formBuilderModifier
     */
    public function __construct(
        FormBuilderModifier $formBuilderModifier,
        TranslatorInterface $translator
    ) {
        $this->formBuilderModifier = $formBuilderModifier;
        $this->translator = $translator;
    }

    /**
     * @param int|null $productId
     * @param FormBuilderInterface $productFormBuilder
     */
    public function modify(
        FormBuilderInterface $productFormBuilder,
        int $productId,
        array $fields
    ): void {

        $this->formBuilderModifier->addBefore(
            $productFormBuilder,
            'shipping',
            'dwfproductextrafields',
            DwfProductExtraFieldsTabType::class,
            [
                'label' => $this->translator->trans('Extra Fields', [], 'Modules.Dwfproductextrafields.Admin'),
                'data' => [
                    'productId' => $productId,
                    'fields' => $fields,
                ],
            ]

        );
    }
}
