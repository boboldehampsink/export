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
