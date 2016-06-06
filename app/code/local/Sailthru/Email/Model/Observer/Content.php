<?php
/**
 * Content API Observer Model
 *
 * @category  Sailthru
 * @package   Sailthru_Email
 * @author    Kwadwo Juantuah <support@sailthru.com>
 */
class Sailthru_Email_Model_Observer_Content extends Sailthru_Email_Model_Abstract
{

    /**
     * Push product to Sailthru using Content API
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Sales_Model_Observer
     */
    public function deleteProduct(Varien_Event_Observer $observer)
    {
        if($this->_isEnabled) {
            try{
                $product = $observer->getEvent()->getProduct();
                if($product->getStatus()=='2') return $this;
                $response = Mage::getModel('sailthruemail/client_content')->deleteProduct($product);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return $this;
    }

    /**
     * Push product to Sailthru using Content API
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Sales_Model_Observer
     */
    public function saveProduct(Varien_Event_Observer $observer)
    {
       if($this->_isEnabled) {
            try{
                $product = $observer->getEvent()->getProduct();
               if($product->getTypeId()=='configurable' && $product->getStatus()=='1' && $this->isSaleable($product->getId())){
                	$response = Mage::getModel('sailthruemail/client_content')->saveProduct($product);
               }else{
               	 	return $this;
               }
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return $this;
    }

    
    public function isSaleable($id)
    {
		$isSaleable		= false;
		$originalStore  = Mage::app()->getStore(); // save the original store setting 
		Mage::app()->setCurrentStore('default'); //switch to the default store
		$_product	= Mage::getModel("catalog/product")->load($id);	
		$isSaleable = $_product->isSaleable();
		Mage::app()->setCurrentStore($originalStore->getId()); // switch back to the original
		unset($_product);
		return $isSaleable;
    }    
    
}