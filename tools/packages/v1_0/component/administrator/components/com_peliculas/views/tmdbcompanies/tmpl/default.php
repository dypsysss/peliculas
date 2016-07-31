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
<form action="<?php echo JRoute::_('index.php?option=com_peliculas&view=tmdbcompanies'); ?>" method="post" name="adminForm" id="adminForm">
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
                    <th width="5%" class="nowrap center hidden-phone"></th>
                    <th>
                        <?php echo JText::_('COM_PELICULAS_HEADING_COMPANY_TITLE'); ?>
                    </th>
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
                        <td class="center hidden-phone">
                            <?php
                            if ($item->get("logo_path")!="") {
                                echo '<img src="' . $this->_TMDB->getImageURL('w92') . $item->get("logo_path") . '"/>';
                            } else {
                                echo '<img src="' . JUri::root() . 'media/com_peliculas/images/icon-person.png"/>';
                            }
                            ?>
                        </td>
                        <td class="nowrap has-context">
                            <div class="pull-left break-word">
                                <strong><?php echo $this->escape($item->getName()); ?></strong>
                                <a href="https://www.themoviedb.org/company/<?php echo $item->getID()."?language=es"; ?>" target="_blank">
                                    &nbsp;<?php echo JText::_('COM_PELICULAS_LINK_TMDB'); ?> <i class="icon-share"></i>
                                </a>
                                <div class="clearfix"></div>
                                <?php // echo var_dump($item); ?>
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
