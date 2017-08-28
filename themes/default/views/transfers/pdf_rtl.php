<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->lang->line("transfer") . " " . $transfer->transfer_no; ?></title>
    <link href="<?php echo $assets ?>styles/style.css" rel="stylesheet">
    <style type="text/css">
        html, body { height: 100%; background: #FFF; }
        body:before, body:after { display: none !important; }
        .table th { text-align: center; padding: 5px; }
        .table td { padding: 4px; }
    </style>
</head>

<body>
<div id="wrap">
    <?php if ($logo) { ?>
        <div class="text-center" style="margin-bottom:20px;">
            <img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                 alt="<?= $Settings->site_name; ?>">
        </div>
    <?php } ?>
    <div class="well well-sm">
        <div class="row bold">
            <div class="col-xs-6 pull-left text-right order_barcodes">
                <?= $this->sma->save_barcode($transfer->transfer_no, 'code128', 66, false); ?>
                <?= $this->sma->qrcode('link', urlencode(site_url('transfers/view/' . $transfer->id)), 2); ?>
            </div>
            <div class="col-xs-4"><?= $this->sma->hrld($transfer->date); ?> : <?= lang("date"); ?>
                <br><?= $transfer->transfer_no; ?> : <?= lang("ref"); ?></div>

            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row">
        <div class="col-xs-5" style="text-align: right">
            <p><?php echo $this->lang->line("to"); ?>:</p>
            <h3><?php echo $to_warehouse->name . " ( " . $to_warehouse->code . " )"; ?></h3>
            <?php echo "<p>" . $to_warehouse->address . "<br>" . $to_warehouse->phone . "<br>" . $to_warehouse->email . "</p>";
            ?>
        </div>
        <div class="col-xs-5" style="text-align: right">
            <p><?php echo $this->lang->line("from"); ?>:</p>
            <h3><?php echo $from_warehouse->name . " ( " . $from_warehouse->code . " )"; ?></h3>
            <?php echo "<p>" . $from_warehouse->address . "<br>" . $from_warehouse->phone . "<br>" . $from_warehouse->email . "</p>";
            ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
                <th style="text-align:center; vertical-align:middle;"><?php echo $this->lang->line("subtotal"); ?></th>

                <th style="vertical-align:middle;"><?php echo $this->lang->line("description"); ?></th>
                <th style="text-align:center; vertical-align:middle;"><?php echo $this->lang->line("quantity"); ?></th>
                <th style="text-align:center; vertical-align:middle;"><?php echo $this->lang->line("unit_price"); ?></th>
                <?php
                if ($Settings->tax1) {
                    echo '<th style="text-align:center; vertical-align:middle;">' . $this->lang->line("tax") . '</th>';
                }
                ?>
                <th style="text-align:center; vertical-align:middle;"><?php echo $this->lang->line("no"); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php $r = 1;
            foreach ($rows as $row):
                ?>
                <tr>
                    <td style="width: 100px; text-align:right; padding-right:10px; vertical-align:middle;"><?php echo $this->sma->formatMoney($row->subtotal); ?></td>
                    <td style="text-align:right;">
                        <?= $row->product_code.' - '.$row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                    </td>
                    <td style="text-align:center; width:80px; "><?php echo $this->sma->formatQuantity($row->unit_quantity).' '.$row->product_unit_code; ?></td>
                    <td style="width: 100px; text-align:right; padding-right:10px; vertical-align:middle;"><?php echo $this->sma->formatMoney($row->net_unit_cost); ?></td>
                    <?php
                    if ($Settings->tax1) {
                        echo '<td style="width: 80px; text-align:right; vertical-align:middle;"><!--<small>(' . $row->tax . ')</small>--> ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                    }
                    ?>
                    <td style="text-align:center; width:25px;"><?php echo $r; ?></td>

                </tr>
                <?php $r++;
            endforeach;
            ?>
            </tbody>
            <tfoot>
            <?php
            $col = 4;
            if ($Settings->tax1) {
                $col += 1;
            }
            ?>

            <?php if ($Settings->tax1) { ?>
                <tr>
                    <td style="text-align:right; padding-right:10px;"><?php echo $this->sma->formatMoney($transfer->total); ?></td>
                    <td colspan="<?php echo $col; ?>"style="text-align:left; padding-right:10px;">(<?php echo $default_currency->code; ?>) <?php echo $this->lang->line("total"); ?></td>

                </tr>
                <?php echo '<tr>
                    <td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($transfer->total_tax) . '</td>
                    <td colspan="' . $col . '" style="text-align:left; padding-right:10px;">' . '(' . $default_currency->code . ')'.  $this->lang->line("product_tax") . '</td>

                </tr>';
            }
            ?>
            <tr>
                <td style="text-align:right; padding-right:10px; font-weight:bold;"><?php echo $this->sma->formatMoney($transfer->grand_total); ?></td>
                <td colspan="<?php echo $col; ?>"
                    style="text-align:left; padding-right:10px; font-weight:bold;">(<?php echo $default_currency->code; ?>) <?php echo $this->lang->line("total_amount"); ?> </td>

            </tr>
            </tfoot>
        </table>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <?php if ($transfer->note || $transfer->note != "") { ?>
                <div class="well well-sm">
                    <p class="bold"> : <?= lang("note"); ?></p>

                    <div><?= $this->sma->decode_html($transfer->note); ?></div>
                </div>
            <?php } ?>
        </div>
        <div class="col-xs-4 pull-left" style="text-align:right;">
            <p><?= $created_by->first_name.' '.$created_by->last_name; ?> : <?= lang("created_by"); ?> </p>

            <p>&nbsp;</p>

            <p>&nbsp;</p>
            <hr>
            <p><?= lang("stamp_sign"); ?></p>
        </div>
        <div class="col-xs-4 pull-right" style="text-align:right;">
            <p>:<?= lang("received_by"); ?> </p>

            <p>&nbsp;</p>

            <p>&nbsp;</p>
            <hr>
            <p><?= lang("stamp_sign"); ?></p>
        </div>
    </div>
</div>
</body>
</html>
