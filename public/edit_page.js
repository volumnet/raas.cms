jQuery(document).ready(function($) {
    $('#mime').autocomplete({
        source: JSON.parse($('#mime').attr('data-types'))
    })
});