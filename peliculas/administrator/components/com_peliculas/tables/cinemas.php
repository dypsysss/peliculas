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

JLoader::register('PeliculasTableBase', JPATH_COMPONENT_ADMINISTRATOR . '/tables/_base.php');

/**
 * Element table
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasTableCinemas extends PeliculasTableBase
{
    /**
     * Constructor
     *
     * @param   JDatabaseDriver  &$_db  Database connector object
     */
    public function __construct(&$_db)
    {
        parent::__construct('#__peliculas_cinemas', 'id', $_db);
    }
}

