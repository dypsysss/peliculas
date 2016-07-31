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
        if (task == 'event.cancel' || document.formvalidator.isValid(document.id('event-form')))
        {
            Joomla.submitform(task, document.getElementById('event-form'));
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_peliculas&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="event-form" class="form-validate" enctype="multipart/form-data">

    <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_PELICULAS_TAB_DETAILS', true)); ?>
        <div class="row-fluid">
            <div class="span9 form-vertical">
                <?php echo $this->form->getControlGroup('description'); ?>
            </div>
            <div class="span3">
                <div class="form-vertical">
                    <?php
                    echo $this->form->renderField('cinema_id');
                    ?>
                </div>
                <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'rooms', JText::_('COM_PELICULAS_FIELDSET_MOVIES', true)); ?>
        <?php echo $this->loadTemplate('rooms'); ?>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
        <div class="row-fluid form-horizontal-desktop">
            <div class="span6">
                <?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
            </div>
            <div class="span6">
                <?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>