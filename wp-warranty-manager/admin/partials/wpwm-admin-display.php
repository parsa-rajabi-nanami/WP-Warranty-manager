<?php

/**
 * Main admin page view — warranty list, search, filter, pagination and CSV import.
 *
 * Available via $view_data (passed from WPWM_Admin::admin_page()):
 *
 * @var array  $view_data['results']       Query results for the current page.
 * @var string $view_data['search']        Current search term.
 * @var string $view_data['filter']        Current status filter (active|inactive|'').
 * @var int    $view_data['total_items']   Total number of matching records.
 * @var int    $view_data['total_pages']   Total number of pages.
 * @var int    $view_data['current_page']  Current page number.
 * @var int    $view_data['per_page']      Records per page.
 * @var string $view_data['base_url']      Base pagination URL with active filters applied.
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

$results      = $view_data['results'];
$search       = $view_data['search'];
$filter       = $view_data['filter'];
$total_items  = $view_data['total_items'];
$total_pages  = $view_data['total_pages'];
$current_page = $view_data['current_page'];
$per_page     = $view_data['per_page'];
$base_url     = $view_data['base_url'];
?>

<div class="wrap">
    <h1><?php esc_html_e('Warranty Manager', 'wp-warranty-manager'); ?></h1>

    <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only redirect flag, no data modified.
    if (isset($_GET['deleted']) && '1' === $_GET['deleted']) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Warranty code deleted successfully.', 'wp-warranty-manager'); ?></p>
        </div>
    <?php endif; ?>

    <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if (isset($_GET['import_error']) && '' !== $_GET['import_error']) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo esc_html(urldecode(sanitize_text_field(wp_unslash($_GET['import_error'])))); ?></p>
        </div>
    <?php endif; ?>

    <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if (isset($_GET['imported'])) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
        <div class="notice notice-success is-dismissible">
            <p>
            <?php
            printf(
                /* translators: %d: number of imported codes */
                esc_html__('%d warranty code(s) imported successfully.', 'wp-warranty-manager'),
                (int) $_GET['imported']
            );
            ?>
            </p>
        </div>
    <?php endif; ?>

    <hr>
    <h2><?php esc_html_e('Import CSV File', 'wp-warranty-manager'); ?></h2>
    <p class="description">
        <?php esc_html_e('The CSV file must have one column where each row is a warranty code. An optional first row containing the header "warranty_code" is detected and skipped.', 'wp-warranty-manager'); ?>
    </p>

    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="import_warranty_csv">
        <?php wp_nonce_field('import_warranty_csv_nonce'); ?>
        <input type="file" name="csv_file" accept=".csv" required>
        <?php submit_button(__('Upload CSV', 'wp-warranty-manager'), 'secondary'); ?>
    </form>

    <p class="description">
        <?php esc_html_e('Not sure about the format?', 'wp-warranty-manager'); ?>
        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=download_warranty_sample'), 'download_warranty_sample_nonce')); ?>">
            <?php esc_html_e('Download a sample CSV', 'wp-warranty-manager'); ?>
        </a>
    </p>

    <hr>
    <h2><?php esc_html_e('Warranty Codes List', 'wp-warranty-manager'); ?></h2>

    <form method="get" action="">
        <input type="hidden" name="page" value="warranty-manager">
        <div class="wpwm-filter-bar">
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
            <span class="wpwm-filter-summary">
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
                                <span class="wpwm-status wpwm-status--active">
                                    ✅ <?php esc_html_e('Active', 'wp-warranty-manager'); ?>
                                </span>
                            <?php else : ?>
                                <span class="wpwm-status wpwm-status--inactive">
                                    ⛔ <?php esc_html_e('Inactive', 'wp-warranty-manager'); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html(WPWM_Date_Helper::format($row->activated_at)); ?></td>
                        <td><?php echo esc_html(WPWM_Date_Helper::format($row->expires_at)); ?></td>
                        <td><?php echo $row->customer_ip ? esc_html($row->customer_ip) : '—'; ?></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=warranty-edit&id=' . $row->id)); ?>"
                                class="button button-small wpwm-btn-edit">✏️ <?php esc_html_e('Edit', 'wp-warranty-manager'); ?></a>

                            <form
                                method="post"
                                action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                                class="wpwm-delete-form"
                                onsubmit="return confirm('<?php esc_attr_e('Are you sure you want to delete this warranty code?', 'wp-warranty-manager'); ?>')">
                                <input type="hidden" name="action" value="delete_warranty_code">
                                <input type="hidden" name="id" value="<?php echo esc_attr($row->id); ?>">
                                <?php wp_nonce_field('delete_warranty_code_nonce'); ?>
                                <button type="submit" class="button button-small wpwm-btn-delete">
                                    🗑️ <?php esc_html_e('Delete', 'wp-warranty-manager'); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7" class="wpwm-empty">
                        <?php esc_html_e('No warranty codes found.', 'wp-warranty-manager'); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1) : ?>
        <div class="wpwm-pagination">
            <?php if ($current_page > 1) : ?>
                <a href="<?php echo esc_url($base_url . '&paged=1'); ?>" class="button">« <?php esc_html_e('First', 'wp-warranty-manager'); ?></a>
                <a href="<?php echo esc_url($base_url . '&paged=' . ($current_page - 1)); ?>" class="button">‹ <?php esc_html_e('Prev', 'wp-warranty-manager'); ?></a>
            <?php endif; ?>

            <?php
            $start = max(1, $current_page - 2);
            $end   = min($total_pages, $current_page + 2);
            if ($start > 1) {
                echo '<span class="wpwm-pagination__ellipsis">...</span>';
            }
            for ($i = $start; $i <= $end; $i++) :
            ?>
                <a href="<?php echo esc_url($base_url . '&paged=' . $i); ?>" class="button <?php echo ($i === $current_page) ? 'button-primary' : ''; ?>">
                    <?php echo (int) $i; ?>
                </a>
            <?php endfor; ?>
            <?php if ($end < $total_pages) {
                echo '<span class="wpwm-pagination__ellipsis">...</span>';
            } ?>

            <?php if ($current_page < $total_pages) : ?>
                <a href="<?php echo esc_url($base_url . '&paged=' . ($current_page + 1)); ?>" class="button"><?php esc_html_e('Next', 'wp-warranty-manager'); ?> ›</a>
                <a href="<?php echo esc_url($base_url . '&paged=' . $total_pages); ?>" class="button"><?php esc_html_e('Last', 'wp-warranty-manager'); ?> »</a>
            <?php endif; ?>

            <span class="wpwm-pagination__summary">
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
