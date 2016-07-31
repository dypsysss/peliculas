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
class JFormFieldVideos extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    protected $type = 'Videos';

    /**
     * Method to get the list of input[type="file"]
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $output = array();

        $Id  = $this->form->getValue('id');

        $tab = JFactory::getDocument()->_getTab();
        $baseURL = JURI::root(true);
        $videos = array();

        if (is_string($this->value)) {
            $videos = (array) json_decode($this->value);
        } else {
            $tmpvideos = (array) $this->value;
            foreach ($tmpvideos as $k => $video) {
                $videos[$k] = (object) $video;
            }
        }

        $output[] = "<ul class='videos' id='videos-list-" . $Id . "'>";
        $output[] = "<li class='videos-header'>";

        $output[] = "<span class='span3'>" . JText::_('COM_PELICULAS_VIDEO_TYPE') . "</span>";
        $output[] = "<span class='span3'>" . JText::_('COM_PELICULAS_VIDEO_KEY') . "</span>";
        $output[] = "<span class='span6'>" . JText::_('COM_PELICULAS_VIDEO_NAME') . "</span>";

        $output[] = "</li>";

        if (!empty($videos)) {
            foreach ($videos as $k => $video) {
                $output[] = "<li id='item-".$k."'>";

                $output[] = $tab . "<div class='imgTools'>";
                $output[] = $tab . $tab . '<a class="video-move-up" title="'.JText::_('JLIB_HTML_MOVE_UP').'"><img src="'. $baseURL . '/media/com_peliculas/images/icons/16/sort_asc.png' .'" alt="Move up" /></a>';
                $output[] = $tab . $tab . '<a class="video-move-down" title="'.JText::_('JLIB_HTML_MOVE_DOWN').'"><img src="'. $baseURL . '/media/com_peliculas/images/icons/16/sort_desc.png' .'" alt="Move down" /></a>';
                $output[] = $tab . $tab . '<a class="video-delete" title="'.JText::_('JACTION_DELETE').'"><img src="'. $baseURL . '/media/com_peliculas/images/icons/16/media_trash.png' .'" alt="Delete" /></a>';
                $output[] = $tab . "</div>";

                $output[] = "<select class='span3' name='" . $this->name . "[".$k."][site]'>";
                $output[] = "<option selected='selected' value='youtube'>YouTube</option></select>";
                $output[] = "</select>";

                $output[] = "<input class='span3' type='text' id='video_" .$Id. "_" . $k . "_key' name='" . $this->name . "[" .$k. "][vkey]' value='".$video->vkey."' />";
                $output[] = "<input class='span6' type='text' id='video_" .$Id. "_" . $k . "_name' name='" . $this->name . "[" .$k. "][vname]' value='".$video->vname."' />";

                $output[] = "</li>";
            }
        }
        $output[] = "</ul>";

        $output[] = "<div class='Video_Footer'>";
        $output[] = "<button id='btnVideoadd_" . $Id . "' class='btn btn-small btn-primary'><span class='icon-plus icon-white'> </span>" . JText::_('COM_PELICULAS_ACTION_ADD') . "</button>";
        $output[] = "</div>";


        $script = array();

        $script[] = 'jQuery(function($){ ';
        $script[] = $tab . 'jQuery("#btnVideoadd_' . $Id . '").click(function (e) {';
        $script[] = $tab . $tab . 'e.preventDefault();addVideoItem();';
        $script[] = $tab . '});';
        $script[] = '});';

        $script[] = "window.addEvent('domready', function() {";

        $script[] = $tab . "var sortOptionsVideo = {";
        $script[] = $tab . $tab . "transition: Fx.Transitions.Back.easeInOut,";
        $script[] = $tab . $tab . "duration: 700,";
        $script[] = $tab . $tab . "mode: 'vertical',";
        $script[] = $tab . $tab . "onComplete: function() {";
        $script[] = $tab . $tab . $tab . "mySort.rearrangeDOM()";
        $script[] = $tab . $tab . "}";
        $script[] = $tab . "};";

        $script[] = $tab . "var mySort = new Fx.Sort($$('ul.videos li'), sortOptionsVideo);";

        $script[] = $tab . "$$('a.video-delete').each(function(item) {";
        $script[] = $tab . $tab . "item.addEvent('click', function() {";
        $script[] = $tab . $tab . $tab . "this.getParent('li').destroy();";
        $script[] = $tab . $tab . $tab . "mySort = new Fx.Sort($$('ul.videos li'), sortOptions);";
        $script[] = $tab . $tab . "});";
        $script[] = $tab . "});";

        $script[] = $tab . "$$('a.video-move-up').each(function(item) {";
        $script[] = $tab . $tab . "item.addEvent('click', function() {";
        $script[] = $tab . $tab . $tab ."var activeLi = this.getParent('li');";
        $script[] = $tab . $tab . $tab ."if (activeLi.getPrevious()) {";
        $script[] = $tab . $tab . $tab . $tab ."mySort.swap(activeLi, activeLi.getPrevious());";
        $script[] = $tab . $tab . $tab ."} else if (this.getParent('ul').getChildren().length > 1 ) {";
        $script[] = $tab . $tab . $tab . $tab ."mySort.swap(activeLi, this.getParent('ul').getLast('li'));";
        $script[] = $tab . $tab . $tab ."}";
        $script[] = $tab . $tab . "});";
        $script[] = $tab . "});";

        $script[] = $tab . "$$('a.video-move-down').each(function(item) {";
        $script[] = $tab . $tab . "item.addEvent('click', function() {";
        $script[] = $tab . $tab . $tab . "var activeLi = this.getParent('li');";
        $script[] = $tab . $tab . $tab . "if (activeLi.getNext()) {";
        $script[] = $tab . $tab . $tab . $tab . "mySort.swap(activeLi, activeLi.getNext());";
        $script[] = $tab . $tab . $tab . "} else if (this.getParent('ul').getChildren().length > 1 ) {";
        $script[] = $tab . $tab . $tab . $tab . "mySort.swap(activeLi, this.getParent('ul').getFirst('li'));";
        $script[] = $tab . $tab . $tab . "}";
        $script[] = $tab . $tab . $tab ."});";
        $script[] = $tab . $tab . "});";

        $script[] = '});';


        $script[] = 'function addVideoItem() {';
        $script[] = $tab . 'var listItems = jQuery("#videos-list-' . $Id . '").children().length;';

        $script[] = $tab . 'var vsite = \'<select class=\"span3\" name=\"' . $this->name . '[\'+listItems+\'][site]\"><option value=\"youtube\">YouTube<\/option><\/select>\'';
        $script[] = $tab . 'var vkey = \'<input class=\"span3\" type=\"text\" id=\"video_' .$Id. '_\'+listItems+\'_key\" name=\"' . $this->name . '[\'+listItems+\'][vkey]\" value=\"\"\/>\'';
        $script[] = $tab . 'var vname = \'<input class=\"span6\" type=\"text\" id=\"video_' .$Id. '_\'+listItems+\'_name\" name=\"' . $this->name . '[\'+listItems+\'][vname]\" value=\"\"\/>\'';

        $script[] = $tab . 'jQuery("#videos-list-' . $Id . '").append("<li class=\"video_Item\">' .
            '"+ vsite + vkey + vname + "' .
            '<\/li>");'
            ;

        $script[] = '};';

        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        return implode("\n", $output);
    }
}