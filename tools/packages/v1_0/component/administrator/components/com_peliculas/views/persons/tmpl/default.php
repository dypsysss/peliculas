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
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$params     = (isset($this->state->params)) ? $this->state->params : new JObject;
$saveOrder	= $listOrder == 'a.ordering';

if ($saveOrder) {
    $saveOrderingUrl = 'index.php?option=com_peliculas&task=persons.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
} else {
    // echo "ordering ".$listOrder."<br/>";
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
    Joomla.orderTable = function()
    {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>
<form action="<?php echo JRoute::_('index.php?option=com_peliculas&view=persons'); ?>" method="post" name="adminForm" id="adminForm">
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
        <?php
        // Search tools bar
        echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
        ?>
        <?php if (empty($this->items)) : ?>
            <div class="alert alert-no-items">
                <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-striped" id="articleList">
                <thead>
                <tr>
                    <th width="1%" class="nowrap center hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
                    </th>
                    <th width="1%" class="hidden-phone">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </th>
                    <th width="5%" class="nowrap center">
                        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                    </th>

                    <th width="5%" class="hidden-phone"></th>
                    <th>
                        <?php echo JHtml::_('searchtools.sort', 'COM_PELICULAS_HEADING_PERSON_TITLE', 'a.name', $listDirn, $listOrder); ?>
                    </th>

                    <th width="10%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap hidden-phone">
                        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
                    $ordering   = ($listOrder == 'ordering');
                    $canCreate  = $user->authorise('core.create',     'com_peliculas');
                    $canEdit    = $user->authorise('core.edit',       'com_peliculas');
                    $canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
                    $canChange  = $user->authorise('core.edit.state', 'com_peliculas') && $canCheckin;
                    ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="order nowrap center hidden-phone">
                            <?php
                            $iconClass = '';
                            if (!$canChange) {
                                $iconClass = ' inactive';
                            } elseif (!$saveOrder) {
                                $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                            }
                            ?>
                            <span class="sortable-handler <?php echo $iconClass ?>">
                                <i class="icon-menu"></i>
                            </span>
                            <?php if ($canChange && $saveOrder) : ?>
                                <input type="text" style="display:none" name="order[]" size="5"
                                       value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
                            <?php endif; ?>
                        </td>

                        <td class="center hidden-phone">
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>

                        <td class="center">
                            <div class="btn-group">
                                <?php echo JHtml::_('jgrid.published', $item->published, $i, 'persons.', $canChange); ?>
                            </div>
                        </td>

                        <td class="center">
                            <?php if ($item->image) : ?>
                                <?php echo "<img src='" . JUri::root() . 'images/peliculas/persons/' . $item->id . '/thumb_' . $item->image . "' />";?>
                            <?php endif; ?>
                        </td>

                        <td class="nowrap has-context">
                            <div class="pull-left">
                                <?php if ($item->checked_out) : ?>
                                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'persons.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php if ($canEdit) : ?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_peliculas&task=person.edit&id=' . (int) $item->id); ?>">
                                        <?php echo $this->escape($item->name); ?></a>
                                <?php else : ?>
                                    <?php echo $this->escape($item->name); ?>
                                <?php endif; ?>

                                <?php if ($item->birthday) : ?>
                                    <br/><span class="small"><?php echo JText::_('COM_PELICULAS_FIELD_PERSON_BIRTHDAY_LABEL'); ?><?php echo $this->escape($item->birthday); ?></span>
                                <?php endif; ?>

                                <?php if ($item->place_of_birth) : ?>
                                    <br/><span class="small"><?php echo JText::_('COM_PELICULAS_FIELD_PERSON_PLACEOFBIRTH_LABEL'); ?><?php echo $this->escape($item->place_of_birth); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <td class="small hidden-phone">
                            <?php echo $this->escape($item->access_level); ?>
                        </td>

                        <td class="center hidden-phone">
                            <?php echo $item->id; ?>
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