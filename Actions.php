<?php
session_start();
require_once('DBConnection.php');
// date_default_timezone_set('Asia/Colombo');
date_default_timezone_set('Asia/Kuala_Lumpur');

class Actions extends DBConnection
{
    function __construct()
    {
        parent::__construct();
    }
    function __destruct()
    {
        parent::__destruct();
    }
    function login()
    {
        extract($_POST);
        $sql = "SELECT * FROM user_list where username = '{$username}' and `password` = '" . md5($password) . "' ";
        @$qry = $this->query($sql)->fetchArray();
        if (!$qry) {
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        } else {
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach ($qry as $k => $v) {
                if (!is_numeric($k))
                    $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function logout()
    {
        session_destroy();
        header("location:./");
    }
    function save_user()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                if (!empty($id)) {
                    if (!empty($data)) $data .= ",";
                    $data .= " `{$k}` = '{$v}' ";
                } else {
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if (empty($id)) {
            $cols[] = 'password';
            $values[] = "'" . md5($username) . "'";
        }
        if (isset($cols) && isset($values)) {
            $data = "(" . implode(',', $cols) . ") VALUES (" . implode(',', $values) . ")";
        }



        @$check = $this->query("SELECT count(user_id) as `count` FROM user_list where `username` = '{$username}' " . ($id > 0 ? " and user_id != '{$id}' " : ""))->fetchArray()['count'];
        if (@$check > 0) {
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        } else {
            if (empty($id)) {
                $sql = "INSERT INTO `user_list` {$data}";
            } else {
                $sql = "UPDATE `user_list` set {$data} where user_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = 'success';
                if (empty($id))
                    $resp['msg'] = 'New User successfully saved.';
                else
                    $resp['msg'] = 'User Details successfully updated.';
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving User Details Failed. Error: ' . $this->lastErrorMsg();
                $resp['sql'] = $sql;
            }
        }
        return json_encode($resp);
    }
    function delete_user()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `user_list` where rowid = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_credentials()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id', 'old_password')) && !empty($v)) {
                if (!empty($data)) $data .= ",";
                if ($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if (!empty($password) && md5($old_password) != $_SESSION['password']) {
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        } else {
            $sql = "UPDATE `user_list` set {$data} where user_id = '{$_SESSION['user_id']}'";
            $save = $this->query($sql);
            if ($save) {
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach ($_POST as $k => $v) {
                    if (!in_array($k, array('id', 'old_password')) && !empty($v)) {
                        if (!empty($data)) $data .= ",";
                        if ($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: ' . $this->lastErrorMsg();
                $resp['sql'] = $sql;
            }
        }
        return json_encode($resp);
    }
    function save_category()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = addslashes(trim($v));
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data)) $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }
        if (empty($id)) {
            $sql = "INSERT INTO `category_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `category_list` set {$data} where category_id = '{$id}'";
        }
        @$check = $this->query("SELECT COUNT(category_id) as count from `category_list` where `name` = '{$name}' " . ($id > 0 ? " and category_id != '{$id}'" : ""))->fetchArray()['count'];
        if (@$check > 0) {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Category already exists.';
        } else {
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = "success";
                if (empty($id))
                    $resp['msg'] = "Category successfully saved.";
                else
                    $resp['msg'] = "Category successfully updated.";
            } else {
                $resp['status'] = "failed";
                if (empty($id))
                    $resp['msg'] = "Saving New Category Failed.";
                else
                    $resp['msg'] = "Updating Category Failed.";
                $resp['error'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_category()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `category_list` where category_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Category successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_supplier()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = addslashes(trim($v));
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data)) $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }
        if (empty($id)) {
            $sql = "INSERT INTO `supplier_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `supplier_list` set {$data} where supplier_id = '{$id}'";
        }
        @$check = $this->query("SELECT COUNT(supplier_id) as count from `supplier_list` where `name` = '{$name}' " . ($id > 0 ? " and supplier_id != '{$id}'" : ""))->fetchArray()['count'];
        if (@$check > 0) {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Supplier already exists.';
        } else {
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = "success";
                if (empty($id))
                    $resp['msg'] = "Supplier successfully saved.";
                else
                    $resp['msg'] = "Supplier successfully updated.";
            } else {
                $resp['status'] = "failed";
                if (empty($id))
                    $resp['msg'] = "Saving New Supplier Failed.";
                else
                    $resp['msg'] = "Updating Supplier Failed.";
                $resp['error'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_supplier()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `supplier_list` where supplier_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Supplier successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_product()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = addslashes(trim($v));
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data)) $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }
        if (empty($id)) {
            $sql = "INSERT INTO `product_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `product_list` set {$data} where product_id = '{$id}'";
        }
        @$check = $this->query("SELECT COUNT(product_id) as count from `product_list` where `product_code` = '{$product_code}' " . ($id > 0 ? " and product_id != '{$id}'" : ""))->fetchArray()['count'];
        if (@$check > 0) {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Product Code already exists.';
        } else {
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = "success";
                if (empty($id))
                    $resp['msg'] = "Product successfully saved.";
                else
                    $resp['msg'] = "Product successfully updated.";
            } else {
                $resp['status'] = "failed";
                if (empty($id))
                    $resp['msg'] = "Saving New Product Failed.";
                else
                    $resp['msg'] = "Updating Product Failed.";
                $resp['error'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_product()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `product_list` where product_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Product successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_stock()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = addslashes(trim($v));
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data)) $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }
        if (empty($id)) {
            $sql = "INSERT INTO `stock_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `stock_list` set {$data} where stock_id = '{$id}'";
        }

        @$save = $this->query($sql);
        if ($save) {
            $resp['status'] = "success";
            if (empty($id))
                $resp['msg'] = "Stock successfully saved.";
            else
                $resp['msg'] = "Stock successfully updated.";
        } else {
            $resp['status'] = "failed";
            if (empty($id))
                $resp['msg'] = "Saving New Stock Failed.";
            else
                $resp['msg'] = "Updating Stock Failed.";
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function delete_stock()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `stock_list` where stock_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Stock successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    // function save_transaction()
    // {
    //     date_default_timezone_set('Asia/Colombo');

    //     extract($_POST);
    //     $data = "";
    //     $receipt_no = time();
    //     $i = 0;
    //     while (true) {
    //         $i++;
    //         $chk = $this->query("SELECT count(transaction_id) `count` FROM `transaction_list` where receipt_no = '{$receipt_no}' ")->fetchArray()['count'];
    //         if ($chk > 0) {
    //             $receipt_no = time() . $i;
    //         } else {
    //             break;
    //         }
    //     }
    //     $_POST['receipt_no'] = $receipt_no;
    //     $_POST['user_id'] = $_SESSION['user_id'];
    //     foreach ($_POST as $k => $v) {
    //         if (!in_array($k, array('id')) && !is_array($_POST[$k])) {
    //             $v = addslashes(trim($v));
    //             if (empty($id)) {
    //                 $cols[] = "`{$k}`";
    //                 $vals[] = "'{$v}'";
    //             } else {
    //                 if (!empty($data)) $data .= ", ";
    //                 $data .= " `{$k}` = '{$v}' ";
    //             }
    //         }
    //     }
    //     if (isset($cols) && isset($vals)) {
    //         $cols_join = implode(",", $cols);
    //         $vals_join = implode(",", $vals);
    //     }
    //     if (empty($id)) {
    //         $sql = "INSERT INTO `transaction_list` ({$cols_join}) VALUES ($vals_join)";
    //     } else {
    //         $sql = "UPDATE `transaction_list` set {$data} where stock_id = '{$id}'";
    //     }

    //     @$save = $this->query($sql);
    //     if ($save) {
    //         $resp['status'] = "success";
    //         $_SESSION['flashdata']['type'] = "success";
    //         if (empty($id))
    //             $_SESSION['flashdata']['msg'] = "Transaction successfully saved.";
    //         else
    //             $_SESSION['flashdata']['msg'] = "Transaction successfully updated.";
    //         if (empty($id))
    //             $last_id = $this->query("SELECT last_insert_rowid()")->fetchArray()[0];
    //         $tid = empty($id) ? $last_id : $id;
    //         $data = "";
    //         foreach ($product_id as $k => $v) {
    //             if (!empty($data)) $data .= ",";
    //             $data .= "('{$tid}','{$v}','{$quantity[$k]}','{$discount[$k]}','{$price[$k]}','{$profit[$k]}')";
    //         }
    //         if (!empty($data))
    //             $this->query("DELETE FROM transaction_items where transaction_id = '{$tid}'");
    //         $sql = "INSERT INTO transaction_items (`transaction_id`,`product_id`,`quantity`,`discount`,`price`,`profit`) VALUES {$data}";
    //         $save = $this->query($sql);
    //         $resp['transaction_id'] = $tid;
    //     } else {
    //         $resp['status'] = "failed";
    //         if (empty($id))
    //             $resp['msg'] = "Saving New Transaction Failed.";
    //         else
    //             $resp['msg'] = "Updating Transaction Failed.";
    //         $resp['error'] = $this->lastErrorMsg();
    //     }
    //     return json_encode($resp);
    // }

    function save_transaction()
    {
        date_default_timezone_set('Asia/Kuala_Lumpur');

        extract($_POST);
        $data = "";
        $receipt_no = time();
        $date_now = date("Y-m-d H:i:s");
        $i = 0;
        while (true) {
            $i++;
            $chk = $this->query("SELECT count(transaction_id) `count` FROM `transaction_list` WHERE receipt_no = '{$receipt_no}' ")->fetchArray()['count'];
            if ($chk > 0) {
                $receipt_no = time() . $i;
            } else {
                break;
            }
        }
        $_POST['receipt_no'] = $receipt_no;
        $_POST['user_id'] = $_SESSION['user_id'];
        $_POST['date_added'] = $date_now; // Add date_added to transaction_list
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id')) && !is_array($_POST[$k])) {
                $v = addslashes(trim($v));
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data)) $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }
        if (empty($id)) {
            $sql = "INSERT INTO `transaction_list` ({$cols_join}) VALUES ({$vals_join})";
            $sql1 = "INSERT INTO `transaction_list_old` ({$cols_join}) VALUES ({$vals_join})";
        } else {
            $sql = "UPDATE `transaction_list` SET {$data} WHERE stock_id = '{$id}'";
        }

        @$save = $this->query($sql);
        if ($save) {
            $resp['status'] = "success";
            $_SESSION['flashdata']['type'] = "success";
            if (empty($id))
                $_SESSION['flashdata']['msg'] = "Transaction successfully saved.";
            else
                $_SESSION['flashdata']['msg'] = "Transaction successfully updated.";
            if (empty($id))
                $last_id = $this->query("SELECT last_insert_rowid()")->fetchArray()[0];
            $tid = empty($id) ? $last_id : $id;
            $data = "";
            foreach ($product_id as $k => $v) {
                if (!empty($data)) $data .= ",";
                $data .= "('{$tid}','{$v}','{$quantity[$k]}','{$discount[$k]}','{$price[$k]}','{$profit[$k]}','{$date_now}')";
            }
            if (!empty($data))
                $this->query("DELETE FROM transaction_items WHERE transaction_id = '{$tid}'");
            $sql = "INSERT INTO transaction_items (`transaction_id`,`product_id`,`quantity`,`discount`,`price`,`profit`,`date_added`) VALUES {$data}";
            $save = $this->query($sql);
            $save = $this->query($sql1);
            $resp['transaction_id'] = $tid;
        } else {
            $resp['status'] = "failed";
            if (empty($id))
                $resp['msg'] = "Saving New Transaction Failed.";
            else
                $resp['msg'] = "Updating Transaction Failed.";
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }

    function delete_transaction()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `transaction_list` where transaction_id = '{$id}'");
        @$delete1 = $this->query("DELETE FROM `transaction_list_old` where receipt_no = '{$rno}'");
        if ($delete && $delete1) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Transaction successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }

    function done_arreas()
    {
        extract($_POST);

        @$done_a = $this->query("UPDATE `transaction_list` set arrears='0' where transaction_id = '{$id}'");
        if ($done_a) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Arreas Sucessfully Done.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }

    //new for credit payment

    function save_ctransaction()
    {
        extract($_POST);
        $data = "";
        $receipt_no = time();
        $i = 0;
        while (true) {
            $i++;
            $chk = $this->query("SELECT count(transaction_id) `count` FROM `ctransaction_list` where receipt_no = '{$receipt_no}' ")->fetchArray()['count'];
            if ($chk > 0) {
                $receipt_no = time() . $i;
            } else {
                break;
            }
        }
        $_POST['receipt_no'] = $receipt_no;
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id')) && !is_array($_POST[$k])) {
                $v = addslashes(trim($v));
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data)) $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }
        if (empty($id)) {
            $sql = "INSERT INTO `ctransaction_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `ctransaction_list` set {$data} where stock_id = '{$id}'";
        }

        @$save = $this->query($sql);
        if ($save) {
            $resp['status'] = "success";
            $_SESSION['flashdata']['type'] = "success";
            if (empty($id))
                $_SESSION['flashdata']['msg'] = "Transaction successfully saved.";
            else
                $_SESSION['flashdata']['msg'] = "Transaction successfully updated.";
            if (empty($id))
                $last_id = $this->query("SELECT last_insert_rowid()")->fetchArray()[0];
            $tid = empty($id) ? $last_id : $id;
            $data = "";
            foreach ($product_id as $k => $v) {
                if (!empty($data)) $data .= ",";
                $data .= "('{$tid}','{$v}','{$quantity[$k]}','{$discount[$k]}','{$price[$k]}','{$profit[$k]}')";
            }
            if (!empty($data))
                $this->query("DELETE FROM transaction_items where transaction_id = '{$tid}'");
            $sql = "INSERT INTO transaction_items (`transaction_id`,`product_id`,`quantity`,`discount`,`price`,`profit`) VALUES {$data}";
            $save = $this->query($sql);
            $resp['transaction_id'] = $tid;
        } else {
            $resp['status'] = "failed";
            if (empty($id))
                $resp['msg'] = "Saving New Transaction Failed.";
            else
                $resp['msg'] = "Updating Transaction Failed.";
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function delete_ctransaction()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `ctransaction_list` where transaction_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Transaction successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }

    function save_client()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = addslashes(trim($v));
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data)) $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }
        if (empty($id)) {
            $sql = "INSERT INTO `cclient_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `cclient_list` set {$data} where client_id = '{$id}'";
        }
        //@$check= $this->query("SELECT COUNT(client_id) as count from `cclient_list` where client_id != '{$id}'")->fetchArray()['count'];
        @$check = $this->query("SELECT COUNT(client_id) as count from `cclient_list` where `name` = '{$name}' " . ($id > 0 ? " and client_id != '{$id}'" : ""))->fetchArray()['count'];
        if (@$check > 0) {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Client id already exists.';
        } else {
            @$save = $this->query($sql);
            if ($save) {
                $resp['status'] = "success";
                if (empty($id))
                    $resp['msg'] = "Client successfully saved.";
                else
                    $resp['msg'] = "Client successfully updated.";
            } else {
                $resp['status'] = "failed";
                if (empty($id))
                    $resp['msg'] = "Saving New Client Failed.";
                else
                    $resp['msg'] = "Updating Client Failed.";
                $resp['error'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_client()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `cclient_list` where client_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Client successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    //pay_list

    function save_pay()
    {
        extract($_POST);
        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id'))) {
                $v = addslashes(trim($v));
                if (empty($id)) {
                    $cols[] = "`{$k}`";
                    $vals[] = "'{$v}'";
                } else {
                    if (!empty($data)) $data .= ", ";
                    $data .= " `{$k}` = '{$v}' ";
                }
            }
        }
        if (isset($cols) && isset($vals)) {
            $cols_join = implode(",", $cols);
            $vals_join = implode(",", $vals);
        }
        if (empty($id)) {
            $sql = "INSERT INTO `cpay_list` ({$cols_join}) VALUES ($vals_join)";
        } else {
            $sql = "UPDATE `cpay_list` set {$data} where pay_id = '{$id}'";
        }
        //@$check= $this->query("SELECT COUNT(client_id) as count from `cclient_list` where client_id != '{$id}'")->fetchArray()['count'];
        @$check = $this->query("SELECT COUNT(pay_id) as count from `cpay_list` where `check_no` = '{$check_no}' " . ($id > 0 ? " and pay_id != '{$id}'" : ""))->fetchArray()['count'];
        if (@$check > 0) {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Pay id already exists.';
        } else {
            @$save = $this->query($sql);

            $this->query("UPDATE `ctransaction_list` set arrears= arrears - $amount where transaction_id = '{$transaction_id}'");
            //$this->query("UPDATE `ctransaction_list` set arrears= 0 where transaction_id = '{$transaction_id}'");

            if ($save) {
                $resp['status'] = "success";
                if (empty($id))
                    $resp['msg'] = "Pay successfully saved.";
                else
                    $resp['msg'] = "Pay successfully updated.";
            } else {
                $resp['status'] = "failed";
                if (empty($id))
                    $resp['msg'] = "Saving New Pay Failed.";
                else
                    $resp['msg'] = "Updating Pay Failed.";
                $resp['error'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_pay()
    {
        extract($_POST);

        @$delete = $this->query("DELETE FROM `cpaylist` where pay_id = '{$id}'");
        if ($delete) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Pay successfully deleted.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }

    function done_pay()
    {
        extract($_POST);


        if ($done_a) {
            $resp['status'] = 'success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Arreas Sucessfully Done.';
        } else {
            $resp['status'] = 'failed';
            $resp['error'] = $this->lastErrorMsg();
        }
        return json_encode($resp);
    }


    ////////////////////////
}



$a = isset($_GET['a']) ? $_GET['a'] : '';
$action = new Actions();
switch ($a) {
    case 'login':
        echo $action->login();
        break;
    case 'login':
        echo $action->login();
        break;
    case 'logout':
        echo $action->logout();
        break;
    case 'logout':
        echo $action->logout();
        break;
    case 'save_user':
        echo $action->save_user();
        break;
    case 'delete_user':
        echo $action->delete_user();
        break;
    case 'update_credentials':
        echo $action->update_credentials();
        break;
    case 'save_category':
        echo $action->save_category();
        break;
    case 'delete_category':
        echo $action->delete_category();
        break;
    case 'save_supplier':
        echo $action->save_supplier();
        break;
    case 'delete_supplier':
        echo $action->delete_supplier();
        break;
    case 'save_product':
        echo $action->save_product();
        break;
    case 'delete_product':
        echo $action->delete_product();
        break;
    case 'save_stock':
        echo $action->save_stock();
        break;
    case 'delete_stock':
        echo $action->delete_stock();
        break;
    case 'save_transaction':
        echo $action->save_transaction();
        break;
    case 'delete_transaction':
        echo $action->delete_transaction();
        break;
    case 'done_arreas':
        echo $action->done_arreas();
        break;
    case 'save_client':
        echo $action->save_client();
        break;
    case 'delete_client':
        echo $action->delete_client();
        break;
    case 'save_ctransaction':
        echo $action->save_ctransaction();
        break;
    case 'delete_ctransaction':
        echo $action->delete_ctransaction();
        break;
    case 'save_pay':
        echo $action->save_pay();
        break;
    case 'delete_pay':
        echo $action->delete_pay();
        break;
    case 'delete_pay':
        echo $action->done_pay();
        break;
    default:
        // default action here
        break;
}
