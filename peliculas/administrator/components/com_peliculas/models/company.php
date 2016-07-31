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
JLoader::register('CompanyHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/company.php');

/**
 * Methods supporting a edit elements.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 **/
class PeliculasModelCompany extends PeliculasModelBaseForm
{
    /**
     * The type alias for this content type.
     */
    public $typeAlias = 'com_peliculas.company';

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  JTable	A database object
     */
    public function getTable($type = 'Companies', $prefix = 'PeliculasTable', $config = array())
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
            return $user->authorise('core.delete', 'com_peliculas.company.' . (int) $record->id);
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
        $form = $this->loadForm('com_peliculas.company', 'company', array('control' => 'jform', 'load_data' => $loadData));

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
        $data = JFactory::getApplication()->getUserState('com_peliculas.edit.company.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_peliculas.company', $data);

        return $data;
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
            $data['published'] = 0;
        }

        if (parent::save($data)) {
            return $this->processImage($data);
        }

        return false;
    }

    /**
     * Method to manage new uploaded images and to remove non existing images
     * @return true on success otherwise false
     */
    public function processImage($data)
    {
        $item = $this->getItem();
        return CompanyHelper::processImage($item, $data);
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
        $localpath = 'images' .DS. 'peliculas' .DS. 'company';

        $pks = (array) $pks;
        $table = $this->getTable();

        // Remove images folder
        foreach ($pks as $i => $pk) {
            if ($table->load($pk))
            {
                if ($this->canDelete($table))
                {
                    $directory = $localpath . DS . $table->id;
                    CompanyHelper::deleteAllImages($directory, $table->image, true);
                }
            }
        }

        return parent::delete($pks);
    }
}