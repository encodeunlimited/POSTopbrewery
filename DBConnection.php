<?php
// if(!is_dir(__DIR__.'./db'))
//     mkdir(__DIR__.'./db');
// //if(!defined('db_file')) define('db_file',__DIR__.'./db/tyre.db');
// if(!defined('db_file')) define('db_file','db/tyre.db'); //from host
// function my_udf_md5($string) {
//     return md5($string);
// }

if(!defined('db_file')) define('db_file','db/tyre.db'); //from host
function my_udf_md5($string) {
    return md5($string);
}



Class DBConnection extends SQLite3{
    protected $db;
    function __construct(){
        $this->open(db_file);
        $this->createFunction('md5', 'my_udf_md5');
        $this->exec("PRAGMA foreign_keys = ON;");

        $this->exec("CREATE TABLE IF NOT EXISTS `user_list` (
            `user_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `fullname` INTEGER NOT NULL,
            `username` TEXT NOT NULL,
            `password` TEXT NOT NULL,
            `type` INTEGER NOT NULL Default 1,
            `status` INTEGER NOT NULL Default 1,
            `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"); 

        //User Comment
        // Type = [ 1 = Administrator, 2 = Cashier]
        // Status = [ 1 = Active, 2 = Inactive]

        $this->exec("CREATE TABLE IF NOT EXISTS `category_list` (
            `category_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 1
        )");
        $this->exec("CREATE TABLE IF NOT EXISTS `supplier_list` (
            `supplier_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL,
            `status` INTEGER NOT NULL DEFAULT 1
        )");
        $this->exec("CREATE TABLE IF NOT EXISTS `product_list` (
            `product_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `product_code` TEXT NOT NULL,
            `category_id` INTEGER NOT NULL,
            `name` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `price` REAL NOT NULL DEFAULT 0,
            `status` INTEGER NOT NULL DEFAULT 1,
            FOREIGN KEY(`category_id`) REFERENCES `category_list`(`category_id`) ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE IF NOT EXISTS `stock_list` (
            `stock_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `supplier_id` TEXT NOT NULL,
            `product_id` TEXT NOT NULL,
            `quantity` REAL NOT NULL DEFAULT 0,
            `expiry_date` DATETIME NOT NULL,
            `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(`product_id`) REFERENCES `product_list`(`product_id`) ON DELETE CASCADE,
            FOREIGN KEY(`supplier_id`) REFERENCES `supplier_list`(`supplier_id`) ON DELETE CASCADE
        )");

        $this->exec("CREATE TABLE IF NOT EXISTS `transaction_list` (
            `transaction_id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
            `receipt_no` TEXT NOT NULL,
            `total` REAL NOT NULL DEFAULT 0,
            `tendered_amount` REAL NOT NULL DEFAULT 0,
            `change` REAL NOT NULL DEFAULT 0,
            `user_id` INTEGER NOT NULL DEFAULT 1,
            `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(`user_id`) REFERENCES `user_list`(`user_id`) ON DELETE SET NULL
        )");

        $this->exec("CREATE TABLE IF NOT EXISTS `transaction_items` (
            `transaction_id` TEXT NOT NULL,
            `product_id` TEXT NOT NULL,
            `quantity` REAL NOT NULL DEFAULT 0,
            `price` REAL NOT NULL DEFAULT 0,
            `date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(`transaction_id`) REFERENCES `transaction_list`(`transaction_id`) ON DELETE CASCADE,
            FOREIGN KEY(`product_id`) REFERENCES `product_list`(`product_id`) ON DELETE CASCADE
        )");

        
        // $this->exec("CREATE TRIGGER IF NOT EXISTS updatedTime_prod AFTER UPDATE on `product_list`
        // BEGIN
        //     UPDATE `product_list` SET date_updated = CURRENT_TIMESTAMP where product_id = product_id;
        // END
        // ");

        $this->exec("INSERT or IGNORE INTO `user_list` VALUES (1,'Administrator','admin',md5('admin123'),1,1, CURRENT_TIMESTAMP)");

    }
    function __destruct(){
         $this->close();
    }
}

$conn = new DBConnection();