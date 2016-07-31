jQuery.noConflict();

(function($) {
    jQuery(document).ready(function() {
        jQuery('#peliculas_carousel_thumbnail').carousel();

        jQuery('.carousel').each(function (index, element) {
            jQuery(this)[index].slide = null;
        });
    });
})(jQuery);