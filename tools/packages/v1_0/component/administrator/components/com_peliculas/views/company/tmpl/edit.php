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
        if (task == 'company.cancel' || document.formvalidator.isValid(document.id('company-form')))
        {
            Joomla.submitform(task, document.getElementById('company-form'));
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_peliculas&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="company-form" class="form-validate" enctype="multipart/form-data">

    <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_PELICULAS_TAB_DETAILS', true)); ?>
        <div class="row-fluid">
            <div class="span9">
                <?php echo $this->form->renderField('headquarters'); ?>
                <?php echo $this->form->renderField('homepage'); ?>
            </div>
            <div class="span3">
                <div class="form-vertical">
                    <?php
                    echo $this->form->renderField('image');

                    if (!empty($this->item->image)) {
                        echo "<div class='control-group'><div class='controls center'>";
                        echo '<a class="modal center" href="' . JUri::root() . 'images/peliculas/company/' . $this->item->id . '/' . $this->item->image . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}" >';
                        echo "<img src='" . JUri::root() . 'images/peliculas/company/' . $this->item->id . '/' . $this->item->image . "' style='width:100px;' />";
                        echo "</a>";
                        echo "</div></div>";
                    }
                    ?>
                    <?php echo $this->form->renderField('themoviedb_id'); ?>
                </div>
                <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
            </div>
        </div>
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