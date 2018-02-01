<?php

class Hackathon_CopyStoreData_Block_Adminhtml_Catalog_Category_Copyform extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
            'action'  => $this->getUrl('*/*/save')
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    => $this->__('Attribute Set and Item Type')
        ));

        $fieldset->addField('category_ids', 'note', array(
            'label'     => $this->__('For which Categories'),
            'title'     => $this->__('Attribute Set'),
            'required'  => true,
            'text'      => $this->getCategoriesIdsHtml()
        ));

        $fieldset->addField('copy_from_store', 'select', array(
            'name'      => 'copy_from_store',
            'label'     => $this->__('Copy from Store'),
            'title'     => $this->__('Copy from Store'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false)
        ));

        $fieldset->addField('copy_to_stores', 'multiselect', array(
            'name'  => 'copy_to_stores',
            'label' => Mage::helper('hackathon_copystoredata')->__('Copy To Store'),
            'title' => Mage::helper('hackathon_copystoredata')->__('Copy To Store'),
            'required' => true,
            'values' => Mage::getSingleton('adminhtml/system_store')
                            ->getStoreValuesForForm(false, false)
        ));

        $fieldset->addField('submit', 'submit', array(
            'label'     => $this->__('Submit'),
            'required'  => true,
            'value'     => 'Submit'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function getCategoriesIdsHtml()
    {
        $html = '';

        // Add select all/unselect all functionality
        $html .= "<a href='#' onclick='toggleSelectAll(true)'>Select All</a>";
        $html .= "<span class='separator'>|</span>";
        $html .= "<a href='#' onclick='toggleSelectAll(false)'>Unselect All</a>";
        $html .= "<script type='text/javascript'>";
        $html .= "  function toggleSelectAll(checked) {";
        $html .= "      var checkboxes = document.getElementsByName('category_ids[]');";
        $html .= "      for (var i = 0; i < checkboxes.length; i++) {";
        $html .= "          checkboxes[i].checked = checked;";
        $html .= "      }";
        $html .= "  }";
        $html .= "</script>";

        $html .= $this->getTreeCategories();

        return $html;
    }

    protected function getTreeCategories($parentId=null, $level=0)
    {
        if ( is_null($parentId) ) {
            $parentId = Mage::app()->getStore()->getRootCategoryId();
        }
        /** @var Mage_Catalog_Model_Resource_Category_Collection $children */
        $children = Mage::getModel('catalog/category')->getCollection()
                       ->addAttributeToSelect('name')
                       ->addAttributeToFilter('parent_id',array('eq' => $parentId));
        $html = '';
        if ( $children->count() ) {
            $html = '<ul>';
            foreach ($children as $category) {
                $html .= '<li>';
                $html .= str_repeat('&nbsp;', $level*4 );
                $html .= sprintf('<input type="checkbox" name="category_ids[]" value="%d">',$category->getId());
                $html .= '&nbsp;&nbsp;' . $category->getName();
                $html .= $this->getTreeCategories($category->getId(), $level+1);
                $html .= '</li>';
            }
            $html .= '</ul>';

        }
        return $html;
    }

}