jQuery(document).ready(function($) {
    window.setTimeout(() => {
        const source = [];
        try {
            source = JSON.parse($('#mime').attr('data-types'));
        } catch (e) {
        }
        $('#mime').autocomplete({ source })
    }, 0); // Чтобы отработал Vue
});