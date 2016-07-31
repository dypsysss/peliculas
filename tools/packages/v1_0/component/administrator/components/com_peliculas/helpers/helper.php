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

/**
 * Peliculas component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasHelper extends JHelperContent
{
    /**
     * Configure the Linkbar.
     *
     * @param   string $vName The name of the active view.
     *
     * @return  void
     */
    public static function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_PELICULAS_SIDEBAR_DASHBOARD'),
            'index.php?option=com_peliculas&view=dashboard',
            $vName == 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_PELICULAS_SIDEBAR_MOVIES'),
            'index.php?option=com_peliculas&view=movies',
            $vName == 'movies'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_PELICULAS_SIDEBAR_FEATURED'),
            'index.php?option=com_peliculas&view=featured',
            $vName == 'featured'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_PELICULAS_SIDEBAR_GENDERS'),
            'index.php?option=com_peliculas&view=genders',
            $vName == 'genders'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_PELICULAS_SIDEBAR_PERSONS'),
            'index.php?option=com_peliculas&view=persons',
            $vName == 'persons'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_PELICULAS_SIDEBAR_COMPANIES'),
            'index.php?option=com_peliculas&view=companies',
            $vName == 'companies'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_PELICULAS_SIDEBAR_CINEMAS'),
            'index.php?option=com_peliculas&view=cinemas',
            $vName == 'cinemas'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_PELICULAS_SIDEBAR_EVENTS'),
            'index.php?option=com_peliculas&view=events',
            $vName == 'events'
        );
    }
}