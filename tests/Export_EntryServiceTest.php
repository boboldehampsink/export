<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Contains unit tests for the Export_EntryService.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 *
 * @coversDefaultClass Craft\Export_EntryService
 * @covers ::<!public>
 */
class Export_EntryServiceTest extends BaseTest
{
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        // Set up parent
        parent::setUpBeforeClass();

        // Require dependencies
        require_once __DIR__ . '/../services/Export_EntryService.php';
        require_once __DIR__ . '/../services/IExportElementType.php';
    }

    /**
     * Export_EntryService should implement IExportElementType
     */
    public function testExportEntryServiceShouldImplementIExportElementType()
    {
        $this->assertInstanceOf('Craft\IExportElementType', new Export_EntryService());
    }

    /**
     * @covers ::getTemplate
     */
    public function testGetTemplateShouldReturnTemplatePath()
    {
        $service = new Export_EntryService();
        $template = $service->getTemplate();
        $this->assertEquals('export/sources/_entry', $template);
    }

    /**
     * @covers ::getGroups
     */
    public function testGetGroupsShouldGetAllEditableEntryGroups()
    {
        $mockSection = $this->getMockBuilder('Craft\SectionModel')
            ->disableOriginalConstructor()
            ->getMock();
        $expectedResult = array($mockSection);

        $mockSectionsService = $this->getMock('Craft\SectionsService');
        $mockSectionsService->expects($this->exactly(1))->method('getEditableSections')->willReturn($expectedResult);
        $this->setComponent(craft(), 'sections', $mockSectionsService);

        $service = new Export_EntryService();
        $result = $service->getGroups();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Get fields should return default entry fields plus fields from entry field layout
     *
     * @covers ::getFields
     */
    public function testGetFieldsShouldReturnDefaultEntryFieldsWhenNoStoredMapFound()
    {
        $settings = array(
            'elementvars' => array(
                'section' => 1,
                'entrytype' => 0,
            )
        );
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
            'title_' => array(
                'name' => 'Title',
                'checked' => 1,
                'entrytype' => null,
            ),
            'slug' => array(
                'name' => 'Slug',
                'checked' => 0,
            ),
            'parent' => array(
                'name' => 'Parent',
                'checked' => 0,
            ),
            'ancestors' => array(
                'name' => 'Ancestors',
                'checked' => 0,
            ),
            'authorId' => array(
                'name' => 'Author',
                'checked' => 0,
            ),
            'postDate' => array(
                'name' => 'Post Date',
                'checked' => 0,
            ),
            'expiryDate' => array(
                'name' => 'Expiry Date',
                'checked' => 0,
            ),
            'enabled' => array(
                'name' => 'Enabled',
                'checked' => 0,
            ),
            'status' => array(
                'name' => 'Status',
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

        $mockEntryType = $this->getMockEntryType();
        $this->setMockSectionsService($mockEntryType);

        $service = new Export_EntryService();
        $result = $service->getFields($settings, $reset);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Get fields should return default entry fields plus fields from entry field layout
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

        $service = new Export_EntryService();
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
                'section' => 1,
                'entrytype' => 1,
            )
        );

        $mockElementCriteria = $this->getMockElementCriteria();
        $this->setElementsService($mockElementCriteria);

        $service = new Export_EntryService();
        $result = $service->setCriteria($settings);

        $this->assertInstanceOf('Craft\ElementCriteriaModel', $result);
    }

    /**
     * @covers ::getAttributes
     */
    public function testGetAttributes()
    {
        $map = array(
            'handle1' => array(
                'checked' => 1
            ),
            'parent' => array(
                'checked' => '0',
            ),
            'ancestors' => array(
                'checked' => '0',
            ),
        );

        $expectedResult = array(
            'handle1' => 'value1',
            'title_' => null,
            'parent' => 'parent',
            'ancestors' => 'ancestor1/ancestor2',
        );

        $mockElementCriteria = $this->getMockElementCriteria();
        $mockElementCriteria->expects($this->exactly(1))->method('first')->willReturn('parent');
        $mockElementCriteria->expects($this->exactly(1))->method('find')->willReturn(array('ancestor1', 'ancestor2'));

        $mockElement = $this->getMockElement();
        $mockElement->expects($this->any())->method('__get')->willReturnMap(array(
            array('handle1', 'value1'),
        ));
        $mockElement->expects($this->exactly(4))->method('getAncestors')->willReturn($mockElementCriteria);

        $mockEntryType = $this->getMockEntryType();
        $this->setMockSectionsService($mockEntryType);


        $service = new Export_EntryService();
        $result = $service->getAttributes($map, $mockElement);


        $this->assertSame($expectedResult, $result);
    }

    /**
     * @param $mockFieldHandle
     * @param $mockFieldName
     * @param $mockFieldType
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
     * @return FieldLayoutModel|MockObject
     */
    private function getMockFieldLayout($mockField)
    {
        $mockFieldLayoutField = $this->getMockBuilder('Craft\FieldLayoutFieldModel')
            ->disableOriginalConstructor()
            ->getMock();
        $mockFieldLayoutField->expects($this->exactly(1))->method('getField')->willReturn($mockField);

        $mockFieldLayoutTab = $this->getMockBuilder('Craft\FieldLayoutTabModel')
            ->disableOriginalConstructor()
            ->getMock();
        $mockFieldLayoutTab->expects($this->exactly(1))->method('getFields')->willReturn(array($mockFieldLayoutField));

        $mockFieldLayout = $this->getMockBuilder('Craft\FieldLayoutModel')
            ->disableOriginalConstructor()
            ->getMock();

        $mockFieldLayout->expects($this->exactly(1))->method('getTabs')->willReturn(array($mockFieldLayoutTab));
        return $mockFieldLayout;
    }

    /**
     * @param $mockFieldLayout
     */
    private function setMockFieldsService($mockFieldLayout)
    {
        $mockFieldsService = $this->getMock('Craft\FieldsService');
        $mockFieldsService->expects($this->exactly(1))->method('getLayoutById')
            ->willReturn($mockFieldLayout);
        $this->setComponent(craft(), 'fields', $mockFieldsService);
    }

    /**
     * @param $mockMap
     * @return Export_MapRecord|MockObject
     */
    private function getMockExportMapRecord($mockMap)
    {
        $mockExportMap = $this->getMockBuilder('Craft\Export_MapRecord')
            ->disableOriginalConstructor()
            ->getMock();;
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

    /**
     * @return EntryTypeModel|MockObject
     */
    private function getMockEntryType()
    {
        $mockEntryType = $this->getMockBuilder('Craft\EntryTypeModel')
            ->disableOriginalConstructor()
            ->getMock();
        return $mockEntryType;
    }

    /**
     * @param $mockEntryType
     */
    private function setMockSectionsService($mockEntryType)
    {
        $mockSectionsService = $this->getMock('Craft\SectionsService');
        $mockSectionsService->expects($this->exactly(1))->method('getEntryTypesBySectionId')
            ->willReturn(array($mockEntryType));
        $this->setComponent(craft(), 'sections', $mockSectionsService);
    }
}
