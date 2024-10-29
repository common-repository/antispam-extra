<?php

/*

Admin component of the Antispam Extra plugin

*/

if ( is_admin() ): ?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br></div>
<h2>Antispam Extra</h2>
<div id="antispam-extra-settings">
<div class="fl">
<h3>Settings</h3>
<form method="post" action="<?php echo get_bloginfo("wpurl"); ?>/wp-admin/options-general.php?page=antispam-extra">
<table>
	<tr>
		<td scope="row" >
			<input name="antispamextra_hide_website_input" id="antispamextra_hide_website_input" type= "checkbox" <?php
				if (get_option('antispamextra_hide_website_input')) echo 'checked="checked"';
			?>/>
			<label for="antispamextra_hide_website_input"><?php echo _e("Don't allow commenters to submit their websites.", 'antispam-extra'); ?></label>
		</td>
	</tr>
	<tr>
		<td scope="row" >
			<input name="antispamextra_disable_website" id="antispamextra_disable_website" type= "checkbox" <?php
				if (get_option('antispamextra_disable_website')) echo 'checked="checked"';
			?>/>
			<label for="antispamextra_disable_website"><?php echo _e("Disable the links to commenters' websites.", 'antispam-extra'); ?></label>
		</td>
	</tr>
	<tr>
		<td scope="row" >
			<input name="antispamextra_deactivate_links" id="antispamextra_deactivate_links" type= "checkbox" <?php
				if (get_option('antispamextra_deactivate_links')) echo 'checked="checked"';
			?>/>
			<label for="antispamextra_deactivate_links"><?php echo _e("Deactivate the links in comments.", 'antispam-extra'); ?></label>
		</td>
	</tr>
	<tr>
		<td scope="row" >
			<input name="antispamextra_disallow_nonreferers" id="antispamextra_disallow_nonreferers" type= "checkbox" <?php
				if (get_option('antispamextra_disallow_nonreferers')) echo 'checked="checked"';
			?>/>
			<label for="antispamextra_disallow_nonreferers"><?php echo _e('Treat comments without proper <a href="http://en.wikipedia.org/wiki/HTTP_referrer" target="_blank">HTTP referer</a> as spam.', 'antispam-extra'); ?></label>
		</td>
	</tr>
	<tr>
	<script type="text/javascript">

jQuery(document).ready(function() {
	jQuery('#antispamextra_spam_response_mode').change(function() {
		if (jQuery(this).is(':checked')) jQuery('#antispamextra_message').show();
		else jQuery('#antispamextra_message').hide();
	});
});

	</script>
		<td scope="row" >
			<input name="antispamextra_spam_response_mode" id="antispamextra_spam_response_mode" type= "checkbox" <?php
				$verbose = get_option('antispamextra_spam_response_mode');
				if ($verbose) echo 'checked="checked"';
			?>/>
			<label for="antispamextra_spam_response_mode"><?php echo _e("Display a message to the spammer.", 'antispam-extra'); ?></label><br/>
			<textarea name="antispamextra_message" id="antispamextra_message" style="<?php if (!$verbose) echo 'display:none';?>"><?php echo get_option('antispamextra_message'); ?></textarea>
		</td>
	</tr>
</table>
</div>
<div class="fl" id="antispam-extra-guide">
<h3>Guide</h3>
If you see no reason to allow commenters to post their websites, select <i>"Don't allow commenters to submit their websites"</i>. The website input field will be removed and all automated submissions with the website field will be detected as spam.
<p>
If you don't select <i>"Display a message to the spammer"</i>, the spam attempt will fail silently. The spammer will probably never know the spam attempt failed.
</p>
<p>
A <a href="http://www.budhiman.com/">Budhiman</a> Wordpress plugin.
</p>
</div>
<div class="cb"></div>
</div>

<?php wp_nonce_field('antispam-extra', 'antispam-extra-action'); ?>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
</div>
<?php endif; ?>
