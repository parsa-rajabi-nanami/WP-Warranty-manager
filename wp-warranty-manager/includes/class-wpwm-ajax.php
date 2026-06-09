<?php
class WPWM_Ajax
{
    public function register()
    {
        add_action(
            'wp_ajax_wp_activate_warranty',
            [$this, 'activate_warranty']
        );

        add_action(
            'wp_ajax_nopriv_wp_activate_warranty',
            [$this, 'activate_warranty']
        );
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
