<?php
/**
 * @version     16/05/15 18:31
 * @package     com_peliculas
 * @copyright	Copyright (C) 2005 - 2016 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::register('PeliculasViewBase', JPATH_COMPONENT . '/views/_base.php');

/**
 * View class for a list of elements.
 *
 * @package     Joomla.Site
 * @subpackage  com_peliculas
 */
class PeliculasViewMovies extends PeliculasViewBase
{
    protected $state;

    protected $items;

    protected $pagination;

    protected $style;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed   A string if successful, otherwise a Error object.
     */
    public function display($tpl = null)
    {
        $app		= JFactory::getApplication();
        $params		= $app->getParams();

        // Get some data from the models
        $this->state	    = $this->get('State');
        $this->items	    = $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->style 		= $this->state->get('style');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseWarning(500, implode("\n", $errors));
            return false;
        }

        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));
        
        $this->params     = &$params;

        $this->load_css();
        $this->load_js();

        $show_introtext 	= $params->get('show_list_text', 1);
        $introtext_limit  	= $params->get('list_text_limit', 100);

        // Prepare the data.
        foreach ($this->items as $item)
        {
            if ($show_introtext)
            {
                $item->description = JHtml::_('content.prepare', $item->description, '', 'com_peliculas.movies');
                $item->description = self::_cleanIntrotext($item->description);
            }
            $item->description = $show_introtext ? self::truncate($item->description, $introtext_limit) : '';
        }


        $this->_prepareDocument();

        parent::display($tpl);
    }

    public static function _cleanIntrotext($introtext)
    {
        $introtext = str_replace('<p>', ' ', $introtext);
        $introtext = str_replace('</p>', ' ', $introtext);
        $introtext = strip_tags($introtext, '<a><em><strong>');
        $introtext = trim($introtext);

        return $introtext;
    }

    public static function truncate($html, $maxLength = 0)
    {
        $baseLength = strlen($html);

        // First get the plain text string. This is the rendered text we want to end up with.
        $ptString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = false);

        for ($maxLength; $maxLength < $baseLength;)
        {
            // Now get the string if we allow html.
            $htmlString = JHtml::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = true);

            // Now get the plain text from the html string.
            $htmlStringToPtString = JHtml::_('string.truncate', $htmlString, $maxLength, $noSplit = true, $allowHtml = false);

            // If the new plain text string matches the original plain text string we are done.
            if ($ptString == $htmlStringToPtString)
            {
                return $htmlString;
            }

            // Get the number of html tag characters in the first $maxlength characters
            $diffLength = strlen($ptString) - strlen($htmlStringToPtString);

            // Set new $maxlength that adjusts for the html tags
            $maxLength += $diffLength;

            if ($baseLength <= $maxLength || $diffLength <= 0)
            {
                return $htmlString;
            }
        }

        return $html;
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument()
    {
        $app   = JFactory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_PELICULAS_DEFAULT_PAGE_TITLE'));
        }
        $title = $this->params->get('page_title', '');

        if (empty($title))
        {
            $title = $app->get('sitename');
        }
        elseif ($app->get('sitename_pagetitles', 0) == 1)
        {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        }
        elseif ($app->get('sitename_pagetitles', 0) == 2)
        {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);
    }
}