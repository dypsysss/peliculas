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

jimport('joomla.html.html.bootstrap');

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_peliculas', JPATH_ADMINISTRATOR);

$nullDate = JFactory::getDbo()->getNullDate();

if ($this->item) : ?>
    <div id="peliculas" class="movie_details <?php echo $this->pageclass_sfx;?>">
        <meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
        <?php // if ($this->params->get('show_page_heading')) : ?>
        <?php // endif; ?>

        <div class="row-fluid">
            <div class="span4">
                <?php
                $baseURL = JURI::root();
                $imgBaseURL  = $baseURL . 'images/peliculas/movie/' . $this->item->id;
                $src = $imgBaseURL . '/' . $this->item->poster;
                echo "<img class='img-responsive' src='". $src ."' alt='".$this->item->name."'>";
                ?>
            </div>

            <div class="span8">
                <div class="page-header">
                    <h1><?php echo $this->escape($this->item->name); ?></h1>
                    <?php if (!empty($this->item->original_title)) : ?>
                        <h2><?php echo $this->item->original_title;?> <small>(<?php echo JText::_('COM_PELICULAS_FIELD_ORIGINALTITLE_LABEL');?>)</small></h2>
                    <?php endif; ?>
                </div>

                <fieldset class="form-horizontal">
                    <?php if ($this->item->f_estreno <> $nullDate) : ?>
                        <div class="control-group">
                            <div class="control-label">
                                <label><?php echo JText::_('COM_PELICULAS_FIELD_FESTRENO_LABEL');?> :</label>
                            </div>
                            <div class="controls">
                                <?php echo JHtml::_('date', $this->item->f_estreno, JText::_('DATE_FORMAT_LC4')); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </fieldset>

                <h4 class="moviefitxa"><?php echo JText::_('COM_PELICULAS_FIELD_DESCRIPCION_LABEL');?></h4>

                <?php echo $this->item->description;?>

                <h4 class="moviefitxa"><?php echo JText::_('COM_PELICULAS_FIELD_FITCHA_LABEL');?></h4>

                <fieldset class="form-horizontal">

                    <?php if (!empty($this->item->gendersnames)) : ?>
                        <div class="control-group">
                            <div class="control-label">
                                <label><?php echo JText::_('COM_PELICULAS_FIELD_GENDERS_LABEL');?> :</label>
                            </div>
                            <div class="controls">
                                <?php
                                $aGeneros = explode('|', $this->item->gendersnames);
                                if (count($aGeneros) >= 1) {
                                    foreach ($aGeneros as $iGenero) {
                                        $genero = explode(':', $iGenero);
                                        echo "<span class='label'>" . $genero[1] . "</span>&nbsp;";
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($this->item->production_countries)) : ?>
                        <div class="control-group">
                            <div class="control-label">
                                <label><?php echo JText::_('COM_PELICULAS_FIELD_COUNTRIES_LABEL');?> :</label>
                            </div>
                            <div class="controls">
                                <?php echo $this->item->production_countries; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($this->item->interpretes)) : ?>
                        <div class="control-group">
                            <div class="control-label">
                                <label><?php echo JText::_('COM_PELICULAS_FIELD_INTERPRETES_LABEL');?> :</label>
                            </div>
                            <div class="controls">
                                <?php echo JHtml::_('persons.bindList', $this->item->interpretes, 'raw');?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($this->item->directores)) : ?>
                        <div class="control-group">
                            <div class="control-label">
                                <label><?php echo JText::_('COM_PELICULAS_FIELD_DIRECTORES_LABEL');?> :</label>
                            </div>
                            <div class="controls">
                                <?php echo JHtml::_('persons.bindList', $this->item->directores, 'raw');?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($this->item->productores)) : ?>
                        <div class="control-group">
                            <div class="control-label">
                                <label><?php echo JText::_('COM_PELICULAS_FIELD_PRODUCTORES_LABEL');?> :</label>
                            </div>
                            <div class="controls">
                                <?php echo JHtml::_('persons.bindList', $this->item->productores, 'raw');?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($this->item->guion)) : ?>
                        <div class="control-group">
                            <div class="control-label">
                                <label><?php echo JText::_('COM_PELICULAS_FIELD_GUION_LABEL');?> :</label>
                            </div>
                            <div class="controls">
                                <?php echo JHtml::_('persons.bindList', $this->item->guion, 'raw');?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($this->item->production_companies)) : ?>
                        <div class="control-group">
                            <div class="control-label">
                                <label><?php echo JText::_('COM_PELICULAS_FIELD_PRODUCTORAS_LABEL');?> :</label>
                            </div>
                            <div class="controls">
                                <?php echo JHtml::_('companies.bindList', $this->item->production_companies, 'raw');?>
                            </div>
                        </div>
                    <?php endif; ?>
                </fieldset>
            </div>
        </div>

        <?php echo $this->loadTemplate('images'); ?>

        <?php echo $this->loadTemplate('videos'); ?>

        <?php echo $this->loadTemplate('events'); ?>
    </div>
    <?php
else:
    echo JText::_('COM_PELICULAS_ITEM_NOT_LOADED');
endif;
?>
