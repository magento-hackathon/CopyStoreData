<?php
class Hackathon_CopyStoreData_Model_System_Config_Source_Attributes
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();

        $attributeArray = array();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsGlobal() == 0 && $attribute->getBackendType() !== 'static') {
                $attributeArray[] = array(
                    'label' => $attribute->getFrontendLabel() ? $attribute->getFrontendLabel() : $attribute->getAttributeCode(),
                    'value' => $attribute->getAttributeCode()
                );
            }
        }
        return $attributeArray; 
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
        $optionsArray = array();
        foreach ($attributes as $attribute){
            if($attribute->getIsGlobal() === 0 && $attribute->getBackendType() == 'static'){
                $optionsArray[$attribute->getAttributecode()] = $attribute->getFrontendLabel();
            }
        }
        return $optionsArray;
    }
}
