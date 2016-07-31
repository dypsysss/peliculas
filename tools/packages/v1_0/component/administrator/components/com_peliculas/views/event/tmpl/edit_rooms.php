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

$document->addScript(JURI::root().'media/com_peliculas/js/peliculas.admin.events.js');

$script = array();
$script[] = "(function($) {";
$script[] = "   jQuery(document).ready(function() {";
$script[] = "       Peliculas.eMovies = new Peliculas.eMovies.App({ selectorTable: 'eventsMoviesTable'});";
$script[] = "       SqueezeBox.initialize({});";
$script[] = "       SqueezeBox.assign(jQuery('a.modal_cinema').get(), {";
$script[] = "           parse: 'rel'";
$script[] = "       });";
$script[] = "   });";
$script[] = "})(jQuery);";


$document->addScriptDeclaration(implode("\n", $script));
?>
<table class="table table-striped" id="eventsMoviesTable">
    <thead>
        <tr>
            <th><?php echo JText::_('COM_PELICULAS_HEADING_ROOM_NAME'); ?></th>
            <th><?php echo JText::_('COM_PELICULAS_HEADING_MOVIE_TITLE'); ?></th>
            <th><?php echo JText::_('COM_PELICULAS_HEADING_HORARIO_TITLE'); ?></th>
            <th class="nowrap center" width="10%">Actions</th>
        </tr>
    </thead>

    <tbody id="eventsMoviesTableBody">
    <?php
    $pcont = 0;
    if (count($this->item->erooms)) {
        foreach($this->item->erooms as $item) {
            $pcont++;
            ?>
            <tr class="row<?php echo $pcont % 2; ?>" rel="<?php echo $pcont;?>">
                <td class="left">
                    <input type="hidden" name="jform[emovie][<?php echo $pcont;?>][id]" id="emovie_<?php echo $pcont;?>_id" value="<?php echo $item->id; ?>" />
                    <input type="hidden" name="jform[emovie][<?php echo $pcont;?>][deleted]" id="emovie_<?php echo $pcont;?>_deleted" value="" />

                    <div class="input-append">
                        <input type="text" readonly="" value="<?php echo $item->room_name;?>" id="dsp_event_<?php echo $pcont;?>_cinemaroom_id">
                        <a class="btn btn-primary modal_cinema" title="Seleccionar" href="index.php?option=com_peliculas&amp;view=cinemasrooms&amp;layout=modal&amp;tmpl=component&amp;field=modalCinemaRoomReturn&amp;filter_cinemaid=<?php echo $this->item->cinema_id;?>&amp;eventid=<?php echo $pcont;?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><i class="icon-search"></i></a>
                        <input type="hidden" id="event_<?php echo $pcont;?>_cinemaroom_id" name="jform[emovie][<?php echo $pcont;?>][cinemaroom_id]" value="<?php echo $item->room_id; ?>">
                    </div>
                </td>

                <td>
                    <div class="input-append">
                        <input type="text" readonly="" value="<?php echo $item->movie_name;?>" id="dsp_emovie_<?php echo $pcont;?>_movie_id">
                        <a class="btn btn-primary modal_cinema" title="Seleccionar" href="index.php?option=com_peliculas&amp;view=movies&amp;layout=modal&amp;tmpl=component&amp;field=modalMovieReturn&amp;emovieid=<?php echo $pcont;?>" rel="{handler: 'iframe', size: {x: 800, y: 500}}"><i class="icon-search"></i></a>
                        <input type="hidden" id="emovie_<?php echo $pcont;?>_movie_id" name="jform[emovie][<?php echo $pcont;?>][movie_id]" value="<?php echo $item->movie_id; ?>">
                    </div>
                </td>

                <td>
                    <textarea class="span12" name="jform[emovie][<?php echo $pcont;?>][info]" id="emovie_<?php echo $pcont;?>_info" rows="3" cols="30"><?php echo $item->informacion; ?></textarea>
                </td>

                <td>
                    <a class="btn eventMovieDelete" href="#">
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
            <td colspan="3"></td>
            <td>
                <a onclick="Peliculas.eMovies.appendItem();" id="btnEventMovieAdd" class="btn btn-small btn-success">
                    <span class="icon-apply icon-white"></span>
                    <?php echo JText::_('COM_PELICULAS_ACTION_ADD');?>
                </a>
            </td>
        </tr>
    </tfoot>
</table>