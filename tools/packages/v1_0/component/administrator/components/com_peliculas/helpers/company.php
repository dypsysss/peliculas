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
class CompanyHelper
{
    protected static $_items = array();

    public static function getCompanyByTmdbID($Id) {
        $item       = null;
        $app        = JFactory::getApplication();

        if (empty(self::$_items[$Id])) {
            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_peliculas/tables');
            $tbl = JTable::getInstance('Companies', 'PeliculasTable');
            $result = $tbl->load(array('themoviedb_id' => $Id));
            if (!$result) {
                $item = JTable::getInstance('Companies', 'PeliculasTable');
                $paramsCfg = JComponentHelper::getParams('com_peliculas');
                $apikey = $paramsCfg->get('themoviedb_api_key');

                $tmdb = new TMDB($apikey, 'es', false);
                $iCompany = $tmdb->getCompany($Id);

                // $image = $tmdb->getImageURL("w185") . $tmdbMovie->get("logo_path");

                // No existe. hay que crear-lo.
                $item->id = 0;
                $item->name         = $iCompany->getName();
                $item->headquarters = $iCompany->get("headquarters");
                $item->homepage     = $iCompany->get("homepage");
                $item->themoviedb_id = $Id;
                $item->published = 1;

                // 	Make sure the data is valid
                if (!$item->check()) {
                    $bError = true;
                    $app->enqueueMessage($item->getError(), 'warning');
                }

                if (!$item->store()) {
                    $bError = true;
                    $app->enqueueMessage($item->getError(), 'warning');
                }

                $data = array();

                if (!empty($iCompany->get("logo_path"))) {
                    $data['poster_path'] = $tmdb->getImageURL("original");
                    $data['poster_image'] = $iCompany->get("logo_path");
                    if (substr($data['poster_image'], 0, 1) == "/") {
                        $newImage = substr($data['poster_image'], 1);
                        $data['poster_image'] = $newImage;
                    }

                    self::processImage($item, $data);
                }

            } else {
                $item = $tbl;
            }

            self::$_items[$Id] = $item;
        }

        return self::$_items[$Id];
    }

    public static function processImage($item, $data = array())
    {
        $params = JComponentHelper::getParams('com_peliculas');

        $localpath = 'images' . DS . 'peliculas' . DS . 'company';
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
            CompanyHelper::deleteImage($tmpname);
            $object_file = $upload->getImageCURL($data['poster_path'] . DS . $data['poster_image'], $tmpname);
            $object_file->newfilename = $data['poster_image'];
        } else {
            $object_file = $upload->upload('uploadimage');
        }

        if ($object_file) {
            /*
            if (file_exists(JPath::clean($object_file->filepath)))
            {
                $JImage = new JImage(JPath::clean($object_file->filepath));
                try {
                    // $JImageOri->toFile($uploadDir . DS . 'logo.jpg', IMAGETYPE_JPEG);
                    // $JImage = new JImage(JPath::clean($uploadDir . DS . 'logo.jpg'));
                    $thumb = $JImage->resize(
                        $params->get('company_thumb_min_width', 100),
                        $params->get('company_thumb_min_height', 150),
                        true
                    );
                    $thumb->toFile($uploadDir . DS . 'thumb_' . $object_file->newfilename);
                } catch (Exception $e) {
                    JError::raiseWarning(100, JText::_('COM_PELICULAS_ERROR_TO_RESIZE_FILE'));
                    return false;
                }
            }
            */

            $table = JTable::getInstance('Companies', 'PeliculasTable');
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

            CompanyHelper::deleteAllImages($localpath , $oldImage);
        }
        return true;
    }

    public static function deleteAllImages($localpath, $filename, $deleteDir = false)
    {
        if (JFolder::exists(JPATH_ROOT .DS.  $localpath)) {
            @chmod(JPATH_ROOT .DS.  $localpath, 0777);
        }
        $original = JPATH_ROOT .DS.  $localpath . DS . $filename;
        CompanyHelper::deleteImage($original);

        $thumbs = JPATH_ROOT .DS. $localpath . DS . 'thumb_' . $filename;
        CompanyHelper::deleteImage($thumbs);

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