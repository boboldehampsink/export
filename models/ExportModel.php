<?php
namespace Craft;

class ExportModel extends BaseModel
{
     
    // Handles
    const HandleStatus       = 'status';
    const HandleId           = 'elementId';
    # Entries
    const HandleTitle        = 'title';
    const HandleAuthor       = 'authorId';
    const HandlePostDate     = 'postDate';
    const HandleExpiryDate   = 'expiryDate';
    const HandleEnabled      = 'enabled';
    const HandleSlug         = 'slug';
    # Users
    const HandleUsername     = 'username';
    const HandleFirstName    = 'firstName';
    const HandleLastName     = 'lastName';
    const HandleEmail        = 'email';
    
    // Fieldtypes
    const FieldTypeEntries     = 'Entries';
    const FieldTypeCategories  = 'Categories';
    const FieldTypeAssets      = 'Assets';
    const FieldTypeUsers       = 'Users';
    const FieldTypeLightswitch = 'Lightswitch';
    
    // Delimiters
    const DelimiterSemicolon = ';';
    const DelimiterComma     = ',';
    const DelimiterPipe      = '|';
    
}
