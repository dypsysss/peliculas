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
 * Gender list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasControllerEvent extends JControllerForm
{
    /**
     * @var     string  The prefix to use with controller messages.
     */
    protected $text_prefix = 'COM_PELICULAS_EVENT';

    /**
     * The URL view list variable.
     *
     * @var    string
     * @since  12.2
     */
    protected $view_list = 'events';
}