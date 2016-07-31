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

JLoader::register('PeliculasViewBase', JPATH_COMPONENT . '/views/_base.php');
JLoader::register('PeliculasHelperEvents', JPATH_COMPONENT . '/helpers/events.php');

/**
 * View class for a list of elements.
 *
 * @package     Joomla.Site
 * @subpackage  com_peliculas
 */
class PeliculasViewMovie extends PeliculasViewBase
{
    protected $state;
    protected $item;
    protected $params;

    /**
     * Execute and display a template script.
     *
     * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
     * 
     * @return  mixed A string if successful, otherwise a Error object.
     * @throws  Exception
     */
    public function display($tpl = null)
    {
        $app    = JFactory::getApplication();
        $params = $app->getParams();
        $user   = JFactory::getUser();
        // Get some data from the models
        $this->state    = $this->get('State');
        $this->item     = $this->get('Item');
        $this->params   = $app->getParams('com_peliculas');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->item->events = PeliculasHelperEvents::getEventsMovies($this->item->id);

        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
        
        $this->load_css();
        $this->load_js();

        $model = $this->getModel();
        $model->hit();
        
        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument()
    {
        $app    = JFactory::getApplication();
        $menus  = $app->getMenu();
        $title  = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_PELICULAS_DEFAULT_PAGE_TITLE'));
        }
        $title = $this->item->name;
            // $this->params->get('page_title', '');
        if (empty($title)) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }
        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }
}