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
 * Peliculas Cinema view.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasViewEvent extends PeliculasViewBase
{
    protected $form;

    protected $item;

    protected $state;

    protected $canDo;

    /**
     * Display the view
     *
     * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @throws  InvalidArgumentException
     * @return  void
     */
    public function display($tpl = null)
    {
        $this->form		= $this->get('Form');
        $this->item		= $this->get('Item');
        $this->state	= $this->get('State');
        $this->canDo	= JHelperContent::getActions('com_peliculas');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new InvalidArgumentException(implode("\n", $errors));
            return false;
        }

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
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $user		= JFactory::getUser();
        $isNew		= ($this->item->id == 0);
        $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        $canDo		= $this->canDo;

        JToolbarHelper::title($isNew ? JText::_('COM_PELICULAS_MANAGER_EVENT_NEW') : JText::_('COM_PELICULAS_MANAGER_EVENT_EDIT'), 'bookmark peliculas-event');

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create'))) {
            JToolbarHelper::apply('event.apply');
            JToolbarHelper::save('event.save');
        }

        if (!$checkedOut && $canDo->get('core.create')) {
            JToolbarHelper::save2new('event.save2new');
        }

        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create')) {
            JToolbarHelper::save2copy('event.save2copy');
        }

        if (empty($this->item->id)) {
            JToolbarHelper::cancel('event.cancel');
        } else {
            JToolbarHelper::cancel('event.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}