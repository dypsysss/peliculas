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

require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/urlmodal.php';

/**
 * Form Field class for Upload image.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_peliculas
 * @see         JFormField
 */
class JFormFieldGallery extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    protected $type = 'Gallery';

    /**
     * Method to get the list of input[type="file"]
     *
     * @return  string  The field input markup.
     */
    protected function getInput()
    {
        $output = array();

        $tab    = JFactory::getDocument()->_getTab();

        //alert & return if GD library for PHP is not enabled
        if (!extension_loaded('gd')) {
            $output .= '<strong>WARNING: </strong>The <a href="http://php.net/manual/en/book.image.php" target="_blank">GD library for PHP</a> was not found. Ensure to install it';
            return $output;
        }

        $media_config   = JComponentHelper::getParams('com_media');

        $Id  = $this->form->getValue('id');

        JHtml::stylesheet('media/com_peliculas/js/crop-avatar/dist/cropper.min.css');
        JHtml::stylesheet('media/com_peliculas/js/crop-avatar/css/joomCropper.css');
        JHtml::script('media/com_peliculas/js/crop-avatar/dist/cropper.min.js');
        JHtml::script('media/com_peliculas/js/crop-avatar/js/joomCropper.js');

        $output[] = '<div class="container" id="joomcrooper-container">';
        $output[] = '<a href="#joomcrooper-modal" role="button" class="btn" data-toggle="modal">' . JText::_('COM_PELICULAS_UPLOAD_FILE') . '</a>';
        $output[] = '<div id="joomcrooper-modal" class="modal fade modal-wide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';

        $output[] = '<div class="modal-dialog">';
        $output[] = '<div class="modal-content">';

        $output[] = '<div class="modal-header">';
        $output[] = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
        $output[] = '<h3>' . JText::_('COM_PELICULAS_GALLERY_MODAL_TITLE') . '</h3>';
        $output[] = '</div>'; // modal-header

        $output[] = '<div class="modal-body">';

        $output[] = $tab . '<div class="joomcrooper-upload">';
        $output[] = $tab . $tab . '<input class="joomcrooper-src" name="new_file_src" type="hidden">';
        $output[] = $tab . $tab . '<input class="joomcrooper-data" name="new_file_data" type="hidden">';
        $output[] = $tab . $tab . '<label for="joomcrooperInput" class="control-label">Local upload</label>';
        $output[] = $tab . $tab . '<input class="joomcrooper-input" id="joomcrooperInput" name="new_file" type="file">';
        $output[] = $tab . '</div>';

        $output[] = $tab . '<div class="row-fluid">';
        $output[] = $tab . $tab . '<div class="span9">';
        $output[] = $tab . $tab . $tab . '<div class="joomcrooper-wrapper"></div>';
        $output[] = $tab . $tab . '</div>';
        $output[] = $tab . $tab . '<div class="span3">';
        $output[] = $tab . $tab . $tab . '<div class="joomcrooper-preview preview-lg"></div>';
        $output[] = $tab . $tab . $tab . '<div class="joomcrooper-preview preview-md"></div>';
        $output[] = $tab . $tab . '</div>';
        $output[] = $tab . '</div>';

        $output[] = '</div>'; // modal-body

        $output[] = '<div class="modal-footer">';
        $output[] = '<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>';
        $output[] = '<button type="button" class="btn btn-primary joomcrooper-save" >Save</button>';
        $output[] = '</div>'; // modal-footer

        $output[] = '</div>'; // Modal Dialog
        $output[] = '</div>'; // Modal container

        $output[] = '</div>'; // Container

        $output[] = '<div class="joomcrooper-loading" tabindex="-1" role="img" aria-label="Loading"></div>';

        $output[] = '</div>'; // Container

        $Id  = $this->form->getValue('id');

        $baseURL = JURI::root(true);
        $imgBaseURL  = $baseURL.'/images/peliculas/backdrops/' . $Id;

        $images = array();
        if (is_string($this->value)) {
            $images = (array) json_decode($this->value);
        } else {
            $tmpimages = (array) $this->value;
            foreach ($tmpimages as $k => $image) {
                $images[$k] = (object) $image;
            }
        }

        $output[] = "<ul class='gallery' id='joomcrooper-list'>";
        if (!empty($images)) {
            foreach ($images as $k => $image) {

                $thumbName = 'thumb-'. $image->name;
                $src = $imgBaseURL . '/' . $image->name;
                $img_thumb = "<img src='" . $imgBaseURL . '/' . $thumbName ."' class='media-object' align='center' border='0' >";

                $output[] = "<li id='item-".$k."'>";
                $output[] = UrlModal::popup( $src, $img_thumb, array('class' => 'pull-left','update' => false, 'img' => true));
                $output[] = $tab . "<input type='hidden' name='" . $this->name. "[" . $k ."][name]' value='" .$image->name. "' />";
                $output[] = $tab . "<div class='media-info'>";

                $output[] = $tab . "<div class='imgTools'>";
                $output[] = $tab . $tab . '<a class="img-move-up" title="'.JText::_('JLIB_HTML_MOVE_UP').'"><img src="'. $baseURL . '/media/com_peliculas/images/icons/16/sort_asc.png' .'" alt="Move up" /></a>';
                $output[] = $tab . $tab . '<a class="img-move-down" title="'.JText::_('JLIB_HTML_MOVE_DOWN').'"><img src="'. $baseURL . '/media/com_peliculas/images/icons/16/sort_desc.png' .'" alt="Move down" /></a>';
                $output[] = $tab . $tab . '<a class="delete-img" title="'.JText::_('JACTION_DELETE').'"><img src="'. $baseURL . '/media/com_peliculas/images/icons/16/media_trash.png' .'" alt="Delete" /></a>';
                $output[] = $tab . "</div>";

                $output[] = $tab . "<div class='control-group'>";
                $output[] = $tab . $tab . "<div class='control-label'>";
                $output[] = $tab . $tab . $tab . "<label for='". $this->id.$k . "title'>" .JText::_('JGLOBAL_TITLE'). "</label>";
                $output[] = $tab . $tab . "</div>";
                $output[] = $tab . $tab . "<div class='controls'>";
                $output[] = $tab . $tab . $tab . "<input id='". $this->id.$k ."title' type='text' name='" . $this->name . "[" . $k . "][title]' value='" .$image->title. "' size='20'/><br />";
                $output[] = $tab . $tab . "</div>";
                $output[] = $tab . "</div>";

                $output[] = $tab . "<div class='control-group'>";
                $output[] = $tab . $tab . "<div class='control-label'>";
                $output[] = $tab . $tab . $tab . "<label for='". $this->id.$k . "desc'>" .JText::_('JGLOBAL_DESCRIPTION'). "</label>";
                $output[] = $tab . $tab . "</div>";
                $output[] = $tab . $tab . "<div class='controls'>";
                $output[] = $tab . $tab . $tab . "<input id='". $this->id.$k . "desc' type='text' name='" . $this->name . "[" . $k . "][description]' value='" .$image->description. "' size='40'/>";
                $output[] = $tab . $tab . "</div>";
                $output[] = $tab . "</div>";

                $output[] = $tab . "</div>";
                $output[] = "</li>";

            }
        }
        $output[] = "</ul>";

        $selector = '#joomcrooper-container';
        $urlToPost = 'index.php?option=com_peliculas&task=movie.upload&id='. $Id .'&format=json&' . JSession::getFormToken(). '=1';

        $script = array();

        $script[] = "(function($){";
        $script[] = $tab . "$(document).ready(function() {";
        $script[] = $tab . $tab . "$('" . $selector . "').joomCropper({";
        $script[] = $tab . $tab . $tab . "UrlPost: '" . $urlToPost . "',";
        $script[] = $tab . $tab . $tab . "defaultFieldName: '" . $this->name . "',";
        $script[] = $tab . $tab . $tab . "defaultFieldNameId: '" . $this->id . "',";
        $script[] = $tab . $tab . $tab . "defaultCountImages: " . count($images) ;
        $script[] = $tab . $tab . "});";
        $script[] = $tab . "});";
        $script[] = "})(jQuery);";

        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        JFactory::getDocument()->addScriptDeclaration("
                window.addEvent('domready', function() {
                    var sortOptions = {
                        transition: Fx.Transitions.Back.easeInOut,
                        duration: 700,
                        mode: 'vertical',
                        onComplete: function() {
                           mySort.rearrangeDOM()
                        }
                    };

                    var mySort = new Fx.Sort($$('ul.gallery li'), sortOptions);

                    $$('a.delete-img').each(function(item) {
                        item.addEvent('click', function() {
                            this.getParent('li').destroy();
                            mySort = new Fx.Sort($$('ul.gallery li'), sortOptions);
                        });
                    });

                    $$('a.img-move-up').each(function(item) {
                        item.addEvent('click', function() {
                            var activeLi = this.getParent('li');
                            if (activeLi.getPrevious()) {
                                mySort.swap(activeLi, activeLi.getPrevious());
                            } else if (this.getParent('ul').getChildren().length > 1 ) {
                                // Swap with the last element
                            	mySort.swap(activeLi, this.getParent('ul').getLast('li'));
                            }
                        });
                    });

                     $$('a.img-move-down').each(function(item) {
                        item.addEvent('click', function() {
                            var activeLi = this.getParent('li');
                            if (activeLi.getNext()) {
                                mySort.swap(activeLi, activeLi.getNext());
                            } else if (this.getParent('ul').getChildren().length > 1 ) {
                                // Swap with the first element
                            	mySort.swap(activeLi, this.getParent('ul').getFirst('li'));
                            }
                        });
                    });

                });"
        );

        return implode("\n", $output);
    }
}