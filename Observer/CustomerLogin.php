<?php
namespace Godogi\Forcenewpassword\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
		protected $_newpassFactory;
		protected $_messageManager;
    	/**
     	* @var \Magento\Framework\App\ResponseFactory
     	*/
    	private $_responseFactory;

    	/**
     	* @var \Magento\Framework\UrlInterface
     	*/
    	private $_url;
    	
    	/**
     	* Customer session
     	*
     	* @var \Magento\Customer\Model\Session
     	*/
    	protected $_customerSession;
    	
    	/**
     	* @var \Magento\Framework\App\Response\RedirectInterface
     	*/
    	protected $_redirect;
	
		public function __construct(
    		\Godogi\Forcenewpassword\Model\NewpassFactory $newpassFactory,
    		\Magento\Framework\Message\ManagerInterface $messageManager,
    		\Magento\Framework\App\ResponseFactory $responseFactory,
      	\Magento\Framework\UrlInterface $url,
      	\Magento\Customer\Model\Session $customerSession,
      	\Magento\Framework\App\Response\RedirectInterface $redirect
		)
		{
    		$this->_newpassFactory = $newpassFactory;
    		$this->_messageManager = $messageManager;
    		$this->_responseFactory = $responseFactory;
    		$this->_customerSession = $customerSession;
    		$this->_redirect = $redirect;
      	$this->_url = $url;
		}

    	public function execute(\Magento\Framework\Event\Observer $observer)
    	{
    		if($this->_customerSession->isLoggedIn()) {
    			$actionName = $observer->getEvent()->getRequest()->getFullActionName();
    			$param = $observer->getEvent()->getRequest()->getParam('changepass');
    			$paramP = $observer->getEvent()->getRequest()->getPost('change_password');
    			if (($actionName == 'customer_account_edit' && $param == 1) || $actionName == 'customer_account_logout' || ($actionName == 'customer_account_editPost' && $paramP == 1)) {
            	return $this;
        		}else{
        			$customer = $this->_customerSession->getCustomer();
    				$costomerId = $customer->getId();
				
    				$newPassC = $this->_newpassFactory->create();
    				$collection = $newPassC->getCollection();
        	
        			$passwordUpdated = 0;
					foreach($collection as $item){
						if($item->getCustomerId() == $costomerId){
							if($item->getPasswordUpdated()){
									$passwordUpdated = 1; // password already updated 
							}else{
									$passwordUpdated = 2; // asked to change password but not yet updated 					
							}
						}
					}
					if($passwordUpdated == 0){
						$newPass = $this->_newpassFactory->create();
        				$newPass->setCustomerId($costomerId);
        				$newPass->setPasswordUpdated(false);
        				$newPass->save();
        				$this->_messageManager->addWarningMessage('Kindly change your password before proceeding.');	
        				$redirectionUrl = $this->_url->getUrl('customer/account/edit/changepass/1/');
        				$this->_responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
        				exit;
					}
					return $this; 
        		}
   		}else{
					return $this;        		
			}
    	}
}