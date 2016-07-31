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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

JLoader::register('PeliculasFile', JPATH_COMPONENT_ADMINISTRATOR . '/libs/file.php');

/**
 * Peliculas component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class GenderHelper
{
    protected static $_generos = array();
    protected static $_generosID = array();

    public static function getGenderByID($Id) {
        
        if (empty(self::$_generosID[$Id])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_peliculas/tables');
            $tbl = JTable::getInstance('genders', 'PeliculasTable');
            $result = $tbl->load(array('id' => $Id));
            if (!$result) {
                return false;
            } else {
                $item = $tbl;
            }
            self::$_generosID[$Id] = $item;
        }
        return self::$_generosID[$Id];
    }

    public static function getGenderByTmdbID($Id) {
        $item   = null;
        $bError = false;
        $app    = JFactory::getApplication();

        if (empty(self::$_generos[$Id])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_peliculas/tables');
            $tbl = JTable::getInstance('genders', 'PeliculasTable');
            $result = $tbl->load(array('themoviedb_id' => $Id));
            if (!$result) {
                $item = JTable::getInstance('genders', 'PeliculasTable');
                $paramsCfg = JComponentHelper::getParams('com_peliculas');
                $apikey = $paramsCfg->get('themoviedb_api_key');

                $tmdb = new TMDB($apikey, 'es', false);
                $iGenre = $tmdb->getGenre($Id);

                // No existe. hay que crear-lo.
                $item->id            = 0;
                $item->name          = $iGenre->getName();
                $item->themoviedb_id = $Id;
                $item->published     = 1;

                // 	Make sure the data is valid
                if (!$item->check()) {
                    $bError = true;
                    $app->enqueueMessage($item->getError(), 'warning');
                }

                if (!$item->store()) {
                    $bError = true;
                    $app->enqueueMessage($item->getError(), 'warning');
                }
            } else {
                $item = $tbl;
            }
            self::$_generos[$Id] = $item;
        }
        return self::$_generos[$Id];
    }
}