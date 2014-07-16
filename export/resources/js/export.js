$(function() {

    if($('#sections').length) {
    
        // Show the fields that match the export type
        $('#types').change(function() {
        
            $('.type').hide();
            $('.' + $(this).val().toLowerCase()).show();     
            
        });

        // Find entry types by chosen section
        $('#sections').change(function() {
        
            $('#entrytypes').html('');
            Craft.postActionRequest('export/getEntryTypes', { 'section': $(this).val() }, function(entrytypes) {
                    
                $.each(entrytypes, function(index, value) {
                    $('#entrytypes').append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            
            });
            
        });
        
    }
    
});