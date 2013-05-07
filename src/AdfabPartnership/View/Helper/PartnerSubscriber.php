<?php

namespace AdfabPartnership\View\Helper;

use Zend\View\Helper\AbstractHelper;

class PartnerSubscriber extends AbstractHelper
{
    protected $partnerService;

    /**
     * @param  int|string $identifier
     * @return string
     */
    public function __invoke($partner, $user)
    {
    	
		if($user == false){
			return $this->getPartnerService()->findSubscribers($partner);
		}
		else
		{
			return $this->getPartnerService()->isSubscriber($partner, $user);
		}
		
        
    }

    /**
     * @param \AdfabPartnership\Service\Partner $partnerService
     */
    public function setPartnerService(\AdfabPartnership\Service\Partner $partnerService)
    {
        $this->partnerService = $partnerService;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPartnerService()
    {
        return $this->partnerService;
    }
}
