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

use DwfProductExtraFields\Component\HttpFoundation\File\OhUploadedFile;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class DwfProductExtraFieldsTabType extends BaseType
{
//    public function buildForm(FormBuilderInterface $builder, array $options): void
//    {
//        $builder->add('dwfproductextrafields', DwfFieldsType::class, $options);
//    }

    private $translator;
    private $context;
    private $module;

    public function __construct(TranslatorInterface $translator, LegacyContext $context)
    {
        $this->translator = $translator;
        $this->context = $context;
        $this->module = \Module::getInstanceByName('dwfproductextrafields');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $languages = $this->context->getLanguages();
        $fields = $options['data']['fields'];

        foreach ($fields as $field) {

            $newPart = array(
                'label' => $field['label'],
                'required' => boolval($field['required']),
            );

            if ($field['is_multi_lang']) {
                $newPart['locales'] = $languages;
            }

            if ($field['hint']) {
                $newPart['help'] = $field['hint'];
            }

            switch ($field['type']) {
                case 'image':
                    $imagePath = $this->module->getExtraFieldImagePath($options['data']['productId']);
                    $imageFile = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $imagePath . DIRECTORY_SEPARATOR . $field['value'];
                    if ($field['value'] && file_exists($imageFile)) {
                        $uploadedFile = new OhUploadedFile($imageFile, $field['value']);
                        $uploadedFile->setWebPath($field['image']);
                        $newPart['data'] = $uploadedFile;
                    }
                    $builder->add($field['name'], ImageType::class, $newPart);

                    break;

                case 'selector':
                    $choices = array();
                    if ($field['choices']) {
                        foreach ($field['choices'] as $val) {
                            $choices[$val['key']] = $val['name'];
                        }

                        $newPart['multiple'] = (bool)$field['multiple'];
                        $value = $field['value'];
                        if (!$newPart['multiple'] && is_array($value)) {
                            $value = reset($value);
                        }
                        $builder->add($field['name'], ChoiceType::class, array_merge($newPart, [
                            'placeholder' => $this->translator->trans('-- Choose --', [], 'Admin.Actions'),
                            'data' => $value,
                            'choices'  => $choices
                        ]));

                    }

                    break;

                case 'textarea':
                    $builder
                        ->add($field['name'], TranslateType::class,
                            array_merge($newPart,
                                [
                                    'type' => TextareaType::class,
                                    'data' => $field['value'],
                                    'hideTabs' => false,
                                    'options' => [
                                        'constraints' => [
                                            new CleanHtml([
                                                'message' => $this->translator->trans('This field is invalid', [], 'Admin.Notifications.Error'),
                                            ]),
                                        ],
                                    ],
                                ]));
                    break;

                case 'checkbox':
                    $builder->add($field['name'], SwitchType::class, array_merge($newPart, [
                        'data' => $field['value'],
                    ]));
                    break;

                case 'textarea_mce':
                    $builder
                        ->add($field['name'], TranslateType::class,
                            array_merge($newPart,
                                [
                                    'type' => FormattedTextareaType::class,
                                    'data' => $field['value'],
                                    'hideTabs' => false,
                                    'options' => [
                                        'constraints' => [
                                            new CleanHtml([
                                                'message' => $this->translator->trans('This field is invalid', [], 'Admin.Notifications.Error'),
                                            ]),
                                        ],
                                    ],
                                ]));
                    break;

                case 'price':
                    $builder->add($field['name'], MoneyType::class, array_merge($newPart, [
                        'currency' => $this->context->getEmployeeCurrency()->iso_code,
                    ]));
                    break;

                case 'non-translatable_text':
                case 'integer':
                case 'decimal':
                    $builder->add($field['name'], TextType::class, array_merge($newPart, [
                        'data' => $field['value'],
                    ]));
                    break;

                case 'date':
                    $builder->add($field['name'], DatePickerType::class, array_merge($newPart, [
                        'data' => $field['value'],
                    ]));
                    break;

                case 'datetime':
                    $builder->add($field['name'], DatePickerType::class, array_merge($newPart, [
                        'data' => $field['value'],
                    ]));
                    break;

                case 'color':
                    $builder->add($field['name'], ColorType::class, array_merge($newPart, [
                        'data' => $field['value'],
                    ]));
                    break;

                case 'repeater';
                    $builder->add($field['name'], RepeaterType::class,
                        array_merge($newPart,
                            [
                                'form_theme' => '@Modules/dwfproductextrafields/views/templates/admin/field_repeater.html.twig',
                                'values' => $field['values'],
                                'elements' => $field['elements'],
                                'collapsed_default' => $field['collapsed_default'],
                                'productId' => $options['data']['productId'],
                            ])
                    );

                    $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($field, $languages) {
                        $data = $event->getData();
                        $form = $event->getForm();

                        if (!isset($data[$field['name']]) || !is_array($data[$field['name']])) {
                            return;
                        }

                        $form->add($field['name'], RepeaterType::class, [
                            'compound' => true,
                            'allow_extra_fields' => true,
                        ]);

                        $repeaterForm = $form->get($field['name']);

                        foreach ($data[$field['name']] as $index => $rowData) {
                            $repeaterForm->add((string)$index, FormType::class, [
                                'compound' => true,
                                'allow_extra_fields' => true,
                            ]);

                            $rowForm = $repeaterForm->get((string)$index);

                            foreach ($field['elements'] ?? [] as $element) {
                                switch ($element['type']) {
                                    case 'text':
                                    case 'textarea':
                                    case 'textarea_mce':
                                        foreach ($languages as $lang) {
                                            $subFieldName = $element['id'] . '_' . $lang['id_lang'];
                                            $rowForm->add($subFieldName, TextType::class);
                                        }
                                        break;
                                    case 'checkbox':
                                    case 'non-translatable_text':
                                        $rowForm->add($element['id'], TextType::class);
                                        break;
                                    case 'image':
                                        $rowForm->add($element['id'].'_files', TextType::class);
                                        break;
                                }
                            }
                        }
                    });

                    break;

                default:
                    if ($newPart['required']) {
                        $newPart['constraints'] = [
                            new DefaultLanguage(),
                        ];
                    }
                    $builder->add($field['name'], TranslatableType::class, array_merge($newPart, [
                        'data' => $field['value'],
                        'type' => TextType::class,
                    ]));
                    break;
            }
        }
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'allow_extra_fields' => true,
        ]);
    }
}
