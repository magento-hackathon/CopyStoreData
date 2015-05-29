<?php
class Hackathon_CopyStoreData_Model_Observer
{
    const STATUS_CONFIG = 'copystoredata/settings/status';
    const STORE_CONFIG = 'copystoredata/settings/copy_from_store';
    const ATTRIBUTES_CONFIG = 'copystoredata/settings/fields';

    public $_copiedStores = array(); // Array to indicate if script has already run for specific store before to prevent loop when stores are pointed at eachother.
    public $_changedAttributes;

	public function copyToStore(Varien_Event_Observer $observer)
	{


        $product = $observer->getProduct();

        if(count($this->_copiedStores) == 0){
            $this->_copiedStores[] = $product->getStoreId();
        }

        $this->_changedAttributes = array_diff($product->getData(), $product->getOrigData());

        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $storeIds = $read->query("SELECT scope_id as store FROM core_config_data WHERE scope = 'stores' AND path = '" . self::STORE_CONFIG . "' AND value =" . $product->getStoreId());

        while ($row = $storeIds->fetch()) {
            $storeId = $row['store'];

            if (Mage::getStoreConfig(self::STATUS_CONFIG, $storeId) && !in_array($storeId,$this->_copiedStores)) {
                $this->_copiedStores[] = $storeId;
                $this->_initProduct($product->getEntityId(), $storeId)->save();
            }

        }
	}
	
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

        $product->setData('url_key',false);
        
        return $product;
    }

    // Initiates product and copies data which are selected in the settings folder.
	protected function _initProduct($productId, $storeId)
    {
	        $product = $this->_loadProduct($productId,$storeId);
			$allowedAttributes = explode(',', Mage::getStoreConfig(self::ATTRIBUTES_CONFIG, $storeId));


            foreach($this->_changedAttributes as $key => $value){
                if(in_array($key,$allowedAttributes)){
                    $product->setData($key,$value);
                }
            }

	
	        Mage::dispatchEvent(
	            'catalog_product_prepare_save',
	            array('product' => $product, 'request' => Mage::app()->getRequest())
	        );
	
	        return $product;
	    
    }

		
}
