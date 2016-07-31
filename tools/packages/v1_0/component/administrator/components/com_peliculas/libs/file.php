<?php
/**
 * Created by PhpStorm.
 * User: carless
 * Date: 19/02/16
 * Time: 19:10
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * File class helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 */
class PeliculasFile
{
    /**
     * The folder we are uploading into
     *
     * @var   string
     */
    protected $folder = '';

    /**
     * Stores the singleton instance of the PeliculasFile.
     *
     * @var    PeliculasFile
     * @since  1.0
     */
    protected static $instance = null;

    /**
     * Returns the global JNegocioFile object, only creating it
     * if it doesn't already exist.
     *
     * @return  PeliculasFile
     *
     * @since   1.0
     */
    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new PeliculasFile();
        }

        return self::$instance;
    }

    /**
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * @param string $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * Upload a file
     *
     * @return  void
     *
     * @since   1.0
     */
    public function upload($inputName = 'imagefile')
    {
        $params_media = JComponentHelper::getParams('com_media');
        $app    = JFactory::getApplication();
        $input  = $app->input;

        // Get the user
        $user  = JFactory::getUser();
        JLog::addLogger(array('text_file' => 'upload.error.php'), JLog::ALL, array('upload'));

        // Get some data from the request
        $file   = $input->files->get($inputName);
        $folder = $input->get('folder', $this->folder, 'path');

        if (empty($file['name'])) {
            return false;
        }

        if (
            $_SERVER['CONTENT_LENGTH'] > ($params_media->get('upload_maxsize', 0) * 1024 * 1024) ||
            $_SERVER['CONTENT_LENGTH'] > (int) (ini_get('upload_max_filesize')) * 1024 * 1024 ||
            $_SERVER['CONTENT_LENGTH'] > (int) (ini_get('post_max_size')) * 1024 * 1024 ||
            $_SERVER['CONTENT_LENGTH'] > (int) (ini_get('memory_limit')) * 1024 * 1024
        )
        {
            JError::raiseWarning(100, JText::_('COM_PELICULAS_ERROR_WARNUPLOADTOOLARGE'));

            return;
        }

        $file['name'] = JFile::makeSafe($file['name']);
        $fileuuid = uniqid() . '_' . time() . '.' . JFile::getExt($file['name']);
        $file['filepath'] = JPath::clean(implode(DIRECTORY_SEPARATOR, array($this->folder, $fileuuid)));

        if (JFile::exists($file['filepath']))
        {
            // A file with this name already exists
            JError::raiseWarning(100, JText::_('COM_PELICULAS_ERROR_FILE_EXISTS'));
            return false;
        }

        // The request is valid
        $err = null;

        // Trigger the onContentBeforeSave event.
        $object_file = new JObject($file);
        $object_file->newfilename = $fileuuid;

        if (!JFile::upload($object_file->tmp_name, $object_file->filepath))
        {
            // Error in upload
            JError::raiseWarning(100, JText::_('COM_PELICULAS_ERROR_UNABLE_TO_UPLOAD_FILE'));
            return false;
        } else {
            JLog::add($object_file->tmp_name . $object_file->filepath, JLog::INFO, 'upload');
        }

        return $object_file;
    }

    public function getImageCURL($strRemote, $strLocal, $usr='', $pwd='') {

        // echo "getImageCURL: <br/> remote: " . $strRemote . "<br/> local : " . $strLocal ."<br/>";

        $out = fopen($strLocal, 'wb');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FILE, $out);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $strRemote);
        if (!empty($usr)) {
            curl_setopt($ch, CURLOPT_USERPWD, $usr . ':' . $pwd);
        }
        curl_exec($ch);
        curl_close($ch);
        fclose($out);

        $object_file = new JObject();
        $object_file->newfilename = $strLocal;
        $object_file->filepath = $strLocal;

        return $object_file;
    }
}