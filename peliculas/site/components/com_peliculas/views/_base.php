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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.application.component.view');

JLoader::register('PeliculasHelper', JPATH_SITE . '/components/com_peliculas/helpers/peliculashelper.php');

require_once JPATH_SITE . '/components/com_peliculas/helpers/route.php';

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

class PeliculasViewBase extends JViewLegacy
{
    function load_css() {
        PeliculasHelper::load_css();
    }

    function load_js() {
        JHtml::_('jquery.framework');
        JHtml::script(JURI::root() . 'media/com_peliculas/js/site.peliculas.all.js', true);
    }}