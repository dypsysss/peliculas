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
?>
<div class="row-fluid">
    <?php if(!empty( $this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else: ?>
        <div id="j-main-container">
    <?php endif;?>
            <div class="peliculas_panel clearfix">
                <div class="cpanel-left">
                    <div class="cpanel">
                        <div class="icon">
                            <a href="index.php?option=com_peliculas&view=movies">
                                <img alt="<?php echo JText::_('COM_PELICULAS_SIDEBAR_MOVIES'); ?>" src="<?php echo JURI::root(); ?>media/com_peliculas/images/icon-48-films.png" />
                                <span><?php echo JText::_('COM_PELICULAS_SIDEBAR_MOVIES'); ?></span>
                            </a>
                        </div>

                        <div class="icon">
                            <a href="index.php?option=com_peliculas&view=genders">
                                <img alt="<?php echo JText::_('COM_PELICULAS_SIDEBAR_GENDERS'); ?>" src="<?php echo JURI::root(); ?>media/com_peliculas/images/icon-48-masks.png" />
                                <span><?php echo JText::_('COM_PELICULAS_SIDEBAR_GENDERS'); ?></span>
                            </a>
                        </div>

                        <div class="icon">
                            <a href="index.php?option=com_peliculas&view=persons">
                                <img alt="<?php echo JText::_('COM_PELICULAS_SIDEBAR_PERSONS'); ?>" src="<?php echo JURI::root(); ?>media/com_peliculas/images/icon-48-persons.png" />
                                <span><?php echo JText::_('COM_PELICULAS_SIDEBAR_PERSONS'); ?></span>
                            </a>
                        </div>

                        <div class="icon">
                            <a href="index.php?option=com_peliculas&view=companies">
                                <img alt="<?php echo JText::_('COM_PELICULAS_SIDEBAR_COMPANIES'); ?>" src="<?php echo JURI::root(); ?>media/com_peliculas/images/icon-48-companies.png" />
                                <span><?php echo JText::_('COM_PELICULAS_SIDEBAR_COMPANIES'); ?></span>
                            </a>
                        </div>

                        <div class="icon">
                            <a href="index.php?option=com_peliculas&view=cinemas">
                                <img alt="<?php echo JText::_('COM_PELICULAS_SIDEBAR_CINEMAS'); ?>" src="<?php echo JURI::root(); ?>media/com_peliculas/images/icon-48-cinema.png" />
                                <span><?php echo JText::_('COM_PELICULAS_SIDEBAR_CINEMAS'); ?></span>
                            </a>
                        </div>

                        <div class="icon">
                            <a href="index.php?option=com_peliculas&view=events">
                                <img alt="<?php echo JText::_('COM_PELICULAS_SIDEBAR_EVENTS'); ?>" src="<?php echo JURI::root(); ?>media/com_peliculas/images/icon-48-events.png" />
                                <span><?php echo JText::_('COM_PELICULAS_SIDEBAR_EVENTS'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="cpanel-right">
                    <div class="cpanel">
                        <div class="peliculas_panel_licence">
                            <div class="peliculas_licence_line"><?php echo JText::_('COM_PELICULAS_INSTALED_VER'); ?> <span><?php echo $this->getVersion();?></span></div>
                            <div class="peliculas_licence_line"><?php echo JText::_('COM_PELICULAS_AUTHOR'); ?> <a target="_blank" href="http://www.cesi.cat"><span>CESI</span></a></div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="peliculas_footer"></div>
    </div>
</div>                
            