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
use \Joomla\CMS\Layout\LayoutHelper;
use \Joomla\CMS\Session\Session;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user       = Factory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_xws_property') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'propertyrecordform.xml');
$canEdit    = $user->authorise('core.edit', 'com_xws_property') && file_exists(JPATH_COMPONENT .  DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'propertyrecordform.xml');
$canCheckin = $user->authorise('core.manage', 'com_xws_property');
$canChange  = $user->authorise('core.edit.state', 'com_xws_property');
$canDelete  = $user->authorise('core.delete', 'com_xws_property');

// Import CSS
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useStyle('com_xws_property.list');

$formatter = new NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
?>

    <div>Please note, the below data is imported directly from the Isle of Man Land registry report produced monthly. There are a lot of typos and we have tried out best to fix this where possible.</div>

<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
      name="adminForm" id="adminForm">

	<?php if(!empty($this->filterForm)) { echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); } ?>
        <div class="table-responsive">
	<table class="table table-striped" id="propertyrecordList">
		<thead>
		<tr>
            <th>Property</th>
<!--			--><?php //if (isset($this->items[0]->state)): ?>
<!--				<th width="5%">-->
<!--	--><?php //echo HTMLHelper::_('grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
<!--</th>-->
<!--			--><?php //endif; ?>
<!---->
<!--							<th class=''>-->
<!--				--><?php //echo HTMLHelper::_('grid.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_ID', 'a.id', $listDirn, $listOrder); ?>
<!--				</th>-->
<!--				<th class=''>-->
<!--				--><?php //echo HTMLHelper::_('grid.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_HOUSENO', 'a.houseno', $listDirn, $listOrder); ?>
<!--				</th>-->
<!--				<th class=''>-->
<!--				--><?php //echo HTMLHelper::_('grid.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_HOUSENAME', 'a.housename', $listDirn, $listOrder); ?>
<!--				</th>-->
<!--				<th class=''>-->
<!--				--><?php //echo HTMLHelper::_('grid.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_STREETNAME', 'a.streetname', $listDirn, $listOrder); ?>
<!--				</th>-->
<!--				<th class=''>-->
<!--				--><?php //echo HTMLHelper::_('grid.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_STREETNAME2', 'a.streetname2', $listDirn, $listOrder); ?>
<!--				</th>-->
<!--				<th class=''>-->
<!--				--><?php //echo HTMLHelper::_('grid.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_TOWN', 'a.town', $listDirn, $listOrder); ?>
<!--				</th>-->
<!--				<th class=''>-->
<!--				--><?php //echo HTMLHelper::_('grid.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_POSTCODE', 'a.postcode', $listDirn, $listOrder); ?>
<!--				</th>-->
				<th class=''>
				<?php echo HTMLHelper::_('grid.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_MARKETVALUE', 'a.marketvalue', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo HTMLHelper::_('grid.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_SALEPRICE', 'a.saleprice', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo HTMLHelper::_('grid.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_AQUIREDDATE', 'a.aquireddate', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo HTMLHelper::_('grid.sort',  'COM_XWS_PROPERTY_PROPERTYRECORDS_COMPLETEDDATE', 'a.completeddate', $listDirn, $listOrder); ?>
				</th>


							<?php if ($canEdit || $canDelete): ?>
					<th class="center">
				<?php echo Text::_('COM_XWS_PROPERTY_PROPERTYRECORDS_ACTIONS'); ?>
				</th>
				<?php endif; ?>

		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<?php $canEdit = $user->authorise('core.edit', 'com_xws_property'); ?>

			
			<tr class="row<?php echo $i % 2; ?>">

<!--				--><?php //if (isset($this->items[0]->state)) : ?>
<!--					--><?php //$class = ($canChange) ? 'active' : 'disabled'; ?>
<!--					<td class="center">-->
<!--	<a class="btn btn-micro --><?php //echo $class; ?><!--" href="--><?php //echo ($canChange) ? Route::_('index.php?option=com_xws_property&task=propertyrecord.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?><!--">-->
<!--	--><?php //if ($item->state == 1): ?>
<!--		<i class="icon-publish"></i>-->
<!--	--><?php //else: ?>
<!--		<i class="icon-unpublish"></i>-->
<!--	--><?php //endif; ?>
<!--	</a>-->
<!--</td>-->
<!--				--><?php //endif; ?>

<!--								<td>-->
<!---->
<!--					--><?php //echo $item->id; ?>
<!--				</td>-->
<!--				<td>-->
<!---->
<!--					--><?php //echo $item->houseno; ?>
<!--				</td>-->
<!--				<td>-->
<!--				--><?php //$canCheckin = Factory::getUser()->authorise('core.manage', 'com_xws_property.' . $item->id) || $item->checked_out == Factory::getUser()->id; ?>
<!--				--><?php //if($canCheckin && $item->checked_out > 0) : ?>
<!---->
<!--	<a href="--><?php //echo Route::_('index.php?option=com_xws_property&task=propertyrecord.checkin&id=' . $item->id .'&'. Session::getFormToken() .'=1'); ?><!--">					--><?php //echo HTMLHelper::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'propertyrecord.', false); ?><!--</a>-->
<!--				--><?php //endif; ?>
<!--				<a href="--><?php //echo Route::_('index.php?option=com_xws_property&view=propertyrecord&id='.(int) $item->id); ?><!--">-->
<!--				--><?php //echo $this->escape($item->housename); ?><!--</a>-->
<!--				</td>-->
<!--				<td>-->
<!---->
<!--					--><?php //echo $item->streetname; ?>
<!--				</td>-->
<!--				<td>-->
<!---->
<!--					--><?php //echo $item->streetname2; ?>
<!--				</td>-->
<!--				<td>-->
<!---->
<!--					--><?php //echo $item->town; ?>
<!--				</td>-->
<!--				<td>-->
<!---->
<!--					--><?php //echo $item->postcode; ?>
<!--				</td>-->
                <td>
                    <?php
                    $propertyParts = array();
                    if(!empty($item->houseno) && $item->houseno !== 0) $propertyParts[] = $item->houseno;
                    if(!empty($item->housename) && $item->housename !== 0) $propertyParts[] = $item->housename;
                    if(!empty($item->streetname) && $item->streetname !== 0) $propertyParts[] = $item->streetname;
                    if(!empty($item->streetname2) && $item->streetname2 !== 0) $propertyParts[] = $item->streetname2;
                    if(!empty($item->town) && $item->town !== 0) $propertyParts[] = $item->town;
                    if(!empty($item->postcode) && $item->postcode !== 0) $propertyParts[] = $item->postcode;

                    echo implode(', ', $propertyParts);
                    ?>
                </td>
				<td>
					<?php
                    echo str_replace('.00', '', $formatter->formatCurrency($item->marketvalue, 'GBP')), PHP_EOL;
                    ?>
				</td>
				<td>
					<?php
                    echo str_replace('.00', '', $formatter->formatCurrency($item->saleprice, 'GBP')), PHP_EOL;
                    ?>
				</td>
				<td>

					<?php
					$date = $item->aquireddate;
					echo $date > 0 ? HTMLHelper::_('date', $item->aquireddate, 'd-m-Y') : '-';
					?>				</td>
				<td>

					<?php
					$date = $item->completeddate;
					echo $date > 0 ? HTMLHelper::_('date', $item->completeddate, 'd-m-Y') : '-';
					?>				</td>


								<?php if ($canEdit || $canDelete): ?>
					<td class="center">
					</td>
				<?php endif; ?>

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
        </div>
	<?php if ($canCreate) : ?>
		<a href="<?php echo Route::_('index.php?option=com_xws_property&task=propertyrecordform.edit&id=0', false, 0); ?>"
		   class="btn btn-success btn-small"><i
				class="icon-plus"></i>
			<?php echo Text::_('COM_XWS_PROPERTY_ADD_ITEM'); ?></a>
	<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<?php
    if($canDelete) {
        $wa->addInlineScript("
            jQuery(document).ready(function () {
                jQuery('.delete-button').click(deleteItem);
            });

            function deleteItem() {

                if (!confirm(\"" . Text::_('COM_XWS_PROPERTY_DELETE_MESSAGE') . "\")) {
                    return false;
                }
            }
        ", [], [], ["jquery"]);
    }
?>