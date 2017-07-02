<?php
/**
 * Test class for SmartView_Model_User_Right.
 *
 * @group model
 * @group User
 */
class SmartView_Model_User_RightTest extends SmartView_Test_PHPUnit_TestCase
{
    /**
     * @var SmartView_Model_User_Right
     */
    protected $_object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_object = new SmartView_Model_User_Right;
    }

    /**
     * @covers SmartView_Model_User_Right
     */
    public function testModelUserRight()
    {
        $myUserRight = new SmartView_Model_User_Right();

        // should be null by default
        $this->assertNotNull($myUserRight);
        $this->assertInstanceOf('SmartView_Model_User_Right', $myUserRight);
        $this->assertNull($myUserRight->getId());
        $this->assertNull($myUserRight->getUserId());
        $this->assertNull($myUserRight->getArea());
        $this->assertNull($myUserRight->getLevel());
        $this->assertNotNull($myUserRight->getLevelDescription());
    }

    /**
     * @covers SmartView_Model_User_Right::getId
     */
    public function testGetIdDefault()
    {
        // should be null by default
        $this->assertNull($this->_object->getId());
    }

    /**
     * Test setter/getter
     *
     * @covers SmartView_Model_User_Right::setId
     * @covers SmartView_Model_User_Right::getId
     */
    public function testSetId()
    {
        $this->_object->setId(1);
        $this->assertEquals(1, $this->_object->getId());

        $this->_object->setId('string');
        $this->assertEquals(1, $this->_object->getId());
        $this->assertInternalType('int', $this->_object->getId());

        $this->_object->setId(1.00);
        $this->assertEquals(1, $this->_object->getId());
        $this->assertInternalType('int', $this->_object->getId());
    }

    /**
     * Test setter/getter
     *
     * @covers SmartView_Model_User_Right::setUserId
     * @covers SmartView_Model_User_Right::getUserId
     */
    public function testSetUserId()
    {
        $this->_object->setUserId(1);
        $this->assertEquals(1, $this->_object->getUserId());

        $this->_object->setUserId(3.00);
        $this->assertEquals(3, $this->_object->getUserId());
    }

    /**
     * @covers SmartView_Model_User_Right::setArea
     * @covers SmartView_Model_User_Right::getArea
     */
    public function testSetArea()
    {
        $this->_object->setArea(SmartView_Model_User_Right::AREA_SHIPMENTS);
        $this->assertEquals(SmartView_Model_User_Right::AREA_SHIPMENTS, $this->_object->getArea());

        $this->_object->setArea(FALSE);
        $this->assertInternalType('string', $this->_object->getArea());

        $this->_object->setArea(13.2);
        $this->assertInternalType('string', $this->_object->getArea());
    }

    /**
     * @covers SmartView_Model_User_Right::setLevel
     * @covers SmartView_Model_User_Right::getLevel
     */
    public function testGetLevel()
    {
        $this->_object->setLevel(SmartView_Model_User_Right::ACCESS_LEVEL_MODIFY);
        $this->assertEquals(SmartView_Model_User_Right::ACCESS_LEVEL_MODIFY, $this->_object->getLevel());
        $this->assertInternalType('int', $this->_object->getLevel());

        $this->_object->setLevel(25);
        $this->assertInternalType('int', $this->_object->getLevel());

        $this->_object->setLevel('level');
        $this->assertInternalType('int', $this->_object->getLevel());
    }

    /**
     * @covers SmartView_Model_User_Right::getLevelDescription
     */
    public function testGetLevelDescription()
    {
        $this->_object->setLevel(SmartView_Model_User_Right::ACCESS_LEVEL_DELETE);
        $this->assertEquals("Delete", $this->_object->getLevelDescription());
        $this->_object->setLevel(SmartView_Model_User_Right::ACCESS_LEVEL_MODIFY);
        $this->assertEquals("Modify", $this->_object->getLevelDescription());
        $this->_object->setLevel(SmartView_Model_User_Right::ACCESS_LEVEL_VIEW);
        $this->assertEquals("View", $this->_object->getLevelDescription());
        $this->_object->setLevel(SmartView_Model_User_Right::ACCESS_LEVEL_NONE);
        $this->assertEquals("None", $this->_object->getLevelDescription());
        $this->_object->setLevel(5000);
        $this->assertEquals("5000", $this->_object->getLevelDescription());
    }

    /**
     * @covers SmartView_Model_User_Right::hasAvailableLevel
     */
    public function testHasAvailableLevel()
    {
        $this->assertEquals('<img src="/images/icons/Silk_cross.png"/>', $this->_object->hasAvailableLevel(SmartView_Model_User_Right::ACCESS_LEVEL_MODIFY));

        $this->_object->setLevel(SmartView_Model_User_Right::ACCESS_LEVEL_VIEW);
        $this->_object->setArea(SmartView_Model_User_Right::AREA_AUDIT_TRAIL);
        $this->assertEquals('<i>n/a</i>', $this->_object->hasAvailableLevel(SmartView_Model_User_Right::ACCESS_LEVEL_MODIFY));

        $this->_object->setLevel(SmartView_Model_User_Right::ACCESS_LEVEL_VIEW);
        $this->_object->setArea(SmartView_Model_User_Right::AREA_NODES);
        $this->assertEquals('<i>n/a</i>', $this->_object->hasAvailableLevel(SmartView_Model_User_Right::ACCESS_LEVEL_DELETE));
    }
}
