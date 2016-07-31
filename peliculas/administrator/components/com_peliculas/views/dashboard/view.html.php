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

require JPATH_COMPONENT_ADMINISTRATOR.'/views/_base.php';

/**
 * Peliculas Dashboard view.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasViewDashboard extends PeliculasViewBase
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
        PeliculasHelper::addSubmenu('dashboard');

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     */
    protected function addToolbar()
    {

        $canDo = JHelperContent::getActions('com_peliculas');

        JToolbarHelper::title(JText::_('COM_PELICULAS_TITLE_DASHBOARD'), 'peliculas-dashboard.png');

        if ($canDo->get('core.admin'))
        {
            JToolbarHelper::preferences('com_peliculas');
        }
    }

    public function getVersion() {
        $input 	= JFactory::getApplication()->input;
        $ext 	= $input->getString('option', '');

        $db 	= JFactory::getDBO();
        $query 	= "SELECT manifest_cache FROM #__extensions WHERE element ='".$ext."' AND type='component' ";
        $db->setQuery($query);

        $mc 	= json_decode($db->loadResult());
        $version = $mc->version;

        return $version;
    }
}