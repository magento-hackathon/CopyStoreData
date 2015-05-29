<?php

/**
 * Class Hackathon_CopyStoreData_Model_Observer
 */
class Hackathon_CopyStoreData_Model_Observer extends Varien_Event_Observer
{
    /**
     * Copys attribute data based on selections
     *
     * @param $observer
     */
    public function controller_action_postdispatch_adminhtml_catalog_product_action_attribute_save($observer)
    {
        $helper = Mage::helper('adminhtml/catalog_product_edit_action_attribute');
        $productIds = $helper->getProductIds();
        $copyFromId = $helper->getSelectedStoreId();
        $copyToIds = Mage::app()->getRequest()->getParam('copy_to_stores');

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
                    if (!is_object($value) && is_object($attribute)) {
                        if ($attribute->getBackendType() != 'static' && $attribute->getIsGlobal() == 0) {
                            $productToCopy->setData($key, $value);
                            $productToCopy->setStoreId($copyToId)->getResource()->saveAttribute($productToCopy, $key);
                        }
                    }

                }
            }
        }
    }
}