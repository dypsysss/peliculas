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

class UrlModal
{
    /**
     * Object constructor
     *
     * Can be overloaded/supplemented by the child class
     *
     * @param 	object 	An optional Config object with configuration options.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Returns a reference to a global Editor object, only creating it
     * if it doesn't already exist.
     *
     * @access	public
     * @return	UrlModal	The UrlModal object.
     */
    function &getInstance() {
        static $instance;
        if (!isset($instance)) {
            $instance = new UrlModal();
        }
        return $instance;
    }

    /**
     * Displays a url in a lightbox
     *
     * @param $url
     * @param $text
     * @param array options(
     * 					'width',
     * 					'height',
     * 					'top',
     * 					'left',
     * 					'class',
     * 					'update',
     * 					'img'
     * 					)
     * @return popup html
     */
    public static function popup($url, $text, $options = array()) {
        $html = "";

        if (!empty($options['onclose'])) {
            JHTML::_('behavior.modal', 'a.modal', array('onClose' => $options['onclose']));
        } elseif (!empty($options['update'])) {
            JHTML::_('behavior.modal', 'a.modal', array('onClose' => '\function(){UpdatePage();}'));
        } else {
            JHTML::_('behavior.modal');
        }

        if (empty($options['class'])) {
            $class = "modal";
        } else {
            $class = "modal " .$options['class'];
        }

        // set the $handler_string based on the user's browser
        $handler_string = "{handler:'iframe',size:{x: window.innerWidth-80, y: window.innerHeight-80}, onShow:$('sbox-window').setStyles({'padding': 0})}";
        jimport('joomla.environment.browser');
        $browser = JBrowser::getInstance();
        if ($browser->getBrowser() == 'msie') {
            // if IE, use
            $handler_string = "{handler:'iframe',size:{x:window.getSize().scrollSize.x-80, y: window.getSize().size.y-80}, onShow:$('sbox-window').setStyles({'padding': 0})}";
        }

        $handler = (!empty($options['img'])) ? "{handler:'image'}" : $handler_string;

        if (!empty($options['width'])) {
            if (empty($options['height'])) {
                $options['height'] = 480;
            }
            $handler = "{handler: 'iframe', size: {x: " . $options['width'] . ", y: " . $options['height'] . "}}";
        }

        // $class = (!empty($options['class'])) ? $options['class'] : '';

        $html = "<a class=\"$class\" href=\"$url\" rel=\"$handler\" >\n";
        $html .= "$text\n";
        $html .= "</a>\n";

        return $html;
    }
}