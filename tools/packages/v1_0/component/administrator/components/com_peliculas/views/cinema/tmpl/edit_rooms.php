<?php
/**
 * @version     13/04/15 16:05
 * @package     com_peliculas
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();

$document->addScript(JURI::root().'media/com_peliculas/js/peliculas.admin.rooms.js');

$script = array();
$script[] = "(function($) {";
$script[] = "   jQuery(document).ready(function() {";

$script[] = "       Peliculas.rooms = new Peliculas.rooms.App({ selectorTable: 'cinemaRoomsTable'});";

// $script[] = "       jQuery('#btnRoomAdd').bind('click', AddRoom);";
// $script[] = "       jQuery('.cinemaRoomDelete').bind('click', DeleteRoom);";

$script[] = "   });";
$script[] = "})(jQuery);";
/*
$script[] = "function AddRoom(){";
$script[] = "   var count = jQuery('#cinemaRoomsTableBody').children('tr').length;";
$script[] = "   count = count + 1;";
$script[] = "   jQuery('#cinemaRoomsTableBody').append(";
$script[] = "   \"<tr rel='\"+ count +\"'>\"+";
$script[] = "   \"<td>\" + ";
$script[] = "        \"<input type='hidden' name='jform[cinemaroom][\"+ count +\"][id]' value='-1' />\" + ";
$script[] = "        \"<input type='hidden' name='jform[cinemaroom][\"+ count +\"][deleted]' value='' />\" + ";
$script[] = "        \"<input type='text' name='jform[cinemaroom][\"+ count +\"][name]' value='' class='inputbox'/>\" + ";
$script[] = "   \"</td>\" + ";
$script[] = "   \"<td>\" + ";
$script[] = "        \"<a class='btn cinemaRoomDelete' href='#'><span class='icon-delete'></span>" . JText::_('COM_PELICULAS_ACTION_DELETE') . "</a>\" +";
$script[] = "   \"</td>\" + ";
$script[] = "   \"</tr>\");";
$script[] = "};";

$script[] = "function DeleteRoom(e){";
$script[] = "   var event = e || window.event;";
$script[] = "   if (event.stop) {";
$script[] = "       event.stop();";
$script[] = "   }";
$script[] = "   event.preventDefault();";
$script[] = "   var row = event.target.getParent().getParent();";
$script[] = "   var num = event.target.getParent().getParent().getAttribute('rel');";

$script[] = "   var IDDelete     = 'CinemaRoom_' + num + '_deleted';";

$script[] = "   if (jQuery(\"#\" + IDDelete)) {";
$script[] = "       jQuery(\"#\" + IDDelete).val(1);";
$script[] = "   }";
$script[] = "   row.setStyle('display', 'none');";

$script[] = "};";
*/

$document->addScriptDeclaration(implode("\n", $script));
?>
<table class="table table-striped" id="cinemaRoomsTable">
    <thead>
        <tr>
            <th><?php echo JText::_('COM_PELICULAS_HEADING_ROOM_NAME'); ?></th>
            <th class="nowrap center" width="10%">Actions</th>
        </tr>
    </thead>

    <tbody id="cinemaRoomsTableBody">
    <?php
    $pcont = 0;
    if (count($this->item->rooms)) {
        foreach($this->item->rooms as $room) {
            $pcont++;
            ?>
            <tr class="row<?php echo $pcont % 2; ?>" rel="<?php echo $pcont;?>">
                <td class="left">
                    <input type="hidden" name="jform[cinemaroom][<?php echo $pcont;?>][id]" id="CinemaRoom_<?php echo $pcont;?>_id" value="<?php echo $room->id; ?>" />
                    <input type="hidden" name="jform[cinemaroom][<?php echo $pcont;?>][deleted]" id="CinemaRoom_<?php echo $pcont;?>_deleted" value="" />

                    <input type="text" name="jform[cinemaroom][<?php echo $pcont;?>][name]" id="CinemaRoom_<?php echo $pcont;?>_name" value="<?php echo $room->name; ?>" class="inputbox" />
                </td>

                <td>
                    <a class="btn cinemaRoomDelete" href="#">
                        <span class="icon-delete"></span>
                        <?php echo JText::_('COM_PELICULAS_ACTION_DELETE');?>
                    </a>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>

    <tfoot>
        <tr>
            <td></td>
            <td>
                <a onclick="Peliculas.rooms.appendItem();" id="btnRoomAdd" class="btn btn-small btn-success">
                    <span class="icon-apply icon-white"></span>
                    <?php echo JText::_('COM_PELICULAS_ACTION_ADD');?>
                </a>
            </td>
        </tr>
    </tfoot>
</table>
