<?php

/**
 * Main admin page view — warranty list, search, filter, pagination and CSV import.
 *
 * Available variables (passed from WPWM_Admin::admin_page()):
 *
 * @var array  $results       Query results for the current page.
 * @var string $search        Current search term.
 * @var string $filter        Current status filter (active|inactive|'').
 * @var int    $total_items   Total number of matching records.
 * @var int    $total_pages   Total number of pages.
 * @var int    $current_page  Current page number.
 * @var int    $per_page      Records per page.
 * @var string $base_url      Base pagination URL with active filters applied.
 *
 * @link       https://github.com/parsa-rajabi-nanami/WP-Warranty-manager
 * @since      1.0.0
 *
 * @package    WP_Warranty_Manager
 * @subpackage WP_Warranty_Manager/admin/partials
 */

if (! defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php esc_html_e('Warranty Manager', 'wp-warranty-manager'); ?></h1>

    <?php if (isset($_GET['deleted']) && '1' === $_GET['deleted']) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Warranty code deleted successfully.', 'wp-warranty-manager'); ?></p>
        </div>
    <?php endif; ?>

    <hr>
    <h2><?php esc_html_e('Import CSV File', 'wp-warranty-manager'); ?></h2>
    <p class="description"><?php esc_html_e('The CSV file must have one column where each row is a warranty code.', 'wp-warranty-manager'); ?></p>

    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="import_warranty_csv">
        <?php wp_nonce_field('import_warranty_csv_nonce'); ?>
        <input type="file" name="csv_file" accept=".csv" required>
        <?php submit_button(__('Upload CSV', 'wp-warranty-manager'), 'secondary'); ?>
    </form>

    <hr>
    <h2><?php esc_html_e('Warranty Codes List', 'wp-warranty-manager'); ?></h2>

    <form method="get" action="">
        <input type="hidden" name="page" value="warranty-manager">
        <div style="display:flex;gap:10px;align-items:center;margin-bottom:15px;flex-wrap:wrap;">
            <input
                type="text"
                name="s"
                value="<?php echo esc_attr($search); ?>"
                placeholder="<?php esc_attr_e('Search warranty code...', 'wp-warranty-manager'); ?>"
                class="regular-text">
            <select name="filter_status">
                <option value=""><?php esc_html_e('All statuses', 'wp-warranty-manager'); ?></option>
                <option value="active" <?php selected($filter, 'active'); ?>><?php esc_html_e('Active', 'wp-warranty-manager'); ?></option>
                <option value="inactive" <?php selected($filter, 'inactive'); ?>><?php esc_html_e('Inactive', 'wp-warranty-manager'); ?></option>
            </select>
            <button type="submit" class="button button-secondary">
                <?php esc_html_e('Filter', 'wp-warranty-manager'); ?>
            </button>
            <?php if ($search || $filter) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=warranty-manager')); ?>" class="button">
                    <?php esc_html_e('Clear filter', 'wp-warranty-manager'); ?>
                </a>
            <?php endif; ?>
            <span style="color:#666;font-size:13px;">
                <?php
                printf(
                    /* translators: %s: number of results */
                    esc_html__('%s codes found', 'wp-warranty-manager'),
                    number_format($total_items)
                );
                ?>
                &nbsp;|&nbsp;
                <?php
                printf(
                    /* translators: 1: current page, 2: total pages */
                    esc_html__('Page %1$d of %2$d', 'wp-warranty-manager'),
                    $current_page,
                    $total_pages ?: 1
                );
                ?>
            </span>
        </div>
    </form>

    <table class="widefat striped">
        <thead>
            <tr>
                <th><?php esc_html_e('ID', 'wp-warranty-manager'); ?></th>
                <th><?php esc_html_e('Warranty Code', 'wp-warranty-manager'); ?></th>
                <th><?php esc_html_e('Status', 'wp-warranty-manager'); ?></th>
                <th><?php esc_html_e('Activation Date', 'wp-warranty-manager'); ?></th>
                <th><?php esc_html_e('Expiration Date', 'wp-warranty-manager'); ?></th>
                <th><?php esc_html_e('Customer IP', 'wp-warranty-manager'); ?></th>
                <th><?php esc_html_e('Actions', 'wp-warranty-manager'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (! empty($results)) : ?>
                <?php foreach ($results as $row) : ?>
                    <tr>
                        <td><?php echo esc_html($row->id); ?></td>
                        <td><code><?php echo esc_html($row->warranty_code); ?></code></td>
                        <td>
                            <?php if ('active' === $row->status) : ?>
                                <span style="color:green;font-weight:bold;">
                                    ✅ <?php esc_html_e('Active', 'wp-warranty-manager'); ?>
                                </span>
                            <?php else : ?>
                                <span style="color:#999;font-weight:bold;">
                                    ⛔ <?php esc_html_e('Inactive', 'wp-warranty-manager'); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($this->to_jalali($row->activated_at)); ?></td>
                        <td><?php echo esc_html($this->to_jalali($row->expires_at)); ?></td>
                        <td><?php echo $row->customer_ip ? esc_html($row->customer_ip) : '—'; ?></td>
                        <td>

                            <a href="<?php echo esc_url(admin_url('admin.php?page=warranty-edit&id=' . $row->id)); ?>"
                                class="button button-small"
                                style="margin-left:5px;">✏️ <?php esc_html_e('Edit', 'wp-warranty-manager'); ?></a>

                            <form
                                method="post"
                                action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                                style="display:inline;"
                                onsubmit="return confirm('<?php esc_attr_e('Are you sure you want to delete this warranty code?', 'wp-warranty-manager'); ?>')">
                                <input type="hidden" name="action" value="delete_warranty_code">
                                <input type="hidden" name="id" value="<?php echo esc_attr($row->id); ?>">
                                <?php wp_nonce_field('delete_warranty_code_nonce'); ?>
                                <button type="submit" class="button button-small" style="color:#cc0000;">
                                    🗑️ <?php esc_html_e('Delete', 'wp-warranty-manager'); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7" style="text-align:center;padding:20px;">
                        <?php esc_html_e('No warranty codes found.', 'wp-warranty-manager'); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1) : ?>
        <div style="margin-top:20px;display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
            <?php if ($current_page > 1) : ?>
                <a href="<?php echo esc_url($base_url . '&paged=1'); ?>" class="button">« <?php esc_html_e('First', 'wp-warranty-manager'); ?></a>
                <a href="<?php echo esc_url($base_url . '&paged=' . ($current_page - 1)); ?>" class="button">‹ <?php esc_html_e('Prev', 'wp-warranty-manager'); ?></a>
            <?php endif; ?>

            <?php
            $start = max(1, $current_page - 2);
            $end   = min($total_pages, $current_page + 2);
            if ($start > 1) {
                echo '<span style="padding:4px 8px;">...</span>';
            }
            for ($i = $start; $i <= $end; $i++) :
            ?>
                <a href="<?php echo esc_url($base_url . '&paged=' . $i); ?>" class="button <?php echo ($i === $current_page) ? 'button-primary' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            <?php if ($end < $total_pages) {
                echo '<span style="padding:4px 8px;">...</span>';
            } ?>

            <?php if ($current_page < $total_pages) : ?>
                <a href="<?php echo esc_url($base_url . '&paged=' . ($current_page + 1)); ?>" class="button"><?php esc_html_e('Next', 'wp-warranty-manager'); ?> ›</a>
                <a href="<?php echo esc_url($base_url . '&paged=' . $total_pages); ?>" class="button"><?php esc_html_e('Last', 'wp-warranty-manager'); ?> »</a>
            <?php endif; ?>

            <span style="color:#666;font-size:13px;margin-right:10px;">
                <?php
                printf(
                    /* translators: 1: first item, 2: last item, 3: total */
                    esc_html__('Showing %1$d to %2$d of %3$s codes', 'wp-warranty-manager'),
                    (($current_page - 1) * $per_page) + 1,
                    min($current_page * $per_page, $total_items),
                    number_format($total_items)
                );
                ?>
            </span>
        </div>
    <?php endif; ?>
</div>