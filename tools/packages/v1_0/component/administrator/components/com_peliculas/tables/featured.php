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

/**
 * Featured table
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasTableFeatured extends JTable
{
    /**
     * Constructor
     *
     * @param   JDatabaseDriver  &$_db  Database connector object
     */
    public function __construct(&$_db)
    {
        parent::__construct('#__peliculas_featured', 'movie_id', $_db);
        $this->checked_out_time = $_db->getNullDate();
    }

    public function check()
    {
        //For new insertion
        if (empty($this->id)) {
            $this->ordering = $this->getNextOrder();
        }

        return true;
    }
}