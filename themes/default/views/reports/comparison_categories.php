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
    var checkedCategoriesId = [];
    var categoriesArr = [];
    var categoriesArrayToDisplay = [];
    $(document).ready(function () {
        $("#clearAll").click(function(){
            clearAllSelection();
        })
        $('#PrRData').on('click', '.category_link_comparison td:nth-child(8) input', function (e) {
            selectProductToCompare(e)
        });
        oTable = $('#PrRData').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('reports/getCategoriesToComparison/?v=1'.$v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                nRow.id = aData[7];
                nRow.className = "category_link_comparison";
                $(nRow.lastChild).css("text-align", "center");
                $(nRow.lastChild).attr("class", "squaredFour_" + aData[7]);
                var inputId = "squaredFour_" + aData[7];
                eventFilterFired(inputId);
                return nRow;
            },
            "aoColumns": [null, null, {"mRender": decimalFormat, "bSearchable": false}, {
                "mRender": decimalFormat,
                "bSearchable": false
            }, {"mRender": currencyFormat, "bSearchable": false}, {
                "mRender": currencyFormat,
                "bSearchable": false
            }, {"mRender": currencyFormat, "bSearchable": false}, {"bSortable": false,
                "mRender": function (data, type, row) {
                    var checkbox = '<input check="false" type="checkbox" class="editor-active squaredFour_' + row[7] + '" id="squaredFour_' + row[7] + '">' +
                        '<label class="squaredFour_label" for="squaredFour_' + row[7] + '"></label>';
                    return checkbox;
                },
                className: "dt-body-center"
            }
            ],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var pQty = 0, sQty = 0, pAmt = 0, sAmt = 0, pl = 0;
                for (var i = 0; i < aaData.length; i++) {
                    pQty += parseFloat(aaData[aiDisplay[i]][2]);
                    sQty += parseFloat(aaData[aiDisplay[i]][3]);
                    pAmt += parseFloat(aaData[aiDisplay[i]][4]);
                    sAmt += parseFloat(aaData[aiDisplay[i]][5]);
                    pl += parseFloat(aaData[aiDisplay[i]][6]);

                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[2].innerHTML = decimalFormat(parseFloat(pQty));
                nCells[3].innerHTML = decimalFormat(parseFloat(sQty));
                nCells[4].innerHTML = currencyFormat(parseFloat(pAmt));
                nCells[5].innerHTML = currencyFormat(parseFloat(sAmt));
                nCells[6].innerHTML = currencyFormat(parseFloat(pl));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('category_code');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('category_name');?>]", filter_type: "text", data: []},
        ], "footer");
    });

    function getProductDataById(rowID) {

        categoriesArr.forEach(function (entry, index) {
            if (entry.categoryId === rowID) {
                categoriesArrayToDisplay.push(entry)
                setInGraph(categoriesArrayToDisplay);
            }
        })
    }
    function removeAllItems(rowsIdArr) {

        rowsIdArr.forEach(function (item, index) {
            removeCategoryId(item);
            categoriesArrayToDisplay.splice(item, 1);
            setInGraph(categoriesArrayToDisplay);
        })
    }
    function removeItemByRowId(rowId) {

        categoriesArr.forEach(function (entry, index) {
            if (entry.categoryId === rowId) {
                var index = categoriesArrayToDisplay.indexOf(entry);
                categoriesArrayToDisplay.splice(index, 1);
                setInGraph(categoriesArrayToDisplay);
            }
        })
    }

    function addCategoriesId(rowId) {

        checkedCategoriesId.push(rowId);
    }

    function removeCategoryId(rowId) {

        checkedCategoriesId.splice(rowId, 1);
    }

    function clearAllSelection(){

        var rowIdsArr=[];
        for(i=0;i<checkedCategoriesId.length;i++){

            var rowId = checkedCategoriesId[i];
            rowIdsArr.push(rowId);
            var row = $('#' + rowId);
            checkControl = $(row).attr("check","false");
            $(row).removeAttr("checked");
        }
        removeAllItems(rowIdsArr);
    }

    function selectProductToCompare(e) {
        var rowId = e.target.id;
        var checkControl;
        var formatRowId = rowId.replace(/.+_/, "");
        if ($(e.target).attr("check") === "false") {
            checkControl = $(e.target).attr("check", "true");
            getProductDataById(formatRowId);
            addCategoriesId(rowId);
        } else {
            checkControl = $(e.target).attr("check", "false");
            $(e.target).removeAttr("checked");
            removeItemByRowId(formatRowId);
           removeCategoryId(rowId);
        }
    }

    function eventFilterFired(inputId) {

        var getRows = $('#PrRData tbody tr td:last-child input');
        if (getRows.length === 0 && checkedCategoriesId.length > 0) {
            for (i = 0; i < checkedCategoriesId.length; i++) {
                var checkBoxId = checkedCategoriesId[i];
                setCheckInCheckBox(inputId, checkBoxId);
            }
        }
        getRows.each(function (index, element) {
            var getRowsId = $(element).attr("id");
            for (i = 0; i < checkedCategoriesId.length; i++) {
                var checkBoxId = checkedCategoriesId[i];
                setCheckInCheckBox(inputId, checkBoxId);
            }
        });
    }
    function setCheckInCheckBox(inputId, checkBoxId) {

        if (inputId === checkBoxId) {
            if (window.Promise) {
                var promise = new Promise(function (resolve, reject) {
                    var id = checkBoxId;
                    resolve(id);
                });
                promise.then(function (data) {
                    var x = document.getElementById(data)
                    var checkIfElementExists = document.getElementById(data);
                    if (checkIfElementExists === null) {
                        return;
                    }
                    document.getElementById(data).checked = "true";
                    document.getElementById(data).setAttribute("check", "true");
                }, function (error) {
                    console.log('Promise rejected.');
                    console.log(error.message);
                });
            }
        }
    }
    function sendRequest() {
        var json;
        $.ajax({
            url: '<?= site_url('reports/getCategoriesToComparison/?v=1'.$v) ?>', success: function (result) {
                json = JSON.parse(result);
                formatJsonData(json)
            }
        });
    }
    function formatJsonData(json) {

        var productObj = {}
        var productData = json.aaData;
        productData.forEach(function (entry, index) {
            var categoryId = entry[7];
            var categoryCode = entry[0];
            var categoryName = entry[1];
            var purchasedMoney = Number(entry[2]);
            var sold = Number(entry[3]);
            var purchasedAmount = Number(entry[4]);
            var soldAmount = Number(entry[5]);
            var profitLoss = Number(entry[6]);
            productObj = {
                "categoryId": categoryId,
                "categoryCode": categoryCode,
                "categoryName": categoryName,
                "purchasedMoney": purchasedMoney,
                "sold": sold,
                "soldAmount":soldAmount,
                "purchasedAmount": purchasedAmount,
                "profitLoss": profitLoss
            }
            categoriesArr.push(productObj);
        });
    }

    function setInGraph(categoriesArr) {

        var data = [];
        var series = [];
        var legend = {}
        var objSeries = {};
        var nameLength = 0;
        console.log(categoriesArr)
        for (i = 0; i < categoriesArr.length; i++) {
            data.push(categoriesArr[i].purchasedAmount, categoriesArr[i].soldAmount, categoriesArr[i].profitLoss);
            objSeries = {"type": "spline", "name": categoriesArr[i].categoryName, "data": data};
            data = [];
            series.push(objSeries);
        }
        var legendName = ["<?= lang('comparison_purchased') ?>", "<?= lang('comparison_sold') ?>", "<?= lang('comparison_balance') ?>"];
        $('#prod-chart').highcharts({
            chart: {},
            credits: {enabled: false},
            title: {text: ''},
            xAxis: {
                categories: legendName, crosshair: {
                    enabled: true
                }
            },
            yAxis: {tickInterval: 100, title: ""},
            tooltip: {
                formatter: function () {
                    return '<table>' +
                        ' <tr>' +
                        '<th style="text-align: center">' + this.key + '</th>' +
                        '</tr>' +
                        '<tr>' +
                        '<td>' + this.series.name + '</td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td>' + currencyFormat(this.y) + '</td>' +
                        '</tr>' +
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
                <div id="form">
                   <?php echo form_open("reports/comparison_categories"); ?>
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
                <div class="clearfix"></div>
                <div class="box">
                    <div class="box-header">
                        <h2 class="blue"><i
                                class="fa-fw fa fa-barcode"></i><?= lang('comparison_between_categories'); ?> <?php
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
                    <div id="prod-chart"
                         style="width:100%; height:450px;position:relative;border: 2px solid;direction: ltr;"></div>
                </div>
                <div class="table-responsive">
                    <table id="PrRData"
                           class="table table-striped table-bordered table-condensed table-hover dfTable reports-table"
                           style="margin-bottom:5px;">
                        <thead>
                        <tr class="active">
                            <th><?= lang("category_code"); ?></th>
                            <th><?= lang("category_name"); ?></th>
                            <th><?= lang("purchased"); ?></th>
                            <th><?= lang("sold"); ?></th>
                            <th><?= lang("purchased_amount"); ?></th>
                            <th><?= lang("sold_amount"); ?></th>
                            <th><?= lang("profit_loss"); ?></th>
                            <th>
                                <div
                                    style="  text-align: center;    padding-left: 16px;"><?= lang("show_graph"); ?></div>
                                <div style=" display: table-cell;    width: 96%;  ">
                                    <button style="border-radius: 10px !important; color:black;font-weight: bold"
                                            id="clearAll" class=" btn btn-sm"
                                            value="clear"><?= lang("comparison_clear"); ?></button>
                                </div>
                            </th>
                        </tr>
                        </thead>
                        <tbody colspan="7">
                        <tr>
                            <td colspan="7" rowspan="7"
                                class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th></th>
                            <th></th>
                            <th><?= lang("purchased"); ?></th>
                            <th><?= lang("sold"); ?></th>
                            <th><?= lang("purchased_amount"); ?></th>
                            <th><?= lang("sold_amount"); ?></th>
                            <th><?= lang("profit_loss"); ?></th>
                            <th></th>

                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>


<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
