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

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_peliculas')) {
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JTable::addIncludePath( JPATH_COMPONENT_ADMINISTRATOR . '/tables');

$input      = JFactory::getApplication()->input;
$ajax       = $input->getBool('no_html', false);
$adminlang 	= JFactory::getLanguage();

/*
if ($input->getCmd('task') == '') {
    $input->set('task', 'dashboard.display');
}
*/

$controller	= JControllerLegacy::getInstance('peliculas');
$controller->execute($input->getCmd('task'));
$controller->redirect();

