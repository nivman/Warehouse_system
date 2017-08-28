<script src="<?= $assets; ?>js/hc/highcharts.js"></script>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$v = "";

if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}


?>
<script>
    (function () {
    var productArr=[];
    var productsArrayToDisplay=[];
    var checkedProductsId=[];
    $(document).ready(function () {
        function spb(x) {
            v = x.split('__');
            return '('+formatQuantity2(v[0])+') <strong>'+formatMoney(v[1])+'</strong>';
        }
        $("#clearAll").click(function(){
            clearAllSelection();

        })
        $('#PrRData').on( 'click','.product_link_comparison td:nth-child(7) input', function (e) {
            selectProductToCompare(e)

        } );

             oTable = $('#PrRData').dataTable({

             bFilter: true,
             "aaSorting": [[3, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,

            'sAjaxSource': '<?= site_url('reports/getProductsReport/?v=1'.$v) ?>',

            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[6];
                nRow.className = "product_link_comparison";
                $(nRow.lastChild).css("text-align","center");
                $(nRow.lastChild).attr("class","squaredFour_"+aData[6]);
                var inputId= "squaredFour_"+aData[6];
                eventFilterFired(inputId);
                return nRow;
            },

            "aoColumns": [null, null, {"mRender": spb}, {"mRender": spb}, {"mRender": currencyFormat}, {"mRender": spb},{"bSortable": false,"mRender": function ( data, type, row ) {

                        var checkbox = '<input check="false" type="checkbox" class="editor-active squaredFour_'+row[6]+'" id="squaredFour_'+row[6]+ '">' +
                         '<label class="squaredFour_label" for="squaredFour_'+row[6]+ '"></label>';
                        return checkbox;

                },
                className: "dt-body-center"
            }],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var pq = 0, sq = 0, bq = 0, pa = 0, sa = 0, ba = 0, pl = 0;
                for (var i = 0; i < aaData.length; i++) {
                    p = (aaData[aiDisplay[i]][2]).split('__');
                    s = (aaData[aiDisplay[i]][3]).split('__');
                    b = (aaData[aiDisplay[i]][5]).split('__');
                    pq += parseFloat(p[0]);
                    pa += parseFloat(p[1]);
                    sq += parseFloat(s[0]);
                    sa += parseFloat(s[1]);
                    bq += parseFloat(b[0]);
                    ba += parseFloat(b[1]);
                    pl += parseFloat(aaData[aiDisplay[i]][4]);

                }

                var nCells = nRow.getElementsByTagName('th');
                nCells[2].innerHTML = '<div class="text-right">('+formatQuantity2(pq)+') '+formatMoney(pa)+'</div>';
                nCells[3].innerHTML = '<div class="text-right">('+formatQuantity2(sq)+') '+formatMoney(sa)+'</div>';
                nCells[4].innerHTML = currencyFormat(parseFloat(pl));
                nCells[5].innerHTML = '<div class="text-right">('+formatQuantity2(bq)+') '+formatMoney(ba)+'</div>';
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('product_code');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('product_name');?>]", filter_type: "text", data: []},
        ], "footer");

    });
    function selectProductToCompare(e){
        var rowId =e.target.id;
        var checkControl;
        var formatRowId= rowId.replace(/.+_/,"");
        if($(e.target).attr("check")==="false"){
            checkControl = $(e.target).attr("check","true");
            getProductDataById(formatRowId);
            addProductId(rowId);
        }else{
            checkControl = $(e.target).attr("check","false");
            $(e.target).removeAttr("checked");
            removeItemByRowId(formatRowId);
            removeProductId(rowId);
        }
        }
     function clearAllSelection(){
        var rowIdsArr=[];

         for(i=0;i<checkedProductsId.length;i++){

             var rowId = checkedProductsId[i];
             rowIdsArr.push(rowId);
             var row = $('#' + rowId);
             checkControl = $(row).attr("check","false");
             $(row).removeAttr("checked");
         }
         removeAllItems(rowIdsArr);

     }
     function eventFilterFired( inputId ) {
         var getRows = $('#PrRData tbody tr td:last-child input');
         if(getRows.length===0 && checkedProductsId.length>0){
             for(i=0;i<checkedProductsId.length;i++){
                 var checkBoxId = checkedProductsId[i];
                 setCheckInCheckBox(inputId,checkBoxId);
             }
         }
         getRows.each(function(index,element ) {
            var getRowsId = $(element).attr("id");
            for(i=0;i<checkedProductsId.length;i++){
                var checkBoxId = checkedProductsId[i];
                setCheckInCheckBox(inputId,checkBoxId);
            }
         });
    }
        function setCheckInCheckBox(inputId,checkBoxId){
            if(inputId===checkBoxId){
                if (window.Promise) {
                    var promise = new Promise(function(resolve, reject) {
                        var id =checkBoxId;
                        resolve(id);

                    });
                    promise.then(function(data) {
                        var x = document.getElementById(data)
                        var checkIfElementExists = document.getElementById(data);
                        if(checkIfElementExists===null){
                            return;
                        }
                        document.getElementById(data).checked="true";
                        document.getElementById(data).setAttribute("check","true");
                    }, function(error) {
                        console.log('Promise rejected.');
                        console.log(error.message);
                    });
                }
            }
        }
    function addProductId(rowId){

        checkedProductsId.push(rowId);

    }
    function removeProductId(rowId){
            checkedProductsId.splice(rowId, 1);
    }
    function removeAllItems(rowsIdArr){
        rowsIdArr.forEach(function(item,index){
                 removeProductId(item);
                productsArrayToDisplay.splice(item,1);
                setInGraph(productsArrayToDisplay);

        })

    }
    function removeItemByRowId(rowId){
        productArr.forEach(function(entry,index){
            if(entry.prodId===rowId){
                productsArrayToDisplay.splice(entry,1)
                setInGraph(productsArrayToDisplay);
            }
        })
    }
    function getProductDataById(rowID){
        productArr.forEach(function(entry,index){
            if(entry.prodId===rowID){
                productsArrayToDisplay.push(entry)
                setInGraph(productsArrayToDisplay);
            }
        })
    }

    function sendRequest(){
        var json;
        $.ajax({url: '<?= site_url('reports/getProductsReport/?v=1'.$v) ?>', success: function(result){
         json = JSON.parse(result);
         formatJsonData(json)
        }});
    }
    function formatJsonData(json){

        var productObj={}
        var productData = json.aaData;
        productData.forEach(function(entry,index) {
            var formatPurchased  = entry[2].split('__');
            var purchasedMQty = Number(formatPurchased[0]);
            var purchasedMoney = Number(formatPurchased[1]);
            var formatSold  = entry[3].split('__');
            var soldMoney = Number(formatSold[1]);
            var soldMQty = Number(formatSold[0]);
            var prodStockData  = entry[5].split('__');
            var prodStockMoney = Number(prodStockData[1]);
            var prodStockQty = Number(prodStockData[0]);
            var prodBalance   = Number(entry[4]);
            var prodName = entry[1];
            var prodCode = entry[0];
            var prodId = entry[6];
            productObj = {"prodId":prodId,"prodName":prodName,"prodCode":prodCode,"purchasedMoney":purchasedMoney,"purchasedMQty":purchasedMQty,"soldMoney":soldMoney,"soldMQty":soldMQty,"prodBalance":prodBalance,"prodStockMoney":prodStockMoney,"prodStockQty":prodStockQty}
            productArr.push(productObj);

        });

    }

    function setInGraph(productArr){
        var data =[];
        var series=[];
        var legend={}
        var objSeries={};
        var nameLength=0;
        for(i=0;i<productArr.length;i++){
            data.push(productArr[i].purchasedMoney,productArr[i].soldMoney,productArr[i].prodBalance);
            objSeries = {"type":"spline","name":productArr[i].prodName,"data":data};
            data =[];
            series.push(objSeries);
        }


        var legendName = ["<?= lang('comparison_purchased') ?>","<?= lang('comparison_sold') ?>","<?= lang('comparison_balance') ?>"];
        $('#prod-chart').highcharts({
            chart: {},
            credits: {enabled: false},
            title: {text: ''},
            xAxis: {categories: legendName, crosshair: {
                enabled: true
            }},
            yAxis: {tickInterval: 100, title: ""},
            tooltip: {
                formatter: function () {
                    return '<table>'+
                   ' <tr>'+
                    '<th style="text-align: center">'+ this.key +'</th>'+

                    '</tr>'+
                    '<tr>'+
                    '<td>'+this.series.name+'</td>'+

                    '</tr>'+
                    '<tr>'+
                    '<td>' +  currencyFormat(this.y) + '</td>'+
                    '</tr>'+

                    '</table>'

                },
                useHTML: true, borderWidth: 2, shadow: true, valueDecimals: site.settings.decimals,
                style: {fontSize: '14px', padding: '0', color: 'black'}
            },
            legend: {
                rtl: true,
                floating: true,
                verticalAlign: 'top',
                layout: 'vertical',
                itemWidth: 70,
                symbolPadding: 10,
                symbolWidth: 5,
                align: 'right',
                x: 20,
                y: 0
            },
            series
        });
    }

    sendRequest();
    })()
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });

    });
</script>
<div class="box">
    <div class="row">
        <div class="col-lg-12">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-barcode"></i><?= lang('comparison_between_products'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div id="form">
        <?php echo form_open("reports/comparison"); ?>
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <?= lang("start_date", "start_date"); ?>
                    <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="inputs-report-from form-control datetime" id="start_date"'); ?>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <?= lang("end_date", "end_date"); ?>
                    <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="inputs-report-from form-control datetime" id="end_date"'); ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div
                class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
        </div>
        <?php echo form_close(); ?>
    </div>

    <div id="prod-chart" style="width:100%; height:450px;position:relative;border: 2px solid;direction: ltr;"></div>
        </div>
        </div>
</div>


<div class="table-responsive">

    <table id="PrRData"
           class="table table-striped table-bordered table-condensed table-hover dfTable reports-table"
           style="margin-bottom:5px;">
        <thead>
        <tr class="active">

            <th><?= lang("product_code"); ?></th>
            <th><?= lang("product_name"); ?></th>
            <th><?= lang("purchased"); ?></th>
            <th><?= lang("sold"); ?></th>
            <th><?= lang("profit_loss"); ?></th>
            <th><?= lang("stock_in_hand"); ?></th>
            <th><div  style=" display: table-cell; width: 100%;    text-align: left;    padding-left: 16px;"><?= lang("show_graph"); ?></div><div style=" display: table-cell;    width: 50%;    vertical-align: top;"><button style="border-radius: 10px !important; color:black;font-weight: bold"  id="clearAll" class=" btn btn-sm" value="clear"><?= lang("comparison_clear"); ?></button></div></th>

        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="6" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
        </tr>
        </tbody>
        <tfoot class="dtFilter">
        <tr class="active">
            <th></th>
            <th></th>
            <th><?= lang("purchased"); ?></th>
            <th><?= lang("sold"); ?></th>
            <th><?= lang("profit_loss"); ?></th>
            <th><?= lang("stock_in_hand"); ?></th>
        </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>

