<?php
/**
 * @version     16/05/15 18:31
 * @package     com_peliculas
 * @copyright	Copyright (C) 2005 - 2016 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$document = JFactory::getDocument();

$document->addScript(JURI::root() . 'media/com_peliculas/js/jquery.lightbox.js');
$document->addStyleSheet(JURI::root() . 'media/com_peliculas/css/jquery.lightbox.css');
$lightboxInit = '
function initJSlightBox(){
	jQuery("a.lightbox").lightBox({
        imageLoading: "'.JURI::root().'media/com_peliculas/images/icons/loading.gif",
        imageBtnClose: "'.JURI::root().'media/com_peliculas/images/icons/close.gif",
        imageBtnPrev: "'.JURI::root().'media/com_peliculas/images/icons/prev.gif",
        imageBtnNext: "'.JURI::root().'media/com_peliculas/images/icons/next.gif",
        imageBlank: "'.JURI::root().'media/com_peliculas/images/icons/blank.gif",
        txtImage: "Imagen",
        txtOf: "de"
    });
}
jQuery(function() { 
    initJSlightBox(); 
});
';
$document->addScriptDeclaration($lightboxInit);


$baseURL        = JURI::root();
$imgBaseURL     = $baseURL . 'images/peliculas/backdrops/' . $this->item->id;
$columns 		= 4;
$show_captions  = true;
$show_controls  = true;
$show_indicators= false;
$ispan      	= 12 / $columns;

if (is_string($this->item->imagenes)) {
    $images = (array) json_decode($this->item->imagenes);
} else {
    $images = (array) $this->item->imagenes;
    foreach ($images as $k => $image) {
        $images[$k] = (object) $image;
    }
}

if (!empty($images)) {
    ?>
    <div class="row-fluid"><div class="span12">
    <div class="peliculas_block">
        <div class="peliculas_block_header">
            <span class="peliculas_header"><?php echo JText::_('COM_PELICULAS_FIELDSET_IMAGES');?></span>
        </div>

        <div class="peliculas_block_content">
            <?php

            echo '<div id="peliculas_carousel_thumbnail" class="carousel slide hidden-phone">';

            $ncont      = 0;
            $icolumn    = 0;
            $htmlImages = array();

            foreach ($images as $k => $image) {
                $imageName = $image->name;
                $image_big_src      = $imgBaseURL . '/big-'     . $imageName;
                $image_thumb_src    = $imgBaseURL . '/thumb-'   . $imageName;

                if (($ncont % $columns) == 0) {
                    $icolumn++;
                }

                $alt = $image->name;
                if (empty($alt)) {
                    $alt = $this->item->name;
                }

                $itemDsp = '<div class="peliculas_thumb span' . $ispan . '">';
                $itemDsp .= "<a class='lightbox' id='main_image_full_" . $k . "' href='" . $image_big_src . "'";
                $itemDsp .= " title='" . htmlspecialchars($image->title) . "' >";
                $itemDsp .= "<img src='" . $image_thumb_src . "' alt='" . $alt . "' style='max-width:100%;width:100%;' title='" . $alt. "' />";
                $itemDsp .= "</a>";
                $itemDsp .= "</div>\n";


                if (!isset($htmlImages[$icolumn])) {
                    $htmlImages[$icolumn] = '';
                }
                $htmlImages[$icolumn] .= $itemDsp;

                $ncont++;
            }

            if ($show_indicators) {
                // Display indicadors
                echo "<ol class='carousel-indicators'>\n";
                foreach ($htmlImages as $key => $itemcolumn) {
                    $active = ($key == 1 ? 'active' : '');

                    $dspKey = (int)$key - 1;
                    echo "<li data-target='#peliculas_carousel_thumbnail' data-slide-to='" . $dspKey . "' class='carousel-item-indicator " . $active . "'></li>\n";
                }
                echo "</ol>\n";
            }

            // Display Carousel items
            echo "<div class='carousel-inner'>\n";
            foreach ($htmlImages as $key => $itemcolumn) {
                $active = ($key == 1 ? 'active' : '');

                echo "<div class='item " . $active . "'>\n";
                echo "<div class='row-fluid'>\n";
                echo $itemcolumn;
                echo "</div>\n"; // <!--/row-fluid-->
                echo "</div>\n"; // <!--/item-->
            }
            echo "</div>\n"; // <!--/carousel-inner-->

            if ($show_controls) {
                echo "<a class='left carousel-control' href='#peliculas_carousel_thumbnail' data-slide='prev'>‹</a>";
                echo "<a class='right carousel-control' href='#peliculas_carousel_thumbnail' data-slide='next'>›</a>";
            }

            echo '</div>';
            ?>
        </div>
    </div>
    </div></div>
    <?php
}