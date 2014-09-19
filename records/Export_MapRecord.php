<?php
namespace Craft;

class Export_MapRecord extends BaseRecord
{

    public function getTableName()
    {
        return 'export_map';
    }

    protected function defineAttributes()
    {
        return array(
            'settings' => AttributeType::Mixed,
            'map'      => AttributeType::Mixed
        );
    }
    
}