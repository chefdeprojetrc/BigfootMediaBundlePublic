<?php

namespace Bigfoot\Bundle\MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MediaMetadataType
 * @package Bigfoot\Bundle\MediaBundle\Form
 */
class MediaMetadataType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $data = $event->getData();
                    $form = $event->getForm();

                    if (!$data) {
                        return null;
                    }

                    $form->add(
                        'value',
                        'text',
                        array(
                            'label' => $data->getType()
                        )
                    );
                }
            )
            ->add('value')
            ->add(
                'translation',
                'translatable_entity'
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bigfoot\Bundle\MediaBundle\Entity\MediaMetadata'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_media_mediametadatatype';
    }
}
