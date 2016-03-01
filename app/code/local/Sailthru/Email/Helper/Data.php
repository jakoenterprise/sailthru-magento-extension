<?php
/**
 *
 * @category  Sailthru
 * @package   Sailthru_Email
 * @author    Kwadwo Juantuah <support@sailthru.com>
 */
class Sailthru_Email_Helper_Data extends Mage_Core_Helper_Abstract {
    /*
        * Config paths to use
        */
    const XML_PATH_ENABLED                                  = 'sailthru/api/enabled';
    const XML_PATH_ENABLE_LOGGING                           = 'sailthru/api/enable_logging';
    const XML_PATH_DEFAULT_EMAIL_LIST                       = 'sailthru/email/default_list';
    const XML_PATH_DEFAULT_NEWSLETTER_LIST                  = 'sailthru/email/newsletter_list';
    const XML_PATH_SENDER_EMAIL                             = 'sailthru/email/sender_email';
    const XML_PATH_HORIZON_ENABLED                          = 'sailthru/horizon/active';
    const XML_PATH_HORIZON_DOMAIN                           = 'sailthru/horizon/horizon_domain';
    const XML_PATH_CONCIERGE_ENABLED                        = 'sailthru/horizon/concierge_enabled';
    const XML_PATH_REMINDER_TIME                            = 'sailthru/email/reminder_time';
    const XML_PATH_TRANSACTION_EMAIL_ENABLED                = 'sailthru/email/enable_transactional_emails';
    const XML_PATH_IMPORT_SUBSCRIBERS                       = 'sailthru/subscribers/import_subscribers';

    /**
     * Check to see if Sailthru plugin is enabled
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return (boolean) Mage::getStoreConfig(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Get log path
     * @param type $store
     * @return string
     */
    public function getLogPath($store = null)
    {
        $log_path = Mage::getStoreConfig(self::XML_PATH_LOG_PATH, $store);
        return !empty($log_path) ? $log_path : null;

    }

    /**
     * Get Horizon domain
     * @param type $store
     * @return string
     */
    public function getHorizonDomain($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_HORIZON_DOMAIN, $store);
    }

    /**
     * Get master list
     * @param type $store
     * @return string
     */
    public function getMasterList($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_EMAIL_LIST, $store);
    }

    /**
     * Check to see if Horizon is enabled
     *
     * @return bool
     */
    public function isHorizonEnabled($store = null)
    {
        return (boolean) Mage::getStoreConfig(self::XML_PATH_HORIZON_ENABLED, $store);
    }

    /**
     * Check to see if Concierge is enabled
     *
     * @return bool
     */
    public function isConciergeEnabled($store = null)
    {
        return (boolean) Mage::getStoreConfig(self::XML_PATH_CONCIERGE_ENABLED, $store);
    }

    public function importSubscribers($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_IMPORT_SUBSCRIBERS, $store);
    }

    public function isTransactionalEmailEnabled($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TRANSACTION_EMAIL_ENABLED, $store);
    }

    public function getSenderEmail()
    {
        return Mage::getStoreConfig(self::XML_PATH_SENDER_EMAIL);
    }

    public function getDefaultList($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_EMAIL_LIST, $store);
    }

    public function getNewsletterList($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_DEFAULT_NEWSLETTER_LIST, $store);
    }

    public function getReminderTime($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_REMINDER_TIME, $store);
    }


    public function isLoggingEnabled($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ENABLE_LOGGING, $store);
    }


    public function formatAmount($amount = null)
    {
        if (is_numeric($amount)) {
            return intval($amount*100);
        }

        return 0;

    }

    public function debug($object)
    {
        echo '<pre>';
        print_r($object);
        echo '</pre>';
    }
    public function getSailthruProductImage($imageName, $dimension=null){
    	if($imageName=="no_selection") return '';
        $resizedImage           = '';
        $dim                    = '';
        $sailthruImageUrl       = Mage::getBaseUrl('media'). 'catalog' . DS . 'product' .DS .'cache' . DS. 'sailthru';
        $mediaDir               = Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product';
        $sailthruDir            = $mediaDir .DS .'cache' . DS . 'sailthru';
        if($dimension){
            $pos    = strpos($dimension, 'x');
            if($pos===false){
                $sailthruDir            = $sailthruDir . DS . $dimension.'x';
                $sailthruImageUrl       = $sailthruImageUrl . DS .$dimension.'x' ;
            }else {
                $sailthruDir            = $sailthruDir . DS . $dimension ;
                $sailthruImageUrl       = $sailthruImageUrl . DS . $dimension ;
            }
        }
        if (file_exists($sailthruDir . $imageName)) {
            return $resizedImage      = $sailthruImageUrl.$imageName;
        }elseif (file_exists($mediaDir . $imageName)) {
            if (!is_dir($sailthruDir)) {
                mkdir($sailthruDir);
            }
            //resizing image
            $_image = new Varien_Image($mediaDir . $imageName);
            $_image->constrainOnly(true);
            $_image->keepAspectRatio(false);
            $_image->keepFrame(true);
            $_image->keepTransparency(true);
            if($dimension){
                $dim=  explode('x',$dimension);
                if(isset($dim[1]))
                $_image->resize($dim[0], $dim[1]);
                else
                $_image->resize($dim[0]);
            }
            $_image->save($sailthruDir . $imageName);
            $resizedImage   = $sailthruImageUrl.$imageName;
        }
        return $resizedImage;
    }    
}