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

$input     = JFactory::getApplication()->input;
$field     = $input->getCmd('field');
$function  = 'jSelectPerson_' . $field;
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_peliculas&view=tmdbpersons&layout=modal&tmpl=component');?>" method="post" name="adminForm" id="adminForm">
    <fieldset class="filter">
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_PELICULAS_SEARCH_IN_NAME'); ?>" data-placement="bottom"/>
            </div>
            <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom"><i class="icon-search"></i></button>
                <button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
            </div>
        </div>
    </fieldset>

    <table class="table table-striped" id="articleList">
        <thead>
        <tr>
            <th width="5%" class="nowrap center hidden-phone">
            </th>
            <th>
                <?php echo JText::_('COM_PELICULAS_HEADING_PERSON_TITLE'); ?>
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
        <?php
        if (count($this->items)) {
            foreach ($this->items as $i => $item) :
                ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center hidden-phone">
                        <?php
                        if ($item->getProfile()!="") {
                            echo '<img src="' . $this->_TMDB->getImageURL('w92') . $item->getProfile() . '"/>';
                        } else {
                            echo '<img src="' . JUri::root() . 'media/com_peliculas/images/icon-person.png"/>';
                        }
                        ?>
                    </td>
                    <td class="has-context">
                        <div class="pull-left break-word">
                            <strong>
                                <a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->getID(); ?>', '<?php echo $this->escape(addslashes($item->getName())); ?>' );">
                                    <?php echo $this->escape($item->getName()); ?>
                                </a>
                            </strong>
                            <a href="https://www.themoviedb.org/person/<?php echo $item->getID()."?language=es"; ?>" target="_blank">
                                &nbsp;<?php echo JText::_('COM_PELICULAS_LINK_TMDB'); ?> <i class="icon-share"></i>
                            </a>
                            <?php // echo $item->get('popularity'); ?>
                            <div class="clearfix"></div>
                            <?php
                            $conocidopor = $item->get('known_for');
                            if (isset($conocidopor)) {
                                if (is_array($conocidopor)) {
                                    foreach ($conocidopor as $iPeli) {
                                        if (isset($iPeli['title'])) {
                                            echo $iPeli['title'] . ", ";
                                        }
                                    }
                                }
                            }
                            ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach;
        }?>
        </tbody>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>