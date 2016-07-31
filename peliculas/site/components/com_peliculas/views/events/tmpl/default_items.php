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

JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$listOrder  		= $this->escape($this->state->get('list.ordering'));
$listDirn   		= $this->escape($this->state->get('list.direction'));
?>

<?php if (empty($this->items)) : ?>
    <p><?php echo JText::_('COM_PELICULAS_NO_ITEMS'); ?></p>
<?php else : ?>
    <div class="peliculas_list_container">
        <div class="content-fluid">
        <?php
        $actEvent = -1;
        $nColumns = 2;
        $nColGrid = 12/$nColumns;
        $ncont = 0;
        $nitem = 0;
        foreach ($this->items as $item) {

            if ($item->id!=$actEvent) {
                $actEvent = $item->id;
                $ncont = 0;
            }

            $this->item = $item;

            if ($ncont==0) {
                if ($nitem!=0) {
                    echo '</div>';
                    echo "<div class='row-fluid'>";
                }
                echo JLayoutHelper::render('events.item', array('item' => $this->item, 'params' => $this->params));
            }

            if (($ncont % $nColumns)==0) {
                if ($ncont!=0) {
                    echo "</div>";
                }
                echo "<div class='row-fluid'>";
            }

            echo "<div class='span".$nColGrid."'>";
            echo JLayoutHelper::render('events.movie', array('item' => $this->item, 'params' => $this->params));
            // $this->item = &$item;
            // $layout = new JLayoutFile('events.movie');
            // echo $layout->render($this);
            echo "</div>";

            /*
            echo "event:" . $item->id . "-" . $item->name;
            echo " sala:" . $item->room_name;
            echo " movie:" . $item->movie_name;
            echo "<br/>";
            */

            $ncont++;
            $nitem++;
        }
        ?>
        </div>
    </div>
<?php endif; ?>
