<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Contains unit tests for the ExportService.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@nerds.company>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   MIT
 *
 * @link      http://github.com/boboldehampsink
 *
 * @coversDefaultClass Craft\ExportService
 * @covers ::<!public>
 */
class ExportServiceTest extends BaseTest
{
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        // Set up parent
        parent::setUpBeforeClass();

        // Require dependencies
        require_once __DIR__.'/../services/ExportService.php';
        require_once __DIR__.'/../services/IExportElementType.php';
        require_once __DIR__.'/../services/Export_EntryService.php';
        require_once __DIR__.'/../services/Export_UserService.php';
        require_once __DIR__.'/../services/Export_CategoryService.php';
        require_once __DIR__.'/../records/Export_MapRecord.php';
        require_once __DIR__.'/../models/ExportModel.php';
    }

    /**
     * Save map should save a new map when record not found.
     *
     * @covers ::saveMap
     */
    public function testSaveMapShouldSaveNewMapWhenRecordNotFound()
    {
        $settings = array();
        $map = array();

        $service = $this->getMockExportService();

        $mockExportMap = $this->getMockExportMap();
        $service->expects($this->exactly(1))->method('findMap')
            ->with($this->isInstanceOf('CDbCriteria'))->willReturn(null);
        $service->expects($this->exactly(1))->method('getNewMap')->willReturn($mockExportMap);
        $mockExportMap->expects($this->exactly(1))->method('save')->with(false);

        $service->saveMap($settings, $map);
    }

    /**
     * Save map should save an existing map when record found.
     *
     * @covers ::saveMap
     */
    public function testSaveMapShouldSaveExistingMapWhenRecordFound()
    {
        $settings = array();
        $map = array();

        $service = $this->getMockExportService();

        $mockExportMap = $this->getMockExportMap();
        $service->expects($this->exactly(1))->method('findMap')
            ->with($this->isInstanceOf('CDbCriteria'))->willReturn($mockExportMap);
        $service->expects($this->exactly(0))->method('getNewMap');
        $mockExportMap->expects($this->exactly(1))->method('save')->with(false);

        $service->saveMap($settings, $map);
    }

    /**
     * Download with unknown type should throw exception.
     *
     * @covers ::download
     * @covers ::getService
     *
     * @expectedException Exception
     * @expectedExceptionMessage Unknown Element Type Service called.
     */
    public function testDownloadShouldThrowExceptionWhenServiceDoesNotExists()
    {
        $settings = array(
            'type' => 'TypeDoesNotExist',
        );

        $service = new ExportService();
        $service->download($settings);
    }

    /**
     * Download of type entry should use export_entry service.
     *
     * @param array $settings
     *
     * @dataProvider provideValidDownloadTypes
     * @covers ::download
     * @covers ::getService
     */
    public function testDownloadWithTypeShouldUseCorrectExportService(array $settings)
    {
        $mockElementCriteria = $this->getMockElementCriteria();
        $this->setMockExportTypeService($settings['type'], $mockElementCriteria);

        $service = new ExportService();
        $service->download($settings);
    }

    /**
     * Download with valid settings should export data.
     *
     * @param array  $settings
     * @param array  $attributes
     * @param array  $sources
     * @param string $expectedResult
     *
     * @dataProvider provideValidDownloadSettings
     * @covers ::download
     * @covers ::parseFieldData
     */
    public function testDownloadWithValidSettingsShouldExportData(array $settings, array $attributes, $sources = array(), $expectedResult)
    {
        $elementIds = array(1);

        $mockElementCriteria = $this->getMockElementCriteria(array('ids' => $elementIds));
        $this->setMockExportTypeService($settings['type'], $mockElementCriteria, $attributes);

        if (@$settings['map']['authorId']['checked']) {
            $mockUser = $this->getMockUser();
            $this->setMockUsersService($mockUser);
        }

        $mockElement = $this->getMockElement();
        $this->setMockElementsService($mockElement);

        $mockField = $this->getMockField();
        $this->setMockFieldsService($mockField);

        $this->setMockPluginsService($sources);

        $service = new ExportService();
        $data = $service->download($settings);

        $this->assertEquals($expectedResult, $data);
    }

    /**
     * @param string $fieldType
     * @param mixed  $data
     * @param string $expectedResult
     *
     * @dataProvider provideFieldTypeData
     * @covers ::parseFieldData
     */
    public function testParseFieldDataWithFieldTypes($fieldType, $data, $expectedResult)
    {
        $fieldHandle = 'fieldHandle';

        $mockField = $this->getMockField();
        $mockField->expects($this->any())->method('__get')->willReturnMap(array(
            array('handle', $fieldHandle),
            array('type', $fieldType),
        ));

        $this->setMockFieldsService($mockField);

        $service = new ExportService();
        $result = $service->parseFieldData($fieldHandle, $data);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @covers ::getCustomTableRow
     */
    public function testGetCustomTableRowShouldReturnCustomTemplateForFieldTemplate()
    {
        $fieldHandle = 'fieldHandle';
        $expectedResult = 'customTemplate';
        $responses = array(
            array($fieldHandle => $expectedResult),
        );

        $this->setMockPluginsService($responses);

        $service = new exportService();
        $result = $service->getCustomTableRow($fieldHandle);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * @covers ::getCustomTableRow
     */
    public function testGetCustomTableRowShouldReturnFalseWhenNoCustomTemplateFound()
    {
        $fieldHandle = 'fieldHandle';
        $responses = array();

        $this->setMockPluginsService($responses);

        $service = new exportService();
        $result = $service->getCustomTableRow($fieldHandle);

        $this->assertFalse($result);
    }

    /**
     * Data provider for valid download types.
     *
     * @return array
     */
    public function provideValidDownloadTypes()
    {
        return array(
            'Entry' => array(
                'settings' => array(
                    'type' => 'Entry',
                ),
            ),
            'User' => array(
                'settings' => array(
                    'type' => 'User',
                ),
            ),
            'Category' => array(
                'settings' => array(
                    'type' => 'Category',
                ),
            ),
        );
    }

    /**
     * Data provider for valid download settings.
     *
     * @return array
     */
    public function provideValidDownloadSettings()
    {
        $now = new DateTime();

        return array(
            'Entry' => array(
                'settings' => array(
                    'type' => 'Entry',
                    'offset' => 0,
                    'limit' => 10,
                    'elementvars' => array(
                        'section' => '14',
                        'entrytype' => '',
                    ),
                    'map' => array(
                        'elementId' => array(
                            'name' => 'ID',
                            'label' => 'ID',
                            'checked' => '1',
                        ),
                        'slug' => array(
                            'name' => 'Slug',
                            'label' => 'Slug',
                            'checked' => '1',
                        ),
                        'authorId' => array(
                            'name' => 'Author',
                            'label' => 'Author',
                            'checked' => '1',
                        ),
                        'postDate' => array(
                            'name' => 'Post Date',
                            'label' => 'Post Date',
                            'checked' => '1',
                        ),
                        'expiryDate' => array(
                            'name' => 'Expiry Date',
                            'label' => 'Expiry Date',
                            'checked' => '',
                        ),
                        'enabled' => array(
                            'name' => 'Enabled',
                            'label' => 'Enabled',
                            'checked' => '1',
                        ),
                        'status' => array(
                            'name' => 'Status',
                            'label' => 'Status',
                            'checked' => '',
                        ),
                    ),
                ),
                'attributes' => array(
                    'elementId' => 1,
                    'slug' => 'testslug',
                    'authorId' => 2,
                    'postDate' => $now,
                    'expiryDate' => new DateTime(),
                    'enabled' => '1',
                    'status' => 'live',
                ),
                'sources' => array(),
                'result' => 'ID,Slug,Author,"Post Date",Enabled'."\r\n".'1,testslug,,'.(string) $now.',Yes'."\r\n",
            ),
            'EntryFromSource' => array(
                'settings' => array(
                    'type' => 'Entry',
                    'offset' => 0,
                    'limit' => 10,
                    'elementvars' => array(
                        'section' => '14',
                        'entrytype' => '',
                    ),
                    'map' => array(
                        'elementId' => array(
                            'name' => 'ID',
                            'label' => 'ID',
                            'checked' => '1',
                        ),
                        'slug' => array(
                            'name' => 'Slug',
                            'label' => 'Slug',
                            'checked' => '1',
                        ),
                    ),
                ),
                'attributes' => array(),
                'sources' => array(
                    'plugin' => array(
                        array(
                            'elementId' => 1,
                            'slug' => 'testslug',
                        ),
                    ),
                ),
                'result' => 'ID,Slug'."\r\n".'1,testslug'."\r\n",
            ),
            'User' => array(
                'settings' => array(
                    'type' => 'User',
                    'elementvars' => array(
                        'groups' => array(1),
                    ),
                    'map' => array(
                        'elementId' => array(
                            'name' => 'ID',
                            'label' => 'ID',
                            'checked' => '',
                        ),
                        'username' => array(
                            'name' => 'Username',
                            'label' => 'Username',
                            'checked' => '1',
                        ),
                        'firstName' => array(
                            'name' => 'First Name',
                            'label' => 'First Name',
                            'checked' => '1',
                        ),
                        'lastName' => array(
                            'name' => 'Last Name',
                            'label' => 'Last Name',
                            'checked' => '1',
                        ),
                        'email' => array(
                            'name' => 'Email',
                            'label' => 'Email',
                            'checked' => '1',
                        ),
                        'enabled' => array(
                            'name' => 'Enabled',
                            'label' => 'Enabled',
                            'checked' => '1',
                        ),
                        'status' => array(
                            'name' => 'Status',
                            'label' => 'Status',
                            'checked' => '',
                        ),
                    ),
                ),
                'attributes' => array(
                    'elementId' => 1,
                    'username' => 'name',
                    'firstName' => 'Hanzel',
                    'lastName' => 'Grimm',
                    'email' => 'Hanzel.Grimm@gmail.com',
                    'enabled' => '0',
                    'status' => 'live',
                ),
                'sources' => array(),
                'result' => 'Username,"First Name","Last Name",Email,Enabled'."\r\n".'name,Hanzel,Grimm,Hanzel.Grimm@gmail.com,No'."\r\n",
            ),
        );
    }

    /**
     * Data provider for field type data.
     *
     * @return array
     */
    public function provideFieldTypeData()
    {
        return array(
            'default' => array(
                'fieldType' => 'default',
                'data' => 'default',
                'expectedResult' => 'default',
            ),
            'object' => array(
                'fieldType' => 'object',
                'data' => (object) array('value' => 'test'),
                'expectedResult' => 'test',
            ),
            'Lightswitch yes' => array(
                'fieldType' => 'Lightswitch',
                'data' => '1',
                'expectedResult' => 'Yes',
            ),
            'Lightswitch no' => array(
                'fieldType' => 'Lightswitch',
                'data' => '0',
                'expectedResult' => 'No',
            ),
            'RichText' => array(
                'fieldType' => 'RichText',
                'data' => '<b>test</b>',
                'expectedResult' => '<b>test</b>',
            ),
            'MultiSelect' => array(
                'fieldType' => 'MultiSelect',
                'data' => array(
                    (object) array('value' => 'option1'),
                    (object) array('value' => 'option2'),
                ),
                'expectedResult' => 'option1, option2',
            ),
            'Table' => array(
                'fieldType' => 'Table',
                'data' => array(array(
                    array('column1' => 'value1'),
                    array('column2' => 'value2'),
                )),
                'expectedResult' => 'value1, value2',
            ),
            'Entries' => array(
                'fieldType' => 'Entries',
                'data' => 'dummy',
                'expectedResult' => 'dummy',
            ),
        );
    }

    /**
     * @param array $methods
     *
     * @return ElementCriteriaModel|MockObject
     */
    private function getMockElementCriteria(array $methods = array())
    {
        $mockElementCriteria = $this->getMockBuilder('Craft\ElementCriteriaModel')
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($methods as $method => $result) {
            $mockElementCriteria->expects($this->any())->method($method)->willReturn($result);
        }

        return $mockElementCriteria;
    }

    /**
     * @param string               $type
     * @param ElementCriteriaModel $mockElementCriteria
     * @param array                $attributes
     */
    private function setMockExportTypeService($type, ElementCriteriaModel $mockElementCriteria, array $attributes = array())
    {
        $className = 'Craft\Export_'.$type.'Service';
        if (class_exists($className)) {
            $mockExportTypeService = $this->getMock($className);
            $mockExportTypeService->expects($this->any())->method('setCriteria')->willReturn($mockElementCriteria);
            $mockExportTypeService->expects($this->any())->method('getAttributes')->willReturn($attributes);
            $this->setComponent(craft(), 'export_'.strtolower($type), $mockExportTypeService);
        }
    }

    /**
     * @param $mockElement
     */
    private function setMockElementsService($mockElement)
    {
        $mockElementsService = $this->getMockBuilder('Craft\ElementsService')
            ->disableOriginalConstructor()
            ->getMock();
        $mockElementsService->expects($this->any())->method('getElementById')->willReturn($mockElement);
        $this->setComponent(craft(), 'elements', $mockElementsService);
    }

    /**
     * @param $mockUser
     */
    private function setMockUsersService($mockUser)
    {
        $mockUsersService = $this->getMockBuilder('Craft\UsersService')
            ->disableOriginalConstructor()
            ->getMock();
        $mockUsersService->expects($this->any())->method('getUserById')->willReturn($mockUser);
        $this->setComponent(craft(), 'users', $mockUsersService);
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
     * @return UserModel|MockObject
     */
    private function getMockUser()
    {
        $mockUser = $this->getMockBuilder('Craft\UserModel')
            ->disableOriginalConstructor()
            ->getMock();

        return $mockUser;
    }

    /**
     * @param $mockField
     */
    private function setMockFieldsService($mockField)
    {
        $mockFieldsService = $this->getMockBuilder('Craft\FieldsService')
            ->disableOriginalConstructor()
            ->getMock();
        $mockFieldsService->expects($this->any())->method('getFieldByHandle')->willReturn($mockField);
        $this->setComponent(craft(), 'fields', $mockFieldsService);
    }

    /**
     * @return FieldModel|MockObject
     */
    private function getMockField()
    {
        $mockField = $this->getMockBuilder('Craft\FieldModel')
            ->disableOriginalConstructor()
            ->getMock();

        return $mockField;
    }

    /**
     * @return Export_MapRecord|MockObject
     */
    private function getMockExportMap()
    {
        $mockExportMap = $this->getMockBuilder('Craft\Export_MapRecord')
            ->disableOriginalConstructor()
            ->getMock();

        return $mockExportMap;
    }

    /**
     * @return ExportService|MockObject
     */
    private function getMockExportService()
    {
        $service = $this->getMockBuilder('Craft\ExportService')
            ->setMethods(array('findMap', 'getNewMap'))
            ->getMock();

        return $service;
    }

    /**
     * @param $sources
     */
    private function setMockPluginsService($sources)
    {
        $mockPluginsService = $this->getMockBuilder('Craft\PluginsService')
            ->disableOriginalConstructor()
            ->getMock();
        $mockPluginsService->expects($this->any())->method('call')->willReturn($sources);
        $this->setComponent(craft(), 'plugins', $mockPluginsService);
    }
}
