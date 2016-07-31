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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Gender list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasControllerMovie extends JControllerLegacy
{
    public function upload()
    {
        $params_media = JComponentHelper::getParams('com_media');
        $params_peliculas = JComponentHelper::getParams('com_peliculas');

        JFactory::getDocument()->setMimeEncoding( 'application/json' );
        JFactory::getApplication()->setHeader('Content-Disposition','attachment;filename="progress-upload-results.json"', true);
        JFactory::getApplication()->setHeader('Content-Type', 'application/json', true);
        // JApplicationWeb::setHeader('Content-Disposition','attachment;filename="progress-upload-results.json"');
        // JResponse::setHeader('Content-Disposition','attachment;filename="progress-upload-results.json"');

        // Check for request forgeries
        if (!JSession::checkToken('request')) {
            $response = array(
                'status' => 0,
                'filename' => '',
                'error' => JText::_('JINVALID_TOKEN')
            );
            echo json_encode($response);
            return;
        }

        // Get the user
        $user  = JFactory::getUser();
        JLog::addLogger(array('text_file' => 'upload.error.php'), JLog::ALL, array('upload'));

        // Get some data from the request
        $file   	= $this->input->files->get('file');
        $cropdata 	= $this->input->getString('cropdata');
        $elementId 	= $this->input->getInt('id');

        if (!empty($cropdata)) {
            $imgCropData = json_decode(stripslashes($cropdata));
        } else {
            $imgCropData = new stdClass();
            $imgCropData->x = 0;
            $imgCropData->y = 0;
            $imgCropData->width = $params_peliculas->get('gallery_big_min_width', 800);
            $imgCropData->height = $params_peliculas->get('gallery_big_min_height', 450);
        }

        if (
            $_SERVER['CONTENT_LENGTH'] > ($params_media->get('upload_maxsize', 0) * 1024 * 1024) ||
            $_SERVER['CONTENT_LENGTH'] > (int) (ini_get('upload_max_filesize')) * 1024 * 1024 ||
            $_SERVER['CONTENT_LENGTH'] > (int) (ini_get('post_max_size')) * 1024 * 1024 ||
            $_SERVER['CONTENT_LENGTH'] > (int) (ini_get('memory_limit')) * 1024 * 1024
        ) {
            $response = array(
                'status' => 0,
                'filename' => '',
                'error' => JText::_('COM_PELICULAS_UPLOAD_ERROR_WARNFILETOOLARGE')
            );
            echo json_encode($response);
            return;
        }

        // Set FTP credentials, if given
        JClientHelper::setCredentialsFromRequest('ftp');

        // Make the filename safe
        $file['name'] = JFile::makeSafe($file['name']);

        if (isset($file['name']) && isset($file['tmp_name']))
        {
            $uri_folder = DS. 'images' .DS. 'peliculas' .DS. 'backdrops';
            $url_folder = '/images/peliculas/backdrops';

            if ($elementId>=1) {
                $uri_folder .= DS .$elementId .DS;
                $url_folder .= '/'. $elementId . '/';
            } else {
                $uri_folder .= DS .'temp' .DS;
                $url_folder .= '/temp/';
            }

            if (!JFolder::exists(JPATH_ROOT . $uri_folder)) {

                if (!JFolder::create(JPATH_ROOT . $uri_folder)) {
                    // File exists
                    JLog::add('Directory create error: ' . JPATH_ROOT . $uri_folder . ' by user_id ' . $user->id, JLog::INFO, 'upload');

                    $response = array(
                        'status' => 0,
                        'urlfolder' => '',
                        'urifolder' => '',
                        'filename' => '',
                        'error' => JText::_('COM_PELICULAS_UPLOAD_ERROR_DIRECTORY_ERROR')
                    );

                    echo json_encode($response);
                    return;
                }
            }

            $fileuuid = uniqid() . '_' . time() . '.' . JFile::getExt($file['name']);
            $filename = 'original_' . $fileuuid;
            $filecrop = $fileuuid;

            $object_file = new JObject($file);
            $object_file->uri_folder    = JPATH_ROOT . $uri_folder;
            $object_file->url_folder    = JURI::root(true) . $url_folder;
            $object_file->filename      = $filename;
            $object_file->filecrop      = $filecrop;

            if (JFile::exists($object_file->uri_folder . $object_file->filename))
            {
                // File exists
                JLog::add('File exists: ' . $object_file->uri_folder . $object_file->filename . ' by user_id ' . $user->id, JLog::INFO, 'upload');

                $response = array(
                    'status' => 0,
                    'urlfolder' => '',
                    'urifolder' => '',
                    'filename' => '',
                    'error' => JText::_('COM_PELICULAS_UPLOAD_ERROR_FILE_EXISTS')
                );

                echo json_encode($response);
                return;
            }

            if (!JFile::upload($object_file->tmp_name, $object_file->uri_folder . $object_file->filename))
            {
                // Error in upload
                JLog::add('Error on upload: ' . $object_file->uri_folder . $object_file->filename, JLog::INFO, 'upload');

                $response = array(
                    'status' => 0,
                    'urlfolder' => $object_file->url_folder,
                    'urifolder' => $object_file->uri_folder,
                    'filename' => $object_file->filecrop,
                    'error' => JText::_('COM_PELICULAS_UPLOAD_ERROR_UNABLE_TO_UPLOAD_FILE')
                );

                echo json_encode($response);
                return;
            } else {

                JLog::add($object_file->uri_folder . $object_file->filename, JLog::INFO, 'upload');

                if (file_exists(JPath::clean($object_file->uri_folder . $object_file->filename)))
                {
                    // Crop image
                    $JImage = new JImage(JPath::clean($object_file->uri_folder . $object_file->filename));
                    try
                    {
                        $image = $JImage->crop($imgCropData->width, $imgCropData->height, $imgCropData->x, $imgCropData->y, true);
                        $image->toFile($object_file->uri_folder .$object_file->filecrop);
                    } catch (Exception $e) {
                        $response = array(
                            'status' => 1,
                            'urlfolder' => $object_file->url_folder,
                            'urifolder' => $object_file->uri_folder,
                            'filename' => $object_file->filecrop,
                            'error' => JText::sprintf('COM_PELICULAS_UPLOAD_COMPLETE', substr($object_file->filepath, strlen($object_file->uri_folder)))
                        );

                        echo json_encode($response);
                        return;
                    }

                    // Remove Original file
                    JFile::delete($object_file->uri_folder.$object_file->filename);
                }

                $response = array(
                    'status' => 1,
                    'urlfolder' => $object_file->url_folder,
                    'urifolder' => $object_file->uri_folder,
                    'filename' => $object_file->filecrop,
                    'error' => JText::sprintf('COM_PELICULAS_UPLOAD_COMPLETE', substr($object_file->filename, strlen($object_file->uri_folder)))
                );

                echo json_encode($response);
                return;
            }
        } else {
            $response = array(
                'status' => 0,
                'urlfolder' => '',
                'urifolder' => '',
                'filename' => '',
                'error' => JText::_('COM_PELICULAS_UPLOAD_ERROR_BAD_REQUEST')
            );

            echo json_encode($response);
            return;
        }

        JFactory::getApplication()->close();
        die();
    }
}