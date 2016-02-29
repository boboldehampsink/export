<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Contains unit tests for the Export_UserService.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@nerds.company>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   MIT
 *
 * @link      http://github.com/boboldehampsink
 *
 * @coversDefaultClass Craft\Export_UserService
 * @covers ::<!public>
 */
class Export_UserServiceTest extends BaseTest
{
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        // Set up parent
        parent::setUpBeforeClass();

        // Require dependencies
        require_once __DIR__.'/../services/Export_UserService.php';
        require_once __DIR__.'/../services/IExportElementType.php';
    }

    /**
     * Export_UserService should implement IExportElementType.
     */
    public function testExportUserServiceShouldImplementIExportElementType()
    {
        $this->assertInstanceOf('Craft\IExportElementType', new Export_UserService());
    }

    /**
     * @covers ::getTemplate
     */
    public function testGetTemplateShouldReturnTemplatePath()
    {
        $service = new Export_UserService();
        $template = $service->getTemplate();
        $this->assertEquals('export/sources/_user', $template);
    }

    /**
     * @covers ::getGroups
     */
    public function testGetGroupsShouldGetAllUserGroups()
    {
        $expectedResult = array('userGroup');

        $mockUserGroupsService = $this->getMock('Craft\UserGroupsService');
        $mockUserGroupsService->expects($this->exactly(1))->method('getAllGroups')->willReturn($expectedResult);
        $this->setComponent(craft(), 'userGroups', $mockUserGroupsService);

        $service = new Export_UserService();
        $result = $service->getGroups();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @covers ::getGroups
     */
    public function testGetGroupsShouldReturnTrueWhenNoUserGroupsFound()
    {
        $mockUserGroupsService = $this->getMock('Craft\UserGroupsService');
        $mockUserGroupsService->expects($this->exactly(1))->method('getAllGroups')->willReturn(array());
        $this->setComponent(craft(), 'userGroups', $mockUserGroupsService);

        $service = new Export_UserService();
        $result = $service->getGroups();

        $this->assertTrue($result);
    }

    /**
     * Get fields should return default user fields plus fields from user field layout.
     *
     * @covers ::getFields
     */
    public function testGetFieldsShouldReturnDefaultUserFieldsWhenNoStoredMapFound()
    {
        $settings = array();
        $reset = false;
        $stored = null;
        $mockFieldHandle = 'mockFieldHandle';
        $mockFieldName = 'mockFieldName';
        $mockFieldType = 'mockFieldType';
        $expectedResult = array(
            'id' => array(
                'name' => 'ID',
                'checked' => 0,
            ),
            'username' => array(
                'name' => 'Username',
                'checked' => 1,
            ),
            'firstName' => array(
                'name' => 'First Name',
                'checked' => 1,
            ),
            'lastName' => array(
                'name' => 'Last Name',
                'checked' => 1,
            ),
            'email' => array(
                'name' => 'Email',
                'checked' => 1,
            ),
            'preferredLocale' => array(
                'name' => 'Preferred Locale',
                'checked' => 0,
            ),
            'weekStartDay' => array(
                'name' => 'Week Start Day',
                'checked' => 0,
            ),
            'status' => array(
                'name' => 'Status',
                'checked' => 0,
            ),
            'lastLoginDate' => array(
                'name' => 'Last Login Date',
                'checked' => 0,
            ),
            'invalidLoginCount' => array(
                'name' => 'Invalid Login Count',
                'checked' => 0,
            ),
            'lastInvalidLoginDate' => array(
                'name' => 'Last Invalid Login Date',
                'checked' => 0,
            ),
            $mockFieldHandle => array(
                'name' => $mockFieldName,
                'checked' => 1,
                'fieldtype' => $mockFieldType,
            ),
        );

        $this->setMockExportService($stored);
        $mockField = $this->getMockField($mockFieldHandle, $mockFieldName, $mockFieldType);
        $mockFieldLayout = $this->getMockFieldLayout($mockField);
        $this->setMockFieldsService($mockFieldLayout);

        $service = new Export_UserService();
        $result = $service->getFields($settings, $reset);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Get fields should return default user fields plus fields from user field layout.
     *
     * @covers ::getFields
     */
    public function testGetFieldsShouldReturnStoredMapWhenResetNotSet()
    {
        $settings = array();
        $reset = false;

        $mockMap = array('MockMap');

        $mockExportMap = $this->getMockExportMapRecord($mockMap);
        $this->setMockExportService($mockExportMap);

        $service = new Export_UserService();
        $result = $service->getFields($settings, $reset);

        $this->assertSame($mockMap, $result);
    }

    /**
     * @covers ::setCriteria
     */
    public function testSetCriteriaShouldReturnCriteria()
    {
        $settings = array(
            'sort' => 'asc',
            'offset' => 0,
            'limit' => 10,
            'elementvars' => array(
                'groups' => 1,
            ),
        );

        $mockElementCriteria = $this->getMockElementCriteria();
        $this->setElementsService($mockElementCriteria);

        $service = new Export_UserService();
        $result = $service->setCriteria($settings);

        $this->assertInstanceOf('Craft\ElementCriteriaModel', $result);
    }

    /**
     * @param array $map
     * @param array $expectedResult
     *
     * @dataProvider provideValidAttributesMap
     * @covers ::getAttributes
     */
    public function testGetAttributes(array $map, array $expectedResult)
    {
        $mockElement = $this->getMockElement();
        $mockElement->expects($this->any())->method('__get')->willReturnCallback(
            function ($handle) {
                if ($handle == 'exception') {
                    throw new Exception('MockException');
                } elseif ($handle != 'parent' && $handle != 'ancestors') {
                    return $handle . '_value';
                }
                return null;
            }
        );

        $service = new Export_UserService();
        $result = $service->getAttributes($map, $mockElement);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function provideValidAttributesMap()
    {
        return array(
            'valid' => array(
                'map' => array(
                    'handle1' => array(
                        'checked' => 1,
                    ),
                    'exception' => array(
                        'checked' => 1,
                    )
                ),
                'expectedResult' => array(
                    'handle1' => 'handle1_value',
                    'exception' => null,
                ),
            )
        );
    }

    /**
     * @param $mockFieldHandle
     * @param $mockFieldName
     * @param $mockFieldType
     *
     * @return FieldModel|MockObject
     */
    private function getMockField($mockFieldHandle, $mockFieldName, $mockFieldType)
    {
        $mockField = $this->getMockBuilder('Craft\FieldModel')
            ->disableOriginalConstructor()
            ->getMock();

        $mockField->expects($this->any())->method('__get')->willReturnMap(array(
            array('handle', $mockFieldHandle),
            array('name', $mockFieldName),
            array('type', $mockFieldType),
        ));

        return $mockField;
    }

    /**
     * @param $stored
     */
    private function setMockExportService($stored)
    {
        $mockExportService = $this->getMock('Craft\ExportService');
        $mockExportService->expects($this->exactly(1))->method('findMap')->willReturn($stored);
        $this->setComponent(craft(), 'export', $mockExportService);
    }

    /**
     * @param $mockField
     *
     * @return FieldLayoutModel|MockObject
     */
    private function getMockFieldLayout($mockField)
    {
        $mockFieldLayoutField = $this->getMockBuilder('Craft\FieldLayoutFieldModel')
            ->disableOriginalConstructor()
            ->getMock();
        $mockFieldLayoutField->expects($this->exactly(1))->method('getField')->willReturn($mockField);

        $mockFieldLayout = $this->getMockBuilder('Craft\FieldLayoutModel')
            ->disableOriginalConstructor()
            ->getMock();
        $mockFieldLayout->expects($this->exactly(1))->method('getFields')->willReturn(array($mockFieldLayoutField));

        return $mockFieldLayout;
    }

    /**
     * @param $mockFieldLayout
     */
    private function setMockFieldsService($mockFieldLayout)
    {
        $mockFieldsService = $this->getMock('Craft\FieldsService');
        $mockFieldsService->expects($this->exactly(1))->method('getLayoutByType')
            ->with(ElementType::User)->willReturn($mockFieldLayout);
        $this->setComponent(craft(), 'fields', $mockFieldsService);
    }

    /**
     * @param $mockMap
     *
     * @return Export_MapRecord|MockObject
     */
    private function getMockExportMapRecord($mockMap)
    {
        $mockExportMap = $this->getMockBuilder('Craft\Export_MapRecord')
            ->disableOriginalConstructor()
            ->getMock();
        $mockExportMap->expects($this->exactly(1))->method('__get')->with('map')->willReturn($mockMap);

        return $mockExportMap;
    }

    /**
     * @return ElementCriteriaModel|MockObject
     */
    private function getMockElementCriteria()
    {
        $mockElementCriteria = $this->getMockBuilder('Craft\ElementCriteriaModel')
            ->disableOriginalConstructor()
            ->getMock();

        return $mockElementCriteria;
    }

    /**
     * @param $mockElementCriteria
     */
    private function setElementsService($mockElementCriteria)
    {
        $mockElementsService = $this->getMockBuilder('Craft\ElementsService')
            ->disableOriginalConstructor()
            ->getMock();
        $mockElementsService->expects($this->any())->method('getCriteria')->willReturn($mockElementCriteria);
        $this->setComponent(craft(), 'elements', $mockElementsService);
    }

    /**
     * @return BaseElementModel|MockObject
     */
    private function getMockElement()
    {
        $mockElement = $this->getMockBuilder('Craft\BaseElementModel')
            ->disableOriginalConstructor()
            ->getMock();

        return $mockElement;
    }
}
