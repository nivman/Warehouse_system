<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $this->lang->line('purchase') . ' ' . $inv->reference_no; ?></title>
    <link href="<?php echo $assets ?>styles/style.css" rel="stylesheet">
    <style type="text/css">
        html, body {
            height: 100%;
            background: #FFF;
        }
        body:before, body:after {
            display: none !important;
        }
        .table th {
            text-align: center;
            padding: 5px;
        }
        .table td {
            padding: 4px;
        }
    </style>
</head>

<body>
<div id="wrap">
    <div class="row">
        <div class="col-lg-12">
            <?php if ($logo) { ?>
                <div class="text-center" style="margin-bottom:20px;">
                    <img src="<?= base_url('assets/uploads/logos/' . $biller->logo); ?>" alt="<?= $biller->company != '-' ? $biller->company : $biller->name; ?>">
                </div>
            <?php }
            ?>
            <div class="clearfix"></div>
            <div class="row padding10">

                <div class="col-xs-5" style="text-align:right;">
                    :<?php echo $this->lang->line("to"); ?>
                    <h2 class=""><?= $customer->company ? $customer->company : $customer->name; ?></h2>
                    <?= $customer->company ? '' : 'תזכירים:' . $customer->name; ?>
                    <?php
                    echo $customer->address . '<br />' . $customer->city . ' ' . $customer->postal_code . ' ' . $customer->state . '<br />' . $customer->country;
                    echo '<p>';
                    if ($customer->vat_no != "-" && $customer->vat_no != "") {
                        echo$customer->vat_no. " : ". lang("vat_no") . "<br>";
                    }
                    if ($customer->cf1 != '-' && $customer->cf1 != '') {

                        echo  $customer->cf1 ." : " . lang('ccf1') . '<br>';
                    }
                    if ($customer->cf2 != '-' && $customer->cf2 != '') {
                        echo  $customer->cf2 ." : " . lang('ccf2') . '<br>';
                    }
                    if ($customer->cf3 != '-' && $customer->cf3 != '') {
                        echo  $customer->cf3 ." : " . lang('ccf3') . '<br>';
                    }
                    if ($customer->cf4 != '-' && $customer->cf4 != '') {
                        echo  $customer->cf4 ." : " . lang('ccf4') . '<br>';
                    }
                    if ($customer->cf5 != '-' && $customer->cf5 != '') {
                        echo  $customer->cf5 ." : " . lang('ccf5') . '<br>';
                    }
                    if ($customer->cf6 != '-' && $customer->cf6 != '') {
                        echo  $customer->cf6 ." : " . lang('ccf6') . '<br>';
                    }
                    echo '</p>';
                    echo lang('tel') . ': ' . $customer->phone . '<br />' . $customer->email . " : ".lang('email');
                    ?>
                </div>

                <div class="col-xs-5" style="text-align:right;">
                    :<?php echo $this->lang->line("from"); ?>
                    <h2 class=""><?= $biller->company != '-' ? $biller->company : $biller->name; ?></h2>
                    <?= $biller->company ? '' : 'תזכירים: ' . $biller->name; ?>
                    <?php
                    echo $biller->address . '<br />' . $biller->city . ' ' . $biller->postal_code . ' ' . $biller->state . '<br />' . $biller->country;
                    echo '<p>';
                    if ($biller->vat_no != "-" && $biller->vat_no != "") {
                        echo  $biller->vat_no ." : " . lang("vat_no") . "<br>";
                    }
                    if ($biller->cf1 != '-' && $biller->cf1 != '') {
                        echo $biller->cf1 ." : " . lang('bcf1') . '<br>';
                    }
                    if ($biller->cf2 != '-' && $biller->cf2 != '') {
                        echo $biller->cf2 ." : " . lang('bcf2') . '<br>';
                    }
                    if ($biller->cf3 != '-' && $biller->cf3 != '') {
                        echo $biller->cf3 ." : " . lang('bcf3') . '<br>';
                    }
                    if ($biller->cf4 != '-' && $biller->cf4 != '') {
                        echo $biller->cf4 ." : " . lang('bcf4') . '<br>';
                    }
                    if ($biller->cf5 != '-' && $biller->cf5 != '') {
                        echo $biller->cf5 ." : " . lang('bcf5') . '<br>';
                    }
                    if ($biller->cf6 != '-' && $biller->cf6 != '') {
                        echo $biller->cf6 ." : " . lang('bcf6') . '<br>';
                    }
                    echo '</p>';
                    echo lang('tel') . ': ' . $biller->phone . '<br />' .$biller->email. " : ". lang('email');
                    ?>
                    <div class="clearfix"></div>
                </div>

            </div>
            <div class="clearfix"></div>
            <div class="row padding10"  style="text-align:right;">
                <div class="col-xs-5">
                    <span class="bold"><?= $Settings->site_name; ?></span><br>
                    <?= $warehouse->name ?>

                    <?php
                    echo $warehouse->address . '<br>';
                    echo ($warehouse->phone ? lang('tel') . ': ' . $warehouse->phone . '<br>' : '') . ($warehouse->email ? $warehouse->email ." : ". lang('email'):'');
                    ?>
                    <div class="clearfix"></div>
                </div>
                <div class="col-xs-5" style="text-align:right;">
                    <div class="bold">
                        <?= $this->sma->hrld($inv->date); ?> : <?= lang('date'); ?><br>
                        <?= $inv->reference_no; ?> : <?= lang('ref'); ?><br>
                        <?php if (!empty($inv->return_sale_ref)) {
                            echo '<br>'. $inv->return_sale_ref ." : ". lang("return_ref");
                        } ?>
                        <div class="clearfix"></div>
                        <div class="order_barcodes">
                            <?= $this->sma->qrcode('link', urlencode(site_url('sales/view/' . $inv->id)), 2); ?>
                            <?= $this->sma->save_barcode($inv->reference_no, 'code128', 66, false); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="clearfix"></div>
            <?php
            $col = 4;
            if ( $Settings->product_discount && $inv->product_discount != 0) {
                $col++;
            }
            if ($Settings->tax1 && $inv->product_tax > 0) {
                $col++;
            }
            if ( $Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                $tcol = $col - 2;
            } elseif ( $Settings->product_discount && $inv->product_discount != 0) {
                $tcol = $col - 1;
            } elseif ($Settings->tax1 && $inv->product_tax > 0) {
                $tcol = $col - 1;
            } else {
                $tcol = $col;
            }
            ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th><?= lang('subtotal'); ?></th>
                        <?php
                        if ($Settings->tax1 && $inv->product_tax > 0) {
                            echo '<th>' . lang('tax') . '</th>';
                        }
                        if ( $Settings->product_discount && $inv->product_discount != 0) {
                            echo '<th>' . lang('discount') . '</th>';
                        }
                        ?>
                        <th><?= lang('unit_price'); ?></th>
                        <th><?= lang('quantity'); ?></th>
                        <th><?= lang('description'); ?> (<?= lang('code'); ?>)</th>
                        <th><?= lang('no'); ?></th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php $r = 1;
                    foreach ($rows as $row):
                        ?>
                        <tr>
                            <td style="vertical-align:middle; text-align:right; width:110px;"><?= $this->sma->formatMoney($row->subtotal); ?></td>

                            <?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="width: 90px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 && $row->tax_code ? '<small>(' . $row->tax_code . ')</small> ' : '') . $this->sma->formatMoney($row->item_tax) . '</td>';
                            }
                            if ( $Settings->product_discount && $inv->product_discount != 0) {
                                echo '<td style="width: 90px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                            }
                            ?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity).' '.$row->product_unit_code; ?></td>

                            <td style="text-align:right; width:90px;"><?= $this->sma->formatMoney($row->unit_price); ?></td>


                            <td style="vertical-align:middle;text-align:right;">
                                <?= $row->product_code.' - '.$row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                            </td>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    if ($return_rows) {
                        echo '<tr class="warning"><td colspan="'.($col+1).'" class="no-border"><strong>'.lang('returned_items').'</strong></td></tr>';
                        foreach ($return_rows as $row):
                            ?>
                            <tr class="warning">
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_code.' - '.$row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                    <?= $row->details ? '<br>' . $row->details : ''; ?>
                                    <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                </td>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity).' '.$row->product_unit_code; ?></td>
                                <td style="text-align:right; width:100px;"><?= $this->sma->formatMoney($row->unit_price); ?></td>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<td style="text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 && $row->tax_code ? '<small>('.$row->tax_code.')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                }
                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                    echo '<td style="text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                }
                                ?>
                                <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney($row->subtotal); ?></td>
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                    }
                    ?>
                    </tbody>
                    <tfoot>

                    <?php if ($inv->grand_total != $inv->total) {
                        ?>
                        <tr>
                            <td style="text-align:right;"><?= $this->sma->formatMoney($return_sale ? (($inv->total + $inv->product_tax)+($return_sale->total + $return_sale->product_tax)) : ($inv->total + $inv->product_tax)); ?></td>
                            <?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->product_tax+$return_sale->product_tax) : $inv->product_tax) . '</td>';
                            }
                            if ( $Settings->product_discount && $inv->product_discount != 0) {
                                echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->product_discount+$return_sale->product_discount) : $inv->product_discount) . '</td>';
                            }
                            ?>

                            <td colspan="<?= $tcol; ?>" style="text-align:left;"><?= $default_currency->code; ?> <?= lang('total'); ?> </td>

                        </tr>
                    <?php }
                    ?>
                    <?php
                    if ($return_sale) {
                        echo '<tr><td style="text-align:right;">' . $this->sma->formatMoney($return_sale->grand_total) . '</td><td colspan="' . $col . '" style="text-align:right;">' . $default_currency->code .' '. lang("return_total") . '</td></tr>';
                    }
                    if ($inv->surcharge != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right;">' . lang("return_surcharge") . ' (' . $default_currency->code . ')</td><td style="text-align:right;">' . $this->sma->formatMoney($inv->surcharge) . '</td></tr>';
                    }
                    ?>
                    <?php if ($inv->order_discount != 0) {
                        echo '<tr><td style="text-align:right;">'.($inv->order_discount_id ? '<small>('.$inv->order_discount_id.')</small> ' : '') . $this->sma->formatMoney($return_sale ? ($inv->order_discount+$return_sale->order_discount) : $inv->order_discount) . '</td><td colspan="' . $col . '" style="text-align:left;">'. $default_currency->code .' ' . lang('order_discount') .'</td></tr>';
                    }
                    ?>
                    <?php if ($Settings->tax2 && $inv->order_tax != 0) {
                        echo '<tr><td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->order_tax+$return_sale->order_tax) : $inv->order_tax) . '</td><td colspan="' . $col . '" style="text-align:left;">'.$default_currency->code . ' ' . lang('order_tax') .'</td></tr>';
                    }
                    ?>
                    <?php if ($inv->shipping != 0) {
                        echo '<tr><td style="text-align:right;">' . $this->sma->formatMoney($inv->shipping) . '</td><td colspan="' . $col . '" style="text-align:left;">' .$default_currency->code . ' '. lang('shipping') .'</td></tr>';
                    }
                    ?>
                    <tr>
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total); ?></td>
                        <td colspan="<?= $col; ?>"
                            style="text-align:left; font-weight:bold;"> <?= $default_currency->code; ?> <?= lang('total_amount'); ?>

                        </td>

                    </tr>

                    <tr>
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid); ?></td>
                        <td colspan="<?= $col; ?>" style="text-align:left; font-weight:bold;"> <?= $default_currency->code; ?> <?= lang('paid'); ?> </td>

                    </tr>
                    <tr>
                        <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney(($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total) - ($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid)); ?></td>
                        <td colspan="<?= $col; ?>" style="text-align:left; font-weight:bold;"> <?= $default_currency->code; ?> <?= lang('balance'); ?>

                        </td>

                    </tr>

                    </tfoot>
                </table>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <?php if ($inv->note || $inv->note != '') { ?>
                        <div class="well well-sm">
                            <p class="bold">:<?= lang('note'); ?></p>

                            <div><?= $this->sma->decode_html($inv->note); ?></div>
                        </div>
                    <?php }
                    ?>
                </div>
                <div class="clearfix"></div>
                <div class="col-xs-4  pull-right">
                    <p style="height: 80px;text-align:right;"> <?= $biller->company != '-' ? $biller->company : $biller->name; ?> : <?= lang('seller'); ?> </p>
                    <hr>
                    <p style="text-align: right"><?= lang('stamp_sign'); ?></p>
                </div>
                <div class="col-xs-4  pull-left">
                    <p style="height: 80px;text-align:right;"><?= $customer->company ? $customer->company : $customer->name; ?> : <?= lang('customer'); ?> </p>
                    <hr>
                    <p style="text-align: right"><?= lang('stamp_sign'); ?></p>
                </div>
                <div class="clearfix"></div>
                <?php if ($customer->award_points != 0 && $Settings->each_spent > 0) { ?>
                    <div class="col-xs-4 pull-right">
                        <div class="well well-sm">
                            <?=
                            '<p>'.lang('this_sale').': '.floor(($inv->grand_total/$Settings->each_spent)*$Settings->ca_point)
                            .'<br>'.
                            lang('total').' '.lang('award_points').': '. $customer->award_points . '</p>';?>
                        </div>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
</div>
</body>
</html>