<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<div class="cookie-law-info-tab-content" data-id="<?php echo esc_attr( $target_id );?>">
    <h3><?php _e('Advanced','cookie-law-info'); ?></h3>
    <p><?php _e('Sometimes themes apply settings that clash with plugins. If that happens, try adjusting these settings.', 'cookie-law-info'); ?></p>

    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e('Reset settings', 'cookie-law-info'); ?></th>
            <td>
                <input type="submit" name="delete_all_settings" value="<?php _e('Delete settings and reset', 'cookie-law-info'); ?>" class="button-secondary" onclick="cli_store_settings_btn_click(this.name); if(confirm('<?php _e('Are you sure you want to delete all your settings?', 'cookie-law-info'); ?>')){  }else{ return false;};" />
                <span class="cli_form_help"><?php _e('Warning: Resets all your current settings to default.', 'cookie-law-info'); ?></span>
            </td>
        </tr>
    </table>
    <?php do_action('wt_cli_after_advanced_settings'); ?>
    <?php 
        include "admin-settings-save-button.php";
    ?>
</div>