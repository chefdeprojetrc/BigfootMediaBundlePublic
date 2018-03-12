<?php

namespace Bigfoot\Bundle\MediaBundle\Form;

use Bigfoot\Bundle\CoreBundle\Form\Type\BigfootTagType;
use Bigfoot\Bundle\MediaBundle\Entity\Media;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class MediaType
 * @package Bigfoot\Bundle\MediaBundle\Form
 */
class MediaType extends AbstractType
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $this->entityManager;

        $builder
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($em) {
                    $data = $event->getData();
                    $form = $event->getForm();

                    if (!$data) {
                        return null;
                    }

                    $mediaRepo = $em->getRepository('BigfootMediaBundle:Media');
                    $mediaRepo->initMetadata($data);

                    $form->add(
                        'metadatas',
                        CollectionType::class,
                        array(
                            'label' => ' ',
                            'entry_type' => MediaMetadataType::class,
                            'entry_options' => array(
                                'required' => false,
                                'attr' => array(
                                    'class' => 'metadatas'
                                )
                            ),
                        )
                    );
                }
            )
            ->add(
                'tags',
                BigfootTagType::class,
                array(
                    'label' => 'Tags',
                )
            )
            ->add(
                'cropPosition',
                ChoiceType::class,
                array(
                    'label' => 'Position du crop',
                    'choices' => [
                        'En haut à gauche' => Media::CROP_TOP_LEFT,
                        'En haut' => Media::CROP_TOP_CENTER,
                        'En haut à droite' => Media::CROP_TOP_RIGHT,
                        'A gauche' => Media::CROP_CENTER_LEFT,
                        'Au centre' => Media::CROP_CENTER_CENTER,
                        'A droite' => Media::CROP_CENTER_RIGHT,
                        'En bas à gauche' => Media::CROP_BOTTOM_LEFT,
                        'En bas' => Media::CROP_BOTTOM_CENTER,
                        'En bas à droite' => Media::CROP_BOTTOM_RIGHT,
                    ]
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Bigfoot\Bundle\MediaBundle\Entity\Media'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_media_mediatype';
    }
}
