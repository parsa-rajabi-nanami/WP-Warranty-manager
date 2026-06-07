<?php

/**

 * Plugin Name: WP Warranty Manager

 * Plugin URI: https://par30shop.com/

 * Description: افزونه حرفه‌ای مدیریت و فعال‌سازی گارانتی محصولات با قابلیت درون‌ریزی CSV، پنل مدیریت، شورتکد و ثبت تاریخ فعال‌سازی. [warranty_form]

 * Version: 1.0.0

 * Author: Parsa Rajabi

 * License: GPL2

 * Text Domain: wp-warranty-manager

 */



if (!defined('ABSPATH')) {

    exit;

}

class WP_Warranty_Manager

{



    private $table_name;



    public function __construct()

    {

        global $wpdb;

        $this->table_name = $wpdb->prefix . 'warranty_codes';



        register_activation_hook(__FILE__, [$this, 'create_table']);



        add_action('admin_menu', [$this, 'admin_menu']);

        add_action('admin_post_import_warranty_csv', [$this, 'import_csv']);

        add_shortcode('warranty_form', [$this, 'render_warranty_form']);



        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);



        add_action('wp_ajax_wp_activate_warranty', [$this, 'ajax_activate_warranty']);

        add_action('wp_ajax_nopriv_wp_activate_warranty', [$this, 'ajax_activate_warranty']);

    }



    /**

     * ساخت جدول دیتابیس

     */

    public function create_table()

    {

        global $wpdb;



        $charset_collate = $wpdb->get_charset_collate();



        $sql = "CREATE TABLE {$this->table_name} (

            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

            warranty_code VARCHAR(255) NOT NULL,

            status VARCHAR(50) DEFAULT 'inactive',

            activated_at DATETIME NULL,

            expires_at DATETIME NULL,

            customer_ip VARCHAR(100) NULL,

            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

            PRIMARY KEY (id),

            UNIQUE KEY warranty_code (warranty_code)

        ) $charset_collate;";



        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);

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



    /**

     * ایمپورت CSV

     */

    public function import_csv()

    {

        if (!current_user_can('manage_options')) {

            wp_die('دسترسی غیرمجاز');

        }



        check_admin_referer('import_warranty_csv_nonce');



        if (empty($_FILES['csv_file']['tmp_name'])) {

            wp_redirect(admin_url('admin.php?page=warranty-manager'));

            exit;

        }



        global $wpdb;



        $file = fopen($_FILES['csv_file']['tmp_name'], 'r');



        while (($data = fgetcsv($file, 1000, ',')) !== false) {



            $code = sanitize_text_field($data[0]);



            if (empty($code)) {

                continue;

            }



            $exists = $wpdb->get_var($wpdb->prepare(

                "SELECT id FROM {$this->table_name} WHERE warranty_code = %s",

                $code

            ));



            if (!$exists) {

                $wpdb->insert(

                    $this->table_name,

                    [

                        'warranty_code' => $code,

                        'status' => 'inactive'

                    ],

                    ['%s', '%s']

                );

            }

        }



        fclose($file);



        wp_redirect(admin_url('admin.php?page=warranty-manager'));

        exit;

    }



    /**

     * استایل فرم

     */

    public function enqueue_assets()

    {

        wp_register_style('wp-warranty-style', false);

        wp_enqueue_style('wp-warranty-style');

        $custom_css = '

            .wp-warranty-box {

                max-width: 500px;

                margin: auto;

                padding: 25px;

                border-radius: 8px;

                background: #fff;

                box-shadow: 0 0 15px rgba(0, 0, 0, 0.08);

            }



            .wp-warranty-box input {

                width: 100%;

                padding: 12px;

                margin-bottom: 15px;

                border: 1px solid #ddd;

                border-radius: 8px;

            }



            .wp-warranty-box button {

                background: #2271b1;

                color: #fff;

                border: none;

                padding: 12px 20px;

                border-radius: 8px;

                cursor: pointer;

                width: 100%;

            }



            .wp-warranty-message {

                display: none;

                margin-top: 15px;

                padding: 12px;

                border-radius: 8px;

                background: #f5f5f5;

            }



            .wp-warranty-loading {

                opacity: .6;

                pointer-events: none;

            }

        ';

        wp_add_inline_style('wp-warranty-style', $custom_css);



        wp_enqueue_script('jquery');

        wp_register_script('wp-warranty-script', '', ['jquery'], '1.0', true);

        wp_enqueue_script('wp-warranty-script');

        wp_localize_script('wp-warranty-script', 'wpWarranty', [

            'ajax_url' => admin_url('admin-ajax.php'),

            'nonce' => wp_create_nonce('wp_warranty_ajax_nonce')

        ]);

        $custom_js = "

            jQuery(document).ready(function($){



                $('#wp-warranty-form').on('submit', function(e){



                    e.preventDefault();



                    let form = $(this);



                    let code = form.find('input[name=\"warranty_code\"]').val();



                    form.addClass('wp-warranty-loading');



                    $('.wp-warranty-message')

                        .stop(true,true)

                        .hide()

                        .html('در حال بررسی...')

                        .fadeIn(200);



                    $.ajax({

                        url: wpWarranty.ajax_url,

                        type: 'POST',

                        data: {

                            action: 'wp_activate_warranty',

                            nonce: wpWarranty.nonce,

                            warranty_code: code

                        },



                        success: function(response){



                            form.removeClass('wp-warranty-loading');



                            if(response.success){

                                $('.wp-warranty-message')

                                    .stop(true,true)

                                    .hide()

                                    .html(response.data.message)

                                    .fadeIn(200);

                            }else{

                                $('.wp-warranty-message')

                                    .stop(true,true)

                                    .hide()

                                    .html(response.data.message)

                                    .fadeIn(200);

                            }



                        },



                        error: function(){



                            form.removeClass('wp-warranty-loading');



                            $('.wp-warranty-message')

                                .stop(true,true)

                                .hide()

                                .html('خطای سرور رخ داد')

                                .fadeIn(200);



                        }



                    });



                });



            });

        ";

        wp_add_inline_script('wp-warranty-script', $custom_js);

    }



    /**

     * فرم فعال‌سازی

     */

    public function render_warranty_form()

    {

        ob_start();

        ?>



        <div class="wp-warranty-box">



            <form id="wp-warranty-form">



                <input type="text" name="warranty_code" placeholder="کد گارانتی را وارد کنید" required>



                <button type="submit">

                    فعال‌سازی گارانتی

                </button>



            </form>



            <div class="wp-warranty-message"></div>



        </div>



        <?php



        return ob_get_clean();

    }



    public function ajax_activate_warranty()

    {

        check_ajax_referer('wp_warranty_ajax_nonce', 'nonce');



        global $wpdb;



        $code = sanitize_text_field($_POST['warranty_code']);



        if (empty($code)) {



            wp_send_json_error([

                'message' => 'کد گارانتی وارد نشده است.'

            ]);



        }



        $record = $wpdb->get_row($wpdb->prepare(

            "SELECT * FROM {$this->table_name} WHERE warranty_code = %s",

            $code

        ));



        if (!$record) {



            wp_send_json_error([

                'message' => 'کد گارانتی یافت نشد.'

            ]);



        }



        if ($record->status === 'active') {



            wp_send_json_error([

                'message' => 'این گارانتی قبلاً فعال شده است.<br>تاریخ انقضا: ' . esc_html($record->expires_at)

            ]);



        }



        $activated_at = current_time('mysql');



        $expires_at = date('Y-m-d H:i:s', strtotime('+1 year'));



        $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);



        $wpdb->update(

            $this->table_name,

            [

                'status' => 'active',

                'activated_at' => $activated_at,

                'expires_at' => $expires_at,

                'customer_ip' => $ip

            ],

            [

                'id' => $record->id

            ],

            ['%s', '%s', '%s', '%s'],

            ['%d']

        );



        wp_send_json_success([

            'message' => 'گارانتی با موفقیت فعال شد.<br>تاریخ انقضا: ' . esc_html($expires_at)

        ]);

    }

}



new WP_Warranty_Manager();