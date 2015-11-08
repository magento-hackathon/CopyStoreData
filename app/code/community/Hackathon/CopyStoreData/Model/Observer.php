<?php

/**
 * Class Hackathon_CopyStoreData_Model_Observer
 */
class Hackathon_CopyStoreData_Model_Observer
{
    /**
     *
     */
    const STATUS_CONFIG = 'copystoredata/settings/status';
    /**
     *
     */
    const STORE_CONFIG = 'copystoredata/settings/copy_from_store';
    /**
     *
     */
    const ATTRIBUTES_CONFIG = 'copystoredata/settings/fields';

    /**
     * @var array
     */
    public $_copiedStores = array(); // Array to indicate if script has already run for specific store before to prevent loop when stores are pointed at eachother.
    /**
     * @var
     */
    public $_changedAttributes = array();

    /**
     * @param Varien_Event_Observer $observer
     */
    public function copyToStore(Varien_Event_Observer $observer)
    {
        $product = $observer->getProduct();

        if (count($this->_copiedStores) == 0) {
            $this->_copiedStores[] = $product->getStoreId();

            $originalData = $product->getOrigData();
            foreach($product->getData() as $key => $value){
                if(!is_object($value) && isset($originalData[$key]) && $originalData[$key] != $value){
                    $this->_changedAttributes[$key] = $value;
                }
            }
        }

        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $storeIds = $read->query("SELECT scope_id as store FROM " . $resource->getTableName('core_config_data') . " WHERE scope = 'stores' AND path = '" . self::STORE_CONFIG . "' AND value =" . $product->getStoreId());

        while ($row = $storeIds->fetch()) {
            $storeId = $row['store'];

            if (Mage::getStoreConfig(self::STATUS_CONFIG, $storeId) && !in_array($storeId, $this->_copiedStores)) {
                $this->_copiedStores[] = $storeId;
                $this->_initProduct($product->getEntityId(), $storeId)->save();
            }

        }
    }

    /**
     * @param $productId
     * @param $storeId
     * @return mixed
     */
    protected function _loadProduct($productId, $storeId)
    {
        $product = Mage::getModel('catalog/product')
            ->setStoreId($storeId);

        $product->setData('_edit_mode', true);

        if ($productId) {
            try {
                $product->load($productId);
            } catch (Exception $e) {
                $product->setTypeId(Mage_Catalog_Model_Product_Type::DEFAULT_TYPE);
                Mage::logException($e);
            }
        }

        /**
         * Check "Use Default Value" checkboxes values
         */
        if ($useDefaults = Mage::app()->getRequest()->getPost('use_default')) {
            foreach ($useDefaults as $attributeCode) {
                $product->setData($attributeCode, false);
            }
        }

        $product->setData('url_key', false);

        return $product;
    }

    // Initiates product and copies data which are selected in the settings folder.
    /**
     * @param $productId
     * @param $storeId
     * @return mixed
     */
    protected function _initProduct($productId, $storeId)
    {
        $product = $this->_loadProduct($productId, $storeId);
        $allowedAttributes = explode(',', Mage::getStoreConfig(self::ATTRIBUTES_CONFIG, $storeId));

        foreach ($this->_changedAttributes as $key => $value) {
            if (in_array($key, $allowedAttributes)) {
                $product->setData($key, $value);
            }
        }

        Mage::dispatchEvent(
            'catalog_product_prepare_save',
            array('product' => $product, 'request' => Mage::app()->getRequest())
        );

        return $product;
    }

    /**
     * Copys attribute data based on selections
     *
     * @param $observer
     */
    public function controller_action_postdispatch_adminhtml_catalog_product_action_attribute_save($observer)
    {
        $copyToIds = Mage::app()->getRequest()->getParam('copy_to_stores');

        if ($copyToIds) {
            $helper = Mage::helper('adminhtml/catalog_product_edit_action_attribute');
            $productIds = $helper->getProductIds();
            $copyFromId = $helper->getSelectedStoreId();

            try {
                foreach ($copyToIds as $copyToId) {
                    $productsToCopy = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect('*')
                        ->addStoreFilter($copyFromId)
                        ->setStoreId($copyFromId)
                        ->addAttributeToFilter('entity_id', array('in' => $productIds));

                    foreach ($productsToCopy as $productToCopy) {
                        $productDataArray = $productToCopy->getData();
                        foreach ($productDataArray as $key => $value) {
                            $attribute = $productToCopy->getResource()->getAttribute($key);
                            if (!is_object($value) &&
                                is_object($attribute) &&
                                $attribute->getBackendType() != 'static' &&
                                $attribute->getIsGlobal() == 0 &&
                                $attribute->getAttributeCode() != "url_key") {
                                    $rawValue = Mage::getResourceModel('catalog/product')->getAttributeRawValue($productToCopy->getId(), $attribute->getAttributeCode(), $copyFromId);
                                    $productToCopy->setData($key, ($rawValue == NULL ? false : $value));
                                    $productToCopy->setStoreId($copyToId)->getResource()->saveAttribute($productToCopy, $key);
                            }
                        }
                    }
                }
                Mage::getSingleton('adminhtml/session')
                    ->init('core', 'adminhtml')
                    ->addSuccess('Products were successfully copied.');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->init('core', 'adminhtml')
                    ->addError($e->getMessage());
            }
        }
    }
}