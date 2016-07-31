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

jimport('joomla.application.component.controller');

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
        // Set the default view name and format from the Request.
        $vName = $this->input->get('view', 'movies');
        $this->input->set('view', $vName);

        $safeurlparams = array(
            'id'                => 'INT',
            'limit'             => 'UINT',
            'limitstart'        => 'UINT',
            'filter' 			=> 'STRING',
            'filter_order'      => 'CMD',
            'filter_order_Dir'  => 'CMD',
            'type_id'  			=> 'INT',
            'category_id'  		=> 'INT',
            'lang'              => 'CMD',
            'Itemid' 			=> 'INT'
        );

        parent::display($cachable, $safeurlparams);
    }
}