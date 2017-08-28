<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>$(document).ready(function () {
        CURI = '<?= site_url('reports/profit_loss'); ?>';
    });</script>
<style>@media print {
        .fa {
            color: #EEE;
            display: none;
        }

        .small-box {
            border: 1px solid #CCC;
        }
    }</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-bars"></i><?= lang('profit_loss'); ?></h2>

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
                    <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-2 tip" title =" <?= $this->sma->formatMoney($total_purchases->paid) . ' ' . lang('paid') ?>
                                    & <?= $this->sma->formatMoney($total_purchases->tax) . ' ' . lang('tax') ?>">
                            <div class="small-box padding1010 turquoiseOne">
                                <h4 class="bold"><?= lang('purchases') ?></h4>
                                <h5 class="bold"><?= $this->sma->formatMoney($total_purchases->total_amount) ?></h5>
                                <p class="bold"><?= $total_purchases->total . ' ' . lang('purchases') ?> </p>
                                <p>&nbsp;</p>
                            </div>
                        </div>
                        <div class="col-sm-2 tip" title = "<?= $this->sma->formatMoney($total_sales->paid) . ' ' . lang('paid') ?>
                                    & <?= $this->sma->formatMoney($total_sales->tax) . ' ' . lang('tax') ?>">
                            <div class="small-box padding1010 turquoiseTwo">
                                <h4 class="bold"><?= lang('sales') ?></h4>
                                <h5 class="bold"><?= $this->sma->formatMoney($total_sales->total_amount) ?></h5>
                                <p class="bold"><?= $total_sales->total . ' ' . lang('sales') ?> </p>
                                <p>&nbsp;</p>
                            </div>
                        </div>

                        <div class="col-sm-2 tip"  title="<?= $this->sma->formatMoney($total_received_cash->total_amount) . ' ' . lang('cash') ?>
                                    , <?= $this->sma->formatMoney($total_received_cc->total_amount) . ' ' . lang('CC') ?>
                                    , <?= $this->sma->formatMoney($total_received_cheque->total_amount) . ' ' . lang('cheque') ?>">
                            <div class="small-box padding1010 turquoiseThree">
                                <h4 class="bold"><?= lang('payments_received') ?></h4>


                                <h5 class="bold"><?= $this->sma->formatMoney($total_received->total_amount) ?></h5>

                                <p class="bold"><?= $total_received->total . ' ' . lang('received') ?> </p>

                                <p>&nbsp;</p>

                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="small-box padding1010 turquoiseFour">
                                <h4 class="bold"><?= lang('payments_returned') ?></h4>


                                <h5 class="bold"><?= $this->sma->formatMoney($total_returned->total_amount) ?></h5>

                                <p><?= $total_returned->total . ' ' . lang('returned') ?></p>

                                <p>&nbsp;</p>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="small-box padding1010 turquoiseFive">
                                <h4 class="bold"><?= lang('payments_sent') ?></h4>


                                <h5 class="bold"><?= $this->sma->formatMoney($total_paid->total_amount) ?></h5>

                                <p><?= $total_paid->total . ' ' . lang('sent') ?></p>

                                <p>&nbsp;</p>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="small-box padding1010 turquoiseSix">
                                <h4 class="bold"><?= lang('expenses') ?></h4>


                                <h5 class="bold"><?= $this->sma->formatMoney($total_expenses->total_amount) ?></h5>

                                <p class="bold"><?= $total_expenses->total . ' ' . lang('expenses') ?></p>

                                <p>&nbsp;</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="col-sm-2 tip" title="<?= $this->sma->formatMoney($total_received->total_amount) . ' ' . lang('received') ?>
                                    - <?= $this->sma->formatMoney($total_returned->total_amount) . ' ' . lang('returned') ?>
                                    - <?= $this->sma->formatMoney($total_paid->total_amount) . ' ' . lang('sent') ?>
                                    - <?= $this->sma->formatMoney($total_expenses->total_amount) . ' ' . lang('expenses') ?>">
                            <div class="small-box padding1010 greenFour">
                                <h4 class="bold"><?= lang('payments') ?></h4>
                                <h5 class="bold"><?= $this->sma->formatMoney($total_received->total_amount - $total_returned->total_amount - $total_paid->total_amount - $total_expenses->total_amount) ?></h5>

                                <p>&nbsp;</p>
                            </div>
                        </div>

                        <div class="col-sm-2 tip" title="<?= $this->sma->formatMoney($total_sales->total_amount) . ' ' . lang('sales') ?>
                                    - <?= $this->sma->formatMoney($total_purchases->total_amount) . ' ' . lang('purchases') ?>">
                            <div class="small-box padding1010 greenOne">
                                <h4 class="bold"><?= lang('profit_loss') ?></h4>


                                <h5 class="bold"><?= $this->sma->formatMoney($total_sales->total_amount - $total_purchases->total_amount) ?></h5>

                                <p>&nbsp;</p>
                            </div>
                        </div>
                        <div class="col-sm-2 tip" title="<?= $this->sma->formatMoney($total_sales->total_amount) . ' ' . lang('sales') ?>
                                    - <?= $this->sma->formatMoney($total_sales->tax) . ' ' . lang('tax') ?>
                                    - <?= $this->sma->formatMoney($total_purchases->total_amount) . ' ' . lang('purchases') ?>">
                            <div class="small-box padding1010 greenTwo">
                                <h4 class="bold"><?= lang('profit_loss_sales') ?></h4>


                                <h5 class="bold"><?= $this->sma->formatMoney($total_sales->total_amount - $total_purchases->total_amount - $total_sales->tax) ?></h5>
                                <p>&nbsp;</p>
                            </div>
                        </div>
                        <div class="col-sm-2 tip" title="(<?= $this->sma->formatMoney($total_sales->total_amount) . ' ' . lang('sales') ?>
                                    - <?= $this->sma->formatMoney($total_sales->tax) . ' ' . lang('tax') ?>) -
                                    (<?= $this->sma->formatMoney($total_purchases->total_amount) . ' ' . lang('purchases') ?>
                                    - <?= $this->sma->formatMoney($total_purchases->tax) . ' ' . lang('tax') ?>)">
                            <div class="small-box padding1010 greenThree">
                                <h4 class="bold"><?= lang('profit_loss_net') ?></h4>


                                <h5 class="bold"><?= $this->sma->formatMoney(($total_sales->total_amount - $total_sales->tax) - ($total_purchases->total_amount - $total_purchases->tax)) ?></h5>

                                <p>&nbsp;</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>

