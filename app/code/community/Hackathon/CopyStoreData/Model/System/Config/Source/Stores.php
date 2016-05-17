<?php
class Hackathon_CopyStoreData_Model_System_Config_Source_Stores
{
    /**
     * Options getter
     *
     * @return array
     */

    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            foreach (Mage::app()->getWebsites() as $website) {
                foreach ($website->getGroups() as $group) {
                    foreach ($group->getStores() as $store) {
                        $this->_options[] = array(
                            'value' => $store->getId(),
                            'label' => sprintf('%s - %s - %s', $website->getName(), $group->getName(), $store->getName())
                        );
                    }
                }
            }
        }
        return $this->_options;
    }
}
