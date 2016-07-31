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

$params     = $displayData->params;
$item       = $displayData->item;
$nullDate   = JFactory::getDbo()->getNullDate();
$linkItem   = JRoute::_(PeliculasHelperRoute::getPeliculaRoute($item->slug));
?>
<div class="peliculas_grid_item">

    <div class="grid_content">
        <div class="image_content">
            <a id="image_<?php print $item->id; ?>" href="<?php print $linkItem;?>" alt="<?php print $item->name; ?>" title="<?php print $item->name; ?>">
                <?php
                $baseURL = JURI::root();
                $imgBaseURL  = $baseURL . 'images/peliculas/movie/' . $item->id;
                $src = $imgBaseURL . '/thumb_' . $item->poster;
                echo "<img class='img-responsive' src='". $src ."' alt='".$item->name."'>";
                ?>
            </a>
        </div>
        <div class="info">
            <?php if ($params->get('show_list_name')) : ?>
                <div itemprop="name">
                    <a class="title" href="<?php print $linkItem;?>" alt="<?php print $item->name; ?>" title="<?php print $item->name; ?>" itemprop="url">
                        <?php print $item->name; ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="break-word">
                <?php
                if ($item->gendersnames) {
                    $aGeneros = explode('|', $item->gendersnames);
                    if (count($aGeneros) >= 1) {
                        foreach ($aGeneros as $iGenero) {
                            $genero = explode(':', $iGenero);
                            echo "<span class='label'>" . $genero[1] . "</span>&nbsp;";
                        }
                    }
                }
                ?>
            </div>

            <div>
                <?php echo $item->description; ?>
            </div>
        </div>

        <?php if ($params->get('show_readmore') || $params->get('show_festreno')) : ?>
            <div class="readmore">
                <hr/>
                <?php if ($params->get('show_readmore')) : ?>
                    <div class="pull-left">
                        <a href="<?php echo $linkItem; ?>" itemprop="url">
                            <?php echo JText::_('COM_PELICULAS_READ_MORE');?>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if ($params->get('show_festreno')) : ?>
                    <div class="pull-right">
                        <?php
                        if ($item->f_estreno <> $nullDate) {
                            echo JHtml::_('date', $item->f_estreno, JText::_('DATE_FORMAT_LC4'));
                            echo '&nbsp;<i class="icon-calendar"></i>';
                        }?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
