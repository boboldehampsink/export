<?php

namespace Craft;

/**
 * Export model.
 *
 * Contains reserved handles.
 *
 * @author    Bob Olde Hampsink <b.oldehampsink@itmundi.nl>
 * @copyright Copyright (c) 2015, Bob Olde Hampsink
 * @license   http://buildwithcraft.com/license Craft License Agreement
 *
 * @link      http://github.com/boboldehampsink
 */
class ExportModel extends BaseModel
{
    /**
     * Handles.
     */
    const HandleId           = 'id';
    const HandleStatus       = 'status';
    const HandleParent       = 'parent';
    const HandleAncestors    = 'ancestors';
    const HandleTitle        = 'title';
    const HandleSlug         = 'slug';

    # Entries
    const HandleAuthor       = 'authorId';
    const HandlePostDate     = 'postDate';
    const HandleExpiryDate   = 'expiryDate';
    const HandleEnabled      = 'enabled';

    # Users
    const HandleUsername             = 'username';
    const HandleFirstName            = 'firstName';
    const HandleLastName             = 'lastName';
    const HandleEmail                = 'email';
    const HandlePreferredLocale      = 'preferredLocale';
    const HandleWeekStartDay         = 'weekStartDay';
    const HandleLastLoginDate        = 'lastLoginDate';
    const HandleInvalidLoginCount    = 'invalidLoginCount';
    const HandleLastInvalidLoginDate = 'lastInvalidLoginDate';

    /**
     * Fieldtypes.
     */
    const FieldTypeEntries      = 'Entries';
    const FieldTypeCategories   = 'Categories';
    const FieldTypeAssets       = 'Assets';
    const FieldTypeUsers        = 'Users';
    const FieldTypeLightswitch  = 'Lightswitch';
    const FieldTypeTable        = 'Table';
    const FieldTypeRichText     = 'RichText';
    const FieldTypeCheckboxes   = 'Checkboxes';
    const FieldTypeRadioButtons = 'RadioButtons';
    const FieldTypeDropdown     = 'Dropdown';
    const FieldTypeMultiSelect  = 'MultiSelect';
    const FieldTypeDate         = 'Date';
}
