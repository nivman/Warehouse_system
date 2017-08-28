<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <base href="<?= site_url() ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $page_title ?> - <?= lang('warehouse_management_system') ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/favicon.png"/>
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-migrate-1.2.1.min.js"></script>

    <![endif]-->
    <noscript>
        <style type="text/css">#loading {
                display: none;
            }</style>
    </noscript>
    <?php if ($Settings->user_rtl) { ?>
        <link href="<?= $assets ?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?= $assets ?>styles/style-rtl.css" rel="stylesheet"/>
        <script type="text/javascript">
            $(document).ready(function () {
                $('.pull-right, .pull-left').addClass('flip');
            });
        </script>
    <?php } ?>
    <script type="text/javascript">
        $(window).load(function () {
            $("#loading").fadeOut("slow");
        });
        function getStyle(el,styleProp)
        {
            var x = document.getElementById(el) || document.body;
            if (x.currentStyle)
                var y = x.currentStyle[styleProp];
            else if (window.getComputedStyle)
                var y = document.defaultView.getComputedStyle(x,null).getPropertyValue(styleProp);
            return y;
        }
          onload = function() {
              var direction = getStyle(null, 'direction');
              if (direction === 'rtl') {
                  var all = document.getElementsByTagName("*");
                  for (var i = 0, max = all.length; i < max; i++) {
                      var str = all[i].innerText;
                      if (str !== undefined)
                      {
                          var res = str.match(/-/g);
                      if (res !== null) {
                          if (all[i].tagName === "P" || all[i].tagName === "H5") {
                              $(all[i]).css("direction", "ltr")
                          }
                      }
                  }
                  }
              }
              ;
          }
    </script>
</head>

<body>
<div id="loading"></div>
<div id="app_wrapper">
    <header id="header" class="navbar">
        <div class="container">
            <div class="btn-group visible-xs pull-right btn-visible-sm">
                <button class="navbar-toggle btn" type="button" data-toggle="collapse" data-target="#sidebar_menu">
                    <span class="fa fa-bars"></span>
                </button>
                <a href="<?= site_url('users/profile/' . $this->session->userdata('user_id')); ?>" class="btn">
                    <span class="fa fa-user"></span>
                </a>
                <a href="<?= site_url('logout'); ?>" class="btn">
                    <span class="fa fa-sign-out"></span>
                </a>
            </div>
            <div class="header-nav">
                <a class="navbar-brand" href="<?= site_url() ?>"><span
                        class="logo"><?= lang('warehouse_management_system') ?></span></a>
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown user_avatar_border">
                        <a class="btn account dropdown-toggle" data-toggle="dropdown" href="#">
                            <img alt=""
                                 src="<?= $this->session->userdata('avatar') ? site_url() . 'assets/uploads/avatars/thumbs/' . $this->session->userdata('avatar') : base_url('assets/images/' . $this->session->userdata('gender') . '.png'); ?>"
                                 class="mini_avatar img-rounded">

                            <div class="user">
                                <span><?= lang('welcome') ?> <?= $this->session->userdata('first_name'); ?></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="<?= site_url('users/profile/' . $this->session->userdata('user_id')); ?>">
                                    <i class="fa fa-user"></i> <?= lang('profile'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= site_url('users/profile/' . $this->session->userdata('user_id') . '/#cpassword'); ?>"><i
                                        class="fa fa-key"></i> <?= lang('change_password'); ?>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?= site_url('logout'); ?>">
                                    <i class="fa fa-sign-out"></i> <?= lang('logout'); ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <span class="nav navbar-nav headerDivider pull-right"></span>

                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown hidden-xs">
                        <a class="btn header-button" title="<?= lang('dashboard') ?>"
                           data-placement="bottom" href="<?= site_url('welcome') ?>">
                            <p><?= lang('dashboard'); ?></p>
                        </a>
                    </li>
                    <?php if ($Owner) { ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn header-button" title="<?= lang('settings') ?>" data-placement="bottom"
                               href="<?= site_url('system_settings') ?>">
                                <p><?= lang('settings'); ?></p>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if ($info) { ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn tip" title="<?= lang('notifications') ?>" data-placement="bottom" href="#"
                               data-toggle="dropdown">
                                <i class="fa fa-info-circle"></i>
                                <span class="number blightOrange black"><?= sizeof($info) ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header">
                                    <i class="fa fa-info-circle"></i>
                                    <?= lang('notifications'); ?></li>
                                <li class="dropdown-content">
                                    <div class="scroll-div">
                                        <div class="top-menu-scroll">
                                            <ol class="oe">
                                                <?php foreach ($info as $n) {
                                                    echo '<li>' . $n->comment . '</li>';
                                                } ?>
                                            </ol>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php if ($events) { ?>
                        <li class="dropdown hidden-xs">
                            <a class="btn tip" title="<?= lang('calendar') ?>" data-placement="bottom" href="#"
                               data-toggle="dropdown">
                                <i class="fa fa-calendar"></i>
                                <span class="number blightOrange black"><?= sizeof($events) ?></span>
                            </a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header">
                                    <i class="fa fa-calendar"></i> <?= lang('upcoming_events'); ?>
                                </li>
                                <li class="dropdown-content">
                                    <div class="top-menu-scroll">
                                        <ol class="oe">
                                            <?php foreach ($events as $event) {
                                                echo '<li>' . date($dateFormats['php_ldate'], strtotime($event->start)) . ' <strong>' . $event->title . '</strong><br>' . $event->description . '</li>';
                                            } ?>
                                        </ol>
                                    </div>
                                </li>
                                <li class="dropdown-footer">
                                    <a href="<?= site_url('calendar') ?>" class="btn-block link">
                                        <i class="fa fa-calendar"></i> <?= lang('calendar') ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } else { ?>
                        <li class="dropdown hidden-xs">
                            <a class="btn  header-button" title="<?= lang('calendar') ?>" data-placement="bottom"
                               href="<?= site_url('calendar') ?>">
                                <p><?= lang('calendar'); ?></p>
                            </a>
                        </li>
                    <?php } ?>

                    <?php if (($Owner || $Admin || $GP['reports-quantity_alerts']) && ($qty_alert_num > 0 || $exp_alert_num > 0)) { ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn header-button" title="<?= lang('alerts') ?>"
                               data-placement="left" data-toggle="dropdown" href="#">
                                <p><?= lang('notifications'); ?></p>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="<?= site_url('reports/quantity_alerts') ?>" class="">
                                        <span class="label label-danger pull-right"
                                              style="margin-top:3px;"><?= $qty_alert_num; ?></span>
                                        <span style="padding-right: 35px;"><?= lang('quantity_alerts') ?></span>
                                    </a>
                                </li>

                            </ul>
                        </li>
                    <?php } ?>
                    <!--                    --><?php //if ($Owner) { ?>
                    <!--                        <li class="dropdown">-->
                    <!--                            <a class="btn bdarkGreen tip" id="today_profit"-->
                    <!--                               title="<span>--><? //= lang('today_profit') ?><!--</span>"-->
                    <!--                               data-placement="bottom" data-html="true" href="-->
                    <? //= site_url('reports/profit') ?><!--"-->
                    <!--                               data-toggle="modal" data-target="#myModal">-->
                    <!--                                <i class="fa fa-hourglass-2"></i>-->
                    <!--                            </a>-->
                    <!--                        </li>-->
                    <!--                    --><?php //} ?>
                </ul>
            </div>
        </div>
    </header>

    <div class="container" id="container">
        <div class="row" id="main-con">
            <table class="lt">
                <tr>
                    <td class="sidebar-con">
                        <div id="sidebar-left">
                            <div class="sidebar-nav nav-collapse collapse navbar-collapse" id="sidebar_menu">
                                <ul class="nav main-menu">
                                    <li class="mm_welcome">
                                        <a href="<?= site_url() ?>">

                                            <span class="text side-menu-test"> <?= lang('dashboard'); ?></span>
                                        </a>
                                    </li>

                                    <?php
                                    if ($Owner || $Admin) {
                                        ?>

                                        <li class="mm_products">
                                            <a class="dropmenu" href="#">

                                                <span class="text side-menu-test"> <?= lang('products'); ?> </span>
                                                <span class="chevron closed"></span>
                                            </a>
                                            <ul>
                                                <li id="products_index">
                                                    <a class="submenu" href="<?= site_url('products'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('list_products'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="products_add">
                                                    <a class="submenu" href="<?= site_url('products/add'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('add_product'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="products_print_barcodes">
                                                    <a class="submenu"
                                                       href="<?= site_url('products/print_barcodes'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('print_barcode_label'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="products_quantity_adjustments">
                                                    <a class="submenu"
                                                       href="<?= site_url('products/quantity_adjustments'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('quantity_adjustments'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="products_add_adjustment">
                                                    <a class="submenu"
                                                       href="<?= site_url('products/add_adjustment'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('add_adjustment'); ?></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="mm_sales">
                                            <a class="dropmenu" href="#">

                                    <span class="text side-menu-test"> <?= lang('sales'); ?>
                                    </span> <span class="chevron closed"></span>
                                            </a>
                                            <ul>
                                                <li id="sales_index">
                                                    <a class="submenu" href="<?= site_url('sales'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('list_sales'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="sales_add">
                                                    <a class="submenu" href="<?= site_url('sales/add'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('add_sale'); ?></span>
                                                    </a>
                                                </li>

                                                <li id="sales_deliveries">
                                                    <a class="submenu" href="<?= site_url('sales/deliveries'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('deliveries'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="quotes_index">
                                                    <a class="submenu" href="<?= site_url('quotes'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('list_quotes'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="quotes_add">
                                                    <a class="submenu" href="<?= site_url('quotes/add'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('add_quote'); ?></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="mm_purchases">
                                            <a class="dropmenu" href="#">

                                    <span class="text side-menu-test"> <?= lang('purchases'); ?>
                                    </span> <span class="chevron closed"></span>
                                            </a>
                                            <ul>
                                                <li id="purchases_index">
                                                    <a class="submenu" href="<?= site_url('purchases'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('list_purchases'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="purchases_add">
                                                    <a class="submenu" href="<?= site_url('purchases/add'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('add_purchase'); ?></span>
                                                    </a>
                                                </li>

                                                <li id="purchases_expenses">
                                                    <a class="submenu" href="<?= site_url('purchases/expenses'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('list_expenses'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="purchases_add_expense">
                                                    <a class="submenu" href="<?= site_url('purchases/add_expense'); ?>"
                                                       data-toggle="modal" data-target="#myModal">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('add_expense'); ?></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="mm_auth mm_customers mm_suppliers mm_billers">
                                            <a class="dropmenu" href="#">
                                                <span class="text side-menu-test"> <?= lang('people'); ?> </span>
                                                <span class="chevron closed"></span>
                                            </a>
                                            <ul>
                                                <?php if ($Owner) { ?>
                                                    <li id="auth_users">
                                                        <a class="submenu" href="<?= site_url('users'); ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('list_users'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="auth_create_user">
                                                        <a class="submenu" href="<?= site_url('users/create_user'); ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('new_user'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="billers_index">
                                                        <a class="submenu" href="<?= site_url('billers'); ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('list_billers'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="billers_index">
                                                        <a class="submenu" href="<?= site_url('billers/add'); ?>"
                                                           data-toggle="modal" data-target="#myModal">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('add_biller'); ?></span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <li id="customers_index">
                                                    <a class="submenu" href="<?= site_url('customers'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('list_customers'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="customers_index">
                                                    <a class="submenu" href="<?= site_url('customers/add'); ?>"
                                                       data-toggle="modal" data-target="#myModal">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('add_customer'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="suppliers_index">
                                                    <a class="submenu" href="<?= site_url('suppliers'); ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('list_suppliers'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="suppliers_index">
                                                    <a class="submenu" href="<?= site_url('suppliers/add'); ?>"
                                                       data-toggle="modal" data-target="#myModal">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('add_supplier'); ?></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>

                                        <?php if ($Owner) { ?>
                                            <li class="mm_system_settings">
                                                <a class="dropmenu" href="#">
                                                    <span class="text side-menu-test"> <?= lang('settings'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="system_settings_index">
                                                        <a href="<?= site_url('system_settings') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('system_settings'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_change_logo">
                                                        <a href="<?= site_url('system_settings/change_logo') ?>"
                                                           data-toggle="modal" data-target="#myModal">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('change_logo'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_currencies">
                                                        <a href="<?= site_url('system_settings/currencies') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('currencies'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_customer_groups">
                                                        <a href="<?= site_url('system_settings/customer_groups') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('customer_groups'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_categories">
                                                        <a href="<?= site_url('system_settings/categories') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('categories'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_expense_categories">
                                                        <a href="<?= site_url('system_settings/expense_categories') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('expense_categories'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_units">
                                                        <a href="<?= site_url('system_settings/units') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('units'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_brands">
                                                        <a href="<?= site_url('system_settings/brands') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('brands'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_tax_rates">
                                                        <a href="<?= site_url('system_settings/tax_rates') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('tax_rates'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_warehouses">
                                                        <a href="<?= site_url('system_settings/warehouses') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('warehouses'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_email_templates">
                                                        <a href="<?= site_url('system_settings/email_templates') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('email_templates'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_user_groups">
                                                        <a href="<?= site_url('system_settings/user_groups') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('group_permissions'); ?></span>
                                                        </a>
                                                    </li>
                                                    <li id="system_settings_backups">
                                                        <a href="<?= site_url('system_settings/backups') ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('backups'); ?></span>
                                                        </a>
                                                    </li>

                                                </ul>
                                            </li>
                                        <?php } ?>
                                        <li class="mm_reports">
                                            <a class="dropmenu" href="#">

                                                <span class="text side-menu-test"> <?= lang('reports'); ?> </span>
                                                <span class="chevron closed"></span>
                                            </a>
                                            <ul>

                                                <li id="individual_comparison_between_products">
                                                    <a href="<?= site_url('reports/comparison') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('comparison_between_products'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="comparison_between_categories">
                                                    <a href="<?= site_url('reports/comparison_categories') ?>">
                                                        <span class="text side-menu-test"> <?= lang('comparison_between_categories'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="products_and_categories_pie">
                                                    <a href="<?= site_url('reports/products_and_categories_pie') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('products_and_categories_pie'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_warehouse_stock">
                                                    <a href="<?= site_url('reports/warehouse_stock') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('warehouse_stock'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_best_sellers">
                                                    <a href="<?= site_url('reports/best_sellers') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('best_sellers'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_quantity_alerts">
                                                    <a href="<?= site_url('reports/quantity_alerts') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('product_quantity_alerts'); ?></span>
                                                    </a>
                                                </li>

                                                <li id="reports_products">
                                                    <a href="<?= site_url('reports/products') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('products_report'); ?></span>
                                                    </a>
                                                <li id="reports_adjustments">
                                                    <a href="<?= site_url('reports/adjustments') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('adjustments_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_categories">
                                                    <a href="<?= site_url('reports/categories') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('categories_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_brands">
                                                    <a href="<?= site_url('reports/brands') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('brands_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_daily_sales">
                                                    <a href="<?= site_url('reports/daily_sales') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('daily_sales'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_monthly_sales">
                                                    <a href="<?= site_url('reports/monthly_sales') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('monthly_sales'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_sales">
                                                    <a href="<?= site_url('reports/sales') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('sales_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_payments">
                                                    <a href="<?= site_url('reports/payments') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('payments_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_profit_loss">
                                                    <a href="<?= site_url('reports/profit_loss') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('profit_and_loss'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_daily_purchases">
                                                    <a href="<?= site_url('reports/daily_purchases') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('daily_purchases'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_monthly_purchases">
                                                    <a href="<?= site_url('reports/monthly_purchases') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('monthly_purchases'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_purchases">
                                                    <a href="<?= site_url('reports/purchases') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('purchases_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_expenses">
                                                    <a href="<?= site_url('reports/expenses') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('expenses_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_customer_report">
                                                    <a href="<?= site_url('reports/customers') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('customers_report'); ?></span>
                                                    </a>
                                                </li>
                                                <li id="reports_supplier_report">
                                                    <a href="<?= site_url('reports/suppliers') ?>">
                                                        <span
                                                            class="text side-menu-test"> <?= lang('suppliers_report'); ?></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>

                                    <?php
                                    } else { // not owner and not admin
                                        ?>
                                        <?php if ($GP['products-index'] || $GP['products-add'] || $GP['products-barcode'] || $GP['products-adjustments'] || $GP['products-stock_count']) { ?>
                                            <li class="mm_products">
                                                <a class="dropmenu" href="#">
                                    <span class="text"> <?= lang('products'); ?>
                                    </span> <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="products_index">
                                                        <a class="submenu" href="<?= site_url('products'); ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('list_products'); ?></span>
                                                        </a>
                                                    </li>
                                                    <?php if ($GP['products-add']) { ?>
                                                        <li id="products_add">
                                                            <a class="submenu" href="<?= site_url('products/add'); ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('add_product'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($GP['products-barcode']) { ?>
                                                        <li id="products_sheet">
                                                            <a class="submenu"
                                                               href="<?= site_url('products/print_barcodes'); ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('print_barcode_label'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($GP['products-adjustments']) { ?>
                                                        <li id="products_quantity_adjustments">
                                                            <a class="submenu"
                                                               href="<?= site_url('products/quantity_adjustments'); ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('quantity_adjustments'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="products_add_adjustment">
                                                            <a class="submenu"
                                                               href="<?= site_url('products/add_adjustment'); ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('add_adjustment'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($GP['products-stock_count']) { ?>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        <?php } ?>

                                        <?php if ($GP['sales-index'] || $GP['sales-add'] || $GP['sales-deliveries']) { ?>
                                            <li class="mm_sales">
                                                <a class="dropmenu" href="#">
                                    <span class="text side-menu-test"> <?= lang('sales'); ?>
                                    </span> <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="sales_index">
                                                        <a class="submenu" href="<?= site_url('sales'); ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('list_sales'); ?></span>
                                                        </a>
                                                    </li>
                                                    <?php if ($GP['sales-add']) { ?>
                                                        <li id="sales_add">
                                                            <a class="submenu" href="<?= site_url('sales/add'); ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('add_sale'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['sales-deliveries']) { ?>
                                                        <li id="sales_deliveries">
                                                            <a class="submenu"
                                                               href="<?= site_url('sales/deliveries'); ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('deliveries'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        <?php } ?>

                                        <?php if ($GP['quotes-index'] || $GP['quotes-add']) { ?>
                                            <li class="mm_quotes">
                                                <a class="dropmenu" href="#">
                                                    <span class="text side-menu-test"> <?= lang('quotes'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="sales_index">
                                                        <a class="submenu" href="<?= site_url('quotes'); ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('list_quotes'); ?></span>
                                                        </a>
                                                    </li>
                                                    <?php if ($GP['quotes-add']) { ?>
                                                        <li id="sales_add">
                                                            <a class="submenu" href="<?= site_url('quotes/add'); ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('add_quote'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        <?php } ?>

                                        <?php if ($GP['purchases-index'] || $GP['purchases-add'] || $GP['purchases-expenses']) { ?>
                                            <li class="mm_purchases">
                                                <a class="dropmenu" href="#">
                                                 <span class="text side-menu-test"> <?= lang('purchases'); ?>
                                               </span> <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <li id="purchases_index">
                                                        <a class="submenu" href="<?= site_url('purchases'); ?>">
                                                            <span
                                                                class="text side-menu-test"> <?= lang('list_purchases'); ?></span>
                                                        </a>
                                                    </li>
                                                    <?php if ($GP['purchases-add']) { ?>
                                                        <li id="purchases_add">
                                                            <a class="submenu" href="<?= site_url('purchases/add'); ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('add_purchase'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($GP['purchases-expenses']) { ?>
                                                        <li id="purchases_expenses">
                                                            <a class="submenu"
                                                               href="<?= site_url('purchases/expenses'); ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('list_expenses'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="purchases_add_expense">
                                                            <a class="submenu"
                                                               href="<?= site_url('purchases/add_expense'); ?>"
                                                               data-toggle="modal" data-target="#myModal">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('add_expense'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        <?php } ?>
                                        <?php if ($GP['customers-index'] || $GP['customers-add'] || $GP['suppliers-index'] || $GP['suppliers-add']) { ?>
                                            <li class="mm_auth mm_customers mm_suppliers mm_billers">
                                                <a class="dropmenu" href="#">
                                                    <span class="text side-menu-test"> <?= lang('people'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <?php if ($GP['customers-index']) { ?>
                                                        <li id="customers_index">
                                                            <a class="submenu" href="<?= site_url('customers'); ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('list_customers'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['customers-add']) { ?>
                                                        <li id="customers_index">
                                                            <a class="submenu" href="<?= site_url('customers/add'); ?>"
                                                               data-toggle="modal" data-target="#myModal">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('add_customer'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['suppliers-index']) { ?>
                                                        <li id="suppliers_index">
                                                            <a class="submenu" href="<?= site_url('suppliers'); ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('list_suppliers'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['suppliers-add']) { ?>
                                                        <li id="suppliers_index">
                                                            <a class="submenu" href="<?= site_url('suppliers/add'); ?>"
                                                               data-toggle="modal" data-target="#myModal">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('add_supplier'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        <?php } ?>

                                        <?php if ($GP['individual_comparison_between_products'] || $GP['reports-quantity_alerts'] || $GP['reports-products'] || $GP['reports-monthly_sales'] || $GP['reports-sales'] || $GP['reports-payments'] || $GP['reports-purchases'] || $GP['reports-customers'] || $GP['reports-suppliers'] || $GP['reports-expenses']) { ?>
                                            <li class="mm_reports">
                                                <a class="dropmenu" href="#">

                                                    <span class="text side-menu-test"> <?= lang('reports'); ?> </span>
                                                    <span class="chevron closed"></span>
                                                </a>
                                                <ul>
                                                    <?php if ($GP['individual_comparison_between_products']) { ?>
                                                        <li id="individual_comparison_between_products">
                                                            <a href="<?= site_url('reports/comparison') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('comparison_between_products'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if ($GP['reports-quantity_alerts']) { ?>
                                                        <li id="reports_quantity_alerts">
                                                            <a href="<?= site_url('reports/quantity_alerts') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('product_quantity_alerts'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }

                                                    if ($GP['reports-products']) { ?>
                                                        <li id="reports_products">
                                                            <a href="<?= site_url('reports/products') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('products_report'); ?></span>
                                                            </a>
                                                        </li>


                                                        <li id="reports_adjustments">
                                                            <a href="<?= site_url('reports/adjustments') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('adjustments_report'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="reports_categories">
                                                            <a href="<?= site_url('reports/categories') ?>">
                                                                <span
                                                                    class="text"> <?= lang('categories_report'); ?></span>
                                                            </a>
                                                        </li>
                                                        <li id="reports_brands">
                                                            <a href="<?= site_url('reports/brands') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('brands_report'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['reports-daily_sales']) { ?>
                                                        <li id="reports_daily_sales">
                                                            <a href="<?= site_url('reports/daily_sales') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('daily_sales'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['reports-monthly_sales']) { ?>
                                                        <li id="reports_monthly_sales">
                                                            <a href="<?= site_url('reports/monthly_sales') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('monthly_sales'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['reports-sales']) { ?>
                                                        <li id="reports_sales">
                                                            <a href="<?= site_url('reports/sales') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('sales_report'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['reports-payments']) { ?>
                                                        <li id="reports_payments">
                                                            <a href="<?= site_url('reports/payments') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('payments_report'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['reports-daily_purchases']) { ?>
                                                        <li id="reports_daily_purchases">
                                                            <a href="<?= site_url('reports/daily_purchases') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('daily_purchases'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['reports-monthly_purchases']) { ?>
                                                        <li id="reports_monthly_purchases">
                                                            <a href="<?= site_url('reports/monthly_purchases') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('monthly_purchases'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['reports-purchases']) { ?>
                                                        <li id="reports_purchases">
                                                            <a href="<?= site_url('reports/purchases') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('purchases_report'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['reports-expenses']) { ?>
                                                        <li id="reports_expenses">
                                                            <a href="<?= site_url('reports/expenses') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('expenses_report'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['reports-customers']) { ?>
                                                        <li id="reports_customer_report">
                                                            <a href="<?= site_url('reports/customers') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('customers_report'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php }
                                                    if ($GP['reports-suppliers']) { ?>
                                                        <li id="reports_supplier_report">
                                                            <a href="<?= site_url('reports/suppliers') ?>">
                                                                <span
                                                                    class="text side-menu-test"> <?= lang('suppliers_report'); ?></span>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                        <?php } ?>

                                    <?php } ?>
                                    <li class="mm_notifications">
                                        <a class="submenu" href="<?= site_url('notifications'); ?>">
                                            <span class="text side-menu-test"> <?= lang('notifications'); ?></span>
                                        </a>
                                    </li>
                                    <li></li>
                                </ul>
                            </div>

                        </div>
                    </td>
                    <td class="content-con">
                        <div id="content">
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <ul class="breadcrumb">
                                        <?php
                                        foreach ($bc as $b) {

                                            if ($b['link'] === '#') {
                                                echo '<li class="active">' . $b['page'] . '</li>';
                                            } else {
                                                echo '<li><a href="' . $b['link'] . '">' . $b['page'] . '</a></li>';
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <?php if ($message) { ?>
                                        <div class="alert alert-success">
                                            <button data-dismiss="alert" class="close" type="button">×</button>
                                            <?= $message; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($error) { ?>
                                        <div class="alert alert-danger">
                                            <button data-dismiss="alert" class="close" type="button">×</button>
                                            <?= $error; ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($warning) { ?>
                                        <div class="alert alert-warning">
                                            <button data-dismiss="alert" class="close" type="button">×</button>
                                            <?= $warning; ?>
                                        </div>
                                    <?php } ?>
                                    <?php
                                    if ($info) {
                                        foreach ($info as $n) {
                                            if (!$this->session->userdata('hidden' . $n->id)) {
                                                ?>
                                                <div class="alert alert-info">
                                                    <a href="#" id="<?= $n->id ?>" class="close hideComment external"
                                                       data-dismiss="alert">&times;</a>
                                                    <?= $n->comment; ?>
                                                </div>
                                            <?php }
                                        }
                                    } ?>
                                    <div class="alerts-con"></div>