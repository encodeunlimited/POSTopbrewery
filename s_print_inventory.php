<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="image/favicon.ico">
    <link rel="stylesheet" href="./Font-Awesome-master/css/all.min.css">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./DataTables/datatables.min.css">
    <script src="./DataTables/datatables.min.js"></script>
    <script src="./Font-Awesome-master/js/all.min.js"></script>
    <script src="./js/script.js"></script>
    <style>
        :root {
            --bs-success-rgb: 71, 222, 152 !important;
        }

        html,
        body {
            height: 100%;
            width: 100%;
        }

        main {
            height: 100%;
            display: flex;
            flex-flow: column;
        }

        #page-container {
            flex: 1 1 auto;
            overflow: auto;
        }

        #topNavBar {
            flex: 0 1 auto;
        }

        .thumbnail-img {
            width: 50px;
            height: 50px;
            margin: 2px
        }

        .truncate-1 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }

        .truncate-3 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .modal-dialog.large {
            width: 80% !important;
            max-width: unset;
        }

        .modal-dialog.mid-large {
            width: 50% !important;
            max-width: unset;
        }

        @media (max-width:720px) {

            .modal-dialog.large {
                width: 100% !important;
                max-width: unset;
            }

            .modal-dialog.mid-large {
                width: 100% !important;
                max-width: unset;
            }

        }

        .display-select-image {
            width: 60px;
            height: 60px;
            margin: 2px
        }

        img.display-image {
            width: 100%;
            height: 45vh;
            object-fit: cover;
            background: black;
        }

        /* width */
        ::-webkit-scrollbar {
            width: 5px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #888;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .img-del-btn {
            right: 2px;
            top: -3px;
        }

        .img-del-btn>.btn {
            font-size: 10px;
            padding: 0px 2px !important;
        }

        .body-bg {
            /* background-color: bisque; */
            background-color: #f1f1f1;
        }

        #uni_modal .modal-footer {
            display: none !important;
        }

        .grid-container {
            width: 100%;
            border-collapse: collapse;
        }

        .cell {
            padding: 10px;
            text-align: center;
        }

        tr:first-child .cell {
            text-align: center;
        }

        tr:last-child .cell {
            text-align: left;
        }
    </style>
</head>

<body class="body-bg">

    <div class="container-fluid">
        <div id="outprint_receipt">
            <div class="text-center fs-6 fs-bold">
                <span><img src="image\logoabc.png" width='100' height='100'></span><br>
                <span>
                    <h6 class="fw-bold">201303306948 (002282392-A)</h6>
                </span>
                <small>Tel: 03-42856778 | 019-6000671</small><br>
                <small>E-mail : topbrewery@gmail.com</small><br>
                <small>No. 24, jalan bunga tanjung 8a, taman muda, 68000 ampang, selangor.</small><br>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr class="text-dark">
                        <!-- <th class="py-0 px-1" style="font-size: small; text-align: center;">
                            <small class="fw-bold">No24. JLN BUNGA TANJUNG 8A</small><br>
                            <small class="fw-bold">TMN MUDA 56100 SELANGOR</small>
                        </th> -->
                    </tr>
                    <tr>
                        <td style="font-size: small;  text-align: center;">
                            Date & Time : <?php echo date("d-m-Y H:i:s") ?><br>
                        </td>
                    </tr>
                </thead>
            </table>

            <?php
            // Include your database connection file if needed

            // Start the session if not started already
            // session_start();
            require_once("DBConnection.php");

            // Retrieve data from the database and generate the table HTML
            $tableHTML = '<table class="table table-striped table-hover">';
            $tableHTML .= '<colgroup>
                    <col width="20%">
                    <col width="20%">
                    <col width="40%">
                    <col width="20%">
                </colgroup>';
            $tableHTML .= '<thead>
                    <tr>
                        <th class="py-0 px-1">Category</th>
                        <th class="py-0 px-1">Product Code</th>
                        <th class="py-0 px-1">Product Name</th>
                        <th class="py-0 px-1">Available Quantity</th>
                    </tr>
                </thead>';
            $tableHTML .= '<tbody>';

            function getMainItemDetails($product_id)
            {
                if (strpos($product_id, '*') !== false) {
                    list($main_id, $multiplier) = explode('*', $product_id);
                } else {
                    $main_id = $product_id;
                    $multiplier = 1; // Default multiplier if no asterisk is found
                }
                return [
                    'main_id' => $main_id,
                    'multiplier' => intval($multiplier)
                ];
            }

            $sql = "SELECT p.*, c.name AS cname FROM `product_list` p INNER JOIN `category_list` c ON p.category_id = c.category_id WHERE p.status = 1 AND p.category_id IN (1,6,9,10,12,14) ORDER BY p.category_id ASC";
            $qry = $conn->query($sql);
            while ($row = $qry->fetchArray()) {
                $stock_in = $conn->query("SELECT SUM(quantity) AS `total` FROM `stock_list` WHERE strftime('%s', `expiry_date` || '23:59:59') >= strftime('%s', CURRENT_TIMESTAMP) AND product_id = '{$row['product_id']}'")->fetchArray()['total'];
                // $stock_out = $conn->query("SELECT SUM(quantity) AS `total` FROM `transaction_items` WHERE product_id = '{$row['product_id']}'")->fetchArray()['total'];

                $stock_out = 0;

                // Calculate stock out
                $sql1 = "SELECT c.*, p.product_code as pcode FROM `product_list` p INNER JOIN `transaction_items` c ON p.product_id = c.product_id WHERE p.status = 1 ORDER BY `name` ASC";
                $qry1 = $conn->query($sql1);

                while ($row1 = $qry1->fetchArray()) {
                    $details = getMainItemDetails($row1['pcode']);

                    if ($details['main_id'] == $row['product_code']) {
                        if ($details['multiplier'] > 1) {
                            $stock_out += $details['multiplier'] * $row1['quantity'];
                        } else {
                            $stock_out += $row1['quantity'];
                        }
                    }
                }

                $stock_in = $stock_in > 0 ? $stock_in : 0;
                $stock_out = $stock_out > 0 ? $stock_out : 0;
                $qty = $stock_in - $stock_out;
                $qty = $qty > 0 ? $qty : 0;

                $details = getMainItemDetails($row['product_code']);

                if ($details['multiplier'] > 1) {
                } else {
                    $tableHTML .= '<td class="td py-0 px-1">' . $row['cname'] . '</td>';
                    $tableHTML .= '<td class="td py-0 px-1">' . $row['product_code'] . '</td>';
                    $tableHTML .= '<td class="td py-0 px-1">' . $row['name'] . '</td>';
                    $tableHTML .= '<td class="td py-0 px-1">' . $qty . '</td>';
                    $tableHTML .= '</tr>';
                }
            }
            $tableHTML .= '</tbody>';
            $tableHTML .= '</table>';

            echo $tableHTML;

            ?>