<?php

namespace AdfabPartnership\Service;

use AdfabPartnership\Entity\Partner as PartnerEntity;

use Zend\Form\Form;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use AdfabPartnership\Options\ModuleOptions;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;
use Zend\Stdlib\ErrorHandler;

class Partner extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var PartnerMapperInterface
     */
    protected $partnerMapper;
	
	protected $subscriberMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var UserServiceOptionsInterface
     */
    protected $options;

    /**
     *
     * This service is ready for all types of games
     *
     * @param  array                            $data
     * @param  string                           $entityClass
     * @param  string                           $formClass
     * @return \AdfabPartnership\Entity\Partner
     */
    public function create(array $data, $formClass)
    {
        $partner  = new PartnerEntity;
        $entityManager = $this->getServiceManager()->get('adfabpartnership_doctrine_em');

        $form  = $this->getServiceManager()->get($formClass);

        $form->bind($partner);

        $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
        $media_url = $this->getOptions()->getMediaUrl() . '/';

        $input = $form->getInputFilter()->get('name');
        $noObjectExistsValidator = new NoObjectExistsValidator(array(
                'object_repository' => $entityManager->getRepository('AdfabPartnership\Entity\Partner'),
                'fields'            => 'name',
                'messages'          => array('objectFound' => 'Ce nom existe déjà !')
        ));

        $input->getValidatorChain()->addValidator($noObjectExistsValidator);
		
		if (!empty($data['website']))
		{
			$data['website'] = $this->verifyProtocol($data['website']);
		}
		
		 if (!empty($data['facebook']))
		{
			$data['facebook'] = $this->verifyProtocol($data['facebook'], 'https://');
		}

        $form->setData($data);

        if (!$form->isValid()) {
            return false;
        }

        $partner = $this->getPartnerMapper()->insert($partner);

        if (!empty($data['uploadLogo']['tmp_name'])) {
            ErrorHandler::start();
            move_uploaded_file($data['uploadLogo']['tmp_name'], $path . $partner->getId() . "-" . $data['uploadLogo']['name']);
            $partner->setLogo($media_url . $partner->getId() . "-" . $data['uploadLogo']['name']);
            ErrorHandler::stop(true);
        }

        if (!empty($data['uploadSmallLogo']['tmp_name'])) {
            ErrorHandler::start();
            move_uploaded_file($data['uploadSmallLogo']['tmp_name'], $path . $partner->getId() . "-" . $data['uploadSmallLogo']['name']);
            $partner->setSmallLogo($media_url . $partner->getId() . "-" . $data['uploadSmallLogo']['name']);
            ErrorHandler::stop(true);
        }

        $partner = $this->getPartnerMapper()->update($partner);

        return $partner;
    }

    /**
     *
     * This service is ready for all types of games
     *
     * @param  array                        $data
     * @param  string                       $entityClass
     * @param  string                       $formClass
     * @return \AdfabPartner\Entity\Partner
     */
    public function edit(array $data, $partner, $formClass)
    {
        $entityManager = $this->getServiceManager()->get('adfabpartnership_doctrine_em');

        $form  = $this->getServiceManager()->get($formClass);

        $form->bind($partner);

        $path = $this->getOptions()->getMediaPath() . DIRECTORY_SEPARATOR;
        $media_url = $this->getOptions()->getMediaUrl() . '/';

        // TODO : This verification needs to be done after removing the edited partner...
        /*$input = $form->getInputFilter()->get('name');
        $noObjectExistsValidator = new NoObjectExistsValidator(array(
                'object_repository' => $entityManager->getRepository('AdfabPartnership\Entity\Partner'),
                'fields'            => 'name',
                'messages'          => array('objectFound' => 'Ce nom existe déjà !')
        ));

        $input->getValidatorChain()->addValidator($noObjectExistsValidator);*/

        if (!empty($data['website']))
		{
			$data['website'] = $this->verifyProtocol($data['website']);
		}
		
		 if (!empty($data['facebook']))
		{
			$data['facebook'] = $this->verifyProtocol($data['facebook'], 'https://');
		}
        
        $form->setData($data);

        if (!$form->isValid()) {
            return false;
        }

        if (!empty($data['uploadLogo']['tmp_name'])) {
            ErrorHandler::start();
            move_uploaded_file($data['uploadLogo']['tmp_name'], $path . $partner->getId() . "-" . $data['uploadLogo']['name']);
            $partner->setLogo($media_url . $partner->getId() . "-" . $data['uploadLogo']['name']);
            ErrorHandler::stop(true);
        }

        if (!empty($data['uploadSmallLogo']['tmp_name'])) {
            ErrorHandler::start();
            move_uploaded_file($data['uploadSmallLogo']['tmp_name'], $path . $partner->getId() . "-" . $data['uploadSmallLogo']['name']);
            $partner->setSmallLogo($media_url . $partner->getId() . "-" . $data['uploadSmallLogo']['name']);
            ErrorHandler::stop(true);
        }

        $partner = $this->getPartnerMapper()->update($partner);

        return $partner;
    }

    /**
     *
     * @param array $data
     */
    public function isSubscriber($partner, $user)
    {
        return $this->getSubscriberMapper()->isSubscriber($partner, $user);
    }
	
	 public function findSubscribers($partner)
    {
        return $this->getSubscriberMapper()->findSubscribers($partner);
    }

    /**
     * Newsletter optins are updated
     *
     * @param array $data
     */
    public function updateNewsletter(array $data, $user, $partner)
    {
        $subscription = new \AdfabPartnership\Entity\Subscriber();
        $subscription->setPartner($partner);
        $subscription->setUser($user);
        $subscription->setActive($data['optin']);
        $subscription = $this->getSubscriberMapper()->update($subscription);

        return $subscription;
    }

    public function getActivepartners()
    {
        $em = $this->getServiceManager()->get('adfabpartnership_doctrine_em');

        $query = $em->createQuery('SELECT p FROM AdfabPartnership\Entity\Partner p WHERE p.active = true');
        $partners = $query->getResult();

        return $partners;
    }

    /**
     * getPartnerMapper
     *
     * @return PartnerMapperInterface
     */
    public function getPartnerMapper()
    {
        if (null === $this->partnerMapper) {
            $this->partnerMapper = $this->getServiceManager()->get('adfabpartnership_partner_mapper');
        }

        return $this->partnerMapper;
    }

    /**
     * setPartnerMapper
     *
     * @param  PartnerMapperInterface $entryMapper
     * @return Partner
     */
    public function setPartnerMapper($partnerMapper)
    {
        $this->partnerMapper = $partnerMapper;

        return $this;
    }

    /**
     * getSubscriberMapper
     *
     * @return SubscriberMapperInterface
     */
    public function getSubscriberMapper()
    {
        if (null === $this->subscriberMapper) {
            $this->subscriberMapper = $this->getServiceManager()->get('adfabpartnership_subscriber_mapper');
        }

        return $this->subscriberMapper;
    }

    /**
     * setSubscriberMapper
     *
     * @param  SubscriberMapperInterface $subscriberMapper
     * @return Subscriber
     */
    public function setSubscriberMapper($subscriberMapper)
    {
        $this->subscriberMapper = $subscriberMapper;

        return $this;
    }

    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceManager()->get('adfabpartnership_module_options'));
        }

        return $this->options;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $serviceManager
     * @return User
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
	
	/**
     * Add protocol to an url if missing
     *
     * @param  url
	 * @param  append
     * @return string
     */
     public function verifyProtocol($url, $append = 'http://')
	 {
	 	if(strpos($url,'http://') === false && strpos($url,'https://') === false)
		{
			$url = $append.$url;
		}
		
		return $url;
	 }
}	 

