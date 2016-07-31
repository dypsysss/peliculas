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
 * Script file of Peliculas component
 */
class com_PeliculasInstallerScript
{
    /**
     * method to install the component
     *
     * @return void
     */
    function install($parent)
    {
        // $parent is the class calling this method
        $parent->getParent()->setRedirectURL('index.php?option=com_peliculas');
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent)
    {
        echo '<p>' . JText::_('COM_PELICULAS_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent)
    {
        // $parent is the class calling this method
        echo '<p>' . JText::sprintf('COM_PELICULAS_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        echo '<p>' . JText::_('COM_PELICULAS_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        echo '<p>' . JText::_('COM_PELICULAS_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
    }
}