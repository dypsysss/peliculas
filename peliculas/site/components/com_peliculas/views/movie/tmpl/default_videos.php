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

$videos = array();
if (is_string($this->item->videos)) {
    $videos = (array) json_decode($this->item->videos);
} else {
    $tmpvideos = (array) $this->item->videos;
    foreach ($tmpvideos as $k => $video) {
        $videos[$k] = (object) $video;
    }
}

if (!empty($videos)) {

    $ncont      = 0;
    $columns    = 2;
    $colWidth   = 12 / $columns;
    $icolumn    = 0;

    echo "<div class='row-fluid'>";
    foreach ($videos as $k => $video) {
        if (($ncont % $columns) == 0) {
            if ($ncont>=1) {
                echo "</div>";
            }
            echo "<div class='row-fluid'>";
        }

        echo "<div class='span". $colWidth . "'>";
        ?>
        <div class="peliculas_block">
            <div class="peliculas_block_header">
                <span class="peliculas_header"><?php echo $video->vname;?></span>
            </div>

            <div class="peliculas_block_content">
                <iframe id="ytplayer_<?php echo $ncont?>" type="text/html" width="100%"
                        src="https://www.youtube.com/embed/<?php echo $video->vkey ?>?rel=0&showinfo=0&color=white&iv_load_policy=3"
                        frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
        <?php
        echo "</div>";

        $ncont++;
    }
    echo "</div>";
}