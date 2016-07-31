<?php
/**
 * @version     13/04/15 16:06
 * @package     com_peliculas
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Form Field class for the Joomla Platform.
 */
class JFormFieldSelectTMDBPerson extends JFormField
{
    /**
     * The form field type.
     */
    protected $type = 'SelectTMDBPerson';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $html = array();

        $link = 'index.php?option=com_peliculas&amp;view=tmdbpersons&amp;layout=modal&amp;tmpl=component&amp;field=' . $this->id;

        // Initialize some field attributes.
        $attr = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
        $attr .= $this->required ? ' required' : '';

        // Load the modal behavior script.
        JHtml::_('behavior.modal', 'a.modal_' . $this->id);

        // Build the script.
        $script = array();
        $script[] = '	function jSelectPerson_' . $this->id . '(id, title) {';
        $script[] = '		var old_id = document.getElementById("' . $this->id . '").value;';
        $script[] = '			document.getElementById("jform_name").value = title;';
        $script[] = '			document.getElementById("' . $this->id . '").value = id;';
        $script[] = '			document.getElementById("dsp_' . $this->id . '").value = id;';
        $script[] = '		    Joomla.submitbutton("person.applytmdb");';
        $script[] = '		jModalClose();';
        $script[] = '	}';

        // Add the script to the document head.
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        // Create a dummy text field with the user name.
        $html[] = '<div class="input-append">';
        $html[] = '<input type="text" id="dsp_' . $this->id . '" value="' . $this->value . '"' . ' readonly' . $attr . ' />';

        // Create the user select button.
        if ($this->readonly === false)
        {
            $html[] = '<a class="btn btn-primary modal_' . $this->id . '" title="' . JText::_('COM_PELICULAS_CHANGE_TMDB') . '" href="' . $link . '"' . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
            $html[] = '<i class="icon-search"></i></a>';
        }

        $html[] = '</div>';

        // Create the real field, hidden, that stored the user id.
        $html[] = '<input type="hidden" id="' . $this->id . '" name="' . $this->name . '" value="' . $this->value . '" />';

        return implode("\n", $html);
    }
}