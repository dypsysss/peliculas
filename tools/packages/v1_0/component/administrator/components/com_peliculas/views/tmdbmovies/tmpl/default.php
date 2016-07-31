<?php
/**
 * @version     13/04/15 12:33
 * @package     com_peliculas
 * @copyright	Copyright (C) 2005 - 2016 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');

$params     = (isset($this->state->params)) ? $this->state->params : new JObject;
?>
<form action="<?php echo JRoute::_('index.php?option=com_peliculas&view=tmdbmovies'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
        <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
        <?php if (empty($this->items)) : ?>
            <div class="alert alert-no-items">
                <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-striped" id="articleList">
                <thead>
                <tr>
                    <th class="nowrap hidden-phone" style="width: 100px;"></th>
                    <th>
                        <?php echo JText::_('COM_PELICULAS_HEADING_MOVIE_TITLE'); ?>
                    </th>
                    <th class="nowrap hidden-phone"></th>
                </tr>
                </thead>

                <tfoot>
                <tr>
                    <td colspan="8">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
                </tfoot>
                
                <tbody>
                <?php foreach ($this->items as $i => $item) :
                    ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="hidden-phone" style="width: 100px;">
                            <?php
                            if ($item->getPoster()!="") {
                                echo '<img src="' . $this->_TMDB->getImageURL('w92') . $item->getPoster() . '"/>';
                            } else {
                                echo '<img src="' . JUri::root() . 'media/com_peliculas/images/icon-person.png"/>';
                            }
                            ?>
                        </td>
                        <td class="has-context">
                            <div class="pull-left">
                                <strong><?php echo $this->escape($item->getTitle()); ?></strong>
                                <a href="https://www.themoviedb.org/movie/<?php echo $item->getID()."?language=es"; ?>" target="_blank">
                                    &nbsp;<?php echo JText::_('COM_PELICULAS_LINK_TMDB'); ?> <i class="icon-share"></i>
                                </a><br/>
                                <span class="small">(<?php echo $this->escape($item->get("original_title")); ?>)</span>
                                <div class="clearfix"></div>
                                <div class="break-word">
                                    <?php

                                    foreach($item->get("genre_ids") as $iGen) {
                                        $iGenero = GenderHelper::getGenderByTmdbID($iGen);
                                        echo "<span class='label'>".$iGenero->name."</span> ";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <br/>
                            <p><?php echo $item->get("overview"); ?></p>
                        </td>
                        <td>
                            <div class="nowrap pull-right">
                                <?php echo $this->escape($item->get("release_date")); ?> <i class="icon-calendar"></i>
                                <br/><?php echo $i;?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
