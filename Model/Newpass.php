<?php
namespace Godogi\Forcenewpassword\Model;

use Magento\Framework\Model\AbstractModel;

class Newpass extends AbstractModel
{
	protected function _construct()
	{
		$this->_init('Godogi\Forcenewpassword\Model\ResourceModel\Newpass');
	}
}