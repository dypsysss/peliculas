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
class PeliculasTableMovies extends PeliculasTableBase
{
    /**
     * Constructor
     *
     * @param   JDatabaseDriver  &$_db  Database connector object
     */
    public function __construct(&$_db)
    {
        parent::__construct('#__peliculas_movies', 'id', $_db);
    }

    public function checkUniqueTmdb() {

        if (property_exists($this, 'themoviedb_id') && $this->themoviedb_id>=1) {
            $table = JTable::getInstance('Movies', 'PeliculasTable');

            if ($table->load(array('themoviedb_id' => $this->themoviedb_id)) && ($table->id != $this->id || $this->id == 0))
            {
                $this->setError(JText::_('COM_PELICULAS_WARNING_UNIQUE_TMDBID'));
                return false;
            }
        }

        return true;
    }

    public function bind($array, $ignore = '')
    {
        if (!isset($array['videos'])) {
            $array['videos'] = json_encode(array());
        } else {
            if (isset($array['videos']) && is_array($array['videos'])) {
                $videos = array();
                foreach ($array['videos'] as &$video) {
                    $videos[] = (object)$video;
                }
                $array['videos'] = json_encode($videos);
            }
        }

        if (!isset($array['imagenes'])) {
            $array['imagenes'] = json_encode(array());
        } else {
            if (isset($array['imagenes']) && is_array($array['imagenes'])) {
                $images = array();
                foreach ($array['imagenes'] as &$image) {
                    $images[] = (object)$image;
                }
                $array['imagenes'] = json_encode($images);
            }
        }

        return parent::bind($array, $ignore);
    }
}

