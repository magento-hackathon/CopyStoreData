<?php

class Hackathon_CopyStoreData_Admin_CatalogcategorycopyController extends Mage_Adminhtml_Controller_Action
{

    // URL:  http://[MAGROOT]/admin/catalogcategorycopy/index/key/###########/
    // If "storecode in url" is enabled there is an extra "/admin" before "/catalogcategorycopy"
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
        // Layout will be chosen by Mage_Core_Controller_Varien_Action::addActionLayoutHandles
        // Layout file:        /app/design/adminhtml/default/default/layout/hackathon/copystoredata.xml
        // Item in that file:  adminhtml_catalogcategorycopy_index

        // To debug layout XML enable:
        //header( 'Content-Type: text/xml' ); echo $this->getLayout()->getXmlString(); exit;
    }


}
