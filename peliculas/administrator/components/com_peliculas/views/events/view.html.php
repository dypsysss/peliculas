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

JLoader::register('PeliculasViewBase', JPATH_COMPONENT_ADMINISTRATOR . '/views/_base.php');

/**
 * Peliculas cines view.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasViewEvents extends PeliculasViewBase
{
    protected $items;

    protected $pagination;

    protected $state;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        PeliculasHelper::addSubmenu('events');
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

        JToolbarHelper::title(JText::_('COM_PELICULAS_MANAGER_EVENTS'), 'peliculas-events.png');

        if ($canDo->get('core.create'))
        {
            JToolbarHelper::addNew('event.add');
        }

        if ($canDo->get('core.edit'))
        {
            JToolbarHelper::editList('event.edit');
        }

        if ($canDo->get('core.edit.state'))
        {
            JToolbarHelper::publish('events.publish', 'JTOOLBAR_PUBLISH', true);
            JToolbarHelper::unpublish('events.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            JToolbarHelper::checkin('events.checkin');
        }

        if ($canDo->get('core.delete')) {
            JToolbarHelper::deleteList('', 'events.delete', 'JTOOLBAR_DELETE');
        }

        if ($canDo->get('core.admin'))
        {
            JToolbarHelper::preferences('com_peliculas');
        }
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return array(
            'a.published' => JText::_('JSTATUS'),
            'a.name' => JText::_('COM_JPELICULAS_HEADING_TYPE_TITLE'),
            'a.id' => JText::_('JGRID_HEADING_ID')
        );
    }
}