<?php

namespace AdfabPartnership\Form\Admin;

use Zend\Form\Form;
use Zend\Form\Element;
use ZfcBase\Form\ProvidesEventsForm;
use Zend\I18n\Translator\Translator;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\ServiceManager;

class Partner extends ProvidesEventsForm
{
    protected $serviceManager;

    public function __construct($name = null, ServiceManager $serviceManager, Translator $translator)
    {
        parent::__construct($name);

        $entityManager = $serviceManager->get('adfabpartnership_doctrine_em');

        // The form will hydrate an object
        // This is the secret for working with collections with Doctrine
        // (+ add'Collection'() and remove'Collection'() and "cascade" in corresponding Entity
        // https://github.com/doctrine/DoctrineModule/blob/master/docs/hydrator.md
        //$this->setHydrator(new DoctrineHydrator($entityManager, 'AdfabPartnership\Entity\Partner'));

        parent::__construct();
        $this->setAttribute('enctype','multipart/form-data');

        $this->add(array(
            'name' => 'id',
            'type'  => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => 0,
            ),
        ));

        $this->add(array(
            'name' => 'name',
            'options' => array(
                'label' => $translator->translate('Nom', 'adfabpartnership'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Nom', 'adfabpartnership'),
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'active',
            'options' => array(
                //'empty_option' => $translator->translate('Is the answer correct ?', 'adfabpartnership'),
                'value_options' => array(
                    '0' => $translator->translate('Non', 'adfabpartnership'),
                    '1' => $translator->translate('Oui', 'adfabpartnership'),
                ),
                'label' => $translator->translate('Actif', 'adfabpartnership'),
            ),
        ));

        // Adding an empty upload field to be able to correctly handle this on the service side.
        $this->add(array(
            'name' => 'uploadLogo',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => $translator->translate('Logo', 'adfabpartnership'),
            ),
        ));
        $this->add(array(
            'name' => 'logo',
            'type'  => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                    'value' => '',
            ),
        ));

        // Adding an empty upload field to be able to correctly handle this on the service side.
        $this->add(array(
            'name' => 'uploadSmallLogo',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => $translator->translate('Petit logo', 'adfabpartnership'),
            ),
        ));

        $this->add(array(
            'name' => 'smallLogo',
            'type'  => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'value' => '',
            ),
        ));

        $this->add(array(
            'name' => 'website',
            'options' => array(
                'label' => $translator->translate('Site Web', 'adfabpartnership'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Site Web', 'adfabpartnership'),
            ),
        ));

        $this->add(array(
            'name' => 'facebook',
            'options' => array(
                'label' => $translator->translate('Page Facebook', 'adfabpartnership'),
            ),
            'attributes' => array(
                'type' => 'text',
                'placeholder' => $translator->translate('Page Facebook', 'adfabpartnership'),
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'newsletter',
            'options' => array(
            //'empty_option' => $translator->translate('Is the answer correct ?', 'adfabpartnership'),
                'value_options' => array(
                    '0' => $translator->translate('Non', 'adfabpartnership'),
                    '1' => $translator->translate('Oui', 'adfabpartnership'),
                ),
                'label' => $translator->translate('Activer l\'inscription newsletter', 'adfabpartnership'),
            ),
        ));

        $this->add(array(
            'name' => 'bouncePage',
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'label' => $translator->translate('Page de recirculation', 'adfabuser'),
                'value_options' => array(
                    '1' => $translator->translate('Oui', 'adfabuser'),
                    '0' => $translator->translate('Non', 'adfabuser'),
                ),
            ),
        ));

        $submitElement = new Element\Button('submit');
        $submitElement
        ->setLabel($translator->translate('Create', 'adfabpartnership'))
        ->setAttributes(array(
            'type'  => 'submit',
        ));

        $this->add($submitElement, array(
            'priority' => -100,
        ));

    }
}
