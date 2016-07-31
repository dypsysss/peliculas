<?php
/**
 * @version     8/07/16 2:17
 * @package     com_peliculas
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carless@cesigrup.com> - http://www.cesi.cat
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Base class for a Controller
 *
 * @abstract
 * @package     Joomla.Site
 * @subpackage  com_peliculas
 */
class PeliculasController extends JControllerLegacy
{
    /**
     * Method to display a view.
     *
     * @param   boolean     $cachable   If true, the view output will be cached
     * @param   array|bool  $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}
     *
     * @return  JController This object to support chaining.
     */
    public function display($cachable = false, $urlparams = false)
    {
        $view   = $this->input->get('view', 'dashboard');
        $layout = $this->input->get('layout', 'default');
        $id     = $this->input->getInt('id');

        JFactory::getApplication()->input->set('view', $view);

        parent::display();

        return $this;
    }
}