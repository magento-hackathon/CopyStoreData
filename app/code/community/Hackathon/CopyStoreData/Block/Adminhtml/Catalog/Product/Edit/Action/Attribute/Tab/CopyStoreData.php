<?php
class Hackathon_CopyStoreData_Block_Adminhtml_Catalog_Product_Edit_Action_Attribute_Tab_CopyStoreData
    extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function getTabLabel()
    {
        return Mage::helper('catalog')->__('Copy Store Data');
    }

    public function getTabTitle()
    {
        return Mage::helper('catalog')->__('Copy Store Data');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
// copy_from_store
// copy_to_stores
