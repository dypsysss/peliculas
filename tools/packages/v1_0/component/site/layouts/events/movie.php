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

$item   = $displayData['item'];
$params = $displayData['params'];

$linkMovie = JRoute::_('index.php?option=com_peliculas&view=movie&id=' . $item->movie_slug);
?>
<div class="peliculas_grid_item">
    <div class="grid_content">
        <div class="image_content">
            <a id="image_<?php print $item->movie_id; ?>" href="<?php print $linkMovie;?>" alt="<?php print $item->movie_name; ?>" title="<?php print $item->movie_name; ?>">
                <?php
                $baseURL = JURI::root();
                $imgBaseURL  = $baseURL . 'images/peliculas/movie/' . $item->movie_id;
                $src = $imgBaseURL . '/thumb_' . $item->poster;
                echo "<img class='img-responsive' src='". $src ."' alt='".$item->movie_name."'>";
                ?>
            </a>
        </div>

        <div class="info">
            <?php if ($params->get('show_list_name')) : ?>
                <div itemprop="name">
                    <a class="title" href="<?php print $linkMovie;?>" alt="<?php print $item->movie_name; ?>" title="<?php print $item->movie_name; ?>" itemprop="url">
                        <?php echo $item->movie_name;?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="horarios">
                <?php echo $item->informacion;?>
            </div>
        </div>

        <?php if ($params->get('show_readmore')) : ?>
            <div class="readmore">
                <hr/>
                <div class="pull-left">
                    <a href="<?php echo $linkMovie; ?>" itemprop="url">
                        <?php echo JText::_('COM_PELICULAS_READ_MORE');?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
