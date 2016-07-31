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

// echo var_dump($this->item->events)."<hr/>";

if (!empty($this->item->events)) : ?>
<div class="row-fluid">
    <div class="span12">
        <?php
        $nitem    = 0;
        $actEvent = -1;
        $nColumns = 2;
        $nColGrid = 12/$nColumns;
        $ncont    = 0;

        foreach ($this->item->events as $item) {
            if ($item->id!=$actEvent) {
                $actEvent = $item->id;
                $ncont = 0;
            }

            if ($ncont==0) {
                if ($nitem>=1) {
                    echo "</div></div></div>";
                }
                echo '<div class="peliculas_block">';
                echo JLayoutHelper::render('events.cinema', array('item' => $item, 'params' => $this->params));
                echo '<div class="peliculas_block_content">';
            }

            if (($ncont % $nColumns)==0) {
                if ($ncont!=0) {
                    echo "</div>";
                }
                echo "<div class='row-fluid'>";
            }

            echo "<div class='span".$nColGrid."'>";
            echo JLayoutHelper::render('events.movie', array('item' => $item, 'params' => $this->params));
            echo "</div>";
            $ncont++;
            $nitem++;
        }
        echo "</div></div>";
        ?>
    </div>
</div>
<?php endif; ?>