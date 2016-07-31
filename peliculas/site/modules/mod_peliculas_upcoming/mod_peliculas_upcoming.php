<?php
/**
 * @package     Peliculas
 * @copyright	Copyright (C) 2005 - 2016 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// Include the helper functions only once
require_once (dirname(__FILE__).'/helper.php');
JLoader::register('PeliculasHelper', JPATH_SITE . '/components/com_peliculas/helpers/peliculashelper.php');

// Load component language
JFactory::getLanguage()->load('com_peliculas', JPATH_BASE.'/components/com_peliculas');

// Get module data
$list = ModPeliculasUpcomingHelper::getList($params);

JHtml::_('jquery.framework');
PeliculasHelper::load_css();

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
require JModuleHelper::getLayoutPath('mod_peliculas_upcoming', $params->get('layout', 'default'));