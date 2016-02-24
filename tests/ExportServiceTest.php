<?php

namespace Craft;

use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Contains unit tests for the ExportService.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
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
        require_once __DIR__ . '/../services/ExportService.php';
        require_once __DIR__ . '/../services/IExportElementType.php';
        require_once __DIR__ . '/../services/Export_EntryService.php';
        require_once __DIR__ . '/../services/Export_UserService.php';
        require_once __DIR__ . '/../services/Export_CategoryService.php';
        require_once __DIR__ . '/../records/Export_MapRecord.php';
        require_once __DIR__ . '/../models/ExportModel.php';
    }

    /**
     * Save map should save a new map when record not found
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
     * Save map should save an existing map when record found
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
     * Download with unknown type should throw exception
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
     * Download of type entry should use export_entry service
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
     * Download with valid settings should export data
     *
     * @param array $settings
     * @param array $attributes
     * @param string $expectedResult
     *
     * @dataProvider provideValidDownloadSettings
     * @covers ::download
     */
    public function testDownloadWithValidSettingsShouldExportData(array $settings, array $attributes, $expectedResult)
    {
        $elementIds = array(1);

        $mockElementCriteria = $this->getMockElementCriteria(array('ids' => $elementIds));
        $this->setMockExportTypeService($settings['type'], $mockElementCriteria, $attributes);

        $mockElement = $this->getMockElement();
        $this->setMockElementsService($mockElement);

        if (@$settings['map']['authorId']['checked']) {
            $mockUser = $this->getMockUser();
            $this->setMockUsersService($mockUser);
        }

        $mockField = $this->getMockField();
        $this->setMockFieldsService($mockField);

        $service = new ExportService();
        $data = $service->download($settings);

        $this->assertEquals($expectedResult, $data);
    }

    /**
     * Data provider for valid download types
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
     * Data provider for valid download settings
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
                'result' => 'ID,Slug,Author,"Post Date",Enabled' . "\r\n" . '1,testslug,,' . (string)$now . ',Yes' . "\r\n",
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
                'result' => 'Username,"First Name","Last Name",Email,Enabled' . "\r\n" . 'name,Hanzel,Grimm,Hanzel.Grimm@gmail.com,No' . "\r\n",
            ),
        );
    }

    /**
     * @param array $methods
     * @return ElementCriteriaModel|MockObject
     */
    private function getMockElementCriteria(array $methods = array())
    {
        $mockElementCriteria = $this->getMockBuilder('Craft\ElementCriteriaModel')
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($methods as $method => $result) {
            $mockElementCriteria->expects($this->exactly(1))->method($method)->willReturn($result);
        }

        return $mockElementCriteria;
    }

    /**
     * @param string $type
     * @param ElementCriteriaModel $mockElementCriteria
     * @param array $attributes
     */
    private function setMockExportTypeService($type, ElementCriteriaModel $mockElementCriteria, array $attributes = array())
    {
        $className = 'Craft\Export_' . $type . 'Service';
        if (class_exists($className)) {
            $mockExportTypeService = $this->getMock($className);
            $mockExportTypeService->expects($this->any())->method('setCriteria')->willReturn($mockElementCriteria);
            $mockExportTypeService->expects($this->any())->method('getAttributes')->willReturn($attributes);
            $this->setComponent(craft(), 'export_' . strtolower($type), $mockExportTypeService);
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
}
