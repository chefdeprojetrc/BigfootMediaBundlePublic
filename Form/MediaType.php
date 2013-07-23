<?php

namespace Bigfoot\Bundle\MediaBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                if (!$data) {
                    return null;
                }

                $mediaRepo = $this->entityManager->getRepository('BigfootMediaBundle:Media');
                $mediaRepo->initMetadata($data);

                $form->add('metadatas', 'collection', array(
                    'type' => new MediaMetadataType(),
                    'options' => array(
                        'required' => false,
                    ),
                ));
            })
            ->add('tags', 'bigfoot_tag')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Bigfoot\Bundle\MediaBundle\Entity\Media'
        ));
    }

    public function getName()
    {
        return 'bigfoot_media_mediatype';
    }
}
