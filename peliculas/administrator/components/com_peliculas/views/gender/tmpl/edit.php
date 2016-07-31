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

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'gender.cancel' || document.formvalidator.isValid(document.id('gender-form')))
        {
            Joomla.submitform(task, document.getElementById('gender-form'));
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_peliculas&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="gender-form" class="form-validate" enctype="multipart/form-data">

    <div class="form-horizontal">

        <div class="row-fluid form-horizontal-desktop">
            <div class="span8">
                <?php
                echo $this->form->renderField('name');
                ?>
                <?php echo $this->form->renderField('themoviedb_id'); ?>
            </div>
            <div class="span4">
                <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>

                <fieldset class="form-vertical">
                    <?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
                </fieldset>
            </div>
        </div>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>