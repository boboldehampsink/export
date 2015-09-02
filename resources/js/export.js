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
        
            $('#entrytypes').html('<option value="">' + Craft.t('All') + '</option>');
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
            
        // Enable sorting
        $('table.sortable tbody').sortable().disableSelection();
    
    }

    $('.js-btn-export').click(function(){
        $('.js-export-spinner').removeClass('hidden');
    });

});
