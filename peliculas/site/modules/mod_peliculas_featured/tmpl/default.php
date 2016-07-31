<?php
/**
 * @package     Peliculas
 * @copyright	Copyright (C) 2005 - 2016 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (count($list)>=1) {
    echo '<div class="peliculasfeatured' . $moduleclass_sfx . '">';

    $numColumns = (int) $params->get('columns', 2);
    $ncont      = 1;
    $ColumWidth = 100 / $numColumns;

    foreach ($list as $k=>$item) {

        $linkMovie = JRoute::_('index.php?option=com_peliculas&view=movie&id=' . $item->slug);
        // $linkMovie = substr($linkMovie, 0, strpos($linkMovie, '?'));

        echo "<div class='column' style='width:" . (int)$ColumWidth . "%;'>";

        $baseURL = JURI::root();
        $imgBaseURL  = $baseURL . 'images/peliculas/movie/' . $item->id;
        $src = $imgBaseURL . '/thumb_' . $item->poster;

        echo "<a href='" . $linkMovie . "' title='" . $item->name . "'>";
        echo "<img class='img-responsive' src='". $src ."' alt='".$item->name."'>";
        echo "</a>";
        echo "</div>";

        if (($ncont % $numColumns)==0) {
            echo "<div class='clearfix'></div>";
        }
        $ncont++;
    }
    echo '</div>';
}