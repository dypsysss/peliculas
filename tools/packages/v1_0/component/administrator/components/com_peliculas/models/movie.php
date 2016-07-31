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

JLoader::register('PeliculasModelBaseForm', JPATH_COMPONENT_ADMINISTRATOR . '/models/_baseform.php');
JLoader::register('MovieHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/movie.php');

use Joomla\Utilities\ArrayHelper;

/**
 * Methods supporting a edit elements.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 **/
class PeliculasModelMovie extends PeliculasModelBaseForm
{
    /**
     * The type alias for this content type.
     */
    public $typeAlias = 'com_peliculas.movie';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable	A database object
     */
    public function getTable($type = 'Movies', $prefix = 'PeliculasTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   object    $record    A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     */
    protected function canDelete($record)
    {
        if (!empty($record->id))
        {
            $user = JFactory::getUser();
            return $user->authorise('core.delete', 'com_peliculas.movie.' . (int) $record->id);
        }
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  mixed    A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_peliculas.movie', 'movie', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_peliculas.edit.movie.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_peliculas.movie', $data);

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer     The id of the primary key.
     *
     * @return  mixed       Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk))
        {
            $item->multigenders = "";

            if (!empty($item->id))  {
                $helper = new MovieHelper();
                $item->multigenders = $helper->getGenders($item->id);
            }
        }

        return $item;
    }

    /**
     * Method to save the form data.
     *
     * @param   array  $data  The form data.
     *
     * @return  boolean  True on success, False on error.
     */
    public function save($data)
    {
        $input = JFactory::getApplication()->input;

        // Alter the title for save as copy
        if ($input->get('task') == 'save2copy') {
            $data['name'] = $data['name'] . ' (Copia)';
            $data['published']  = 0;
            $data['imagenes'] 	= '[]';
            $data['videos'] 	= '[]';
        }

        if (empty($data['multigenders'])) {
            $data['multigenders'] = '[]';
        }

        if (!empty($data['f_estreno'])) {
            $ymd = DateTime::createFromFormat('d-m-Y', $data['f_estreno'])->format('Y-m-d');
            $festreno = new JDate($ymd);
            $data['f_estreno'] = $festreno->toSql();
        }

        $lreturn = parent::save($data);
        if ($lreturn) {
            $lreturnGen = $this->processGenders($data);
            $lreturnImg = $this->processImage($data);
            $lreturnGallery = $this->processGallery($data);

            return $lreturnGen && $lreturnImg && $lreturnGallery;
        }

        return false;
    }

    public function processGallery($data)
    {
        $item = $this->getItem();
        return MovieHelper::processGallery($item, $data);
    }

    public function processGenders($data) {
        $item = $this->getItem();

        if (count($data['multigenders'])) {
            $helper = new MovieHelper();
            $helper->deleteGender($item->id);
            $helper->addGenderMapping($item->id, $data['multigenders']);
        }

        return true;
    }
    
    /**
     * Method to manage new uploaded images and to remove non existing images
     * @return true on success otherwise false
     */
    public function processImage($data)
    {
        $item = $this->getItem();
        return MovieHelper::processImage($item, $data);
    }

    /**
     * Method to delete one or more records.
     *
     * @param   array  &$pks  An array of record primary keys.
     *
     * @return  boolean  True if successful, false if an error occurs.
     */
    public function delete(&$pks)
    {
        $localpath = 'images' .DS. 'peliculas' .DS. 'movie';

        $pks = (array) $pks;
        $table = $this->getTable();

        // Remove images folder
        foreach ($pks as $i => $pk) {
            if ($table->load($pk))
            {
                if ($this->canDelete($table))
                {
                    $directory = $localpath . DS . $table->id;
                    MovieHelper::deleteAllImages($directory, $table->poster, true);
                }
            }
        }

        return parent::delete($pks);
    }

    /**
     * Method to toggle the featured setting of articles.
     *
     * @param   array    $pks    The ids of the items to toggle.
     * @param   integer  $value  The value to toggle to.
     *
     * @return  boolean  True on success.
     */
    public function featured($pks, $value = 0)
    {
        // Sanitize the ids.
        $pks = (array) $pks;
        
        // Sanitize input.
        $pks    = ArrayHelper::toInteger($pks);

        if (empty($pks)) {
            $this->setError(JText::_('COM_PELICULA_NO_ITEM_SELECTED'));
            return false;
        }

        $table = $this->getTable('Featured', 'PeliculasTable');

        try
        {
            $db = $this->getDbo();
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__peliculas_movies'))
                ->set('featured = ' . (int) $value)
                ->where('id IN (' . implode(',', $pks) . ')');
            $db->setQuery($query);
            $db->execute();

            if ((int) $value == 0) {
                // Adjust the mapping table.
                // Clear the existing features settings.
                $query = $db->getQuery(true)
                    ->delete($db->quoteName('#__peliculas_featured'))
                    ->where('movie_id IN (' . implode(',', $pks) . ')');
                $db->setQuery($query);
                $db->execute();
            } else {
                // First, we find out which of our new featured articles are already featured.
                $query = $db->getQuery(true)
                    ->select('f.movie_id')
                    ->from('#__peliculas_featured AS f')
                    ->where('movie_id IN (' . implode(',', $pks) . ')');
                $db->setQuery($query);

                $old_featured = $db->loadColumn();

                // We diff the arrays to get a list of the articles that are newly featured
                $new_featured = array_diff($pks, $old_featured);

                // Featuring.
                $tuples = array();

                foreach ($new_featured as $pk) {
                    $tuples[] = $pk . ', 0';
                }

                if (count($tuples)) {
                    $db = $this->getDbo();
                    $columns = array('movie_id', 'ordering');
                    $query = $db->getQuery(true)
                        ->insert($db->quoteName('#__peliculas_featured'))
                        ->columns($db->quoteName($columns))
                        ->values($tuples);
                    $db->setQuery($query);
                    $db->execute();
                }
            }
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }

        $table->reorder();
        $this->cleanCache();

        return true;
    }
}