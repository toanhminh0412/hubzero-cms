<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<section class="main section">
	<div class="grid nobreak">
		<?php if (Request::getString('code')) { ?>
			<br />
			<p><?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_THANK_YOU', $this->userName); ?></p>
			<br />
			<?php if (isset($this->permissionGranted) && $this->permissionGranted) { ?>
			<p>You have successfully granted permission to manage the ORICD record.</p>
			<?php } else { ?>
			<p><?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_YOUR_ORCID'); ?><img src="<?php echo Request::root()?>/core/components/com_members/site/assets/img/orcid_16x16.png" class="logo" width="16" height="16" alt="iD"/> <?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_IS'); ?> <?php echo $this->userORCID; ?></p>
			<br />
			<p><?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_INDICATION_MESSAGE'); ?></p>
			<?php } ?>
		<?php } elseif (Request::getString('error') && Request::getString('error_description')) { ?>
			<p><?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_DENY'); ?><a class="btn" href="https://orcid.org/signin" rel="nofollow external"><?php echo Lang::txt('COM_MEMBERS_REDIRECT_ORCID_SIGN_IN_OR_REGISTER'); ?></a></p>
		<?php } ?>
	</div>
</section>