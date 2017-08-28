<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script src="<?= $assets; ?>js/hc/highcharts.js"></script>
<script src="<?= $assets; ?>js/hc/highcharts-3d.js"></script>

<?php
function row_status($x)
{
    if ($x == null) {
        return '';
    } elseif ($x == 'pending') {
        return '<div class="text-center"><span class="label label-warning">' . lang($x) . '</span></div>';
    } elseif ($x == 'completed' || $x == 'paid' || $x == 'sent' || $x == 'received') {
        return '<div class="text-center"><span class="label label-success">' . lang($x) . '</span></div>';
    } elseif ($x == 'partial' || $x == 'transferring') {
        return '<div class="text-center"><span class="label label-info">' . lang($x) . '</span></div>';
    } elseif ($x == 'due') {
        return '<div class="text-center"><span class="label label-danger">' . lang($x) . '</span></div>';
    } else {
        return '<div class="text-center"><span class="label label-default">' . lang($x) . '</span></div>';
    }
}

?>
<?php if (($Owner || $Admin) && $chatData) {
    $prevmonth = date('M-Y', strtotime('-1 month'));
    $engmonth = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Fri');
    $hebmonth = array('ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'ספטמבר', 'אוקטובר', 'נובמבר', 'דצמבר', 'ינואר');
    if ($this->Settings->user_language == 'hebrew') {
        $setprevmonth = str_replace($engmonth, $hebmonth, $prevmonth);
    } else {
        $setprevmonth = $prevmonth;

    }

    foreach ($chatData as $month_sale) {
        $month = date('M-Y', strtotime($month_sale->month));
        if ($this->Settings->user_language == 'hebrew') {
            $hebmonths = str_replace($engmonth, $hebmonth, $month);
            $months[] = $hebmonths;
            $setmonth = $hebmonths;
        } else {
            $month = date('M-Y', strtotime($month_sale->month));
            $months[] = $month;
            $setmonth = $month;
        }
        $msales[] = $month_sale->sales;
        $mtax1[] = $month_sale->tax1;
        $mtax2[] = $month_sale->tax2;
        $mpurchases[] = $month_sale->purchases;
        $mtax3[] = $month_sale->ptax;
    }
    ?>
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-lg-12">
            <div class="box">

                <div class="box-content">


                    <div class="col-lg-1 col-md-2 col-xs-6">
                        <a class="turquoiseOne white quick-button small" href="<?= site_url('products') ?>">
                            <p class="dashboard-button"><?= lang('products') ?></p>
                        </a>
                    </div>
                    <div class="col-lg-1 col-md-2 col-xs-6">
                        <a class="turquoiseOne white quick-button small" href="<?= site_url('sales') ?>">
                            <p class="dashboard-button"><?= lang('sales') ?></p>
                        </a>
                    </div>

                    <div class="col-lg-1 col-md-2 col-xs-6">
                        <a class="turquoiseOne white quick-button small" href="<?= site_url('quotes') ?>">
                            <p class="dashboard-button"><?= lang('quotes') ?></p>
                        </a>
                    </div>

                    <div class="col-lg-1 col-md-2 col-xs-6">
                        <a class="turquoiseOne white quick-button small" href="<?= site_url('purchases') ?>">
                            <p class="dashboard-button"><?= lang('purchases') ?></p>
                        </a>
                    </div>
                    <div class="col-lg-1 col-md-2 col-xs-6">
                        <a class="turquoiseOne white quick-button small" href="<?= site_url('purchases/expenses'); ?>">
                            <p class="dashboard-button"> <?= lang('expenses'); ?></p>
                        </a>
                    </div>
                    <div class="col-lg-1 col-md-2 col-xs-6">
                        <a class="turquoiseOne white quick-button small" href="<?= site_url('transfers') ?>">
                            <p class="dashboard-button"><?= lang('transfers') ?></p>
                        </a>
                    </div>

                    <div class="col-lg-1 col-md-2 col-xs-6">
                        <a class="turquoiseOne white quick-button small" href="<?= site_url('customers') ?>">
                            <p class="dashboard-button"><?= lang('customers') ?></p>
                        </a>
                    </div>

                    <div class="col-lg-1 col-md-2 col-xs-6">
                        <a class="turquoiseOne white quick-button small" href="<?= site_url('suppliers') ?>">
                            <p class="dashboard-button"><?= lang('suppliers') ?></p>
                        </a>
                    </div>

                    <div class="col-lg-1 col-md-2 col-xs-6">
                        <a class="turquoiseOne white quick-button small" href="<?= site_url('notifications') ?>">
                            <p class="dashboard-button"><?= lang('notifications') ?></p>

                        </a>
                    </div>

                    <?php if ($Owner) { ?>
                        <div class="col-lg-1 col-md-2 col-xs-6">
                            <a class="turquoiseOne white quick-button small" href="<?= site_url('auth/users') ?>">

                                <p class="dashboard-button"><?= lang('users') ?></p>
                            </a>
                        </div>
                        <div class="col-lg-1 col-md-2 col-xs-6">
                            <a class="turquoiseOne white quick-button small" href="<?= site_url('system_settings') ?>">
                                <p class="dashboard-button"><?= lang('settings') ?></p>
                            </a>
                        </div>
                        <div class="col-lg-1 col-md-2 col-xs-6">
                            <a class="turquoiseOne white quick-button small" href="<?= site_url('reports') ?>">
                                <p class="dashboard-button"><?= lang('reports') ?></p>
                            </a>
                        </div>
                    <?php } ?>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-eq-height">
        <div class="col-lg-6 box-qty-alert-table">
            <div class="box">
                <div class="box-header">
                    <h2 class="blue"><i
                            class="fa-fw fa fa-calendar-o"></i><?= lang('product_quantity_alerts') . ' - ' . ($warehouse_id ? $warehouse->name : lang('all_warehouses')) . ''; ?>
                    </h2>

                    <div class="box-icon">
                        <ul class="btn-tasks">
                            <?php if (!empty($warehouses)) { ?>
                                <li class="dropdown">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                        <i class="icon fa fa-building-o tip" data-placement="right"
                                           title="<?= lang("warehouses") ?>"></i>
                                    </a>
                                    <ul class="dropdown-menu pull-right tasks-menus" role="menu"
                                        aria-labelledby="dLabel">
                                        <li>
                                            <a href="<?= site_url('reports/quantity_alerts') ?>">
                                                <i class="fa fa-building-o"></i> <?= lang('all_warehouses') ?>
                                            </a>
                                        </li>
                                        <li class="divider"></li>
                                        <?php
                                        foreach ($warehouses as $warehouse) {
                                            echo '<li ' . ($warehouse_id && $warehouse_id == $warehouse->id ? 'class="active"' : '') . '><a href="' . site_url('reports/quantity_alerts/' . $warehouse->id) . '"><i class="fa fa-building"></i>' . $warehouse->name . '</a></li>';
                                        }
                                        ?>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="box-content">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive">
                                <table id="PQData" cellpadding="0" cellspacing="0" border="0"
                                       class="table table-bordered table-condensed table-hover table-striped dfTable reports-table">
                                    <thead>
                                    <tr class="active">
                                        <th style="min-width:40px; width: 40px; text-align: center;"><?php echo $this->lang->line("image"); ?></th>
                                        <th><?php echo $this->lang->line("product_code"); ?></th>
                                        <th><?php echo $this->lang->line("product_name"); ?></th>
                                        <th><?php echo $this->lang->line("quantity"); ?></th>
                                        <th><?php echo $this->lang->line("alert_quantity"); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="5"
                                            class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                                    </tr>
                                    </tbody>
                                    <tfoot class="dtFilter">
                                    <tr class="active">
                                        <th style="min-width:40px; width: 40px; text-align: center;"><?php echo $this->lang->line("image"); ?></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="box box-warehouse-chart">
                <div class="box-header">

                    <h2 class="blue"><i
                            class="fa-fw fa fa-bar-chart-o"></i><?= lang('warehouse_stock') . ' - ' . ($warehouse ? $warehouse->name : lang('all_warehouses')) . ''; ?>

                    </h2>

                    <div class="box-icon">
                        <ul class="btn-tasks">
                            <?php if (!empty($warehouses) && ($Owner || $Admin)) { ?>
                                <li class="dropdown">
                                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i
                                            class="icon fa fa-building-o tip" data-placement="right"
                                            title="<?= lang("warehouses") ?>"></i></a>
                                    <ul class="dropdown-menu pull-right tasks-menus" role="menu"
                                        aria-labelledby="dLabel">
                                        <li><a href="<?= site_url('reports/warehouse_stock') ?>"><i
                                                    class="fa fa-building-o"></i> <?= lang('all_warehouses') ?></a></li>
                                        <li class="divider"></li>
                                        <?php
                                        foreach ($warehouses as $warehouse) {
                                            echo '<li ' . ($warehouse_id && $warehouse_id == $warehouse->id ? 'class="active"' : '') . '><a href="' . site_url('reports/warehouse_stock/' . $warehouse->id) . '"><i class="fa fa-building"></i>' . $warehouse->name . '</a></li>';
                                        }
                                        ?>
                                    </ul>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="box-content box-content-warehouse-chart">
                    <div id="chart" style="width:100%; height:450px;"></div>
                </div>

            </div>
        </div>

    </div>

    <div class="box" style="margin-bottom: 15px;">
        <div class="box-header">

            <h2 class="blue"> <?php if ($Owner) { ?>

                    <a class="tip" id="today_profit"
                       title="<span><?= lang('today_profit') ?></span>"
                       data-placement="bottom" data-html="true" href="<?= site_url('reports/profit') ?>"
                       data-toggle="modal" data-target="#myModal">
                        <i class="turquoiseFour fa fa-hourglass-2"></i>
                    </a>

                <?php } ?><?= lang('overview_chart'); ?></h2>
        </div>
        <div class="box-content">
            <div class="row">
                <div class="col-md-12">


                    <div id="ov-chart" style="width:100%; height:450px;"></div>
                    <p class="text-center"><?= lang("chart_lable_toggle"); ?></p>

                    <div class="form-group">
                        <input value="<?= lang('change_graph_type_spline'); ?>" class="btn btn-primary main-chart"
                               name="change_graph_type_spline" id="change-graph-type-spline" type="button">
                        <input value="<?= lang('change_graph_type_column'); ?>" class="btn btn-primary main-chart"
                               name="change_graph_type_column" id="change-graph-type-column" type="button">
                        <input value="<?= lang('change_graph_type_default'); ?>" class="btn btn-primary main-chart"
                               name="change_graph_type_default" id="change-graph-type-default" type="button">
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($Owner || $Admin) { ?>

<?php } else { ?>
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-lg-12">
            <div class="box">

                <div class="box-content">

                    <?php if ($GP['products-index']) { ?>
                        <div class="col-lg-1 col-md-2 col-xs-6">
                            <a class="bblue white quick-button small" href="<?= site_url('products') ?>">
                                <i class="fa fa-barcode"></i>

                                <p><?= lang('products') ?></p>
                            </a>
                        </div>
                    <?php }
                    if ($GP['sales-index']) { ?>
                        <div class="col-lg-1 col-md-2 col-xs-6">
                            <a class="bdarkGreen white quick-button small" href="<?= site_url('sales') ?>">
                                <i class="fa fa-heart"></i>

                                <p><?= lang('sales') ?></p>
                            </a>
                        </div>
                    <?php }
                    if ($GP['quotes-index']) { ?>
                        <div class="col-lg-1 col-md-2 col-xs-6">
                            <a class="blightOrange white quick-button small" href="<?= site_url('quotes') ?>">
                                <i class="fa fa-heart-o"></i>

                                <p><?= lang('quotes') ?></p>
                            </a>
                        </div>
                    <?php }
                    if ($GP['purchases-index']) { ?>
                        <div class="col-lg-1 col-md-2 col-xs-6">
                            <a class="bred white quick-button small" href="<?= site_url('purchases') ?>">
                                <i class="fa fa-star"></i>

                                <p><?= lang('purchases') ?></p>
                            </a>
                        </div>
                    <?php }
                    if ($GP['transfers-index']) { ?>
                        <div class="col-lg-1 col-md-2 col-xs-6">
                            <a class="bpink white quick-button small" href="<?= site_url('transfers') ?>">
                                <i class="fa fa-star-o"></i>

                                <p><?= lang('transfers') ?></p>
                            </a>
                        </div>
                    <?php }
                    if ($GP['customers-index']) { ?>
                        <div class="col-lg-1 col-md-2 col-xs-6">
                            <a class="bgrey white quick-button small" href="<?= site_url('customers') ?>">
                                <i class="fa fa-users"></i>

                                <p><?= lang('customers') ?></p>
                            </a>
                        </div>
                    <?php }
                    if ($GP['suppliers-index']) { ?>
                        <div class="col-lg-1 col-md-2 col-xs-6">
                            <a class="bgrey white quick-button small" href="<?= site_url('suppliers') ?>">
                                <i class="fa fa-users"></i>

                                <p><?= lang('suppliers') ?></p>
                            </a>
                        </div>
                    <?php }
                    if ($GP['suppliers-index']) { ?>
                        <div class="col-lg-1 col-md-2 col-xs-6">
                            <a class="bgrey white quick-button small" href="<?= site_url('suppliers') ?>">
                                <i class="fa fa-users"></i>

                                <p><?= lang('suppliers') ?></p>
                            </a>
                        </div>


                    <?php } ?>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="row" style="margin-bottom: 15px;">
    <script>
        $(document).ready(function () {
            oTable = $('#PQData').dataTable({
                "aaSorting": [[1, "desc"]],
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "iDisplayLength": <?= $Settings->rows_per_page ?>,
                'bProcessing': true, 'bServerSide': true,
                'sAjaxSource': '<?= site_url('reports/getQuantityAlerts' . ($warehouse_id ? '/' . $warehouse_id : '')) ?>',
                'fnServerData': function (sSource, aoData, fnCallback) {
                    aoData.push({
                        "name": "<?= $this->security->get_csrf_token_name() ?>",
                        "value": "<?= $this->security->get_csrf_hash() ?>"
                    });
                    $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
                },
                "aoColumns": [{
                    "bSortable": false,
                    "mRender": img_hl
                }, null, null, {"mRender": formatQuantity}, {"mRender": formatQuantity}],
            }).fnSetFilteringDelay().dtFilter([
                {column_number: 1, filter_default_label: "[<?=lang('product_code');?>]", filter_type: "text", data: []},
                {column_number: 2, filter_default_label: "[<?=lang('product_name');?>]", filter_type: "text", data: []},
                {column_number: 3, filter_default_label: "[<?=lang('quantity');?>]", filter_type: "text", data: []},
                {
                    column_number: 4,
                    filter_default_label: "[<?=lang('alert_quantity');?>]",
                    filter_type: "text",
                    data: []
                },
            ], "footer");

    let elem =  getComputedStyle(document.getElementsByTagName('body')[0]).direction
    if(elem==="rtl"){
       // alert("rtl_1")
    }else{
     //   alert("ltr_1")
    }
        });
    </script>
    <div class="col-md-12">
        <div class="box">
            <div class="box-header">
                <h2 class="blue"><i class="fa-fw fa fa-tasks"></i> <?= lang('latest_five') ?></h2>
            </div>
            <div class="box-content">
                <div class="row">
                    <div class="col-md-12">

                        <ul id="dbTab" class="nav nav-tabs">
                            <?php if ($Owner || $Admin || $GP['sales-index']) { ?>
                                <li class=""><a href="#sales"><?= lang('sales') ?></a></li>
                            <?php }
                            if ($Owner || $Admin || $GP['quotes-index']) { ?>
                                <li class=""><a href="#quotes"><?= lang('quotes') ?></a></li>
                            <?php }
                            if ($Owner || $Admin || $GP['purchases-index']) { ?>
                                <li class=""><a href="#purchases"><?= lang('purchases') ?></a></li>
                            <?php }
                            if ($Owner || $Admin || $GP['transfers-index']) { ?>
                                <li class=""><a href="#transfers"><?= lang('transfers') ?></a></li>
                            <?php }
                            if ($Owner || $Admin || $GP['customers-index']) { ?>
                                <li class=""><a href="#customers"><?= lang('customers') ?></a></li>
                            <?php }
                            if ($Owner || $Admin || $GP['suppliers-index']) { ?>
                                <li class=""><a href="#suppliers"><?= lang('suppliers') ?></a></li>
                            <?php } ?>
                        </ul>

                        <div class="tab-content">
                            <?php if ($Owner || $Admin || $GP['sales-index']) { ?>

                                <div id="sales" class="tab-pane fade in">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="sales-tbl" cellpadding="0" cellspacing="0" border="0"
                                                       class="table table-bordered table-hover table-striped"
                                                       style="margin-bottom: 0;">
                                                    <thead>
                                                    <tr>
                                                        <th style="width:30px !important;">#</th>
                                                        <th><?= $this->lang->line("date"); ?></th>
                                                        <th><?= $this->lang->line("reference_no"); ?></th>
                                                        <th><?= $this->lang->line("customer"); ?></th>
                                                        <th><?= $this->lang->line("status"); ?></th>
                                                        <th><?= $this->lang->line("total"); ?></th>
                                                        <th><?= $this->lang->line("payment_status"); ?></th>
                                                        <th><?= $this->lang->line("paid"); ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if (!empty($sales)) {
                                                        $r = 1;
                                                        foreach ($sales as $order) {
                                                            echo '<tr id="' . $order->id . '" class="' . ($order->pos ? "receipt_link" : "invoice_link") . '"><td>' . $r . '</td>
                                                            <td>' . $this->sma->hrld($order->date) . '</td>
                                                            <td>' . $order->reference_no . '</td>
                                                            <td>' . $order->customer . '</td>
                                                            <td>' . row_status($order->sale_status) . '</td>
                                                            <td class="text-right">' . $this->sma->formatMoney($order->grand_total) . '</td>
                                                            <td>' . row_status($order->payment_status) . '</td>
                                                            <td class="text-right">' . $this->sma->formatMoney($order->paid) . '</td>
                                                        </tr>';
                                                            $r++;
                                                        }
                                                    } else { ?>
                                                        <tr>
                                                            <td colspan="7"
                                                                class="dataTables_empty"><?= lang('no_data_available') ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php }
                            if ($Owner || $Admin || $GP['quotes-index']) { ?>

                                <div id="quotes" class="tab-pane fade">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="quotes-tbl" cellpadding="0" cellspacing="0" border="0"
                                                       class="table table-bordered table-hover table-striped"
                                                       style="margin-bottom: 0;">
                                                    <thead>
                                                    <tr>
                                                        <th style="width:30px !important;">#</th>
                                                        <th><?= $this->lang->line("date"); ?></th>
                                                        <th><?= $this->lang->line("reference_no"); ?></th>
                                                        <th><?= $this->lang->line("customer"); ?></th>
                                                        <th><?= $this->lang->line("status"); ?></th>
                                                        <th><?= $this->lang->line("amount"); ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if (!empty($quotes)) {
                                                        $r = 1;
                                                        foreach ($quotes as $quote) {
                                                            echo '<tr id="' . $quote->id . '" class="quote_link"><td>' . $r . '</td>
                                                        <td>' . $this->sma->hrld($quote->date) . '</td>
                                                        <td>' . $quote->reference_no . '</td>
                                                        <td>' . $quote->customer . '</td>
                                                        <td>' . row_status($quote->status) . '</td>
                                                        <td class="text-right">' . $this->sma->formatMoney($quote->grand_total) . '</td>
                                                    </tr>';
                                                            $r++;
                                                        }
                                                    } else { ?>
                                                        <tr>
                                                            <td colspan="6"
                                                                class="dataTables_empty"><?= lang('no_data_available') ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php }
                            if ($Owner || $Admin || $GP['purchases-index']) { ?>

                                <div id="purchases" class="tab-pane fade in">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="purchases-tbl" cellpadding="0" cellspacing="0" border="0"
                                                       class="table table-bordered table-hover table-striped"
                                                       style="margin-bottom: 0;">
                                                    <thead>
                                                    <tr>
                                                        <th style="width:30px !important;">#</th>
                                                        <th><?= $this->lang->line("date"); ?></th>
                                                        <th><?= $this->lang->line("reference_no"); ?></th>
                                                        <th><?= $this->lang->line("supplier"); ?></th>
                                                        <th><?= $this->lang->line("status"); ?></th>
                                                        <th><?= $this->lang->line("amount"); ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if (!empty($purchases)) {
                                                        $r = 1;
                                                        foreach ($purchases as $purchase) {
                                                            echo '<tr id="' . $purchase->id . '" class="purchase_link"><td>' . $r . '</td>
                                                    <td>' . $this->sma->hrld($purchase->date) . '</td>
                                                    <td>' . $purchase->reference_no . '</td>
                                                    <td>' . $purchase->supplier . '</td>
                                                    <td>' . row_status($purchase->status) . '</td>
                                                    <td class="text-right">' . $this->sma->formatMoney($purchase->grand_total) . '</td>
                                                </tr>';
                                                            $r++;
                                                        }
                                                    } else { ?>
                                                        <tr>
                                                            <td colspan="6"
                                                                class="dataTables_empty"><?= lang('no_data_available') ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php }
                            if ($Owner || $Admin || $GP['transfers-index']) { ?>

                                <div id="transfers" class="tab-pane fade">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="transfers-tbl" cellpadding="0" cellspacing="0" border="0"
                                                       class="table table-bordered table-hover table-striped"
                                                       style="margin-bottom: 0;">
                                                    <thead>
                                                    <tr>
                                                        <th style="width:30px !important;">#</th>
                                                        <th><?= $this->lang->line("date"); ?></th>
                                                        <th><?= $this->lang->line("reference_no"); ?></th>
                                                        <th><?= $this->lang->line("from"); ?></th>
                                                        <th><?= $this->lang->line("to"); ?></th>
                                                        <th><?= $this->lang->line("status"); ?></th>
                                                        <th><?= $this->lang->line("amount"); ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if (!empty($transfers)) {
                                                        $r = 1;
                                                        foreach ($transfers as $transfer) {
                                                            echo '<tr id="' . $transfer->id . '" class="transfer_link"><td>' . $r . '</td>
                                                <td>' . $this->sma->hrld($transfer->date) . '</td>
                                                <td>' . $transfer->transfer_no . '</td>
                                                <td>' . $transfer->from_warehouse_name . '</td>
                                                <td>' . $transfer->to_warehouse_name . '</td>
                                                <td>' . row_status($transfer->status) . '</td>
                                                <td class="text-right">' . $this->sma->formatMoney($transfer->grand_total) . '</td>
                                            </tr>';
                                                            $r++;
                                                        }
                                                    } else { ?>
                                                        <tr>
                                                            <td colspan="7"
                                                                class="dataTables_empty"><?= lang('no_data_available') ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php }
                            if ($Owner || $Admin || $GP['customers-index']) { ?>

                                <div id="customers" class="tab-pane fade in">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="customers-tbl" cellpadding="0" cellspacing="0" border="0"
                                                       class="table table-bordered table-hover table-striped"
                                                       style="margin-bottom: 0;">
                                                    <thead>
                                                    <tr>
                                                        <th style="width:30px !important;">#</th>
                                                        <th><?= $this->lang->line("company"); ?></th>
                                                        <th><?= $this->lang->line("name"); ?></th>
                                                        <th><?= $this->lang->line("email"); ?></th>
                                                        <th><?= $this->lang->line("phone"); ?></th>
                                                        <th><?= $this->lang->line("address"); ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if (!empty($customers)) {
                                                        $r = 1;
                                                        foreach ($customers as $customer) {
                                                            echo '<tr id="' . $customer->id . '" class="customer_link pointer"><td>' . $r . '</td>
                                            <td>' . $customer->company . '</td>
                                            <td>' . $customer->name . '</td>
                                            <td>' . $customer->email . '</td>
                                            <td>' . $customer->phone . '</td>
                                            <td>' . $customer->address . '</td>
                                        </tr>';
                                                            $r++;
                                                        }
                                                    } else { ?>
                                                        <tr>
                                                            <td colspan="6"
                                                                class="dataTables_empty"><?= lang('no_data_available') ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php }
                            if ($Owner || $Admin || $GP['suppliers-index']) { ?>

                                <div id="suppliers" class="tab-pane fade">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="suppliers-tbl" cellpadding="0" cellspacing="0" border="0"
                                                       class="table table-bordered table-hover table-striped"
                                                       style="margin-bottom: 0;">
                                                    <thead>
                                                    <tr>
                                                        <th style="width:30px !important;">#</th>
                                                        <th><?= $this->lang->line("company"); ?></th>
                                                        <th><?= $this->lang->line("name"); ?></th>
                                                        <th><?= $this->lang->line("email"); ?></th>
                                                        <th><?= $this->lang->line("phone"); ?></th>
                                                        <th><?= $this->lang->line("address"); ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php if (!empty($suppliers)) {
                                                        $r = 1;
                                                        foreach ($suppliers as $supplier) {
                                                            echo '<tr id="' . $supplier->id . '" class="supplier_link pointer"><td>' . $r . '</td>
                                        <td>' . $supplier->company . '</td>
                                        <td>' . $supplier->name . '</td>
                                        <td>' . $supplier->email . '</td>
                                        <td>' . $supplier->phone . '</td>
                                        <td>' . $supplier->address . '</td>
                                    </tr>';
                                                            $r++;
                                                        }
                                                    } else { ?>
                                                        <tr>
                                                            <td colspan="6"
                                                                class="dataTables_empty"><?= lang('no_data_available') ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('.order').click(function () {
            window.location.href = '<?=site_url()?>orders/view/' + $(this).attr('id') + '#comments';
        });
        $('.invoice').click(function () {
            window.location.href = '<?=site_url()?>orders/view/' + $(this).attr('id');
        });
        $('.quote').click(function () {
            window.location.href = '<?=site_url()?>quotes/view/' + $(this).attr('id');
        });
    });
</script>

<?php if (($Owner || $Admin) && $chatData) { ?>
    <style type="text/css" media="screen">
        .tooltip-inner {
            max-width: 500px;

        }
    </style>

    <script type="text/javascript">

        $(function () {

            $('#ov-chart').highcharts({
                chart: {

                    options3d: {
                        enabled: true,
                        alpha: 0,
                        beta: -2,
                        viewDistance: 25
                    }
                },

                plotOptions: {

                    column: {
                        depth: 25
                    },
                    series: {
                        pointWidth: 46
                    }
                },
                credits: {enabled: false},
                title: {text: ''},
                xAxis: {
                    categories: <?= json_encode($months); ?>
                    , labels: {
                        align: 'left',
                        x: 0,
                        y: 30
                    }
                },
                yAxis: {min: 0, title: "", tickInterval: 1000},
                tooltip: {
                    shared: true,
                    followPointer: true,
                    formatter: function () {
                        if (this.key) {
                            return '<div class="tooltip-inner hc-tip" style="margin-bottom:0">' + this.key + '<br><strong>' + currencyFormat(this.y) + '</strong> (' + formatNumber(this.percentage) + '%)';
                        } else {
                            var s = '<div class="well well-sm hc-tip" style="margin-bottom:0;"><h2 style="margin-top:0;">' + this.x + '</h2><table class="table table-striped"  style="margin-bottom:0;">';
                            $.each(this.points, function () {
                                s += '<tr><td style="color:{series.color};padding:0">' + this.series.name + ': </td><td style="color:{series.color};padding:0;text-align:right;"> <b>' +
                                currencyFormat(this.y) + '</b></td></tr>';
                            });
                            s += '</table></div>';
                            return s;
                        }
                    },
                    useHTML: true, borderWidth: 0, shadow: false, valueDecimals: site.settings.decimals,
                    style: {fontSize: '14px', padding: '0', color: 'black'}
                },
                legend: {
                    symbolPadding: -20,
                    itemWidth: 180,
                    itemDistance: 60,
                    layout: 'horizontal',
                    x: -40,
                    y: 0,
                    rtl: true
                },

                series: [{
                    type: 'column',

                    color: {
                        linearGradient: {x1: 0, x2: 0, y1: 0, y2: 1},
                        stops: [
                            [0, '#43ABB0'],
                            [1, '#0C8288']
                        ]
                    },
                    name: '<?= lang("sp_tax"); ?>',
                    data: [<?php
                    echo implode(', ', $mtax1);
                    ?>]
                },
                    {
                        type: 'column',
                        color: {
                            linearGradient: {x1: 0, x2: 0, y1: 0, y2: 1},
                            stops: [
                                [0, '#dbf1ac'],
                                [1, '#B5D375']
                            ]
                        },
                        name: '<?= lang("order_tax"); ?>',
                        data: [<?php
                    echo implode(', ', $mtax2);
                    ?>]
                    },
                    {
                        type: 'column',
                        color: {
                            linearGradient: {x1: 0, x2: 0, y1: 0, y2: 1},
                            stops: [
                                [0, '#0A733B'],
                                [1, '#33A367']
                            ]
                        },
                        name: '<?= lang("sales"); ?>',
                        data: [<?php
                    echo implode(', ', $msales);
                    ?>]
                    }, {
                        type: 'column',
                        color: {
                            linearGradient: {x1: 0, x2: 0, y1: 0, y2: 1},
                            stops: [
                                [0, '#005054'],
                                [1, '#0c8288']
                            ]
                        },
                        name: '<?= lang("purchases"); ?>',
                        data: [<?php echo implode(', ', $mpurchases); ?>],
                        marker: {
                            lineWidth: 2,
                            states: {
                                hover: {
                                    lineWidth: 4
                                }
                            },
                            lineColor: Highcharts.getOptions().colors[3],
                            fillColor: 'white'
                        }
                    }, {
                        type: 'column',
                        color: {
                            linearGradient: {x1: 0, x2: 0, y1: 0, y2: 1},
                            stops: [
                                [0, '#7AD6A5'],
                                [1, '#A9EAC7']
                            ]
                        },
                        name: '<?= lang("pp_tax"); ?>',
                        data: [<?php
                    echo implode(', ', $mtax3);
                    ?>],
                        marker: {
                            lineWidth: 2,
                            states: {
                                hover: {
                                    lineWidth: 4
                                }
                            },
                            lineColor: Highcharts.getOptions().colors[3],
                            fillColor: 'white'
                        }
                    }

                ]
            });

            var chart = $('#ov-chart').highcharts();
            var objChart = chart.series;
            $("#change-graph-type-spline").click(function () {

                objChart.forEach(function (entry, index) {
                    if (index < 5) {
                        entry.update({
                            type: 'spline',
                            animation: {
                                duration: 1500,
                                easing: 'linear'
                            }
                        });
                    }

                });
            })
            $("#change-graph-type-column").click(function () {


                objChart.forEach(function (entry, index) {
                    if (index === 5) {
                        return;
                    }

                    entry.update({

                        type: 'column',
                        plotOptions: {

                            column: {
                                depth: 125
                            },
                            series: {
                                groupPadding: 1,
                                pointWidth: 46//width of the column bars irrespective of the chart size
                            }
                        }
                    });

                });
            })
            $("#change-graph-type-default").click(function () {
                objChart.forEach(function (entry, index) {
                    if (index < 5) {
                        if (index == 3 || index == 4) {
                            entry.update({
                                options3d: {
                                    enabled: true
                                },
                                type: 'spline',
                                animation: {duration: 1500}
                            });
                        } else {
                            entry.update({
                                options3d: {
                                    enabled: true
                                },
                                type: 'column'
                            });
                        }
                    }
                });
            })
            $("#change-graph-type-spline").click()
            $("#change-graph-type-default").click()
        });

    </script>

    <script type="text/javascript">

        $(function () {

            <?php if ($lmbs) { ?>

            $('#lmbschart').highcharts({
                chart: {
                    type: 'column', options3d: {
                        enabled: true,
                        alpha: 0,
                        beta: -2,
                        depth: 50,
                        viewDistance: 25
                    }
                },
                title: {text: ''},
                credits: {enabled: false},
                xAxis: {type: 'category', labels: {x: 10, y: 44, rotation: 60, style: {fontSize: '13px'}}},
                yAxis: {min: 0, title: {text: ''}},
                legend: {enabled: false},
                tooltip: {
                    shared: true,
                    followPointer: true,
                    formatter: function () {
                        var s = '<div class="well well-sm hc-tip" style="margin-bottom:0;"><table class="table table-striped"  style="margin-bottom:0;">';
                        $.each(this.points, function () {
                            s += '<tr>' +
                            '<span style="padding:0;">' + '<b>' + this.key + '</b>' + '</span>' +
                            '<td style="color:{series.color};padding:0">' + this.series.name + ': </td><td style="color:{series.color};padding:0;text-align:right;"> <b>' + currencyFormat(this.y) + '</b>' + '</td>' +
                            '</tr>';
                        });
                        s += '</table></div>';
                        return s;
                    },
                    useHTML: true, borderWidth: 0, shadow: false, valueDecimals: site.settings.decimals,
                    style: {fontSize: '14px', padding: '0', color: 'black'}
                },
                series: [{
                    name: '<?=lang('sold');?>',
                    data: [<?php
                    foreach ($lmbs as $r) {
                        if($r->quantity > 0) {
                            echo "['".$r->product_name."<br>(".$r->product_code.")', ".$r->quantity."],";
                        }
                    }
                    ?>],
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        color: 'black',
                        align: 'right',
                        y: -25,
                        style: {fontSize: '12px'}
                    }
                }]
            });
            <?php } if ($bs) { ?>

            $('#bschart').highcharts({
                chart: {
                    type: 'column', options3d: {
                        enabled: true,
                        alpha: 0,
                        beta: -2,
                        depth: 50,
                        viewDistance: 25
                    }
                },
                title: {text: ''},
                credits: {enabled: false},
                xAxis: {type: 'category', labels: {y: 44, rotation: 60, style: {fontSize: '13px'}}},
                yAxis: {min: 0, title: {text: ''}},
                legend: {enabled: false},
                tooltip: {
                    shared: true,
                    followPointer: true,
                    formatter: function () {
                        var s = '<div class="well well-sm hc-tip" style="margin-bottom:0;"><table class="table table-striped"  style="margin-bottom:0;">';
                        $.each(this.points, function () {
                            s += '<tr>' +
                            '<span style="padding:0;">' + '<b>' + this.key + '</b>' + '</span>' +
                            '<td style="color:{series.color};padding:0">' + this.series.name + ': </td><td style="color:{series.color};padding:0;text-align:right;"> <b>' + currencyFormat(this.y) + '</b>' + '</td>' +
                            '</tr>';
                        });
                        s += '</table></div>';
                        return s;
                    },
                    useHTML: true, borderWidth: 0, shadow: false, valueDecimals: site.settings.decimals,
                    style: {fontSize: '14px', padding: '0', color: 'black'}
                },
                series: [{
                    name: '<?=lang('sold');?>',
                    data: [<?php
                foreach ($bs as $r) {
                    if($r->quantity > 0) {
                        echo "['".$r->product_name."<br>(".$r->product_code.")', ".$r->quantity."],";
                    }
                }
                ?>],

                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        color: '#000',
                        align: 'right',
                        y: -25,
                        style: {fontSize: '12px'}
                    }
                }]
            });

            <?php } ?>
        });
    </script>
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-sm-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="blue"><i
                            class="fa-fw fa fa-line-chart"></i><?= lang('best_sellers'), ' (' . $setmonth . ')'; ?>
                    </h2>
                </div>
                <div class="box-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="bschart" style="width:100%; height:450px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="box">
                <div class="box-header">
                    <h2 class="blue"><i
                            class="fa-fw fa fa-line-chart"></i><?= lang('best_sellers') . ' (' . $setprevmonth . ')'; ?>
                    </h2>
                </div>
                <div class="box-content">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="lmbschart" style="width:100%; height:450px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

        Highcharts.setOptions({
            colors: ['#288D92', '#52bd83', '#437B8D']
        });
        $(function () {
            Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
                return {
                    radialGradient: {cx: 0.5, cy: 0.3, r: 0.7},
                    stops: [[0, color], [1, Highcharts.Color(color).brighten(-0.3).get('rgb')]]
                };
            });
            $('#chart').highcharts({
                chart: {
                    options3d: {
                        enabled: true,
                        alpha: 45,
                        beta: 0
                    },
                 //   plotBackgroundColor: null,
                  //  plotBorderWidth: null,
                    plotShadow: null
                },
                title: {text: ''},
                credits: {enabled: false},
                tooltip: {
                    formatter: function () {
                        return '<div class="tooltip-inner hc-tip" style="margin-bottom:0;">' + this.key + '<br><strong>' + currencyFormat(this.y) + '</strong> (' + formatNumber(this.percentage) + '%)';
                    },
                    followPointer: true,
                    useHTML: true,
                    borderWidth: 0,
                    shadow: true,
                    valueDecimals: site.settings.decimals,
                    style: {fontSize: '12px', padding: '0', color: '#000000'}
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        depth: 35,
                        dataLabels: {
                            enabled: true,
                            formatter: function () {
                                return '<h3 style="margin:-15px 0 0 0;"><b>' + this.point.name + '</b>:<br><b> ' + currencyFormat(this.y) + '</b></h3>';
                            },
                            useHTML: true
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: '<?php echo $this->lang->line("stock_value"); ?>',
                    data: [

                        ['<?php echo $this->lang->line("stock_value_by_price"); ?>', <?php echo $stock->stock_by_price; ?>],
                        ['<?php echo $this->lang->line("stock_value_by_cost"); ?>', <?php echo $stock->stock_by_cost; ?>],
                        ['<?php echo $this->lang->line("profit_estimate"); ?>', <?php echo ($stock->stock_by_price - $stock->stock_by_cost); ?>],
                    ]

                }]
            });

        });
    </script>
<?php } ?>
