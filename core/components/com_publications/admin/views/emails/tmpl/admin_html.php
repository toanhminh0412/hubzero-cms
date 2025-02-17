<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$base = trim(preg_replace('/\/administrator/', '', Request::base()), '/');
$link = $base . '/projects/' . $this->model->project()->get('alias') . '/publications/' . $this->model->get('id');
$pubLink = $base . '/publications/' . $this->model->get('id') . '/' . $this->model->get('version_number');

// Get the actual message
$comment = $this->message;

// Parse admin comment
if (!strstr($comment, '</p>') && !strstr($comment, '<pre class="wiki">'))
{
	$comment = str_replace("<br />", '', $comment);
	$comment = $this->escape($comment);
	$comment = nl2br($comment);
	$comment = str_replace("\t", ' &nbsp; &nbsp;', $comment);
	$comment = preg_replace('/  /', ' &nbsp;', $comment);
}

if ($comment)
{
	$comment = '<p style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;">' . $comment . '</p>';
}

?>

<!-- Start Header -->
<table class="tbl-header" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td width="10%" align="left" valign="bottom" nowrap="nowrap" class="sitename">
				<?php echo Config::get('sitename'); ?>
			</td>
			<td width="80%" align="left" valign="bottom" class="tagline mobilehide">
				<span class="home">
					<a href="<?php echo $base; ?>"><?php echo $base; ?></a>
				</span>
				<br />
				<span class="description"><?php echo Config::get('MetaDesc'); ?></span>
			</td>
			<td width="10%" align="right" valign="bottom" nowrap="nowrap" class="component">
				<?php echo Lang::txt('COM_PUBLICATIONS_PUBLICATIONS'); ?>
			</td>
		</tr>
	</tbody>
</table>
<!-- End Header -->

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td height="30"></td>
		</tr>
	</tbody>
</table>
<!-- End Spacer -->

<!-- Start Message -->
<table class="tbl-message" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td align="left" valign="bottom" style="border-collapse: collapse; color: #666; line-height: 1; padding: 5px; text-align: center;">
			<?php echo $this->subject; ?>
			</td>
		</tr>
	</tbody>
</table>
<!-- End Message -->

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td height="30"></td>
		</tr>
	</tbody>
</table>
<!-- End Spacer -->

<table id="project-info" width="100%"  cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; line-height: 1.6em;">
	<tbody>
		<tr>
			<td class="mobilehide" style="font-size: 2.5em; font-weight: bold; text-align: center; padding: 0 30px 8px 0; vertical-align: top;" align="center" valing="top">
			</td>
			<td width="100%" style="padding: 18px 8px 8px 8px; border-top: 2px solid #e9e9e9;">
				<table width="100%" style="border-collapse: collapse; font-size: 0.9em;" cellpadding="0" cellspacing="0" border="0">
					<tbody>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Title:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->model->get('title'); ?></td>
						</tr>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Version:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><?php echo $this->model->get('version_label'); ?></td>
						</tr>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Created:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left">@ <?php echo $this->model->created('time'); ?> on <?php echo $this->model->created('date'); ?></td>
						</tr>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap; vertical-align: top;" align="right">Project:</th>
							<td style="text-align: left; padding: 0 0.5em;" align="left"><?php echo $this->project->get('title'); ?> (<?php echo $this->project->get('alias'); ?> <?php echo $this->project->isProvisioned() ? ' - ' . Lang::txt('provisioned') : ''; ?>)</td>
						</tr>
						<tr>
							<th style="text-align: right; padding: 0 0.5em; font-weight: bold; white-space: nowrap;" align="right">Link:</th>
							<td style="text-align: left; padding: 0 0.5em;" width="100%" align="left"><a href="<?php echo $pubLink; ?>"><?php echo $pubLink; ?></a></td>
						</tr>
					</tbody>
				</table>

				<table width="100%" style="margin: 18px 0 0 0; border-top: 2px solid #e9e9e9; border-collapse: collapse; font-size: 1em;">
					<tbody>
						<tr>
							<td style="text-align: left; padding: 0 0.5em;" cellpadding="0" cellspacing="0" border="0">
								<div style="line-height: 1.6em; margin: 1em 0; padding: 0; text-align: left;"><?php echo $comment; ?></div>
								<?php if (!empty($this->permissionURL) && !empty($this->permissionTxt)) {?>
									<a href="<?php echo $this->permissionURL; ?>"><?php echo $this->permissionTxt; ?></a>
								<?php } ?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<!-- Start Spacer -->
<table class="tbl-spacer" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td height="30"></td>
		</tr>
	</tbody>
</table>
<!-- End Spacer -->
