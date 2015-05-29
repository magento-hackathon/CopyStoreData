<?php

/**
 * Class Hackathon_CopyStoreData_Block_Adminhtml_Catalog_Product_Edit_Action_Attribute_Tab_CopyStoreData
 */
class Hackathon_CopyStoreData_Block_Adminhtml_Catalog_Product_Edit_Action_Attribute_Tab_CopyStoreData
    extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @return mixed
     */
    public function getWebsiteCollection()
    {
        return Mage::app()->getWebsites();
    }

    /**
     * @param Mage_Core_Model_Website $website
     * @return mixed
     */
    public function getGroupCollection(Mage_Core_Model_Website $website)
    {
        return $website->getGroups();
    }

    /**
     * @param Mage_Core_Model_Store_Group $group
     * @return mixed
     */
    public function getStoreCollection(Mage_Core_Model_Store_Group $group)
    {
        return $group->getStores();
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('catalog')->__('Copy Store Data');
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return Mage::helper('catalog')->__('Copy Store Data');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
