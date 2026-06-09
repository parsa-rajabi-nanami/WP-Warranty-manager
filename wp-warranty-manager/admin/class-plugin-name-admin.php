<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Plugin_Name_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}




	    /**
     * منوی مدیریت
     */
    public function admin_menu()
    {
        add_menu_page(
            'Warranty Manager',
            'Warranty Manager',
            'manage_options',
            'warranty-manager',
            [$this, 'admin_page'],
            'dashicons-shield-alt',
            26
        );
    }

    /**
     * صفحه مدیریت
     */
    public function admin_page()
    {
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM {$this->table_name} ORDER BY id DESC");
        ?>

        <div class="wrap">
            <h1>مدیریت گارانتی</h1>
            <hr>
            <h2>درون‌ریزی فایل CSV</h2>
            <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="import_warranty_csv">
                <?php wp_nonce_field('import_warranty_csv_nonce'); ?>
                <input type="file" name="csv_file" accept=".csv" required>
                <?php submit_button('آپلود CSV'); ?>
            </form>
            <hr>
            <h2>لیست کدهای گارانتی</h2>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>کد گارانتی</th>
                        <th>وضعیت</th>
                        <th>تاریخ فعال‌سازی</th>
                        <th>تاریخ انقضا</th>
                        <th>IP کاربر</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($results)): ?>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td><?php echo esc_html($row->id); ?></td>
                                <td><?php echo esc_html($row->warranty_code); ?></td>
                                <td>
                                    <?php if ($row->status === 'active'): ?>
                                        <span style="color:green;font-weight:bold;">فعال</span>
                                    <?php else: ?>
                                        <span style="color:red;font-weight:bold;">غیرفعال</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($row->activated_at); ?></td>
                                <td><?php echo esc_html($row->expires_at); ?></td>
                                <td><?php echo esc_html($row->customer_ip); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">هیچ کدی ثبت نشده است.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

}
