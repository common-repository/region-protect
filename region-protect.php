<?php
/*
Plugin Name: Region Protect
Plugin URI: 
Description: Protect a particular region of your post from non-members and leechers.
Version: 0.1
Author: Riadh
Author URI:
*/

/*  Copyright 2010  Riadh (email : riadhh05@hotmail.com)
    This file is part of Region Protect.

    Region Protect is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    any later version.

    Region Protect is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Region Protect.  If not, see <http://www.gnu.org/licenses/>.

*/

register_activation_hook(__FILE__, "rprotect_install");
register_deactivation_hook(__FILE__, "rprotect_uninstall");

// [rprotect replace="Login to view."]
function rprotect_func($atts, $content = null) {
	extract(shortcode_atts(array(
		'replace' => get_option('rprotect_replacement_code'),
		'visibility' => get_option('rprotect_default_visibility'),
	), $atts));
	if($content !=null) $ProtectedText = $content; else $ProtectedText = '';
	if ((is_user_logged_in()) || ($visibility == 'public')) 
		return $ProtectedText;
	else
		return '<a href="'.get_option('siteurl').'/wp-login.php">'. $replace .'</a>';
}
add_shortcode('rprotect', 'rprotect_func');

function rprotect_install() {
	if(!get_option('rprotect_replacement_code')) add_option("rprotect_replacement_code", 'Login to view.', '', 'yes');
	if(!get_option('rprotect_default_visibility')) add_option("rprotect_default_visibility", 'private', '', 'yes');
}

function rprotect_uninstall() {
	if(get_option('rprotect_replacement_code')) delete_option("rprotect_replacement_code");
	if(get_option('rprotect_default_visibility')) delete_option("rprotect_default_visibility");
}

// Hook for adding admin menus
add_action('admin_menu', 'rprotect_add_menu');

// Adding the admin Menu
function rprotect_add_menu() {
    add_submenu_page('options-general.php', 'Region Protect', 'Region Protect', 10, __FILE__, 'rprotect_add_adminpage');
}

// action function for adding the administrative page
function rprotect_add_adminpage() { ?>
<div class="wrap">
<h2>Region Protect</h2>
<p style="clear: both;">To use in your posts, add to your posts using the following shortcode: 
[rprotect replace="Login to view." visibility="private"]region to protect[/rprotect]</p>
<p>The 'visibility' attribute is optional, and so is the replace text. The plugin will work with defaults using [rprotect replace="Login to view"]. When visibility is 'private', this region will be visible for members only, and non-members will see the replacement code you specify below. </p>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>

<table class="form-table">
<tr valign="top">
	<td align="right">Replacement Code</td>
	<td><textarea id="rprotect_replacement_code" name="rprotect_replacement_code" cols="60" rows="10"><?php echo get_option('rprotect_replacement_code'); ?></textarea></td>
</tr>
<tr valign="top">
	<td align="right">Default Visibility: </td>
	<td><select id="rprotect_default_visibility" name="rprotect_default_visibility">
	<option value="private" <?php if (get_option('rprotect_default_visibility') == 'private') echo 'selected="selected"'; ?>>Private</option><option value="public" <?php if (get_option('rprotect_default_visibility') == 'public') echo 'selected="selected"'; ?>>Public</option>
	</select></td>
</tr>
</table>

<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="rprotect_replacement_code,rprotect_default_visibility" />
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
<div style="float: left;"><h4 style="float: left;margin: 0px; padding: 7px;">Like this plugin? </h4> <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHTwYJKoZIhvcNAQcEoIIHQDCCBzwCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYADAua+BwjB/i+l5ecbm3kF97ib/4rHXc8nnt2R3mUuXW2VKFlYyum5ywcLv8QBKXrPrhj+VJYBXHq+1GjI72b/VRXfNiLZW09pYFkbBPexVA0Vz8yqNMSx0VJIMQK5c4RuUl8Y7NxwJnhQ0uqBXeRhDwh4tpGObJhZUHShfY7dnjELMAkGBSsOAwIaBQAwgcwGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIkZHSCDcUrJSAgaijn2HowJOyqY3yg/X5z2msrF9+61Ch3NsMMmoS0hb41qki/TKyXRsxEYw07nIqxLSwDcqxjEOxYB+37S57i8ysalB01goxqNK8cdwqD3G2oq64wKF70atIFSNwMbWuNrIdBH13yOeZQUwb6AfiKtVM0tPFMiBddEjHxphZP2WS3NFmXQmLrXUirbGRLd1fn8kt4/bTRksdJ+u+oTGGVQH40YZyWpF1z8mgggOHMIIDgzCCAuygAwIBAgIBADANBgkqhkiG9w0BAQUFADCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wHhcNMDQwMjEzMTAxMzE1WhcNMzUwMjEzMTAxMzE1WjCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAMFHTt38RMxLXJyO2SmS+Ndl72T7oKJ4u4uw+6awntALWh03PewmIJuzbALScsTS4sZoS1fKciBGoh11gIfHzylvkdNe/hJl66/RGqrj5rFb08sAABNTzDTiqqNpJeBsYs/c2aiGozptX2RlnBktH+SUNpAajW724Nv2Wvhif6sFAgMBAAGjge4wgeswHQYDVR0OBBYEFJaffLvGbxe9WT9S1wob7BDWZJRrMIG7BgNVHSMEgbMwgbCAFJaffLvGbxe9WT9S1wob7BDWZJRroYGUpIGRMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbYIBADAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4GBAIFfOlaagFrl71+jq6OKidbWFSE+Q4FqROvdgIONth+8kSK//Y/4ihuE4Ymvzn5ceE3S/iBSQQMjyvb+s2TWbQYDwcp129OPIbD9epdr4tJOUNiSojw7BHwYRiPh58S1xGlFgHFXwrEBb3dgNbMUa+u4qectsMAXpVHnD9wIyfmHMYIBmjCCAZYCAQEwgZQwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tAgEAMAkGBSsOAwIaBQCgXTAYBgkqhkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJBTEPFw0xMDA0MjEwMzA4MDhaMCMGCSqGSIb3DQEJBDEWBBTtC0s/OfUyjee+ADKeqDXYpddnrDANBgkqhkiG9w0BAQEFAASBgKbJ4TJybrdzXSLKBRG4avqOfIl+1efJQM4XqnlRuehvA50/AZxesrowlsk9bgHNneEpLhswajJhaw90LdIcO7T+irQXUiRLcNyqr0th8fgSmzCPatOUQ5jSHXiUAyY5awHykEXLdAyfeyQiR2WXjOKabXUpRxD74Enh8chgLMid-----END PKCS7-----
"><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/fr_XC/i/scr/pixel.gif" width="1" height="1">
</form> <span style="font-size: 18px; display: block; float: left; margin-top: 8px;"> or <a href="mailto:riadhh05@hotmail.com">Comment</a></span></div>
</div>
<?php } ?>