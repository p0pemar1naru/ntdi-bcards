<?php

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

if (!class_exists('NTDI_Bcards_Tables')) {

    class NTDI_Bcards_Tables {

        private $_tableBcards = 'ntdi_bcards';

        function __construct()
        {
            self::createBcardsTable();
        }

        private function createBcardsTable()
        {
            global $wpdb;

            $tableName = $wpdb->prefix . $this->_tableBcards;

$sql = "CREATE TABLE IF NOT EXISTS " . $tableName . " (
card_ID INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
booking_number VARCHAR(255) NOT NULL,
sender_name VARCHAR(255) NOT NULL,
sender_email VARCHAR(255) NOT NULL,
sender_dob DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
recipient_name VARCHAR(255) NOT NULL,
recipient_email VARCHAR(255) NOT NULL,
image_id INT(10) UNSIGNED NOT NULL,
message_text TEXT NOT NULL,
post_id INT(10) UNSIGNED NOT NULL,
card_hash TINYTEXT NOT NULL,
PRIMARY KEY (card_ID)
) ENGINE=InnoDB;";

            dbDelta($sql);
        }

    }

}