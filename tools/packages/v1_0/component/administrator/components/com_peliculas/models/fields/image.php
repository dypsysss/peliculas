<?php
/**
 * @version     7/05/16 20:23
 * @package     Peliculas J3x
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.filesystem.folder');
jimport('joomla.image');

/**
 * Form Field class for Upload image.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 * @see         JFormField
 */
class JFormFieldImage extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    protected $type = 'Image';

    /**
     * The controls the thousand separator
     */
    protected $directory = '';

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param   string  $name  The property name for which to the the value.
     *
     * @return  mixed  The property value or null.
     */
    public function __get($name)
    {
        switch ($name)
        {
            case 'directory':
                return $this->$name;
        }

        return parent::__get($name);
    }

    /**
     * Method to set certain otherwise inaccessible properties of the form field object.
     *
     * @param   string  $name   The property name for which to the the value.
     * @param   mixed   $value  The value of the property.
     *
     * @return  void
     */
    public function __set($name, $value)
    {
        switch ($name)
        {
            case 'directory':
                $this->$name = (string) $value;
                break;

            default:
                parent::__set($name, $value);
        }
    }

    /**
     * Method to attach a JForm object to the field.
     *
     * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
     * @param   mixed             $value    The form field value to validate.
     * @param   string            $group    The field name group control value. This acts as as an array container for the field.
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     *
     * @return  boolean  True on success.
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        if ($return)
        {
            // It is better not to force any default limits if none is specified
            $this->directory = (string) $this->element['directory'];
        }

        return $return;
    }

    /**
     * Method to get input[type="file"]
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $output = '';

        //alert & return if GD library for PHP is not enabled
        if (!extension_loaded('gd')) {
            $output .= '<strong>WARNING: </strong>The <a href="http://php.net/manual/en/book.image.php" target="_blank">GD library for PHP</a> was not found. Ensure to install it';
            return $output;
        }

        $html = array();

        $attr = '';

        // Initialize some field attributes.
        $attr .= !empty($this->class) ? ' class="input-small ' . $this->class . '"' : ' class="input-small"';
        $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';

        // Initialize JavaScript field attributes.
        $attr .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

        JHtml::_('jquery.framework');
        JHtml::_('bootstrap.tooltip');

        $document = JFactory::getDocument();
        $document->addScript(JURI::root().'media/com_peliculas/js/bootstrap.file-input.js');

        $js = "jQuery(function($) {jQuery('input[type=file]').bootstrapFileInput();});";

        $document->addScriptDeclaration($js);

        // The text field.
        $html[] = '<div class="input-prepend input-append">';

        $html[] = '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="'
            . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" readonly="readonly"' . $attr . ' />';

        // $html[] = '<input type="file" name="uploadimage" class="filestyle" data-buttonName="btn-primary"/>';
        $html[] = '<input type="file" name="uploadimage" title="' . JText::_('JLIB_FORM_BUTTON_SELECT') . '" />';

        $html[] = '</div>';

        return implode("\n", $html);
    }
}