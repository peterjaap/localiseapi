<?php
/**
 * Ffm_LocaliseApi extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the OSL 3.0 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 *
 * @category       Ffm
 * @package        Ffm_LocaliseApi
 * @copyright      Copyright (c) 2015
 * @license        OSL 3.0 http://opensource.org/licenses/OSL-3.0
 */
/**
 * General observer
 *
 * @category    Ffm
 * @package     Ffm_LocaliseApi
 * @author      Sander Mangel <s.mangel@fitforme.nl>
 */
class Ffm_LocaliseApi_Model_Queue extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY= 'localiseapi_queue';
    const CACHE_TAG = 'localiseapi_queue';
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'localiseapi_queue';

    /**
     * Parameter name in event
     * @var string
     */
    protected $_eventObject = 'localiseapi';

    /**
     * _construct
     * @access public
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('ffm_localiseapi/queue');
    }

    /**
     * before save module
     * @access protected
     * @return Ffm_LocaliseApi_Model_Queue
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()){
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    public function save()
    {
        if ($this->isDeleted()) {
            return $this->delete();
        }
        if (!$this->_hasModelChanged()) {
            return $this;
        }

        try {
            $this->_beforeSave();

            $resource = Mage::getSingleton('core/resource');
            $write = $resource->getConnection('core_write');

            $write->insertOnDuplicate($resource->getTableName('ffm_localiseapi/queue'),
                array(
                    'assetpool' => $this->getAssetpool(),
                    'identifier' => $this->getIdentifier(),
                    'string' => $this->getString(),
                    'locale' => $this->getLocale(),
                    'created_at' => $this->getCreatedAt(),
                    'updated_at' => $this->getUpdatedAt(),
                ),
                array(
                    'string',
                    'updated_at'
                )
            );

            $this->_afterSave();
        } catch (Exception $e) {
            throw $e;
        }

        return $this;
    }
}
