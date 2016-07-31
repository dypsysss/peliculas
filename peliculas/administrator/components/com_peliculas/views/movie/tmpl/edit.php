<?php
/**
 * @version     13/04/15 16:05
 * @package     com_peliculas
 * @copyright	Copyright (C) 2005 - 2014 CESI Informàtica i comunicions. All rights reserved.
 * @license	    Comercial License
 * @author      carless <carles@serrats.cat> - http://www.serrats.cat
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.modal', 'a.modal');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'movie.cancel' || document.formvalidator.isValid(document.id('movie-form')))
        {
            Joomla.submitform(task, document.getElementById('movie-form'));
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_peliculas&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="movie-form" class="form-validate" enctype="multipart/form-data">

    <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
    
    <div class="form-horizontal">

        <div class="row-fluid form-horizontal-desktop">
            <div class="span9">
                <?php
                echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'main-page'));
                
                echo JHtml::_('bootstrap.addTab', 'myTab', 'main-page', JText::_('COM_PELICULAS_TAB_MAIN', true));

                echo $this->form->getInput('description');

                ?>
                <div class="clearfix"></div>
                <?php
                echo $this->form->renderField('meta_title');
                echo $this->form->renderField('meta_description');
                echo $this->form->renderField('meta_keyword');
                
                echo JHtml::_('bootstrap.endTab');
                ?>
                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_PELICULAS_TAB_DETAILS', true));?>
                <div class="row-fluid form-horizontal-desktop">
                    <div class="span12">
                        <?php echo $this->form->renderField('original_title'); ?>
                        <?php echo $this->form->renderField('original_language'); ?>
                        <?php echo $this->form->renderField('release_date'); ?>
                        <?php echo $this->form->renderField('f_estreno'); ?>
                        <?php echo $this->form->renderField('production_countries'); ?>
                        <?php echo $this->form->renderField('interpretes'); ?>
                        <?php echo $this->form->renderField('directores'); ?>
                        <?php echo $this->form->renderField('productores'); ?>
                        <?php echo $this->form->renderField('guion'); ?>
                        <?php echo $this->form->renderField('production_companies'); ?>
                        <?php echo $this->form->renderField('homepage'); ?>
                        <?php echo $this->form->renderField('duracion'); ?>
                    </div>
                </div>
                <?php echo JHtml::_('bootstrap.endTab'); ?>

                <?php
                echo JHtml::_('bootstrap.addTab', 'myTab', 'gallery', JText::_('COM_PELICULAS_FIELDSET_GALLERY', true));
                echo $this->form->getInput('imagenes');
                echo JHtml::_('bootstrap.endTab');
                ?>

                <?php
                echo JHtml::_('bootstrap.addTab', 'myTab', 'video', JText::_('COM_PELICULAS_FIELDSET_VIDEOS', true));
                echo $this->form->getInput('videos');
                echo JHtml::_('bootstrap.endTab');
                ?>

                <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_PELICULAS_FIELDSET_PUBLISHING', true));?>
                <div class="row-fluid form-horizontal-desktop">
                    <div class="span6">
                        <?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
                    </div>
                    <div class="span6">
                        &nbsp;
                    </div>
                </div>
                <?php echo JHtml::_('bootstrap.endTab'); ?>
                <?php echo JHtml::_('bootstrap.endTabSet'); ?>
            </div>
            <div class="span3">
                <fieldset class="form-vertical">
                <?php
                echo $this->form->renderField('multigenders');
                
                echo $this->form->renderField('poster');
                if (!empty($this->item->poster)) {
                    echo "<div class='control-group'><div class='controls'>";
                    echo '<a class="modal" href="' . JUri::root() . 'images/peliculas/movie/' . $this->item->id . '/' . $this->item->poster . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}" >';
                    echo "<img src='" . JUri::root() . 'images/peliculas/movie/' . $this->item->id . '/thumb_' . $this->item->poster . "' />";
                    echo "</a>";
                    echo "</div></div>";
                }

                echo $this->form->renderField('themoviedb_id');

                echo $this->form->renderField('imdb_id');
                echo $this->form->renderField('published');
                echo $this->form->renderField('access');
                ?>
                </fieldset>
            </div>
        </div>
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>