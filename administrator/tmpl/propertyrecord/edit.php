<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Xws_property
 * @author     Tony Partridge <tony@xws.im>
 * @copyright  2021 Tony Partridge - Xtech Web Services Ltd
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;


HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
?>

<form
	action="<?php echo Route::_('index.php?option=com_xws_property&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="propertyrecord-form" class="form-validate form-horizontal">

	
	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'pprecord')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'pprecord', Text::_('COM_XWS_PROPERTY_TAB_PPRECORD', true)); ?>
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo Text::_('COM_XWS_PROPERTY_FIELDSET_PPRECORD'); ?></legend>
				<?php echo $this->form->renderField('houseno'); ?>
				<?php echo $this->form->renderField('housename'); ?>
				<?php echo $this->form->renderField('streetname'); ?>
				<?php echo $this->form->renderField('streetname2'); ?>
				<?php echo $this->form->renderField('town'); ?>
				<?php echo $this->form->renderField('postcode'); ?>
				<?php echo $this->form->renderField('marketvalue'); ?>
				<?php echo $this->form->renderField('saleprice'); ?>
				<?php echo $this->form->renderField('aquireddate'); ?>
				<?php echo $this->form->renderField('completeddate'); ?>
				<?php echo $this->form->renderField('hash'); ?>
				<?php echo $this->form->renderField('soldformore'); ?>
				<?php echo $this->form->renderField('soldforless'); ?>
			</fieldset>
		</div>
	</div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
	<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
	<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
	<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>
	<input type="hidden" name="jform[parish]" value="<?php echo $this->item->parish; ?>" />

	
	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

	<input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
