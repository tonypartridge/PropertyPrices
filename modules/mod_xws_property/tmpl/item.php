<?php
/**
 * @version     CVS: 1.0.0
 * @package     com_xws_property
 * @subpackage  mod_xws_property
 * @author      Tony Partridge <tony@xws.im>
 * @copyright   2021 Tony Partridge - Xtech Web Services Ltd
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Propertyprices\Module\Xws_property\Site\Helper\Xws_propertyHelper;

$element = Xws_propertyHelper::getItem($params);
?>

<?php if (!empty($element)) : ?>
	<div>
		<?php $fields = get_object_vars($element); ?>
		<?php foreach ($fields as $field_name => $field_value) : ?>
			<?php if (Xws_propertyHelper::shouldAppear($field_name)): ?>
				<div class="row">
					<div class="span4">
						<strong><?php echo Xws_propertyHelper::renderTranslatableHeader($params->get('item_table'), $field_name); ?></strong>
					</div>
					<div
						class="span8"><?php echo Xws_propertyHelper::renderElement($params->get('item_table'), $field_name, $field_value); ?></div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif;
