<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script src="<?= $assets; ?>js/hc/highcharts.js"></script>
    <script>
        $(document).ready(function () {
            oTable = $('#PQData').dataTable({
                "aaSorting": [[1, "desc"]],
                "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
                "iDisplayLength": <?= $Settings->rows_per_page ?>,
                'bProcessing': true, 'bServerSide': true,
                'sAjaxSource': '<?= site_url('reports/warehouseProductTable' . ($warehouse_id ? '/' . $warehouse_id : '')) ?>',
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
                }, null, null, {"mRender": formatQuantity}, null]
            }).fnSetFilteringDelay().dtFilter([
                {column_number: 1, filter_default_label: "[<?=lang('product_code');?>]", filter_type: "text", data: []},
                {column_number: 2, filter_default_label: "[<?=lang('product_name');?>]", filter_type: "text", data: []},
                {column_number: 3, filter_default_label: "[<?=lang('quantity');?>]", filter_type: "text", data: []},
                {column_number: 4, filter_default_label: "[<?=lang('reck');?>]", filter_type: "text", data: []},
            ], "footer");
        });

    </script>
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
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
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
                shadow: false,
                valueDecimals: site.settings.decimals,
                style: {fontSize: '14px', padding: '0', color: '#000000'}
            },
            plotOptions: {
                pie: {
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

<?php if ($Owner || $Admin) { ?>
    <div class="row">
    <div class="col-lg-12">
    <div class="col-lg-6">
    <div class="box" style="margin-top: 15px;">
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
        <div class="box-content">
            <div class="row">
                <div class="col-lg-12">

                    <?php if ($totals) { ?>

                        <div class="small-box padding1010 col-sm-6 turquoiseOne">
                            <div class="inner clearfix">
                                <a>
                                    <h3><?= $this->sma->formatQuantity($totals->total_items) ?></h3>

                                    <p><?= lang('total_items') ?></p>
                                </a>
                            </div>
                        </div>

                        <div class="small-box padding1010 col-sm-6 turquoiseThree">
                            <div class="inner clearfix">
                                <a>
                                    <h3><?= $this->sma->formatQuantity($totals->total_quantity) ?></h3>

                                    <p><?= lang('total_quantity') ?></p>
                                </a>
                            </div>
                        </div>
                        <div class="clearfix" style="margin-top:20px;"></div>
                    <?php } ?>
                    <div id="chart" style="width:100%; height:450px;"></div>
                </div>
            </div>
        </div>
    </div>
        <div class="box">
            <div class="box-header">

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
                                        <th><?php echo $this->lang->line("rack"); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="6" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
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
    </div>
<?php } ?>
<?php if ($Owner || $Admin) { ?>
     <div class="col-lg-6">
    <div class="box" style="margin-top: 15px;">
        <div class="box-header">
            <h2 class="blue"><i class="fa-fw fa fa-bar-chart-o"></i><?= lang('warehouses'); ?>
            </h2>
            <div class="box-icon">
                <div class="form-group choose-date hidden-xs">
                    <div class="controls">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text"
                                   value="<?= ($start ? $this->sma->hrld($start) : '') . ' - ' . ($end ? $this->sma->hrld($end) : ''); ?>"
                                   id="daterange" class="form-control">
                            <span class="input-group-addon"><i class="fa fa-chevron-down"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-content">
            <div class="row">
                <div class="col-lg-12">

                    <?php foreach ($warehouses_report as $warehouse_report) { ?>
                        <div class="row">
                            <div class="small-box padding1010 greenFive">
                                <h4 class="bold"><?= $warehouse_report['warehouse']->name.' ('.$warehouse_report['warehouse']->code.')'; ?></h4>


                                <h5 class="bold"><?= $this->sma->formatMoney(($warehouse_report['total_sales']->total_amount) - ($warehouse_report['total_purchases']->total_amount)) ?></h5>

                                <p>
                                    <?= lang('sales').' - '.lang('purchases'); ?>
                                </p>
                                <hr style="border-color: rgba(255, 255, 255, 0.4);">
                                <p>
                                    <?= $this->sma->formatMoney($warehouse_report['total_sales']->total_amount) . ' ' . lang('sales'); ?>
                                    - <?= $this->sma->formatMoney($warehouse_report['total_sales']->tax) . ' ' . lang('tax') ?>
                                    = <?= $this->sma->formatMoney($warehouse_report['total_sales']->total_amount-$warehouse_report['total_sales']->tax).' '.lang('net_sales'); ?>
                                </p>
                                <p>
                                    <?= $this->sma->formatMoney($warehouse_report['total_purchases']->total_amount) . ' ' . lang('purchases') ?>
                                    - <?= $this->sma->formatMoney($warehouse_report['total_purchases']->tax) . ' ' . lang('tax') ?>
                                    = <?= $this->sma->formatMoney($warehouse_report['total_purchases']->total_amount-$warehouse_report['total_purchases']->tax).' '.lang('net_purchases'); ?>
                                </p>
                                <hr style="border-color: rgba(255, 255, 255, 0.4);">

                                <?= '<h5 class="bold">'.$this->sma->formatMoney((($warehouse_report['total_sales']->total_amount-$warehouse_report['total_sales']->tax))-($warehouse_report['total_purchases']->total_amount-$warehouse_report['total_purchases']->tax)).'</h5>'; ?>
                                <p>
                                    <?= lang('net_sales').' - '.lang('net_purchases'); ?>
                                </p>
                                <hr style="border-color: rgba(255, 255, 255, 0.4);">

                                <?= '<h5 class="bold">'.$this->sma->formatMoney($warehouse_report['total_expenses']->total_amount).'</h5>'; ?>
                                <p>
                                    <?= $warehouse_report['total_expenses']->total.' '.lang('expenses'); ?>
                                </p>

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


<?php } ?>