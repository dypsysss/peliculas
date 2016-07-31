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
class PeliculasTableEvents extends PeliculasTableBase
{
    /**
     * Constructor
     *
     * @param   JDatabaseDriver  &$_db  Database connector object
     */
    public function __construct(&$_db)
    {
        parent::__construct('#__peliculas_events', 'id', $_db);
    }

    /**
     * Overloaded check function
     *
     * @throws UnexpectedValueException
     * @return  boolean  True on success, false on failure
     *
     * @see JTable::check
     */
    public function check()
    {
        $lreturn = parent::check();

        if (empty($this->publish_up))
        {
            $this->publish_up = $this->getDbo()->getNullDate();
        }

        if (empty($this->publish_down))
        {
            $this->publish_down = $this->getDbo()->getNullDate();
        }
        
        return $lreturn;
    }
}

