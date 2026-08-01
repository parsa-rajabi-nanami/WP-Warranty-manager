<?php

/**
 * Main admin page view — warranty list, search, filter, pagination and CSV import.
 *
 * Available via $view_data (passed from WCMGR_Admin::admin_page()):
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
 * @since      1.0.0
 *
 * @package    Warranty_Code_Manager
 * @subpackage Warranty_Code_Manager/admin/partials
 */

if (! defined('ABSPATH')) {
    exit;
}

$wcmgr_results      = $view_data['results'];
$wcmgr_search       = $view_data['search'];
$wcmgr_filter       = $view_data['filter'];
$wcmgr_total_items  = $view_data['total_items'];
$wcmgr_total_pages  = $view_data['total_pages'];
$wcmgr_current_page = $view_data['current_page'];
$wcmgr_per_page     = $view_data['per_page'];
$wcmgr_base_url     = $view_data['base_url'];

// These query parameters only display status notices and never change data.
// phpcs:disable WordPress.Security.NonceVerification.Recommended
$wcmgr_deleted      = isset( $_GET['deleted'] ) && '1' === sanitize_key( wp_unslash( $_GET['deleted'] ) );
$wcmgr_import_error = isset( $_GET['import_error'] ) ? sanitize_text_field( wp_unslash( $_GET['import_error'] ) ) : '';
$wcmgr_imported     = isset( $_GET['imported'] ) ? absint( wp_unslash( $_GET['imported'] ) ) : null;
// phpcs:enable WordPress.Security.NonceVerification.Recommended
?>

<div class="wrap">
    <h1><?php esc_html_e('Warranty Code Manager', 'warranty-code-manager'); ?></h1>

    <?php if ($wcmgr_deleted) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Warranty code deleted successfully.', 'warranty-code-manager'); ?></p>
        </div>
    <?php endif; ?>

    <?php if ('' !== $wcmgr_import_error) : ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo esc_html(urldecode($wcmgr_import_error)); ?></p>
        </div>
    <?php endif; ?>

    <?php if (null !== $wcmgr_imported) : ?>
        <div class="notice notice-success is-dismissible">
            <p>
            <?php
            printf(
                /* translators: %d: number of imported codes */
                esc_html__('%d warranty code(s) imported successfully.', 'warranty-code-manager'),
                (int) $wcmgr_imported
            );
            ?>
            </p>
        </div>
    <?php endif; ?>

    <hr>
    <h2><?php esc_html_e('Import CSV File', 'warranty-code-manager'); ?></h2>
    <p class="description">
        <?php esc_html_e('The CSV file must have one column where each row is a warranty code. An optional first row containing the header "warranty_code" is detected and skipped. To display the warranty registration form on the front end of your website, use the shortcode [warranty_form].', 'warranty-code-manager'); ?>
    </p>

    <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="import_warranty_csv">
        <?php wp_nonce_field('import_warranty_csv_nonce'); ?>
        <input type="file" name="csv_file" accept=".csv" required>
        <?php submit_button(__('Upload CSV', 'warranty-code-manager'), 'secondary'); ?>
    </form>

    <p class="description">
        <?php esc_html_e('Not sure about the format?', 'warranty-code-manager'); ?>
        <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=download_warranty_sample'), 'download_warranty_sample_nonce')); ?>">
            <?php esc_html_e('Download a sample CSV', 'warranty-code-manager'); ?>
        </a>
    </p>

    <hr>
    <h2><?php esc_html_e('Warranty Codes List', 'warranty-code-manager'); ?></h2>

    <form method="get" action="">
        <input type="hidden" name="page" value="warranty-manager">
        <div class="wcmgr-filter-bar">
            <input
                type="text"
                name="s"
                value="<?php echo esc_attr($wcmgr_search); ?>"
                placeholder="<?php esc_attr_e('Search warranty code...', 'warranty-code-manager'); ?>"
                class="regular-text">
            <select name="filter_status">
                <option value=""><?php esc_html_e('All statuses', 'warranty-code-manager'); ?></option>
                <option value="active" <?php selected($wcmgr_filter, 'active'); ?>><?php esc_html_e('Active', 'warranty-code-manager'); ?></option>
                <option value="inactive" <?php selected($wcmgr_filter, 'inactive'); ?>><?php esc_html_e('Inactive', 'warranty-code-manager'); ?></option>
            </select>
            <button type="submit" class="button button-secondary">
                <?php esc_html_e('Filter', 'warranty-code-manager'); ?>
            </button>
            <?php if ($wcmgr_search || $wcmgr_filter) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=warranty-manager')); ?>" class="button">
                    <?php esc_html_e('Clear filter', 'warranty-code-manager'); ?>
                </a>
            <?php endif; ?>
            <span class="wcmgr-filter-summary">
                <?php
                printf(
                    /* translators: %s: number of results */
                    esc_html__('%s codes found', 'warranty-code-manager'),
                    esc_html(number_format_i18n($wcmgr_total_items))
                );
                ?>
                &nbsp;|&nbsp;
                <?php
                printf(
                    /* translators: 1: current page, 2: total pages */
                    esc_html__('Page %1$d of %2$d', 'warranty-code-manager'),
                    (int) $wcmgr_current_page,
                    (int) ($wcmgr_total_pages ?: 1)
                );
                ?>
            </span>
        </div>
    </form>

    <table class="widefat striped">
        <thead>
            <tr>
                <th><?php esc_html_e('ID', 'warranty-code-manager'); ?></th>
                <th><?php esc_html_e('Warranty Code', 'warranty-code-manager'); ?></th>
                <th><?php esc_html_e('Status', 'warranty-code-manager'); ?></th>
                <th><?php esc_html_e('Activation Date', 'warranty-code-manager'); ?></th>
                <th><?php esc_html_e('Expiration Date', 'warranty-code-manager'); ?></th>
                <th><?php esc_html_e('Customer IP', 'warranty-code-manager'); ?></th>
                <th><?php esc_html_e('Actions', 'warranty-code-manager'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (! empty($wcmgr_results)) : ?>
                <?php foreach ($wcmgr_results as $wcmgr_row) : ?>
                    <tr>
                        <td><?php echo esc_html($wcmgr_row->id); ?></td>
                        <td><code><?php echo esc_html($wcmgr_row->warranty_code); ?></code></td>
                        <td>
                            <?php if ('active' === $wcmgr_row->status) : ?>
                                <span class="wcmgr-status wcmgr-status--active">
                                    ✅ <?php esc_html_e('Active', 'warranty-code-manager'); ?>
                                </span>
                            <?php else : ?>
                                <span class="wcmgr-status wcmgr-status--inactive">
                                    ⛔ <?php esc_html_e('Inactive', 'warranty-code-manager'); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html(WCMGR_Date_Helper::format($wcmgr_row->activated_at)); ?></td>
                        <td><?php echo esc_html(WCMGR_Date_Helper::format($wcmgr_row->expires_at)); ?></td>
                        <td><?php echo $wcmgr_row->customer_ip ? esc_html($wcmgr_row->customer_ip) : '—'; ?></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=warranty-edit&id=' . $wcmgr_row->id)); ?>"
                                class="button button-small wcmgr-btn-edit">✏️ <?php esc_html_e('Edit', 'warranty-code-manager'); ?></a>

                            <form
                                method="post"
                                action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                                class="wcmgr-delete-form"
                                onsubmit="return confirm('<?php esc_attr_e('Are you sure you want to delete this warranty code?', 'warranty-code-manager'); ?>')">
                                <input type="hidden" name="action" value="delete_warranty_code">
                                <input type="hidden" name="id" value="<?php echo esc_attr($wcmgr_row->id); ?>">
                                <?php wp_nonce_field('delete_warranty_code_nonce'); ?>
                                <button type="submit" class="button button-small wcmgr-btn-delete">
                                    🗑️ <?php esc_html_e('Delete', 'warranty-code-manager'); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7" class="wcmgr-empty">
                        <?php esc_html_e('No warranty codes found.', 'warranty-code-manager'); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($wcmgr_total_pages > 1) : ?>
        <div class="wcmgr-pagination">
            <?php if ($wcmgr_current_page > 1) : ?>
                <a href="<?php echo esc_url($wcmgr_base_url . '&paged=1'); ?>" class="button">« <?php esc_html_e('First', 'warranty-code-manager'); ?></a>
                <a href="<?php echo esc_url($wcmgr_base_url . '&paged=' . ($wcmgr_current_page - 1)); ?>" class="button">‹ <?php esc_html_e('Prev', 'warranty-code-manager'); ?></a>
            <?php endif; ?>

            <?php
            $wcmgr_start = max(1, $wcmgr_current_page - 2);
            $wcmgr_end   = min($wcmgr_total_pages, $wcmgr_current_page + 2);
            if ($wcmgr_start > 1) {
                echo '<span class="wcmgr-pagination__ellipsis">...</span>';
            }
            for ($wcmgr_i = $wcmgr_start; $wcmgr_i <= $wcmgr_end; $wcmgr_i++) :
            ?>
                <a href="<?php echo esc_url($wcmgr_base_url . '&paged=' . $wcmgr_i); ?>" class="button <?php echo ($wcmgr_i === $wcmgr_current_page) ? 'button-primary' : ''; ?>">
                    <?php echo (int) $wcmgr_i; ?>
                </a>
            <?php endfor; ?>
            <?php if ($wcmgr_end < $wcmgr_total_pages) {
                echo '<span class="wcmgr-pagination__ellipsis">...</span>';
            } ?>

            <?php if ($wcmgr_current_page < $wcmgr_total_pages) : ?>
                <a href="<?php echo esc_url($wcmgr_base_url . '&paged=' . ($wcmgr_current_page + 1)); ?>" class="button"><?php esc_html_e('Next', 'warranty-code-manager'); ?> ›</a>
                <a href="<?php echo esc_url($wcmgr_base_url . '&paged=' . $wcmgr_total_pages); ?>" class="button"><?php esc_html_e('Last', 'warranty-code-manager'); ?> »</a>
            <?php endif; ?>

            <span class="wcmgr-pagination__summary">
                <?php
                printf(
                    /* translators: 1: first item, 2: last item, 3: total */
                    esc_html__('Showing %1$d to %2$d of %3$s codes', 'warranty-code-manager'),
                    (int) ((($wcmgr_current_page - 1) * $wcmgr_per_page) + 1),
                    (int) min($wcmgr_current_page * $wcmgr_per_page, $wcmgr_total_items),
                    esc_html(number_format_i18n($wcmgr_total_items))
                );
                ?>
            </span>
        </div>
    <?php endif; ?>
</div>
