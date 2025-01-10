<?php
namespace CreactiveWeb;

if (! defined('ABSPATH') ) {
    exit;
}

class CustomAdminUserEdit {
    public static function init() {
        add_action('admin_head', [ __CLASS__, 'customStyles' ]);
    }

    public static function customStyles() {
        global $pagenow;
        if ($pagenow === 'user-edit.php') {
            echo '<style>
                .form-table {
                    background-color: #f9f9f9;
                    margin-bottom: 20px;
                    padding: 20px !important;
                    border-radius: 10px;
                }
                .form-table:nth-of-type(odd) {
                    background-color: #e9ecef;
                }
                h2 {
                    background-color: #0071bc;
                    color: white;
                    padding: 10px;
                    border-radius: 5px;
                    font-size: 1.5em;
                }
            </style>';
        }
    }
}