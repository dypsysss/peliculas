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

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

$input  = JFactory::getApplication()->input;
$ajax   = $input->getBool('no_html');

if ($input->getCmd('task') == '') {
    $input->set('task', 'display');
}

$controller	= JControllerLegacy::getInstance('Peliculas');
$controller->execute($input->get('task'));
$controller->redirect();