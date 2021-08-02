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

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Propertyprices\Module\Xws_property\Site\Helper\Xws_propertyHelper;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wr = $wa->getRegistry();
$wr->addRegistryFile('media/mod_xws_property/joomla.asset.json');
$wa->useStyle('mod_xws_property.style')
    ->useScript('mod_xws_property.script');

require ModuleHelper::getLayoutPath('mod_xws_property', $params->get('content_type', 'blank'));
