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
$input      = JFactory::getApplication()->input;
$field      = $input->getCmd('field');
$function   = 'jModalMovies_' . $field;
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$params     = (isset($this->state->params)) ? $this->state->params : new JObject;
$saveOrder	= $listOrder == 'a.ordering';
$eMovieID   = $input->getInt('emovieid' , -1);
?>
<form action="<?php echo JRoute::_('index.php?option=com_peliculas&view=movies&layout=modal&tmpl=component&emovieid='.$eMovieID.'&field='.$field . '&'.JSession::getFormToken().'=1'); ?>" method="post" name="adminForm" id="adminForm">
    <fieldset class="filter clearfix">
        <div class="btn-toolbar">
            <div class="btn-group pull-left">
                <label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_PELICULAS_SEARCH_IN_NAME'); ?>" data-placement="bottom"/>
            </div>
            <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" data-placement="bottom"><i class="icon-search"></i></button>
                <button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" data-placement="bottom" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
            </div>

            <div class="filters pull-right">
                <select name="filter_published" class="input-medium" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
                </select>
            </div>
            <div class="clearfix"></div>
        </div>

        <hr class="hr-condensed" />
    </fieldset>

    <?php if (empty($this->items)) : ?>
        <div class="alert alert-no-items">
            <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else : ?>
        <table class="table table-striped table-condensed">
            <thead>
                <tr>
                    <th width="5%" class="hidden-phone">
                    </th>

                    <th class="title">
                        <?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="center nowrap">
                        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="3">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center hidden-phone">
                        <?php if ($item->poster) : ?>
                            <?php echo "<img src='" . JUri::root() . 'images/peliculas/movie/' . $item->id . '/thumb_' . $item->poster . "' />";?>
                        <?php endif; ?>
                    </td>

                    <td class="nowrap has-context">
                        <div class="pull-left">
                            <a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $eMovieID;?>', '<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>' );">
                                <?php echo $this->escape($item->name); ?>
                            </a>
                            <?php if ($item->original_title) : ?>
                                <br/><span class="small">(<?php echo $this->escape($item->original_title); ?>)</span>
                            <?php endif; ?>

                            <div class="clearfix"></div>
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
                        </div>
                    </td>

                    <td align="center">
                        <?php echo (int) $item->id; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>