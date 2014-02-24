<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL('com_jobs', 'manage', 'users', 'super administrator');
	$jacl->addACL('com_jobs', 'manage', 'users', 'administrator');
	$jacl->addACL('com_jobs', 'manage', 'users', 'manager');
}

include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_services' . DS . 'tables' . DS . 'service.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_services' . DS . 'tables' . DS . 'subscription.php');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_jobs' . DS . 'models' . DS . 'job.php');

include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'admin.php');
include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'application.php');
include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'category.php');
include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'employer.php');
include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'job.php');
include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'prefs.php');
include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'resume.php');
include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'seeker.php');
include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'shortlist.php');
include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'stats.php');
include_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'tables' . DS . 'type.php');

require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'html.php');

$controllerName = JRequest::getCmd('controller', 'jobs');
if (!file_exists(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php'))
{
	$controllerName = 'jobs';
}
require_once(JPATH_COMPONENT . DS . 'controllers' . DS . $controllerName . '.php');
$controllerName = 'JobsController' . ucfirst(strtolower($controllerName));

// Instantiate controller
$controller = new $controllerName();
$controller->execute();
$controller->redirect();

