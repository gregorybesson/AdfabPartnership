<?php
namespace AdfabPartnership;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{

    public function onBootstrap (MvcEvent $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $translator = $serviceManager->get('translator');
        AbstractValidator::setDefaultTranslator($translator,'adfabcore');

        // If AdfabGame is installed, I can add my own partners to benefit from
        // this feature
        $e->getApplication()
            ->getEventManager()
            ->getSharedManager()
            ->attach('Zend\Mvc\Application', 'getPartners', array(
            $this,
            'updatePartners'
        ));
    }

    /**
     * This method get the partners and add them as array to AdfabGame form so
     * that there is non adherence between modules...
     * not that satisfied
     *
     * @param  EventManager $e
     * @return array
     */
    public function updatePartners ($e)
    {
        $partnersArray = $e->getParam('partners');

        $partnerService = $e->getTarget()
            ->getServiceManager()
            ->get('adfabpartnership_partner_service');
        $partners = $partnerService->getActivepartners();

        foreach ($partners as $partner) {
            $partnersArray[$partner->getId()] = $partner->getName();
        }

        return $partnersArray;
    }

    public function getConfig ()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig ()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'partnerSubscriber' => function($sm) {
                $locator = $sm->getServiceLocator();
                $viewHelper = new View\Helper\PartnerSubscriber;
                $viewHelper->setPartnerService($locator->get('adfabpartnership_partner_service'));

                return $viewHelper;
                },
            ),
        );
    }

    public function getServiceConfig ()
    {
        return array(
            'aliases' => array(
                'adfabpartnership_doctrine_em' => 'doctrine.entitymanager.orm_default'
            ),

            'invokables' => array(
                'adfabpartnership_partner_service' => 'AdfabPartnership\Service\Partner'
            ),

            'factories' => array(
                'adfabpartnership_module_options' => function  ($sm) {
                    $config = $sm->get('Configuration');

                    return new Options\ModuleOptions(isset($config['adfabpartnership']) ? $config['adfabpartnership'] : array());
                },
                'adfabpartnership_partner_mapper' => function  ($sm) {
                    return new Mapper\Partner($sm->get('adfabpartnership_doctrine_em'), $sm->get('adfabpartnership_module_options'));
                },
                'adfabpartnership_subscriber_mapper' => function  ($sm) {
                    return new Mapper\Subscriber($sm->get('adfabpartnership_doctrine_em'), $sm->get('adfabpartnership_module_options'));
                },
                'adfabpartnership_partner_form' => function  ($sm) {
                    $translator = $sm->get('translator');
                    $form = new Form\Admin\Partner(null, $sm, $translator);
                    $partner = new Entity\Partner();
                    $form->setInputFilter($partner->getInputFilter());

                    return $form;
                }
            )
        );
    }
}
