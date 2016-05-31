<?php
/**
 * Client Content Model
 *
 * @category  Sailthru
 * @package   Sailthru_Email
 *
 */
class Sailthru_Email_Model_Client_Content extends Sailthru_Email_Model_Client
{

    /**
     * Push product delete to Sailthru using Content API
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Sailthru_Email_Model_Client_Product
     */
    public function deleteProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_eventType = 'adminDeleteProduct';

        try {
            $data = $this->getProductData($product);
            $response = $this->apiDelete('content', $data);
        } catch(Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * Push product save to Sailthru using Content API
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Sailthru_Email_Model_Client_Product
     */
    public function saveProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_eventType = 'adminSaveProduct';

        try {
            $data = $this->getProductData($product);
            $response = $this->apiPost('content', $data);
        } catch(Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    /**
     * Create Product array from Mage_Catalog_Model_Product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getProductData(Mage_Catalog_Model_Product $product)
    {
        try {
            $data = array('url' => str_replace('index.php/','',str_replace('admin.','www.', $product->getProductUrl())),
                'title' => htmlspecialchars($product->getName()),
                //'date' => '',
                'spider' => 1,
                'price' => $product->getPrice(),
                'description' => strip_tags($product->getDescription()),
                'tags' => htmlspecialchars($this->getProductMetaKeyword($product->getId())),
                'images' => array(),
                'vars' => array('sku' => $product->getSku(),
                    'storeId' => '',
                    'typeId' => $product->getTypeId(),
                    'status' => $product->getStatus(),
                    'categoryId' => $product->getCategoryId(),
                    'categoryIds' => $product->getCategoryIds(),
                    'websiteIds' => $product->getWebsiteIds(),
                    'storeIds'  => $product->getStoreIds(),
                    //'attributes' => $product->getAttributes(),
                    'groupPrice' => $product->getGroupPrice(),
                    'formatedPrice' => $product->getFormatedPrice(),
                    'calculatedFinalPrice' => $product->getCalculatedFinalPrice(),
                    'minimalPrice' => $product->getMinimalPrice(),
                    'specialPrice' => $product->getSpecialPrice(),
                    'specialFromDate' => $product->getSpecialFromDate(),
                    'specialToDate'  => $product->getSpecialToDate(),
                    'relatedProductIds' => $product->getRelatedProductIds(),
                    'upSellProductIds' => $product->getUpSellProductIds(),
                    'getCrossSellProductIds' => $product->getCrossSellProductIds(),
                    'isSuperGroup' => $product->isSuperGroup(),
                    'isGrouped'   => $product->isGrouped(),
                    'isConfigurable'  => $product->isConfigurable(),
                    'isSuper' => $product->isSuper(),
                    'isSalable' => $product->isSalable(),
                    'isAvailable'  => $product->isAvailable(),
                    'isVirtual'  => $product->isVirtual(),
                    'isRecurring' => $product->isRecurring(),
                    'isInStock'  => $product->isInStock(),
                    'weight'  => $product->getSku(),
                    'inventory'  => $product->getStockItem()->getStockQty()
                )
            );

            // Add product images
           /* if(self::validateProductImage($product->getImage())) {
                $data['images']['full'] = array ("url" => $product->getImageUrl());
            }

            if(self::validateProductImage($product->getSmallImage())) {
                $data['images']['smallImage'] = array("url" => $product->getSmallImageUrl($width = 88, $height = 77));
            }

            if(self::validateProductImage($product->getThumbnail())) {
                $data['images']['thumb'] = array("url" => $product->getThumbnailUrl($width = 75, $height = 75));
            }*/
            if(self::validateProductImage($product->getImage())) {
		        $pImage         = $product->getImage();
		        $pImageUrl      = "";
		        $pImageUrl      = Mage::helper('sailthruemail')->getSailthruProductImage($pImage);
		        if(empty($pImageUrl)){
		             $pImageUrl = $product->getImageUrl();
		        }		        
                $data['images']['full'] = array ("url" =>  str_replace('admin.','www.',$pImageUrl));
            }

            if(self::validateProductImage($product->getSmallImage())) {
		        $pSmallImage     	= $product->getSmallImage();
		        $pSmallImageUrl 	= "";
		        $pSmallImageUrl     = Mage::helper('sailthruemail')->getSailthruProductImage($pSmallImage,'88x77');
		        if(empty($pSmallImageUrl)){
		             $pSmallImageUrl= $product->getSmallImageUrl($width = 88, $height = 77);
		        }		        
                $data['images']['smallImage'] = array("url" =>  str_replace('admin.','www.',$pSmallImageUrl));
            }

            if(self::validateProductImage($product->getThumbnail())) {
		        $pThumbnail     	= $product->getThumbnail();
		        $pThumbnailUrl 		= "";
		        $pThumbnailUrl     	= Mage::helper('sailthruemail')->getSailthruProductImage($pThumbnail,'75x75');            	
		        if(empty($pThumbnailUrl)){
		              $pThumbnailUrl  = $product->getThumbnailUrl($width = 75, $height = 75);
		        }           	
                $data['images']['thumb'] = array("url" =>  str_replace('admin.','www.',$pThumbnailUrl));
            }

            return $data;


            return $data;
        } catch(Exception $e) {
            Mage::logException($e);
        }

    }

    private static function validateProductImage($image) {
        if(empty($image)) {
            return false;
        }

        if('no_selection' == $image) {
            return false;
        }

        return true;
    }
    /**
     * Get MetaKeywords from product Id
     *
     * @param product Id
     * @return array
     */
    public function getProductMetaKeyword($productId){
		$sailthru_tags	= '';
		$originalStore  = Mage::app()->getStore(); // save the original store setting 
		Mage::app()->setCurrentStore('default'); //switch to the default store
		$_product 		= Mage::getModel("catalog/product")->load($productId);
		$currentCatIds = $_product->getCategoryIds();
		$categoryCollection = Mage::getResourceModel('catalog/category_collection')
							 ->addAttributeToSelect('url_key')
							 ->addAttributeToFilter('entity_id', $currentCatIds)
							 //->addAttributeToFilter('include_in_menu' , 1)
							 ->addIsActiveFilter();
		
		$temp='';
		$count=1;
		foreach ($categoryCollection as $cat){
			if($count==1)
				$temp .= $cat->getUrlKey();
			else
				$temp .= ','.$cat->getUrlKey();
			$count ++;
		}
		$sailthru_tags= $temp;	
		
		$arrColor  = $_product->getAttributeText('color');
		$tempColor='';
		if(is_array($arrColor)){
			foreach ($arrColor as $val){
				$tempColor .= ','.$val;
			}
			$sailthru_tags .= strtolower($tempColor);	
		}else {
			if($arrColor) $sailthru_tags .=','. strtolower($arrColor);
		}	
		$_settings = Mage::getStoreConfig('weltpixel_selector/productpageoptions');
		if($_settings['display_availability'] ){
			if ($_product->isSaleable()):    
				$sailthru_tags .= ',in-stock';
			else :
				$sailthru_tags .= ',out-of-stock';
			endif;
		}
		
		Mage::app()->setCurrentStore($originalStore->getId()); // switch back to the original
		return $sailthru_tags;
	}    
    
}
