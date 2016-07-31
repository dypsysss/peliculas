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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_peliculas.movies');
$params     = (isset($this->state->params)) ? $this->state->params : new JObject;
$saveOrder	= $listOrder == 'fp.ordering';
?>

<form action="<?php echo JRoute::_('index.php?option=com_peliculas&view=featured'); ?>" method="post" name="adminForm" id="adminForm">
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
						<th width="1%" class="hidden-phone">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th width="5%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('searchtools.sort', 'COM_PELICULAS_HEADING_MOVIE_TITLE', 'a.name', $listDirn, $listOrder); ?>
						</th>
						<th width="10%" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ORDERING', 'fp.ordering', $listDirn, $listOrder); ?>
							<?php if ($canOrder && $saveOrder) :?>
								<?php echo JHtml::_('grid.order', $this->items, 'filesave.png', 'featured.saveorder'); ?>
							<?php endif; ?>
						</th>

						<th width="10%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
						</th>

						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
						</th>

						<th width="1%" class="nowrap hidden-phone">
							<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>

				<tbody>
				<?php $count = count($this->items); ?>
				<?php foreach ($this->items as $i => $item) :
					$item->max_ordering = 0;
					$ordering	= ($listOrder == 'fp.ordering');
					$canCreate  = $user->authorise('core.create',     'com_peliculas');
					$canEdit    = $user->authorise('core.edit',       'com_peliculas');
					$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
					$canChange  = $user->authorise('core.edit.state', 'com_peliculas') && $canCheckin;
					?>
					<tr class="row<?php echo $i % 2; ?>">

						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>

						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'movies.', $canChange); ?>
								<?php echo JHtml::_('featured.featured', $item->featured, $i, $canChange); ?>
							</div>
						</td>

						<td class="nowrap has-context">
							<div class="pull-left break-word">
								<?php if ($item->checked_out) : ?>
									<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'inmuebles.', $canCheckin); ?>
								<?php endif; ?>
								<?php if ($canEdit) : ?>
									<a href="<?php echo JRoute::_('index.php?option=com_peliculas&task=movie.edit&return=featured&id=' . (int) $item->id); ?>">
										<?php echo $this->escape($item->name); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->name); ?>
								<?php endif; ?>
								<?php if ($item->original_title) : ?>
									<br/><span class="small">(<?php echo $this->escape($item->original_title); ?>)</span>
								<?php endif; ?>
							</div>
						</td>
						<td class="order">
							<?php if ($canChange && $saveOrder) : ?>
								<div class="input-prepend">
									<?php if ($listDirn == 'ASC') : ?>
										<span class="add-on"><?php echo $this->pagination->orderUpIcon($i, true, 'featured.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
										<span class="add-on"><?php echo $this->pagination->orderDownIcon($i, $count, true, 'featured.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
									<?php elseif ($listDirn == 'DESC') : ?>
										<span class="add-on"><?php echo $this->pagination->orderUpIcon($i, true, 'featured.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
										<span class="add-on"><?php echo $this->pagination->orderDownIcon($i, $count, true, 'featured.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
									<?php endif; ?>
									<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order" />
								</div>
							<?php else : ?>
								<?php echo $item->ordering; ?>
							<?php endif; ?>
						</td>

						<td class="small hidden-phone">
							<?php echo $this->escape($item->access_level); ?>
						</td>

						<td class="center hidden-phone">
							<?php echo (int) $item->hits; ?>
						</td>

						<td class="center hidden-phone">
							<?php echo $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="featured" value="1" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>