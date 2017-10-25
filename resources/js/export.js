<<<<<<< HEAD
$(function() {

    if($('#sections').length) {
    
        // Show the fields that match the export type
        $('#types').change(function() {
        
            $('.type').hide().find('input, select').prop('disabled', true);
            $('.' + $(this).val().toLowerCase()).show().find('input, select').prop('disabled', false);     
            
        });
        
=======
$(function () {

    if ($('#sections').length) {

        // Show the fields that match the export type
        $('#types').change(function () {

            $('.type').hide().find('input, select').prop('disabled', true);
            $('.' + $(this).val().toLowerCase()).show().find('input, select').prop('disabled', false);

        });

>>>>>>> 7543ef1... Able to select options on whether or not you want to export and email the result later or download the responsr
        // Trigger change on load
        $('#types').trigger('change');

        // Find entry types by chosen section
<<<<<<< HEAD
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

=======
        $(document).on('change', '#sections', function () {

            $('#entrytypes').html('<option value="">' + Craft.t('All') + '</option>');
            Craft.postActionRequest('export/getEntryTypes', {'section': $(this).val()}, function (entrytypes) {

                $.each(entrytypes, function (index, value) {
                    $('#entrytypes').append('<option value="' + value.id + '">' + value.name + '</option>');
                });

            });

        });

    }

    if ($('table.sortable tbody').length) {

        // Set an absolute width
        $('table.sortable td').each(function () {
            $(this).width($(this).width()).css('cursor', 'move');
        });

        // Enable sorting
        $('table.sortable tbody').sortable().disableSelection();

    }

    $('.js-btn-export').click(function () {
        $('.js-export-spinner').removeClass('hidden');
    });

    $('#deliveryOption').change(function () {
        var emailInput = $('#emailInput').removeClass('hidden');

        this.value === 'email' ? emailInput.removeClass('hidden') : emailInput.addClass('hidden');
    });

>>>>>>> 7543ef1... Able to select options on whether or not you want to export and email the result later or download the responsr
});
