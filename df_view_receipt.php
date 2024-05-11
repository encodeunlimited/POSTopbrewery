<?php
session_start();
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM `transaction_list` where transaction_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<style>
    #uni_modal .modal-footer{
        display:none !important;
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
<div class="container-fluid">
    <div id="outprint_receipt">
        <div class="text-center fs-6 fs-bold">
            <!-- <span><img src="image\logoabc.png" width='250' height='100'></span><br> -->
            <!-- <span><h3 class="fw-bold">Amarasinghe Tyre Traders</h1></span> -->
            <!-- <small style="line-height:normal;" class="fw-bold">Tel : 077 9939697 | 011 2235863</small><br>
            <small style="line-height:normal;" class="fw-bold ">E-mail : amarasinghetyres@gmail.com</small><br> -->
        </div>
        <table class="table table-striped">
            <colgroup>
                <col width="50%">
                <col width="50%">
            </colgroup>  
            <thead>
            <tr class="text-dark">
                <!-- <th class="py-0 px-1" style="font-size: small; text-align: center;"><small class="fw-bold">Seeduwa Branch</small><br>
                    <small class="fw-bold">No 375B,Liyanagemulla,Seeduwa</small>
                </th>
                <th class="py-0 px-1" style="font-size: small; text-align: center;"><small class="fw-bold">Bopitiya Branch<br>
                    <small class="fw-bold">No 115/5/A,Bopitiya,Pamunugama</small>
                </th> -->
            </tr>
            <tr>
                <td style="font-size: small; text-align: left;">
                    Date : <?php echo date("Y-m-d",strtotime($date_added)) ?><br>
                    Receipt No : <?php echo $receipt_no ?>
                </td>
                <td style="font-size: small; text-align: left;">
                    Payment Method : <?php echo $customer ?><br>
                    <!-- Vehical No : <?php echo $vehical_no ?> -->
                </td>
            </tr>
            </thead>
            <tbody></tbody>
            </table>

        <table class="table table-striped">
            <colgroup>
                <col width="5%">
                <col width="85%">
                <col width="5%">
                <col width="5%">
            </colgroup>  
            <thead>
            <tr class="text-dark">
                <th class="py-0 px-1">QTY</th>
                <th class="py-0 px-1">Product</th>
                <th class="py-0 px-1">Amount</th>
                <th class="py-0 px-1">Disc(%)</th>
            </tr>
            </thead>
            <tbody>
                <?php 
                $items = $conn->query("SELECT i.*, p.name as pname,p.product_code FROM `transaction_items` i inner join product_list p on i.product_id = p.product_id where `transaction_id` = '{$transaction_id}'");
                while($row=$items->fetchArray()):
                ?>
                <tr>
                    <td class="px-1 py-0 align-middle"><?php echo $row['quantity'] ?></td>
                    <td class="px-1 py-0 align-middle">
                        <div class="fw light lh-1">
                            <small><?php echo $row['product_code'] ?></small><br>
                            <small><?php echo $row['pname'] ?></small>
                            
                            <small>(<?php echo number_format($row['price'],2) ?>)</small>
                        </div>
                    </td>
                    <td class="px-1 py-0 align-middle text-end"><?php echo number_format($row['price'] * $row['quantity'],2) ?></td>
                    <td class="px-1 py-0 align-middle text-end"><?php echo number_format($row['discount'] * $row['quantity'],2) ?></td>
                    
                </tr>
                <?php endwhile; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="px-1 py-0 " colspan="3">Total</th>
                    <th class="px-1 py-0 text-end"><?php echo number_format($t_discount+$total,2) ?></th>
                </tr>
                <?php if ($t_discount != 0): ?>
                <tr>
                    <th class="px-1 py-0" colspan="3">Your Discount(-)</th>
                    <th class="px-1 py-0 text-end"><?php echo number_format($t_discount,2) ?></th>
                </tr>
                <?php endif; ?>
                <?php if ($s_desc != 0): ?>
                <tr>
                    <th class="px-1 py-0" colspan="3">Special Discount(-)</th>
                    <th class="px-1 py-0 text-end"><?php echo number_format($s_desc,2) ?></th>
                </tr>
                <tr>
                    <th class="px-1 py-0" colspan="3">Total</th>
                    <th class="px-1 py-0 text-end"><?php echo number_format($total-$s_desc,2) ?></th>
                </tr>
                <?php endif; ?>
                <?php if ($arrears != 0): ?>
                <tr>
                    <th class="px-1 py-0" colspan="3">Your Arrears(-)</th>
                    <th class="px-1 py-0 text-end"><?php echo number_format($arrears,2) ?></th>
                </tr>
                <tr>
                    <th class="px-1 py-0" colspan="3">Gross Total</th>
                    <th class="px-1 py-0 text-end"><?php echo number_format($total-$arrears,2) ?></th>
                </tr>
                <?php endif; ?>
                <!-- <tr>
                    <th class="px-1 py-0" colspan="3">Tendered</th>
                    <th class="px-1 py-0 text-end"><?php echo number_format($tendered_amount,2) ?></th>
                </tr>
                <tr>
                    <th class="px-1 py-0" colspan="3">Change</th>
                    <th class="px-1 py-0 text-end"><?php echo number_format($change,2) ?></th>
                </tr> -->
            </tfoot>
        </table>
        <div class="text-center">
            
            <span class="fw-bolder" >Thank You, Come Again !</span><br>
            <small class="fw-bold lh-1">Software By</small><br>
            <small class="fw-bold lh-1">EncodeUnlimited | 0706291709 | www.encodeunlimited.com</small><br>
        </div>
    </div>
    
    <div class="w-100 d-flex justify-content-end mt-2">
        <?php if(isset($_GET['view_only']) && $_GET['view_only'] == true && $_SESSION['type'] == 1): ?>
        <button class="btn btn-sm btn-danger me-2 rounded-0" type="button" id="delete_data"><i class="fa fa-trash"></i> Delete</button>
        <?php endif; ?>
        <button class="btn btn-sm btn-success me-2 rounded-0" type="button" id="print_receipt"><i class="fa fa-print"></i> Print</button>
        <button class="btn btn-sm btn-dark rounded-0" type="button" data-bs-dismiss="modal">Close</button>
    </div>

</div>
<script>
    $(function(){
        $("#print_receipt").click(function(){
            var h = $('head').clone()
            var p = $('#outprint_receipt').clone()
            var el = $('<div>')
            el.append(h)
            el.append(p)
            var nw = window.open("","","width=500,height=900")
                nw.document.write(el.html())
                nw.document.close()
                setTimeout(() => {
                    nw.print()
                    setTimeout(() => {
                        nw.close()

                        $('#uni_modal').on('hide.bs.modal',function(){
                            if($(this).find('#outprint_receipt').length > 0 && '<?php echo !isset($_GET['view_only']) ?>' == 1){
                                location.reload()
                            }
                        })
                        if('<?php echo !isset($_GET['view_only']) ?>' == 1)
                        $('#uni_modal').modal('hide')
                    }, 150);
                }, 200);
        })
        $('#uni_modal').on('hide.bs.modal',function(){
            if($(this).find('#outprint_receipt').length > 0){
                location.reload()
            }
        })
        $('#uni_modal').modal('hide')
        $('#delete_data').click(function(){
            _conf("Are you sure to delete <b>"+<?php echo $receipt_no ?>+"</b>?",'delete_data',['<?php echo $transaction_id ?>'])
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./Actions.php?a=delete_transaction',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>