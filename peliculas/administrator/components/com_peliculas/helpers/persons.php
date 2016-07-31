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
class PersonsHelper
{
    protected static $_items = array();

    public static function getPersonByTmdbID($Id) {
        $item       = null;
        $app        = JFactory::getApplication();

        if (empty(self::$_items[$Id])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_peliculas/tables');
            $tbl = JTable::getInstance('Persons', 'PeliculasTable');
            $result = $tbl->load(array('themoviedb_id' => $Id));
            if (!$result) {
                $item = self::processTheMovieDB(0, $Id);

                if ($item) {
                    self::$_items[$Id] = $item;
                }

            } else {
                self::$_items[$Id] = $tbl;
            }
        }
        return self::$_items[$Id];
    }

    public static function processTheMovieDB($itemTblID, $themoviedbID) {
        $table = JTable::getInstance('Persons', 'PeliculasTable');

        if ($itemTblID > 0)  {
            // Attempt to load the row.
            $return = $table->load($itemTblID);

            // Check for a table object error.
            if ($return === false && $table->getError()) {
                JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
                return false;
            }
        }

        $params = JComponentHelper::getParams('com_peliculas');
        $apikey = $params->get('themoviedb_api_key');

        $tmdb = new TMDB($apikey, 'es', false);
        $iPerson = $tmdb->getPerson($themoviedbID);

        $table->id              = $itemTblID;
        $table->name            = $iPerson->getName();
        $table->biography       = $iPerson->get("biography");
        $table->birthday        = $iPerson->get("birthday");
        $table->deathday        = $iPerson->get("deathday");
        $table->homepage        = $iPerson->get("homepage");
        $table->place_of_birth  = $iPerson->get("place_of_birth");
        $table->themoviedb_id   = $themoviedbID;
        $table->published       = 1;

        // 	Make sure the data is valid
        if (!$table->check()) {
            JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
            return false;
        }

        if (!$table->store()) {
            JFactory::getApplication()->enqueueMessage($table->getError(), 'error');
            return false;
        }

        $data = array();
        if (!empty($iPerson->get("profile_path"))) {
            $data['poster_path'] = $tmdb->getImageURL("original");
            $data['poster_image'] = $iPerson->get("profile_path");
            if (substr($data['poster_image'],0,1) == "/" ) {
                $newImage = substr($data['poster_image'], 1);
                $data['poster_image'] = $newImage;
            }
            self::processImage($table, $data);
        }

        return $table;
    }

    public static function processImage($item, $data = array())
    {
        $params = JComponentHelper::getParams('com_peliculas');

        $localpath = 'images' . DS . 'peliculas' . DS . 'persons';
        $baseUploadDir = JPATH_ROOT . DS . $localpath;

        if (!JFolder::exists($baseUploadDir)) {
            JFolder::create($baseUploadDir);
        }

        $uploadDir = $baseUploadDir . DS . $item->id;
        $localpath = $localpath . DS . $item->id;

        if (!JFolder::exists($uploadDir)) {
            JFolder::create($uploadDir);
        }

        $upload = PeliculasFile::getInstance();
        $upload->setFolder($uploadDir);

        if (!empty($data['poster_path'])) {
            $tmpname = $uploadDir . DS . $data['poster_image'];
            PersonsHelper::deleteImage($tmpname);
            $object_file = $upload->getImageCURL($data['poster_path'] . DS . $data['poster_image'], $tmpname);
            $object_file->newfilename = $data['poster_image'];
        } else {
            $object_file = $upload->upload('uploadimage');
        }

        if ($object_file) {
            if (file_exists(JPath::clean($object_file->filepath)))
            {
                $JImage = new JImage(JPath::clean($object_file->filepath));
                try {
                    $thumb = $JImage->resize(
                        $params->get('persons_thumb_min_width', 100),
                        $params->get('persons_thumb_min_height', 150),
                        true
                    );
                    $thumb->toFile($uploadDir . DS . 'thumb_' . $object_file->newfilename);
                } catch (Exception $e) {
                    JError::raiseWarning(100, JText::_('COM_PELICULAS_ERROR_TO_RESIZE_FILE'));

                    return false;
                }
            }

            $table = JTable::getInstance('Persons', 'PeliculasTable');
            $table->load($item->id);

            $oldImage = $table->image;

            $table->image = $object_file->newfilename;
            if (!$table->check()) {
                JFactory::getApplication()->enqueueMessage($table->getError(), 'warning');
                return false;
            }

            if (!$table->store()) {
                JFactory::getApplication()->enqueueMessage($table->getError(), 'warning');
                return false;
            }

            PersonsHelper::deleteAllImages($localpath , $oldImage);
        }

        return true;
    }
    
    public static function deleteAllImages($localpath, $filename, $deleteDir = false)
    {
        if (JFolder::exists(JPATH_ROOT .DS.  $localpath)) {
            @chmod(JPATH_ROOT .DS.  $localpath, 0777);
        }
        $original = JPATH_ROOT .DS.  $localpath . DS . $filename;
        PersonsHelper::deleteImage($original);

        $thumbs = JPATH_ROOT .DS. $localpath . DS . 'thumb_' . $filename;
        PersonsHelper::deleteImage($thumbs);

        if (JFolder::exists(JPATH_ROOT .DS.  $localpath) && $deleteDir) {
            JFolder::delete(JPATH_ROOT .DS.  $localpath);
        }
    }

    public static function deleteImage($name) {
        if (JFile::exists($name)) {
            JFile::delete($name);
        }
    }
}