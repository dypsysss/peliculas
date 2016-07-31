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

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Properties controller class.
 *
 * @package     Joomla.Site
 * @subpackage  com_peliculas
 */
class PeliculasControllerMovies extends PeliculasController
{
    protected $default_view = 'movies';

    /**
     * Proxy for getModel.
     */
    public function &getModel($name = 'Movies', $prefix = 'PeliculasModel')
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }
    
    public function search()
    {
        $app = JFactory::getApplication();
        $app->input->set('layout', 'default');
        $this->display();
    }
}