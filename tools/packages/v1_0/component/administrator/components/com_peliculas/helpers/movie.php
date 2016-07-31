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

use Joomla\Utilities\ArrayHelper;

/**
 * Peliculas component helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class MovieHelper
{
    /**
     * Method to get a list of genders for a given item.
     * Normally used for displaying a list of genders within a layout
     *
     * @param   mixed   $ids     The id or array of ids (primary key) of the movie.
     *
     * @return  string  Comma separated list of genders Ids.
     */
    public function getGenders($ids)
    {
        if (empty($ids)) {
            return;
        }

        /**
         * Ids possible formats:
         * ---------------------
         * 	$id = 1;
         *  $id = array(1,2);
         *  $id = array('1,3,4,19');
         *  $id = '1,3';
         */
        $ids = (array) $ids;
        $ids = implode(',', $ids);
        $ids = explode(',', $ids);

        // Sanitize input.
        $ids    = ArrayHelper::toInteger($ids);

        $db = JFactory::getDbo();

        // Load the tags.
        $query = $db->getQuery(true)
            ->select($db->quoteName('g.id'))
            ->from($db->quoteName('#__peliculas_genders') . ' AS g ')
            ->join(
                'INNER', $db->quoteName('#__peliculas_movies_gender') . ' AS m'
                . ' ON ' . $db->quoteName('m.gender_id') . ' = ' . $db->quoteName('g.id')
                . ' AND ' . $db->quoteName('m.movie_id') . ' IN ( ' . implode(',', $ids) . ')'
            );

        $db->setQuery($query);

        $gendersList = $db->loadColumn();
        $listGenders = implode(',', $gendersList);

        return $listGenders;
    }

    /**
     * Method to add gender rows to mapping table.
     *
     * @param   integer     $movieId        ID of the movie item
     * @param   array       $genders        Array of genders to be applied.
     *
     * @return  boolean  true on success, otherwise false.
     */
    public function addGenderMapping($movieId, $genders = array())
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->insert('#__peliculas_movies_gender');
        $query->columns(array($db->quoteName('movie_id'), $db->quoteName('gender_id')));

        if (count($genders)>=1) {
            foreach ($genders as $gender) {
                $query->values((int)$movieId . ', ' . (int)$gender);
            }
            // echo nl2br(str_replace('#__', 'jos_', $query));
            // die();
            $db->setQuery($query);

            return (boolean)$db->execute();
        } else {
            return false;
        }
    }

    /**
     * Method to delete all genders of movie
     *
     * @param   integer $movieId        ID of the movie item
     * @param   array   $genders        Array of genders to be deleted. Use an empty array to delete all existing categories.
     *
     * @return  boolean  true on success, otherwise false.
     */
    public function deleteGender($movieId, $genders = array())
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete('#__peliculas_movies_gender')
            ->where($db->quoteName('movie_id') . ' = ' . (int) $movieId);

        if (is_array($genders) && count($genders) > 0) {
            $genders    = ArrayHelper::toInteger($genders);
            $query->where($db->quoteName('gender_id') . ' IN ' . implode(',', $genders));
        }
        $db->setQuery($query);
        return (boolean) $db->execute();
    }
    
    public static function processImage($item, $data = array())
    {
        $params = JComponentHelper::getParams('com_peliculas');

        $localpath = 'images' . DS . 'peliculas' . DS . 'movie';
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
            MovieHelper::deleteImage($tmpname);
            $object_file = $upload->getImageCURL($data['poster_path'] . DS . $data['poster_image'], $tmpname);
            $object_file->newfilename = $data['poster_image'];
                // 'cartel.' . JFile::getExt($data['poster_image']);
        } else {
            $object_file = $upload->upload('uploadimage');
        }

        if ($object_file) {

            if (file_exists(JPath::clean($object_file->filepath)))
            {
                // Cambiar nombre de la imagen
                $newImageName =  'cartel.' . JFile::getExt($object_file->newfilename);
                MovieHelper::deleteImage($uploadDir .DS. $newImageName);
                JFile::copy($object_file->filepath, $uploadDir .DS. $newImageName);
                MovieHelper::deleteImage($object_file->filepath);
                $object_file->newfilename = $newImageName;
                $object_file->filepath = $uploadDir .DS. $newImageName;

                $JImage = new JImage(JPath::clean($object_file->filepath));
                try {
                    $thumb = $JImage->resize(
                        $params->get('poster_thumb_min_width', 100),
                        $params->get('poster_thumb_min_height', 143),
                        true
                    );
                    $thumb->toFile($uploadDir . DS . 'thumb_' . $object_file->newfilename);
                } catch (Exception $e) {
                    JError::raiseWarning(100, JText::_('COM_PELICULAS_ERROR_TO_RESIZE_FILE'));
                    return false;
                }
            }

            $table = JTable::getInstance('Movies', 'PeliculasTable');
            $table->load($item->id);

            $oldImage = $table->poster;

            $table->poster = $object_file->newfilename;

            if (!$table->check()) {
                JFactory::getApplication()->enqueueMessage($table->getError(), 'warning');
                return false;
            }

            if (!$table->store()) {
                JFactory::getApplication()->enqueueMessage($table->getError(), 'warning');
                return false;
            }
            
            if ($oldImage!=$table->poster) {
                MovieHelper::deleteAllImages($localpath, $oldImage);
            }
        }
        return true;
    }

    public static function processGallery($item, $data = array())
    {
        $lreturn = true;

        $params = JComponentHelper::getParams('com_peliculas');

        $destino_uri_folder = DS. 'images' .DS. 'peliculas' .DS. 'backdrops';
        $destino_url_folder = '/images/peliculas/backdrops';

        $destino_uri = JPath::clean(JPATH_ROOT . $destino_uri_folder .DS. $item->id);

        if (!JFolder::exists($destino_uri)) {
            // Hay que crear el directorio
            JFolder::create($destino_uri);
        }
        $images = json_decode($item->imagenes);
        if (empty($images)) {
            $images = array();
        }

        $aImagesNames = array();

        if (count($images)) {
            $table = JTable::getInstance('Movies', 'PeliculasTable');
            $table->load($item->id);

            $aGallery = array();
            foreach ($images as $iImage) {
                if (isset($iImage->url)) {
                    self::donwloadImage($iImage->url, $iImage->name, 'backdrops', $item->id);
                    $iImage->uri = $destino_uri . DS;
                    self::createThumbnails($iImage);
                }

                if (isset($iImage->uri)) {

                    if (($destino_uri .DS. $iImage->name) != ($iImage->uri . $iImage->name)) {
                        JFile::move($iImage->uri . $iImage->name, $destino_uri .DS. $iImage->name );
                        $iImage->uri = $destino_uri . DS;
                    }

                    self::createThumbnails($iImage);
                }

                $image = new stdClass();
                $image->name = $iImage->name;
                $image->title = $iImage->title;
                $image->description = $iImage->description;

                $aGallery[] = $image;
                $aImagesNames[$iImage->name] = $image;
            }

            $table->imagenes = json_encode($aGallery);
            $table->store();
        }

        if (JFolder::exists($destino_uri)) {
            // Remove image files
            $list = JFolder::files($destino_uri);
            foreach ($list as $filename) {

                if (strpos($filename, 'thumb') === 0) {
                    continue;
                }

                if (strpos($filename, 'big') === 0) {
                    continue;
                }

                if (!isset($aImagesNames[$filename])) {
                    $removeList = JFolder::files($destino_uri, $filename.'$', false, true);
                    foreach ($removeList as $removeFile) {
                        JFile::delete($removeFile);
                    }
                }
            }
        }

        return true;
    }

    public static function createThumbnails($iImage) {

        $params = JComponentHelper::getParams('com_peliculas');

        $image = new JImage($iImage->uri . $iImage->name);

        $params_big['width'] = $params->get('gallery_big_min_width', 800);
        $params_big['height'] = $params->get('gallery_big_min_height', 450);
        $params_big['prefix'] = 'big';

        $big = $image->resize($params_big['width'], $params_big['height'], true);
        $big->toFile($iImage->uri .DS. $params_big['prefix'] . '-' . $iImage->name);

        $imagebig = new JImage($iImage->uri .  $params_big['prefix'] . '-' .$iImage->name);

        $params_thumb['width'] = $params->get('gallery_thumb_min_width', 100);
        $params_thumb['height'] = $params->get('gallery_thumb_min_height', 144);
        $params_thumb['prefix'] = 'thumb';
        $params_thumb['watermark'] = $params->get('gallery_thumb_watermark');

        $thumb = $imagebig->resize($params_thumb['width'], $params_thumb['height'], true);
        $thumb->toFile($iImage->uri .DS. $params_thumb['prefix'] . '-' . $iImage->name);
    }

    public static function deleteAllImages($localpath, $filename, $deleteDir = false)
    {
        if (JFolder::exists(JPATH_ROOT .DS.  $localpath)) {
            @chmod(JPATH_ROOT .DS.  $localpath, 0777);
        }
        $original = JPATH_ROOT .DS.  $localpath . DS . $filename;
        MovieHelper::deleteImage($original);

        $thumbs = JPATH_ROOT .DS. $localpath . DS . 'thumb_' . $filename;
        MovieHelper::deleteImage($thumbs);

        if (JFolder::exists(JPATH_ROOT .DS.  $localpath) && $deleteDir) {
            JFolder::delete(JPATH_ROOT .DS.  $localpath);
        }
    }

    public static function deleteImage($name) {
        if (JFile::exists($name)) {
            JFile::delete($name);
        }
    }

    public static function donwloadImage($imageUrl, $imageName, $imageType = 'movie', $movieID) {
        $params = JComponentHelper::getParams('com_peliculas');

        $localpath = 'images' . DS . 'peliculas' . DS . $imageType;
        $baseUploadDir = JPATH_ROOT . DS . $localpath;

        if (!JFolder::exists($baseUploadDir)) {
            JFolder::create($baseUploadDir);
        }

        if ($movieID>=1) {
            $uploadDir = $baseUploadDir . DS . $movieID;
            $localpath = $localpath . DS . $movieID;
        } else {
            $uploadDir = $baseUploadDir . DS . "temp";
            $localpath = $localpath . DS . "temp";
        }

        if (!JFolder::exists($uploadDir)) {
            JFolder::create($uploadDir);
        }

        $upload = PeliculasFile::getInstance();
        $upload->setFolder($uploadDir);

        if (!empty($imageName)) {
            $tmpname = $uploadDir . DS . $imageName;
            MovieHelper::deleteImage($tmpname);
            $object_file = $upload->getImageCURL($imageUrl, $tmpname);
            $object_file->newfilename = $tmpname;
        }
    }
}