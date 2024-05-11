
<?php 
$dfrom = isset($_GET['date_from']) ? $_GET['date_from'] : date("Y-m-d",strtotime(date("Y-m-d")." -1 week"));
$dto = isset($_GET['date_to']) ? $_GET['date_to'] : date("Y-m-d");
?>
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Sales Report</h3>
    </div>
    <div class="card-body">
        <h5>Filter</h5>
        <div class="row align-items-end">
            <div class="form-group col-md-2">
                <label for="date_from" class="control-label">Date From</label>
                <input type="date" name="date_from" id="date_from" value="<?php echo $dfrom ?>" class="form-control rounded-0">
            </div>
            <div class="form-group col-md-2">
                <label for="date_to" class="control-label">Date To</label>
                <input type="date" name="date_to" id="date_to" value="<?php echo $dto ?>" class="form-control rounded-0">
            </div>
            <div class="form-group col-md-4 d-flex">
                <div class="col-auto">
                    <button class="btn btn-primary rounded-0" id="filter" type="button"><i class="fa fa-filter"></i> Filter</button>
                    <button class="btn btn-success rounded-0" id="print" type="button"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
        </div>
        <hr>
        <div class="clear-fix mb-2"></div>
        <div id="outprint">
        <table class="table table-hover table-striped table-bordered">
            <colgroup>
                <col width="5%">
                <col width="10%">
                <col width="10%">
                <col width="5%">
                <col width="15%">
                <col width="15%">
                <col width="15%">
                <col width="15%">
                <col width="10%">
                <col width="10%">
            </colgroup>
            <thead>
                <tr>
                    <th class="text-center p-0">#</th>
                    <th class="text-center p-0">Date</th>
                    <th class="text-center p-0">Receipt No</th>
                    <th class="text-center p-0">Items</th>
                    <th class="text-center p-0">Method</th>
                    <th class="text-center p-0">Total Amount</th>
                    <th class="text-center p-0">Total Profit</th>
                    <th class="text-center p-0">Amount in arrears</th>
                    <th class="text-center p-0">Processed By</th>
                    <th class="text-center p-0">Arrears Done</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $user_where = "";
                if($_SESSION['type'] != 1){
                    $user_where = " and t.user_id = '{$_SESSION['user_id']}' ";
                    $user_c = " and user_id = '{$_SESSION['user_id']}' ";
                }
                $sql = "SELECT t.*,u.fullname FROM  `transaction_list` t inner join user_list u on t.user_id = u.user_id where date(t.date_added) between '{$dfrom}' and '{$dto}' {$user_where}  order by strftime('%s',date_added) desc";
                $qry = $conn->query($sql);
                
                $i = 1;
                    while($row = $qry->fetchArray()):
                        $items = $conn->query("SELECT count(transaction_id) as `count` FROM `transaction_items` where transaction_id = '{$row['transaction_id']}' ")->fetchArray()['count'];
                        
                ?>
                
                <tr>
                    <td class="text-center p-0"><?php echo $i++; ?></td>
                    <td class="py-0 px-1"><?php echo date("Y-m-d",strtotime($row['date_added'])) ?></td>
                    <td class="py-0 px-1"><a href="javascript:void(0)" class="view_data" data-id="<?php echo $row['transaction_id'] ?>"><?php echo $row['receipt_no'] ?></a></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($items) ?></td>
                    <td class="py-0 px-1 text-end"><?php echo $row['customer'] ?></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($row['total'],2) ?></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($row['t_profit'],2) ?></td>
                    <td class="py-0 px-1 text-end"><?php echo number_format($row['arrears'],2) ?></td>
                    <td class="py-0 px-1"><?php echo $row['fullname'] ?></td>
                    <td class="py-0 px-1">
                    <?php echo $row['arrears'] > 0? "<a href='javascript:void(0)' class='arrears me-1' data-tid = '".$row['transaction_id']."' data-resno = '".$row['receipt_no']."' data-arrears = '".$row['arrears']."'> Arrears Done</a>":'' ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if(!$qry->fetchArray()): ?>
                    <th colspan="6"><center>No Transaction listed in selected date.</center></th>
                <?php endif; ?>
               
            </tbody>
            
                <tr class="bg-dark bg-opacity-25 text-bold"><td class="px-1 py-0 text-end" colspan='4'><H6>Total</H6></td>
                <td class="px-1 py-0 text-end"><?php 
                if($_SESSION['type']==1){$total = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` where date(date_added) between '{$dfrom}' and '{$dto}'")->fetchArray()['total'];}
                else{$total = $conn->query("SELECT sum(total) as `total` FROM `transaction_list` where date(date_added) between '{$dfrom}' and '{$dto}' {$user_c}")->fetchArray()['total'];}
                echo number_format($total,2) ;
                    ?>
                </td>
                <td class="px-1 py-0 text-end"><?php 
                if($_SESSION['type']==1){$t_profit = $conn->query("SELECT sum(t_profit) as `total_profit` FROM `transaction_list` where date(date_added) between '{$dfrom}' and '{$dto}'")->fetchArray()['total_profit'];}
                else{$t_profit = $conn->query("SELECT sum(t_profit) as `total_profit` FROM `transaction_list` where date(date_added) between '{$dfrom}' and '{$dto}' {$user_c}")->fetchArray()['total_profit'];}
                echo number_format($t_profit,2) ;
                    ?>
                </td>
                <td class="px-1 py-0 text-end"><?php 
                if($_SESSION['type']==1){$arrears = $conn->query("SELECT sum(arrears) as `total_arrears` FROM `transaction_list` where date(date_added) between '{$dfrom}' and '{$dto}'")->fetchArray()['total_arrears'];}
                else{$arrears = $conn->query("SELECT sum(arrears) as `total_arrears` FROM `transaction_list` where date(date_added) between '{$dfrom}' and '{$dto}' {$user_c}")->fetchArray()['total_arrears'];}
                echo number_format($arrears,2) ;
                    ?>
                </td>
            </tr>
        </table>
        </div>
    </div>
</div>
<script>


    $(function(){
        //addnew
        $('.arrears').click(function(){
            // uni_modal('Arrears Done for <span class="text-primary">'+$(this).attr('data-resno')+" - Rs."+$(this).attr('data-arrears')+"</span>","manage_arrears.php?transaction_id="+$(this).attr('data-tid'))
            _conf("Are you sure to Done Arrears Payment Rs. <b>"+$(this).attr('data-arrears')+"</b> from Recipt no <b>"+$(this).attr('data-resno')+"</b> ?",'done_data',[$(this).attr('data-tid')])
        })

        /////////////////

        $('.view_data').click(function(){
            uni_modal('Receipt',"view_receipt.php?view_only=true&id="+$(this).attr('data-id'),'')
        })
        $('#filter').click(function(){
            location.href="./?page=sales_report&date_from="+$('#date_from').val()+"&date_to="+$('#date_to').val();
        })
        
        $('table td,table th').addClass('align-middle')

        $('#print').click(function(){
            var h = $('head').clone()
            var p = $('#outprint').clone()
            var el = $('<div>')
            el.append(h)
            if('<?php echo $dfrom ?>' == '<?php echo $dto ?>'){
                date_range = "<?php echo date('M d, Y',strtotime($dfrom)) ?>";
            }else{
                date_range = "<?php echo date('M d, Y',strtotime($dfrom)).' - '.date('M d, Y',strtotime($dto)) ?>";
            }
            el.append("<div class='text-center lh-1 fw-bold'> Amarasinghe Tyre Traders Sales Report<br/>As of<br/>"+date_range+"</div><hr/>")
            p.find('a').addClass('text-decoration-none')
            el.append(p)
            var nw = window.open("","","width=900,height=900")
                nw.document.write(el.html())
                nw.document.close()
                setTimeout(() => {
                    nw.print()
                    setTimeout(() => {
                        nw.close()
                    }, 150);
                }, 200);
        })
         $('table').dataTable({
             columnDefs: [
                 { orderable: false, targets:3 }
             ]
         })
    })
    

    //new
    function done_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./Actions.php?a=done_arreas',
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
    /////////////////////////////
</script>