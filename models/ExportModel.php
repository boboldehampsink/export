<?php
namespace Craft;

class ExportModel extends BaseModel
{

    // Handles
    const HandleStatus       = 'status';
    const HandleId           = 'elementId';
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

    // Fieldtypes
    const FieldTypeEntries     = 'Entries';
    const FieldTypeCategories  = 'Categories';
    const FieldTypeAssets      = 'Assets';
    const FieldTypeUsers       = 'Users';
    const FieldTypeLightswitch = 'Lightswitch';
    const FieldTypeTable       = 'Table';

    // Delimiters
    const DelimiterSemicolon = ';';
    const DelimiterComma     = ',';
    const DelimiterPipe      = '|';
}
