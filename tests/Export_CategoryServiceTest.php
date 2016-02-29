<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Contains unit tests for the Export_CategoryService.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@nerds.company>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   MIT
 *
 * @link      http://github.com/boboldehampsink
 *
 * @coversDefaultClass Craft\Export_CategoryService
 * @covers ::<!public>
 */
class Export_CategoryServiceTest extends BaseTest
{
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        // Set up parent
        parent::setUpBeforeClass();

        // Require dependencies
        require_once __DIR__.'/../services/Export_CategoryService.php';
        require_once __DIR__.'/../services/IExportElementType.php';
    }

    /**
     * Export_CategoryService should implement IExportElementType.
     */
    public function testExportCategoryServiceShouldImplementIExportElementType()
    {
        $this->assertInstanceOf('Craft\IExportElementType', new Export_CategoryService());
    }

    /**
     * @covers ::getTemplate
     */
    public function testGetTemplateShouldReturnTemplatePath()
    {
        $service = new Export_CategoryService();
        $template = $service->getTemplate();
        $this->assertEquals('export/sources/_category', $template);
    }

    /**
     * @covers ::getGroups
     */
    public function testGetGroupsShouldGetAllEditableCategoryGroups()
    {
        $expectedResult = array('editableGroup');

        $mockCategoriesService = $this->getMock('Craft\CategoriesService');
        $mockCategoriesService->expects($this->exactly(1))->method('getEditableGroups')->willReturn($expectedResult);
        $this->setComponent(craft(), 'categories', $mockCategoriesService);

        $service = new Export_CategoryService();
        $result = $service->getGroups();

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Get fields should return default category fields plus fields from category field layout.
     *
     * @covers ::getFields
     */
    public function testGetFieldsShouldReturnDefaultCategoryFieldsWhenNoStoredMapFound()
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
            'title' => array(
                'name' => 'Title',
                'checked' => 1,
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

        $service = new Export_CategoryService();
        $result = $service->getFields($settings, $reset);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Get fields should return default category fields plus fields from category field layout.
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

        $service = new Export_CategoryService();
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
                'group' => 1,
            ),
        );

        $mockElementCriteria = $this->getMockElementCriteria();
        $this->setElementsService($mockElementCriteria);

        $service = new Export_CategoryService();
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

        $this->setAttributesMockElementCriteria($map, $mockElement);

        $service = new Export_CategoryService();
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
                    ),
                    'parent' => array(
                        'checked' => '0',
                    ),
                    'ancestors' => array(
                        'checked' => '0',
                    ),
                ),
                'expectedResult' => array(
                    'handle1' => 'handle1_value',
                    'exception' => null,
                    'parent' => 'parent',
                    'ancestors' => 'ancestor1/ancestor2',
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
            ->with(ElementType::Category)->willReturn($mockFieldLayout);
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

    /**
     * @param array $map
     * @param BaseElementModel|MockObject $mockElement
     */
    private function setAttributesMockElementCriteria(array $map, BaseElementModel $mockElement)
    {
        $mockElementCriteria = $this->getMockElementCriteria();
        if (array_key_exists('parent', $map)) {
            $mockElementCriteria->expects($this->exactly(1))->method('first')
                ->willReturn('parent');
        }
        if (array_key_exists('ancestors', $map)) {
            $mockElementCriteria->expects($this->exactly(1))->method('find')
                ->willReturn(array('ancestor1', 'ancestor2'));
        }
        $mockElement->expects($this->any())->method('getAncestors')->willReturn($mockElementCriteria);
    }
}
