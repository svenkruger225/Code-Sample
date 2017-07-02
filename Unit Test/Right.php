<?php
/**
 * SmartView Site Right Model
 *
 * PHP version 5
 *
 * @category   SmartView
 * @package    SmartView_Administration_Model_Site
 * @author     Arslan Ali <marslan.ali@gmail.com>
 * @license    [url] [description]
 * @link       http://www.antaris-solutions.net
 */

/**
 * Access Right Model
 *
 * SmartView_Model_Site_Right is our domain model
 *
 * @category   SmartView
 * @package    SmartView_Administration_Model_Site
 * @author     Arslan Ali <marslan.ali@gmail.com>
 * @license    [url] [description]
 * @link       http://www.antaris-solutions.net
 */
class SmartView_Model_User_Right extends SmartView_Model_Abstract implements SmartView_Model_Interfaces_Identifiable
{
    use SmartView_Trait_Identifiable;

    /**
     * Access Level
     */
    const ACCESS_LEVEL_NONE   = 0;
    const ACCESS_LEVEL_VIEW   = 1;
    const ACCESS_LEVEL_MODIFY = 2;
    const ACCESS_LEVEL_DELETE = 3;

    /**
     * Access Areas
     */
    const AREA_ALERT_DEVICES      = 'Alert Devices';
    const AREA_AUDIT_TRAIL        = 'Audit Trail';
    const AREA_CARRIERS           = 'Carriers';
    const AREA_CUSTOMERS          = 'Customers';
    const AREA_GRAPHS             = 'Custom Graphs';
    const AREA_ALERT_PROFILES     = 'Event Profiles';
    const AREA_EXCURSIONS         = 'Excursions';
    const AREA_LOCATIONS          = 'Locations';
    const AREA_SECTORS            = 'Sectors';
    const AREA_PACKING_SYSTEMS    = 'Packing System';
    const AREA_PRODUCTS           = 'Products';
    const AREA_NODES              = 'Smart Sensors';
    const AREA_SHIPMENTS          = 'Shipments';
    const AREA_SHIPMENT_ANALYSIS  = 'Shipment Analysis';
    const AREA_SHIPMENT_DASHBOARD = 'Shipment Dashboard';

    /** Uploading Q-Tags is a different right that is more closer to user permission like status */
    const AREA_UPLOAD_QTAG = 'Upload qTag files';

    /**
     * User identifier
     *
     * @var SmartView_Model_User userId
     */
    protected $_userId;

    /**
     * Access Area
     *
     * @var string area
     */
    protected $_area;

    /**
     * Access Level
     *
     * @var int level
     */
    protected $_level;

    /**
     * List of actions to catch for audittrail
     *
     * @var array
     */
    protected $_comparableMethods = array(
        'getUserId'           => SmartView_Model_AuditTrail::TYPE_USER,
        'getLevelDescription' => 'Access level',
        'getArea'             => 'Area'
    );

    /**
     * Set the User Identifier of this Access Right
     *
     * @param $id
     *
     * @internal param \SmartView_Model_User $user
     *
     * @return SmartView_Model_User_Right
     */
    public function setUserId($id)
    {
        $this->_userId = $id;

        return $this;
    }

    /**
     * Retrieve the User Identifier
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->_userId;
    }

    /**
     * Set the Access Area
     *
     * @param $area
     *
     * @return SmartView_Model_User_Right
     */
    public function setArea($area)
    {
        $this->_area = (string)$area;

        return $this;
    }

    /**
     * Retrieve the Access Area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_area;
    }

    /**
     * Set the Access Level
     *
     * @param $level
     *
     * @return SmartView_Model_User_Right
     */
    public function setLevel($level)
    {
        $this->_level = (int)$level;

        return $this;
    }

    /**
     * Retrieve the Access Level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->_level;
    }

    /**
     * Retrieve the Access Level description
     *
     * @return string
     */
    public function getLevelDescription()
    {
        switch ($this->_level) {
            case self::ACCESS_LEVEL_NONE:
                return 'None';
            case self::ACCESS_LEVEL_VIEW:
                return 'View';
            case self::ACCESS_LEVEL_MODIFY:
                return 'Modify';
            case self::ACCESS_LEVEL_DELETE:
                return 'Delete';
            default:
                return '' . $this->_level;
        }
    }

    /**
     * General information about method
     *
     * @param $level
     *
     * @return string
     */
    public function hasAvailableLevel($level)
    {
        $upToView = array(
            self::AREA_AUDIT_TRAIL,
            self::AREA_SHIPMENT_DASHBOARD
        );

        $upToModify = array(
            self::AREA_AUDIT_TRAIL,
            self::AREA_EXCURSIONS,
            self::AREA_LOCATIONS,
            self::AREA_NODES,
            self::AREA_SHIPMENT_DASHBOARD
        );

        if (in_array($this->getArea(), $upToView) && $level == self::ACCESS_LEVEL_MODIFY) {
            return '<i>n/a</i>';
        }

        if (in_array($this->getArea(), $upToModify) && $level == self::ACCESS_LEVEL_DELETE) {
            return '<i>n/a</i>';
        }

        return '<img src="/images/icons/Silk_cross.png"/>';
    }

    public function hasAccess()
    {
        $customerSettings = SmartView_Application::getCurrentSite()->getCustomerSettings();

        $facilityMonitoringAreas = array(
            self::AREA_ALERT_DEVICES,
            self::AREA_SECTORS,
        );
        if (in_array($this->getArea(), $facilityMonitoringAreas)) {
            if (!$customerSettings->hasFacilityMonitoring()) {
                return false;
            }
        }

        $shipmentMonitoringAreas = array(
            self::AREA_CARRIERS,
            self::AREA_CUSTOMERS,
            self::AREA_PACKING_SYSTEMS,
            self::AREA_PRODUCTS,
            self::AREA_SHIPMENTS,
            self::AREA_UPLOAD_QTAG,
        );
        if (in_array($this->getArea(), $shipmentMonitoringAreas)) {
            if (!$customerSettings->hasShipmentMonitoring()) {
                return false;
            }
        }

        $analyticsMonitoringAreas = array(
            self::AREA_SHIPMENT_ANALYSIS,
            self::AREA_SHIPMENT_DASHBOARD,
        );
        if (in_array($this->getArea(), $analyticsMonitoringAreas)) {
            if (!$customerSettings->hasAnalyticsAndReporting()) {
                return false;
            }
        }

        return true;
    }
}
