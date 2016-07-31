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
    echo '<div class="peliculasupcoming' . $moduleclass_sfx . '">';

    $numColumns = (int) $params->get('columns', 2);
    $numImgShow = (int) $params->get('show_num_images', 1);
    $ncont      = 1;
    $ColumWidth = 100 / $numColumns;
    $showul     = false;

    foreach ($list as $k=>$item) {

        $linkMovie = JRoute::_('index.php?option=com_peliculas&view=movie&id=' . $item->slug);

        if (($ncont - 1) >= $numImgShow) {
            if (!$showul) {
                echo "<ul>";
                $showul = true;
            }
            echo "<li>";
            echo "<a href='" . $linkMovie . "' title='" . $item->name . "'>";
            echo $item->name;
            echo "</a></li>";
        } else {
            echo "<div class='column' style='width:" . (int)$ColumWidth . "%;'>";

            $baseURL = JURI::root();
            $imgBaseURL = $baseURL . 'images/peliculas/movie/' . $item->id;
            $src = $imgBaseURL . '/thumb_' . $item->poster;

            echo "<a href='" . $linkMovie . "' title='" . $item->name . "'>";
            echo "<img class='img-responsive' src='" . $src . "' alt='" . $item->name . "'>";
            echo "</a>";
            echo "</div>";

            if ($numColumns >= 2) {
                if (($ncont % $numColumns) == 0) {
                    echo "<div class='clearfix'></div>";
                }
            }
        }
        $ncont++;
    }
    if ($showul) {
        echo "</ul>";
    }
    echo '</div>';
}