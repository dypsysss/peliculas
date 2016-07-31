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

jimport('joomla.application.component.view');

JLoader::register('PeliculasHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');

class PeliculasViewBase extends JViewLegacy
{
    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root().'media/com_peliculas/css/admin.peliculas.css');
        // $document->addScript(JURI::root().'media/com_peliculas/js/admin.peliculas.js');

        $this->sidebar = JHtmlSidebar::render();

        parent::display($tpl);
    }
}