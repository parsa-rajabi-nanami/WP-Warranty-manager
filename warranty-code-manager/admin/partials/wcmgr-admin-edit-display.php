<?php

/**
 * Edit page view — form for editing a single warranty record.
 *
 * Available via $view_data (passed from WCMGR_Admin::edit_page()):
 *
 * @var object $view_data['record']   The warranty record row from the database.
 * @var bool   $view_data['updated']  Whether the record was just successfully updated.
 *
 * @since      1.0.0
 *
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/admin/partials
 */

if (! defined('ABSPATH')) {
    exit;
}

$wcmgr_record  = $view_data['record'];
$wcmgr_updated = $view_data['updated'];
?>

<div class="wrap">
    <h1>
        <?php
        printf(
            /* translators: %d: warranty record ID */
            esc_html__('Edit Warranty Code #%d', 'warranty-code-manager'),
            (int) $wcmgr_record->id
        );
        ?>
    </h1>

    <a href="<?php echo esc_url(admin_url('admin.php?page=warranty-manager')); ?>" class="button">
        ← <?php esc_html_e('Back', 'warranty-code-manager'); ?>
    </a>

    <?php if ($wcmgr_updated) : ?>
        <div class="notice notice-success is-dismissible wcmgr-notice--top">
            <p><?php esc_html_e('Warranty code updated successfully.', 'warranty-code-manager'); ?></p>
        </div>
    <?php endif; ?>

    <br><br>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="edit_warranty_code">
        <input type="hidden" name="id" value="<?php echo esc_attr($wcmgr_record->id); ?>">
        <?php wp_nonce_field('edit_warranty_code_nonce'); ?>

        <table class="form-table">
            <tr>
                <th><label for="warranty_code"><?php esc_html_e('Warranty Code', 'warranty-code-manager'); ?></label></th>
                <td>
                    <input
                        type="text"
                        id="warranty_code"
                        name="warranty_code"
                        value="<?php echo esc_attr($wcmgr_record->warranty_code); ?>"
                        class="regular-text"
                        required>
                </td>
            </tr>
            <tr>
                <th><label for="status"><?php esc_html_e('Status', 'warranty-code-manager'); ?></label></th>
                <td>
                    <select id="status" name="status">
                        <option value="inactive" <?php selected($wcmgr_record->status, 'inactive'); ?>>
                            <?php esc_html_e('Inactive', 'warranty-code-manager'); ?>
                        </option>
                        <option value="active" <?php selected($wcmgr_record->status, 'active'); ?>>
                            <?php esc_html_e('Active', 'warranty-code-manager'); ?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="activated_at"><?php esc_html_e('Activation Date', 'warranty-code-manager'); ?></label></th>
                <td>
                    <input
                        type="datetime-local"
                        id="activated_at"
                        name="activated_at"
                        value="<?php echo $wcmgr_record->activated_at ? esc_attr(wp_date('Y-m-d\TH:i', strtotime($wcmgr_record->activated_at))) : ''; ?>">
                    <p class="description"><?php esc_html_e('Leave empty to clear the activation date.', 'warranty-code-manager'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="expires_at"><?php esc_html_e('Expiration Date', 'warranty-code-manager'); ?></label></th>
                <td>
                    <input
                        type="datetime-local"
                        id="expires_at"
                        name="expires_at"
                        value="<?php echo $wcmgr_record->expires_at ? esc_attr(wp_date('Y-m-d\TH:i', strtotime($wcmgr_record->expires_at))) : ''; ?>">
                    <p class="description"><?php esc_html_e('Leave empty to clear the expiration date.', 'warranty-code-manager'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="customer_ip"><?php esc_html_e('Customer IP', 'warranty-code-manager'); ?></label></th>
                <td>
                    <input
                        type="text"
                        id="customer_ip"
                        name="customer_ip"
                        value="<?php echo esc_attr($wcmgr_record->customer_ip); ?>"
                        class="regular-text">
                </td>
            </tr>
        </table>

        <?php submit_button(__('Save Changes', 'warranty-code-manager')); ?>
    </form>
</div>
