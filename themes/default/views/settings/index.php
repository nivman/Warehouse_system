<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$wm = array('0' => lang('no'), '1' => lang('yes'));
$ps = array('0' => lang("ltr"), '1' => lang("rtl"));
$cps = array('0' => lang("enable"), '1' => lang("disable"));
?>
<script>
    $(document).ready(function () {
        <?php if(isset($message)) { echo 'localStorage.clear();'; } ?>
        var timezones = <?= json_encode(DateTimeZone::listIdentifiers(DateTimeZone::ALL)); ?>;
        $('#timezone').autocomplete({
            source: timezones
        });
        if ($('#protocol').val() == 'smtp') {
            $('#smtp_config').slideDown();
        } else if ($('#protocol').val() == 'sendmail') {
            $('#sendmail_config').slideDown();
        }
        $('#protocol').change(function () {
            if ($(this).val() == 'smtp') {
                $('#sendmail_config').slideUp();
                $('#smtp_config').slideDown();
            } else if ($(this).val() == 'sendmail') {
                $('#smtp_config').slideUp();
                $('#sendmail_config').slideDown();
            } else {
                $('#smtp_config').slideUp();
                $('#sendmail_config').slideUp();
            }
        });
        $('#overselling').change(function () {
            if ($(this).val() == 1) {
                if ($('#accounting_method').select2("val") != 2) {
                    bootbox.alert('<?=lang('overselling_will_only_work_with_AVCO_accounting_method_only')?>');
                    $('#accounting_method').select2("val", '2');
                }
            }
        });
        $('#accounting_method').change(function () {
            var oam = <?=$Settings->accounting_method?>, nam = $(this).val();
            if (oam != nam) {
                bootbox.alert('<?=lang('accounting_method_change_alert')?>');
            }
        });
        $('#accounting_method').change(function () {
            if ($(this).val() != 2) {
                if ($('#overselling').select2("val") == 1) {
                    bootbox.alert('<?=lang('overselling_will_only_work_with_AVCO_accounting_method_only')?>');
                    $('#overselling').select2("val", 0);
                }
            }
        });
        $('#item_addition').change(function () {
            if ($(this).val() == 1) {
                bootbox.alert('<?=lang('product_variants_feature_x')?>');
            }
        });
        var sac = $('#sac').val()
        if (sac == 1) {
            $('.nsac').slideUp();
        } else {
            $('.nsac').slideDown();
        }
        $('#sac').change(function () {
            if ($(this).val() == 1) {
                $('.nsac').slideUp();
            } else {
                $('.nsac').slideDown();
            }
        });
    });
</script>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-cog"></i><?= lang('system_settings'); ?></h2>

    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('update_info'); ?></p>

                <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo form_open_multipart("system_settings", $attrib);
                ?>
                <div class="row">
                    <div class="col-lg-12">

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('site_config') ?></legend>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("language", "language"); ?>
                                    <?php
                                    $lang = array(
                                        'english' => 'English',
                                        'hebrew' => 'hebrew'
                                    );
                                    echo form_dropdown('language', $lang, $Settings->language, 'class="form-control tip" id="language" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="currency"><?= lang("default_currency"); ?></label>

                                    <div class="controls"> <?php
                                        foreach ($currencies as $currency) {
                                            $cu[$currency->code] = $currency->name;
                                        }
                                        echo form_dropdown('currency', $cu, $Settings->default_currency, 'class="form-control tip" id="currency" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("accounting_method", "accounting_method"); ?>
                                    <?php
                                    $am = array(0 => 'FIFO (First In First Out)', 1 => 'LIFO (Last In First Out)', 2 => 'AVCO (Average Cost Method)');
                                    echo form_dropdown('accounting_method', $am, $Settings->accounting_method, 'class="form-control tip" id="accounting_method" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="email"><?= lang("default_email"); ?></label>

                                    <?= form_input('email', $Settings->default_email, 'class="form-control tip" required="required" id="email"'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="rtl"><?= lang("set_view_direction"); ?></label>

                                    <div class="controls">
                                        <?php
                                        echo form_dropdown('rtl', $ps, $Settings->rtl, 'id="rtl" class="form-control tip" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label"
                                           for="customer_group"><?= lang("default_customer_group"); ?></label>
                                    <?php
                                    foreach ($customer_groups as $customer_group) {
                                        $pgs[$customer_group->id] = $customer_group->name;
                                    }
                                    echo form_dropdown('customer_group', $pgs, $Settings->customer_group, 'class="form-control tip" id="customer_group" style="width:100%;" required="required"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label"
                                           for="price_group"><?= lang("default_price_group"); ?></label>
                                    <?php
                                    foreach ($price_groups as $price_group) {
                                        $cgs[$price_group->id] = $price_group->name;
                                    }
                                    echo form_dropdown('price_group', $cgs, $Settings->price_group, 'class="form-control tip" id="price_group" style="width:100%;" required="required"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="captcha"><?= lang("login_captcha"); ?></label>

                                    <div class="controls">
                                        <?php
                                        echo form_dropdown('captcha', $cps, $Settings->captcha, 'id="captcha" class="form-control tip" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>


                            <!--<div class="col-md-4">
                        <div class="form-group">
                            <?= lang('reg_ver', 'reg_ver'); ?>
                            <div class="controls">  <?php
                            echo form_dropdown('reg_ver', $wm, (isset($_POST['reg_ver']) ? $_POST['reg_ver'] : $Settings->reg_ver), 'class="tip form-control" required="required" id="reg_ver" style="width:100%;"');
                            ?> </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('allow_reg', 'allow_reg'); ?>
                            <div class="controls">  <?php
                            echo form_dropdown('allow_reg', $wm, (isset($_POST['allow_reg']) ? $_POST['allow_reg'] : $Settings->allow_reg), 'class="tip form-control" required="required" id="allow_reg" style="width:100%;"');
                            ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?= lang('reg_notification', 'reg_notification'); ?>
                            <div class="controls">  <?php
                            echo form_dropdown('reg_notification', $wm, (isset($_POST['reg_notification']) ? $_POST['reg_notification'] : $Settings->reg_notification), 'class="tip form-control" required="required" id="reg_notification" style="width:100%;"');
                            ?>
                            </div>
                        </div>
                    </div>-->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label"
                                           for="restrict_calendar"><?= lang("calendar"); ?></label>

                                    <div class="controls">
                                        <?php
                                        $opt_cal = array(1 => lang('private'), 0 => lang('shared'));
                                        echo form_dropdown('restrict_calendar', $opt_cal, $Settings->restrict_calendar, 'class="form-control tip" required="required" id="restrict_calendar" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label"
                                           for="warehouse"><?= lang("default_warehouse"); ?></label>

                                    <div class="controls"> <?php
                                        foreach ($warehouses as $warehouse) {
                                            $wh[$warehouse->id] = $warehouse->name . ' (' . $warehouse->code . ')';
                                        }
                                        echo form_dropdown('warehouse', $wh, $Settings->default_warehouse, 'class="form-control tip" id="warehouse" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("default_biller", "biller"); ?>
                                    <?php
                                    $bl[""] = "";
                                    foreach ($billers as $biller) {
                                        $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $Settings->default_biller), 'id="biller" data-placeholder="' . lang("select") . ' ' . lang("biller") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('products') ?></legend>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("product_tax", "tax_rate"); ?>
                                    <?php
                                    echo form_dropdown('tax_rate', $cps, $Settings->default_tax_rate, 'class="form-control tip" id="tax_rate" required="required" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="racks"><?= lang("racks"); ?></label>

                                    <div class="controls">
                                        <?php
                                        echo form_dropdown('racks', $cps, $Settings->racks, 'id="racks" class="form-control tip" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="attributes"><?= lang("attributes"); ?></label>

                                    <div class="controls">
                                        <?php
                                        echo form_dropdown('attributes', $cps, $Settings->attributes, 'id="attributes" class="form-control tip"  required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                             <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label"
                                           for="remove_expired"><?= lang("remove_expired"); ?></label>

                                    <div class="controls">
                                        <?php
                                        $re_opts = array(0 => lang('no') . ', ' . lang('i_ll_remove'), 1 => lang('yes') . ', ' . lang('remove_automatically'));
                                        echo form_dropdown('remove_expired', $re_opts, $Settings->remove_expired, 'id="remove_expired" class="form-control tip" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="image_size"><?= lang("image_size"); ?> (Width :
                                        Height) *</label>

                                    <div class="row">
                                        <div class="col-xs-6">
                                            <?= form_input('iwidth', $Settings->iwidth, 'class="form-control tip" id="iwidth" placeholder="image width" required="required"'); ?>
                                        </div>
                                        <div class="col-xs-6">
                                            <?= form_input('iheight', $Settings->iheight, 'class="form-control tip" id="iheight" placeholder="image height" required="required"'); ?></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="thumbnail_size"><?= lang("thumbnail_size"); ?>
                                        (Width : Height) *</label>

                                    <div class="row">
                                        <div class="col-xs-6">
                                            <?= form_input('twidth', $Settings->twidth, 'class="form-control tip" id="twidth" placeholder="thumbnail width" required="required"'); ?>
                                        </div>
                                        <div class="col-xs-6">
                                            <?= form_input('theight', $Settings->theight, 'class="form-control tip" id="theight" placeholder="thumbnail height" required="required"'); ?>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
<!--                            <div class="col-md-4">-->
<!--                                <div class="form-group">-->
<!--                                    --><?//= lang('watermark', 'watermark'); ?>
<!--                                    --><?php
//                                    echo form_dropdown('watermark', $wm, (isset($_POST['watermark']) ? $_POST['watermark'] : $Settings->watermark), 'class="tip form-control" required="required" id="watermark" style="width:100%;"');
//                                    ?>
<!--                                </div>-->
<!--                            </div>-->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('display_all_products', 'display_all_products'); ?>
                                    <?php
                                    $dopts = array(0 => lang('hide_with_0_qty'), 1 => lang('show_with_0_qty'));
                                    echo form_dropdown('display_all_products', $dopts, (isset($_POST['display_all_products']) ? $_POST['display_all_products'] : $Settings->display_all_products), 'class="tip form-control" required="required" id="display_all_products" style="width:100%;"');
                                    ?>
                                </div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang('update_cost_with_purchase', 'update_cost'); ?>
                                    <?= form_dropdown('update_cost', $wm, $Settings->update_cost, 'class="form-control" id="update_cost" required="required"'); ?>
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border"><?= lang('sales') ?></legend>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="overselling"><?= lang("over_selling"); ?></label>

                                    <div class="controls">
                                        <?php
                                        $opt = array(1 => lang('yes'), 0 => lang('no'));
                                        echo form_dropdown('restrict_sale', $opt, $Settings->overselling, 'class="form-control tip" id="overselling" required="required" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("invoice_tax", "tax_rate2"); ?>
                                    <?php $tr['0'] = lang("disable");
                                    foreach ($tax_rates as $rate) {
                                        $tr[$rate->id] = $rate->name;
                                    }
                                    echo form_dropdown('tax_rate2', $tr, $Settings->default_tax_rate2, 'id="tax_rate2" class="form-control tip" required="required" style="width:100%;"'); ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("set_focus", "set_focus"); ?>
                                    <?php
                                    $sfopts = array(0 => lang('add_item_input'), 1 => lang('last_order_item'));
                                    echo form_dropdown('set_focus', $sfopts, (isset($_POST['set_focus']) ? $_POST['set_focus'] : $Settings->set_focus), 'id="set_focus" data-placeholder="' . lang("select") . ' ' . lang("set_focus") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label" for="invoice_view"><?= lang("invoice_view"); ?></label>

                                    <div class="controls">
                                        <?php
                                        $opt_inv = array(1 => lang('tax_invoice'), 0 => lang('standard'));
                                        echo form_dropdown('invoice_view', $opt_inv, $Settings->invoice_view, 'class="form-control tip" required="required" id="invoice_view" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div style="clear: both; height: 10px;"></div>
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="controls">
                            <?= form_submit('update_settings', lang("update_settings"), 'class="btn btn-primary"'); ?>
                        </div>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
<!--        <div class="alert alert-info" role="alert"><p>-->
<!--                <strong>Cron Job:</strong> <code>0 1 * * * wget -qO- --><?//= site_url('cron/run'); ?><!-- &gt;/dev/null 2&gt;&amp;1</code>-->
<!--                to run at 1:00 AM daily. For local installation, you can run cron job manually at any time.-->
<!--                --><?php //if (!DEMO) { ?>
<!--                    <a class="btn btn-primary btn-xs pull-right" target="_blank" href="--><?//= site_url('cron/run'); ?><!--">Run-->
<!--                        cron job now</a>-->
<!--                --><?php //} ?>
<!--            </p></div>-->
    </div>
</div>
