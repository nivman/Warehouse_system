<script src='<?= $assets; ?>js/hc/highcharts.js'></script>
<script src="<?= $assets; ?>js/hc/highcharts-3d.js"></script>
<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script>

    (function () {

        let productArr = [];
        let detailsForGraphDeploy = [];
        let chartToDisplay;
        const SHOW_ONE_PRODUCT_GRAPH = 1;
        const SHOW_TWO_PRODUCTS_GRAPH = 2;
        const SHOW_ONE_CATEGORY_GRAPH = 3;
        const SHOW_TWO_CATEGORIES_GRAPH = 4;
        const buttonClickColors = 'background-color: #6AC7CC !important;border-color: #6AC7CC !important;';
        $(document).ready(function () {

            $('#totalPurchased,#totalSold,#sideBySide,#totalCategoryPurchased,#totalCategorySold,#sideBySideCategory').on('click', function (e) {
                productArr = [];
                detailsForGraphDeploy = [];
                if (e.target.id !== 'totalCategoryPurchased' && e.target.id !== 'totalCategorySold' && e.target.id !== 'sideBySideCategory') {
                    sendRequest(e.target.id, 'getProductsDataToPieChart/');
                }
                else {
                    sendRequest(e.target.id, 'getCategoriesReportForPie/');
                }
                highLightPressedButton(e.target.id);
            });

            $('#form').hide();
            $('.toggle_down').click(function () {
                $("#form").slideDown();
                return false;
            });
            $('.toggle_up').click(function () {
                $("#form").slideUp();
                return false;
            });
            sendRequest('totalPurchased', 'getProductsDataToPieChart/');
            $('#totalPurchased').addClass('clicked-pie-button');

        });

        function highLightPressedButton(pressButton){
            switch (pressButton){
                case 'totalPurchased':

                    removeHighlightButtonColor('#totalPurchased');
                    $('#totalPurchased').addClass('clicked-pie-button');
                    break;
                case 'totalSold':

                    $('#totalSold').addClass('clicked-pie-button')
                    removeHighlightButtonColor('#totalSold');
                    break;
                case 'sideBySide':
                    $('#sideBySide').addClass('clicked-pie-button');
                    removeHighlightButtonColor('#sideBySide');
                    break;
                case 'totalCategoryPurchased':
                    $('#totalCategoryPurchased').addClass('clicked-pie-button');
                    removeHighlightButtonColor('#totalCategoryPurchased');
                    break;
                case 'totalCategorySold':
                    $('#totalCategorySold').addClass('clicked-pie-button');
                    removeHighlightButtonColor('#totalCategorySold');
                    break
                case 'sideBySideCategory':
                    $('#sideBySideCategory').addClass('clicked-pie-button');
                    removeHighlightButtonColor('#sideBySideCategory');
                    break;
            }
        }

        function removeHighlightButtonColor(id){
            let ids= ['#totalPurchased','#totalSold','#sideBySide','#totalCategoryPurchased','#totalCategorySold','#sideBySideCategory'];
            for(i=0;i<ids.length;i++){
                if(ids[i]!==id){
                    $(ids[i]).removeClass('clicked-pie-button')
                 //   $(ids[i]).css('cssText','background-color: #012734 !important;');

                }
            }
        }

        function sendRequest(buttonId, targetFunction) {

            const baseUrl = 'http://localhost/RC3.0.2.24/reports/';
            let start_date = $("#start_date").val();
            let end_date = $("#end_date").val();
            $.ajax({
                url: baseUrl + targetFunction + '?v=1&start_date=' + start_date + '&end_date=' + end_date,
                success: function (result) {
                    let json = JSON.parse(result);
                    setSortBy(json, buttonId);
                }
            });
        }

        function setSortBy(json, buttonId) {

            detailsForGraphDeploy = [];
            let graphToDisplay = 0;
            if (buttonId === "totalPurchased") {
                totalPurchasedDisplayDetails();
                json["titleName"] = productsChartsTitles()[0];
                graphToDisplay = 1;
            } else if (buttonId === "totalSold") {
                totalSoldDisplayDetails();
                json["titleName"] = productsChartsTitles()[1];
                graphToDisplay = 1;
            } else if (buttonId === "sideBySide") {
                graphToDisplay = 2;
            } else if (buttonId === "totalCategoryPurchased") {
                totalPurchasedDisplayDetailsCategory();
                json["titleName"] = categoriesChartsTitles()[0];
                graphToDisplay = 3;
            } else if (buttonId === "totalCategorySold") {
                totalSoldDisplayDetailsCategory();
                json["titleName"] = categoriesChartsTitles()[1];
                graphToDisplay = 3;
            } else if (buttonId === "sideBySideCategory") {
                graphToDisplay = 4;
            }
            showAndHideGraph(buttonId);
            formatJsonData(json, graphToDisplay);
        }

        function showAndHideGraph(buttonId) {

            let chartsArr = [$('#prod-pie-chart-purchase'), $('#prod-pie-chart-sold')];
            for (i = 0; i < chartsArr.length; i++) {
                if (buttonId === 'sideBySide' || buttonId === 'sideBySideCategory') {
                    chartsArr[i].show();
                } else {
                    chartsArr[i].hide();
                }
            }
            if (buttonId === 'sideBySide' || buttonId === 'sideBySideCategory') {
                $('#prod-pie-chart').hide();
                $('#category-pie-chart').hide();
            } else if (buttonId === 'totalPurchased' || buttonId === 'totalSold') {
                $('#prod-pie-chart').show();
                $('#category-pie-chart').hide();
            } else if (buttonId === 'totalCategoryPurchased' || buttonId === 'totalCategorySold') {
                $('#prod-pie-chart').hide();
                $('#category-pie-chart').show();
            }
        }

        function formatProductsJson(productData) {

            productData.forEach(function (entry, index) {
                let prodId = entry[6];
                let prodCode = entry[0];
                let prodName = entry[1];
                let totalPurchased = Number(entry[2]);
                let totalSold = Number(entry[3]);
                let profitOrLost = Number(entry[4]);
                let stockQtyAmount = Number(entry[5]);
                productObj = {
                    'prodId': prodId,
                    'prodCode': prodCode,
                    'prodName': prodName,
                    'totalPurchased': totalPurchased,
                    'totalSold': totalSold,
                    'profitOrLost': profitOrLost,
                    'stockQtyAmount': stockQtyAmount
                };
                productArr.push(productObj);
            });
            return productArr;
        }

        function formatCategoryJson(productData) {

            productData.forEach(function (entry, index) {
                let prodCode = entry[0];
                let prodName = entry[1];
                let stockBought = Number(entry[2]);
                let stockSold = Number(entry[3]);
                let totalPurchased = Number(entry[4]);
                let totalSold = Number(entry[5]);
                let balance = entry[6];
                productObj = {
                    'prodCode': prodCode,
                    'prodName': prodName,
                    'totalPurchased': totalPurchased,
                    'totalSold': totalSold,
                    'stockBought': stockBought,
                    'stockSold': stockSold,
                    'balance': balance
                };
                productArr.push(productObj);
            });
            return productArr;
        }

        function formatJsonData(json, graphToDisplay) {
            let productObj = {};
            let productData = json.aaData;
            let chartToDisplayArr = [];
            let displayChart = [];
            if (graphToDisplay < 3) {
                productArr = formatProductsJson(productData);
            } else {
                productArr = formatCategoryJson(productData);
            }
            let title = json.titleName;
            if (graphToDisplay === SHOW_ONE_PRODUCT_GRAPH) {
                sortResultByRequest(productArr);
                setInGraph(productArr, chartToDisplay, title);
            } else if (graphToDisplay === SHOW_ONE_CATEGORY_GRAPH) {
                sortResultByRequest(productArr);
                setInGraph(productArr, chartToDisplay, title);
            }
            else {
                chartToDisplayArr = ['prod-pie-chart-purchase', 'prod-pie-chart-sold'];
                if (graphToDisplay === SHOW_TWO_PRODUCTS_GRAPH) {
                    displayChart.push(totalPurchasedDisplayDetails, totalSoldDisplayDetails);
                } else if (graphToDisplay === SHOW_TWO_CATEGORIES_GRAPH) {
                    displayChart.push(totalPurchasedDisplayDetailsCategory, totalSoldDisplayDetailsCategory);
                }
                let i = 0;
                while (i < 2) {
                    displayChart[i]();
                    chartToDisplay = chartToDisplayArr[i];
                    sortResultByRequest(productArr);
                    if (graphToDisplay === SHOW_TWO_PRODUCTS_GRAPH) {
                        title = productsChartsTitles()[i];
                    } else {
                        title = categoriesChartsTitles()[i];
                    }
                    setInGraph(productArr, chartToDisplay, title);
                    i++;
                }
            }
        }

        function productsChartsTitles() {

            let purchased = "<?= lang('total_purchased_per_product'); ?>";
            let sold = "<?= lang('total_sold_per_product'); ?>";
            return [purchased, sold];
        }

        function categoriesChartsTitles() {

            let categoryPurchased = "<?= lang('total_purchased_per_category'); ?>";
            let categorySold = "<?= lang('total_sold_per_category'); ?>";
            return [categoryPurchased, categorySold];
        }

        function totalPurchasedDisplayDetails() {

            detailsForGraphDeploy = [];
            let sortA = new Function('a', 'return a.totalPurchased');
            let sortB = new Function('b', 'return b.totalPurchased');
            let yProperty = new Function('productArr', 'return productArr[i].totalPurchased');
            chartToDisplay = 'prod-pie-chart';
            detailsForGraphDeploy.push(sortA, sortB, yProperty, chartToDisplay);
        }

        function totalSoldDisplayDetails() {

            detailsForGraphDeploy = [];
            let sortA = new Function('a', 'return a.totalSold');
            let sortB = new Function('b', 'return b.totalSold');
            let yProperty = new Function('productArr', 'return productArr[i].totalSold');
            chartToDisplay = 'prod-pie-chart';
            detailsForGraphDeploy.push(sortA, sortB, yProperty, chartToDisplay);
        }

        function totalPurchasedDisplayDetailsCategory() {

            detailsForGraphDeploy = [];
            let sortA = new Function('a', 'return a.totalPurchased');
            let sortB = new Function('b', 'return b.totalPurchased');
            let yProperty = new Function('productArr', 'return productArr[i].totalPurchased');
            chartToDisplay = 'category-pie-chart';
            detailsForGraphDeploy.push(sortA, sortB, yProperty, chartToDisplay);
        }

        function totalSoldDisplayDetailsCategory() {

            detailsForGraphDeploy = [];
            let sortA = new Function('a', 'return a.totalSold');
            let sortB = new Function('b', 'return b.totalSold');
            let yProperty = new Function('productArr', 'return productArr[i].totalSold');
            chartToDisplay = 'category-pie-chart';
            detailsForGraphDeploy.push(sortA, sortB, yProperty, chartToDisplay);
        }

        function sortResultByRequest(productArr) {

            productArr.sort(function (a, b) {
                return parseFloat(detailsForGraphDeploy[0](a)) - parseFloat(detailsForGraphDeploy[1](b));
            });
        }

        function selectSlice(clickChart, compareCart, series, data, prodName) {

            let list = [];
            for (var i = 0; i <= series[0].data.length; i++) {
                list.push(i);
            }
            $.each(list, function (index, value) {
                if (data[index] !== undefined) {

                    if (clickChart.series[0].data[index].name === prodName) {
                        clickChart.series[0].data[index].select()
                    }
                }
            });
        }

        function setHeight(chartToDisplay) {

            if (chartToDisplay == 'prod-pie-chart' || chartToDisplay === 'category-pie-chart') {
                return '45%';
            } else {
                return '90%';
            }
        }

        function setFontSize(chartToDisplay) {

            if (chartToDisplay == 'prod-pie-chart' || chartToDisplay === 'category-pie-chart') {
                return '14px';
            } else {
                return '12px';
            }
        }

        function setInGraph(productArr, chartToDisplay, title) {

            let data = [];
            let series = [];
            let objSeries = {};
            let height = setHeight(chartToDisplay);
            let fontSize = setFontSize(chartToDisplay);
            for (i = 0; i < productArr.length; i++) {
                objSeries = {'name': productArr[i].prodName, 'y': detailsForGraphDeploy[2](productArr)};
                data.push(objSeries);
            }
            series.push({data});
            let chart = new Highcharts.Chart({
                chart: {
                    renderTo: chartToDisplay,
                    type: 'pie',
                    height: height,
                    options3d: {
                        enabled: true,
                        alpha: 45,
                        beta: 0
                    }
                },
                credits: {enabled: false},
                title: {text: title},
                yAxis: {tickInterval: 100, title: ''},
                plotOptions: {
                    pie: {
                        depth: 35,
                        allowPointSelect: true,
                        slicedOffset: 45,
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function (e) {
                                    let target = $(e.target).parents("div").find('[data-highcharts-chart]').prevObject[1].id;
                                    let prodName = this.name;
                                    if (target !== 'prod-pie-chart-purchase' && target !== 'prod-pie-chart-sold') {
                                        return;
                                    }
                                    if (target === 'prod-pie-chart-purchase') {
                                        selectSlice(seriesS, seriesP, series, data, prodName);
                                    } else {
                                        selectSlice(seriesP, seriesS, series, data, prodName);
                                    }
                                }
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            y: -5,
                            format: '\u202B' + '{point.name} {point.percentage:.1f} %',
                            style: {
                                fontSize: fontSize,
                                fontFamily: 'sans-serif',
                                fontWeight: 500,
                                zIndex: 1
                            },
                            useHTML: true
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#FCFFC5',
                    formatter: function () {
                        return '\u202B' + '<b>' + this.point.name + '</b><br><?= lang('total_amount'); ?> ' + ' :  ' + this.point.y;
                    },
                    style: {
                        fontSize: '14px',
                        fontFamily: 'sans-serif',
                        zIndex: 22
                    },
                    useHTML: true
                },
                series
            });
            if (chartToDisplay === "prod-pie-chart-purchase") {
                seriesP = chart;
            } else {
                seriesS = chart;
            }
        }
    })()

</script>
<div class="pie_col_buttons box">
    <div class="pie_buttons">
        <button id="totalPurchased"
                class="btn btn-primary pie_button"><?= lang('total_purchased_per_product'); ?></button>
        <button id="totalSold" class="btn btn-primary pie_button"><?= lang('total_sold_per_product'); ?></button>
        <button id="totalCategoryPurchased"
                class="btn btn-primary pie_button"><?= lang('total_purchased_per_category'); ?></button>
        <button id="totalCategorySold"
                class="btn btn-primary pie_button"><?= lang('total_sold_per_category'); ?></button>

        <button id="sideBySide"
                class="btn btn-primary pie_button"><?= lang('compare_sold_and_purchased_products'); ?></button>
        <button id="sideBySideCategory"
                class="btn btn-primary pie_button"><?= lang('compare_sold_and_purchased_category'); ?></button>
    </div>


</div>
<div class='box'>

    <div class='box-header box_pie_charts_header'>

        <h2 class='pie_charts_header'><i class='fa-fw fa fa-barcode'></i><?= lang('products_and_categories_pie'); ?>
        </h2>

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
        <div class="box-content products_and_categories_pie">
            <div class="row">
                <div class="col-lg-12 products_categories_pie_form">
                    <div id="form" class="form_pie">

                        <div class="row">

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <?= lang("start_date", "start_date"); ?>
                                    <input class="inputs-report-from form-control datetime" id="start_date"/>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <?= lang("end_date", "end_date"); ?>
                                    <input class="inputs-report-from form-control datetime" id="end_date"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id='prod-pie-chart'></div>
    <div id="prod-pie-chart-sold"></div>
    <div id="prod-pie-chart-purchase"></div>
    <div id='category-pie-chart'></div>

</div>

<script type='text/javascript' src='<?= $assets ?>js/html2canvas.min.js'></script>

