<?php

class Hackathon_CopyStoreData_Admin_CatalogcategorycopyController extends Mage_Adminhtml_Controller_Action
{

    // URL:  http://[MAGROOT]/admin/catalog_category_copy/index/key/###########/
    // If "storecode in url" is enabled there is an extra "/admin" before "/jv_adminform"
    public function indexAction()
    {
echo 'TEST TEST';

//        $this->loadLayout();
//        $this->renderLayout();
        // Layout will be chosen by Mage_Core_Controller_Varien_Action::addActionLayoutHandles
        // Layout file:        /app/design/adminhtml/default/default/layout/jeroenvermeulen_adminform.xml
        // Item in that file:  jv_adminform_demo_form_index

        // To debug layout XML enable:
        // header( 'Content-Type: text/xml' ); echo $this->getLayout()->getXmlString(); exit;
    }


}
