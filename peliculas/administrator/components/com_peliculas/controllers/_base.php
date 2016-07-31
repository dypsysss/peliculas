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

class PeliculasBaseController extends JControllerLegacy {

    /**
     * JDatabase object
     *
     * @access  protected
     * @var     object
     */
    var $_db;

    /**
     * Constructor
     *
     * @access  protected
     * @return  void
     * @since   3.1
     */
    function __construct($config = array())
    {
        parent::__construct($config);

        $this->_db        = JFactory::getDBO();
    }
}