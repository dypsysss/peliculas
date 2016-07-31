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

JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$listOrder  		= $this->escape($this->state->get('list.ordering'));
$listDirn   		= $this->escape($this->state->get('list.direction'));
?>

<?php if (empty($this->items)) : ?>
    <p><?php echo JText::_('COM_PELICULAS_NO_ITEMS'); ?></p>
<?php else : ?>
    <div class="peliculas_list_container">
        <form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="pelisForm" id="pelisForm" class="form-inline">
            <fieldset class="filters btn-toolbar clearfix">
                <?php if ($this->params->get('show_filter_field')) :?>
                    <div class="btn-group input-append">
                        <label class="filter-search-lbl" for="filter-search">
                            <?php echo JText::_('COM_PELICULAS_FILTER_LABEL') . '&#160;'; ?>
                        </label>
                        <input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('list.filter')); ?>" class="inputbox" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_PELICULAS_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_PELICULAS_FILTER_SEARCH_DESC'); ?>" />
                        <button title="" class="btn hasTooltip" type="submit" data-original-title="Buscar">
                            <span class="icon-search"></span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if ($this->params->get('show_pagination_limit')) : ?>
                    <div class="btn-group pull-right">
                        <label class="filter-search-lbl" for="limit">
                            <?php echo JText::_('JGLOBAL_DISPLAY_NUM') . '&#160;'; ?>
                        </label>
                        <?php echo $this->pagination->getLimitBox(); ?>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
                <input type="hidden" name="limitstart" value="" />
                <input type="hidden" name="task" value="" />
            </fieldset>

            <div class="content-fluid">
            <?php
            $nColumns = 2;
            $nColGrid = 12/$nColumns;
            $ncont = 0;

            foreach ($this->items as $item) {
                if (($ncont % $nColumns)==0) {
                    if ($ncont!=0) {
                        echo "</div>";
                    }
                    echo "<div class='row-fluid'>";
                }
                echo "<div class='span".$nColGrid."'>";
                $this->item = &$item;
                $layout = new JLayoutFile('movies.grid_item');
                echo $layout->render($this);
                echo "</div>";
                $ncont++;
            }
            echo "</div>";
            ?>
            </div>
            <?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
                <div class="pagination">
                    <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                        <p class="counter pull-right">
                            <?php echo $this->pagination->getPagesCounter(); ?>
                        </p>
                    <?php endif; ?>
                    <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
<?php endif; ?>