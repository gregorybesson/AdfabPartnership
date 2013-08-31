<?php

namespace AdfabPartnership\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AdfabPartnership\Options\ModuleOptions;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    protected $options, $partnerMapper, $adminActionService;

    public function listAction()
    {
        $partnerMapper = $this->getPartnerMapper();
        $partners = $partnerMapper->findAll();
        if (is_array($partners)) {
            $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($partners));
        } else {
            $paginator = $partners;
        }

        $paginator->setItemCountPerPage(100);
        $paginator->setCurrentPageNumber($this->getEvent()->getRouteMatch()->getParam('p'));

        return array(
            'partners' => $paginator,
        );
    }

    public function newsletterAction()
    {
        $partnerId = $this->getEvent()->getRouteMatch()->getParam('partnerId');
        $partner = $this->getPartnerMapper()->findById($partnerId);

        $subscriberMapper = $this->getAdminPartnerService()->getSubscriberMapper();
        $subscribers = $subscriberMapper->findBy(array('partner' => $partner));
        if (is_array($subscribers)) {
            $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($subscribers));
        } else {
            $paginator = $subscribers;
        }

        $paginator->setItemCountPerPage(100);
        $paginator->setCurrentPageNumber($this->getEvent()->getRouteMatch()->getParam('p'));

        return array(
                'subscribers' => $paginator,
                'partner'     => $partner
        );
    }

    public function downloadAction()
    {
        // magically create $content as a string containing CSV data
        $partnerId = $this->getEvent()->getRouteMatch()->getParam('partnerId');
        $partner   = $this->getPartnerMapper()->findById($partnerId);

        $subscriberMapper = $this->getAdminPartnerService()->getSubscriberMapper();
        $subscribers = $subscriberMapper->findBy(array('partner' => $partner));

        $content        = "\xEF\xBB\xBF"; // UTF-8 BOM
        $content       .= "ID;Pseudo;Nom;Prenom;E-mail;Optin\n";
        foreach ($subscribers as $s) {
            $content   .= $s->getUser()->getId()
            . ";" . $s->getUser()->getUsername()
            . ";" . $s->getUser()->getLastname()
            . ";" . $s->getUser()->getFirstname()
            . ";" . $s->getUser()->getEmail()
            . ";" . $s->getActive()
            ."\n";
        }

        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine('Content-Encoding: UTF-8');
        $headers->addHeaderLine('Content-Type', 'text/csv; charset=UTF-8');
        $headers->addHeaderLine('Content-Disposition', "attachment; filename=\"newsletter-". $partner->getName() .".csv\"");
        $headers->addHeaderLine('Accept-Ranges', 'bytes');
        $headers->addHeaderLine('Content-Length', strlen($content));

        $response->setContent($content);

        return $response;
    }

    public function createAction()
    {
        $form = $this->getServiceLocator()->get('adfabpartnership_partner_form');
        $form->get('submit')->setLabel('Créer');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = array_merge(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
            );
            $partner = $this->getAdminPartnerService()->create($data, 'adfabpartnership_partner_form');
            if ($partner) {
                $this->flashMessenger()->setNamespace('adfabpartnership')->addMessage('The partner was created');

                return $this->redirect()->toRoute('admin/adfabpartnership_admin/list');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('adfab-partnership/admin/partner');

        return $viewModel->setVariables(array('form' => $form));
    }

    public function editAction()
    {
        $partnerId = $this->getEvent()->getRouteMatch()->getParam('partnerId');
        $partner = $this->getPartnerMapper()->findById($partnerId);

        $form = $this->getServiceLocator()->get('adfabpartnership_partner_form');
        $form->get('submit')->setLabel('Mettre à jour');

        $request = $this->getRequest();

        $form->bind($partner);

        if ($request->isPost()) {
            $data = array_merge(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
            );
            $partner = $this->getAdminPartnerService()->edit($data, $partner, 'adfabpartnership_partner_form');
            if ($partner) {
                $this->flashMessenger()->setNamespace('adfabpartnership')->addMessage('The partner was updated');

                return $this->redirect()->toRoute('admin/adfabpartnership_admin/list');
            }
        }

        $viewModel = new ViewModel();
        $viewModel->setTemplate('adfab-partnership/admin/partner');

        return $viewModel->setVariables(array('form' => $form));
    }

    public function removeAction()
    {
        // TODO : Remove occurences of this partner in the games
        $partnerId = $this->getEvent()->getRouteMatch()->getParam('partnerId');
        $partner = $this->getPartnerMapper()->findById($partnerId);
        if ($partner) {
            try {
                $this->getPartnerMapper()->remove($partner);
            } catch (\Doctrine\DBAL\DBALException $e) {
            $this->flashMessenger()->setNamespace('adfabpartnership')->addMessage('Vous devez retirer ce partenaire des jeux pour pouvoir le supprimer');
            //throw $e;
        }

            $this->flashMessenger()->setNamespace('adfabpartnership')->addMessage('The partner was deleted');
        }

        return $this->redirect()->toRoute('admin/adfabpartnership_admin/list');
    }

    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceLocator()->get('adfabpartnership_module_options'));
        }

        return $this->options;
    }

    public function getPartnerMapper()
    {
        if (null === $this->partnerMapper) {
            $this->partnerMapper = $this->getServiceLocator()->get('adfabpartnership_partner_mapper');
        }

        return $this->partnerMapper;
    }

    public function setPartnerMapper(ActionMapperInterface $partnerMapper)
    {
        $this->partnerMapper = $partnerMapper;

        return $this;
    }

    public function getAdminPartnerService()
    {
        if (null === $this->adminActionService) {
            $this->adminActionService = $this->getServiceLocator()->get('adfabpartnership_partner_service');
        }

        return $this->adminActionService;
    }

    public function setAdminPartnerService($service)
    {
        $this->adminActionService = $service;

        return $this;
    }
}
