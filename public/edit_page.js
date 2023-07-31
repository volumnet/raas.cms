jQuery(document).ready(function($) {
    window.setTimeout(() => {
        $('#mime').autocomplete({
            source: JSON.parse($('#mime').attr('data-types'))
        })
    }, 0); // Чтобы отработал Vue
});