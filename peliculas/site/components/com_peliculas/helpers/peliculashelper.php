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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class PeliculasHelper {

    public static function load_css() {
        $template   = JFactory::getApplication()->getTemplate();
        $doc        = JFactory::getDocument();

        // Create the template file name based on the layout
        $file = 'site.peliculas.min.css';

        if (JFile::exists(JPATH_THEMES .DIRECTORY_SEPARATOR. $template .DIRECTORY_SEPARATOR. 'css' .DIRECTORY_SEPARATOR. $file)) {
            $doc->addStyleSheet(JURI::root(true) . '/templates/' . $template . 'css/'. $file);
        } else {
            $doc->addStyleSheet(JURI::root(true) . '/media/com_peliculas/css/' . $file);
        }
    }
}