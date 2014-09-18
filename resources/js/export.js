$(function() {

    if($('#sections').length) {
    
        // Show the fields that match the export type
        $('#types').change(function() {
        
            $('.type').hide().find('input, select').prop('disabled', true);
            $('.' + $(this).val().toLowerCase()).show().find('input, select').prop('disabled', false);     
            
        });
        
        // Trigger change on load
        $('#types').trigger('change');

        // Find entry types by chosen section
        $(document).on('change', '#sections', function() {
        
            $('#entrytypes').html('');
            Craft.postActionRequest('export/getEntryTypes', { 'section': $(this).val() }, function(entrytypes) {
                    
                $.each(entrytypes, function(index, value) {
                    $('#entrytypes').append('<option value="' + value.id + '">' + value.name + '</option>');
                });
            
            });
            
        });
        
    }
    
    if($('table.sortable tbody').length) {
        
        // Set an absolute width
        $('table.sortable td').each(function() {
            $(this).width($(this).width()).css('cursor', 'move');
        });
        
        // Get map
        var map = remapArray($('table.sortable').parent('form').serializeArray());
        
        // Check if map for this section already exists
        if(Craft.getLocalStorage('export_' + map.section)) {
        
            // Get the stored map
            var map = JSON.parse(Craft.getLocalStorage('export_' + map.section));
                
            // Create clone of current table
            var clone = $('table.sortable tbody').clone();
            
            // Delete current table
            $('table.sortable tbody tr').remove();
                        
            // Loop through map
            $.each(map, function(key, item) {
                        
                // Inject back to table, in right order
                $('table.sortable tbody').append(clone.find('input[name="' + key + '"]').parents('tr:eq(0)'));
                
            });
                                    
        }
        
        // Enable sorting
        $('table.sortable tbody').sortable({
            update: function(event, ui) {
            
                // Save updated map to localstorage
                var map = remapArray($('table.sortable').parent('form').serializeArray());
                Craft.setLocalStorage('export_' + map.section, JSON.stringify(map));
                
                console.log('New map saved');
            
            }
        }).disableSelection();
    
    }
    
});

function remapArray(array) {

    // Create a map
    var map = {};
    $.each(array, function(key, item) {
        map[item.name] = item.value;
    });
    
    return map;

}