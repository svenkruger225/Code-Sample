<?php
/**
 * File: AirlinesTest.php
 * Date: 08/07/15
 *
 * PHP version 5
 *
 * @package  SmartView\UnitTest
 * @author   Arslan Ali <marslan.ali@gmail.com>
 */

/**
 * Class: SmartView_Model_AirlineTest
 *
 * PHP version 5
 *
 * @package  Model
 * @author   Arslan Ali <marslan.ali@gmail.com>
 *
 * @group    model
 * @group    airlines
 * @group    carriers
 */
class SmartView_Model_AirlineTest extends SmartView_Test_PHPUnit_TestCase
{
    /**
     * @var SmartView_Model_Airline
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new SmartView_Model_Airline;
    }

    public function testGetName()
    {
        $this->_givenSystemAirline();

        $this->assertNotNull($this->_object->getName());
        $this->assertEquals('KLM Airlines - (KLM)', $this->_object->getName());
    }

    public function testGetSystemAirline()
    {
        $this->_givenSystemAirline();
        $this->assertNotNull($this->_object->getSystemAirline());
        $this->assertInternalType('object', $this->_object->getSystemAirline());
    }

    /**
     * General information about method
     */
    public function testInterfaceImplementation()
    {
        $this->_interfaceImplementationTest($this->_object, 'SmartView_Model_Interfaces_Identifiable');
    }

    /**
     * Test setSystemAirlineId/getSystemAirlineId
     *
     */
    public function testSystemAirlineId()
    {
        $this->genericIdTest($this->_object, 'SystemAirline');
    }


    /**
     * Test Usage Count
     *
     */
    public function testUsageCount()
    {
        $count = 15;

        // Create our Mock Service
        $mockService = $this->getMock('SmartView_Service_Shipment');
        $mockService->expects($this->any())->method('getShipmentCount')->with(
            $this->logicalAnd(
                $this->arrayHasKey(SmartView_Service_Shipment::FILTER_OPTION_CARRIER)
            )
        )->will($this->returnValue($count));

        // Create our Mock Service Factory
        $mockServiceFactory = $this->getMock('SmartView_Factory_Service', array('get'), array(), '', '', false);
        $mockServiceFactory->expects($this->any())->method('get')->will($this->returnValue($mockService));
        // Place the Mock Service Factory in our Registry
        Zend_Registry::set(SmartView_Factory_Service::REGNAME, $mockServiceFactory);

        $this->assertEquals($count, $this->_object->getUsageCount());
    }


    /**
     * General information about method
     */
    public function testServices()
    {
        $this->genericServiceTestThatUsesFactory($this->_object, 'Shipment');
    }

    protected function _givenSystemAirline()
    {
        $systemAirline = new SmartView_Administration_Model_Airline();
        $systemAirline->setId(1)
            ->setName('KLM Airlines')
            ->setIata('KLM')
            ->setIcao('KL')
            ->setCountry('Netherlands');

        $administrationMapper = $this->getMock('SmartView_Administration_Model_Mapper_Airline', array());

        $administrationService = $this->getMock('SmartView_Administration_Service_Airline', array('getAdministrationAirline'), array($administrationMapper));
        $administrationService->expects($this->any())->method('getAdministrationAirline')->will($this->returnValue($systemAirline));

        $this->addMockAdministrationService('Airline', $administrationService);
    }
}
 