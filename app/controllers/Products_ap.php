<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Products_ap extends MY_Controller
{

    function __construct()
    {


        parent::__construct();
        require_once('./system/helpers/php_image_magician.php');
        $this->lang->load('auth_app', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->form_validation->set_rules('identity', 'username', 'required|callback_check_login_details');
        $this->form_validation->set_rules('password', 'password', 'required|callback_check_login_details');
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
        $this->load->model('auth_app_model');
        $this->load->model('products_model_ap');
        $this->load->library('ion_auth');
//        if (!$this->loggedIn) {
//            $this->session->set_userdata('requested_page', $this->uri->uri_string());
//            $this->sma->md('login');
//        }

        $this->lang->load('products', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->model('products_model');

        $this->digital_upload_path = 'files/';
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->image_types = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size = '1024';
        $this->popup_attributes = array('width' => '900', 'height' => '600', 'window_name' => 'sma_popup', 'menubar' => 'yes', 'scrollbars' => 'yes', 'status' => 'no', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0');
    }

    function index($warehouse_id = NULL)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
        }

        $this->data['supplier'] = $this->input->get('supplier') ? $this->site->getCompanyByID($this->input->get('supplier')) : NULL;
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => '#', 'page' => lang('products')));
        $meta = array('page_title' => lang('products'), 'bc' => $bc);
        $this->page_construct('products/index', $meta, $this->data);
    }

    function getProducts($warehouse_id = NULL)
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        if ($this->auth_app_model->login($username, $password)) {
            $this->load->model('products_model_ap');
            $this->data['products'] = $this->products_model_ap->joinProductAndCategory();
            echo json_encode($this->data['products']);
        }
    }

    function getComboProduct()
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        $id = $data->id;
        if (!$this->auth_app_model->login($username, $password)) {
            return true;
        }
        $combo = $this->products_model_ap->getProductComboItems($id);

        echo json_encode($combo);
    }

    /* ------------------------------------------------------- */
    function getProductDetails()
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;

        if (!$this->auth_app_model->login($username, $password)) {

            return true;
        }
        $this->data['categories'] = $this->site->getAllCategories();
        $this->data['tax_rates'] = $this->site->getAllTaxRates();
        $this->data['brands'] = $this->site->getAllBrands();
        $this->data['base_units'] = $this->site->getAllBaseUnits();
        $this->data['suppliers'] = $this->site->getAllSuppliers();
        $warehouses = $this->site->getAllWarehouses();

        $product_data = new stdClass();
        $product_data->suppliers = $this->data['suppliers'];
        $product_data->categories = $this->data['categories'];
        $product_data->taxRtes = $this->data['tax_rates'];
        $product_data->brands = $this->data['brands'];
        $product_data->baseUnits = $this->data['base_units'];
        $product_data->warehouses = $warehouses;

        echo json_encode($product_data);

    }

    function getAllWarehouses($warehouse)
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;

        if (!$this->auth_app_model->login($username, $password)) {
            return true;
        }
        $warehouse->warehouse = $this->site->getAllWarehouses();

        echo json_encode($warehouse);
    }

    function modal_view()
    {

        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        $id = $data->id;

        if (!$this->auth_app_model->login($username, $password)) {

            return true;
        }


        // $this->sma->checkPermissions('index', TRUE);

        $pr_details = $this->site->getProductByID($id);

        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('product_not_found'));
            $this->sma->md();
        }
        $this->data['barcode'] = "<img src='" . site_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->products_model->getProductPhotos($id);
        $this->data['category'] = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['warehouses'] = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants'] = $this->products_model->getProductOptions($id);
        echo json_encode($this->data);
        return;
        $this->load->view($this->theme . 'products/modal_view', $this->data);
    }

    public function add($product_saved)
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        if (!$this->auth_app_model->login($username, $password)) {

            return true;
        }
        $warehouses = $this->site->getAllWarehouses();

        $img = $data->img;
        $type = $data->type;
        $name = $data->name;
        $code = $data->code;
        $barcodeSymbol = $data->barcodeSymbol;
        $brand = $data->brand;
        $unit = $data->unit;
        $category = $data->category;
        $subCategory = $data->subCategory;
        $warehouseDetails = $data->warehouseDetails;
        $detailsInvoice = $data->detailsInvoice;
        $defaultSale = $data->defaultSale;
        $defaultPurchase = $data->defaultPurchase;
        $cost = $data->cost;
        $price = $data->price;
        $tax = $data->tax;
        $taxMethod = $data->taxMethod;
        $details = $data->details;
        $quantity = $data->quantity;
        $productQuantity = $data->productQuantity;
        $trackQuantity = $data->trackQuantity;
        $productCustomField1 = $data->productCustomField1;
        $productCustomField2 = $data->productCustomField2;
        $productCustomField3 = $data->productCustomField3;
        $productCustomField4 = $data->productCustomField4;
        $productCustomField5 = $data->productCustomField5;
        $productCustomField6 = $data->productCustomField6;
        $promotionPrice = $data->promotionPrice;
        $promotion = $data->promotion;
        $promotionStartDate = $data->promotionStartDate;
        $promotionEndDate = $data->promotionEndDate;
        $supplierName1 = $data->supplierName1;
        $supplierName2 = $data->supplierName2;
        $supplierName3 = $data->supplierName3;
        $supplierName4 = $data->supplierName4;
        $supplierName5 = $data->supplierName5;
        $supplierPartNo1 = $data->supplierPartNo1;
        $supplierPartNo2 = $data->supplierPartNo2;
        $supplierPartNo3 = $data->supplierPartNo3;
        $supplierPartNo4 = $data->supplierPartNo4;
        $supplierPartNo5 = $data->supplierPartNo5;
        $supplierPriceNumber1 = $data->supplierPriceNumber1;
        $supplierPriceNumber2 = $data->supplierPriceNumber2;
        $supplierPriceNumber3 = $data->supplierPriceNumber3;
        $supplierPriceNumber4 = $data->supplierPriceNumber4;
        $supplierPriceNumber5 = $data->supplierPriceNumber5;

        $array = json_decode($warehouseDetails, true);
        foreach ($array as $key) {
            $warehouse_qty[] = array(
                'warehouse_id' => $key["id"],
                'quantity' => $key["quantity"],
                'rack' => $key["rack"] ? $key["rack"] : NULL
            );
        }
        $photo = "no_image.jpg";
        if ($img != "") {
            $this->load->library('upload');
            $config['upload_path'] = $this->upload_path;
            $config['allowed_types'] = $this->image_types;
            $config['file_name'] = $this->upload->generateRandomString(32);
            $config['max_size'] = $this->allowed_file_size;
            $config['max_width'] = $this->Settings->iwidth;
            $config['max_height'] = $this->Settings->iheight;
            $config['overwrite'] = FALSE;
            $config['max_filename'] = 25;
            $config['encrypt_name'] = TRUE;
            $this->upload->initialize($config);
            $photo = $this->upload->file_name . '.jpg';
            $this->load->library('image_lib');
            $config['image_library'] = 'gd2';
            $config['source_image'] = './' . $this->upload_path . $photo;
            $config['new_image'] = './' . $this->thumbs_path . $photo;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = $this->Settings->twidth;
            $config['height'] = $this->Settings->theight;

            $img = str_replace('data:image/jpg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $img = base64_decode($img);
            $file = $config['source_image'];
            file_put_contents($file, $img);
            $img = base64_decode($img);
            $source_img = imagecreatefromstring($img);
            $rotated_img = imagerotate($source_img, 90, 0);
            imagejpeg($rotated_img, $file, 10);
            $magicianObj = new imageLib($file);
            $magicianObj->resizeImage(50, 50);
            $magicianObj->saveImage('./assets/uploads/thumbs/' . $config['file_name'] . '.jpg', 100);
            imagedestroy($source_img);
        }
        $productData = array(
            'code' => $code,
            'barcode_symbology' => $barcodeSymbol,
            'name' => $name,
            'type' => $type,
            'brand' => $brand,
            'category_id' => $category,
            'subcategory_id' => $subCategory ? $subCategory : NULL,
            'cost' => $this->sma->formatDecimal($cost),
            'price' => $this->sma->formatDecimal($price),
            'unit' => $unit,
            'sale_unit' => $unit,
            'purchase_unit' => $unit,
            'tax_rate' => $tax,
            'tax_method' => $taxMethod,
            'alert_quantity' => $quantity,
            'track_quantity' => $trackQuantity ? $trackQuantity : '0',
            'details' => $detailsInvoice,
            'product_details' => $details,
            'supplier1' => $supplierName1,
            'supplier1price' => $this->sma->formatDecimal($supplierPartNo1),
            'quantity' => $productQuantity,
            'image' => $photo,
            'supplier2' => $supplierName2,
            'supplier2price' => $this->sma->formatDecimal($supplierPartNo2),
            'supplier3' => $supplierName3,
            'supplier3price' => $this->sma->formatDecimal($supplierPartNo3),
            'supplier4' => $supplierName4,
            'supplier4price' => $this->sma->formatDecimal($supplierPartNo4),
            'supplier5' => $supplierName5,
            'supplier5price' => $this->sma->formatDecimal($supplierPartNo5),
            'cf1' => $productCustomField1,
            'cf2' => $productCustomField2,
            'cf3' => $productCustomField3,
            'cf4' => $productCustomField4,
            'cf5' => $productCustomField5,
            'cf6' => $productCustomField6,
            'promotion' => $promotion,
            'promo_price' => $this->sma->formatDecimal($promotionPrice),
            'start_date' => $promotionStartDate ? $this->sma->fsd($promotionStartDate) : NULL,
            'end_date' => $promotionStartDate ? $this->sma->fsd($promotionEndDate) : NULL,
            'supplier1_part_no' => $supplierPriceNumber1,
            'supplier2_part_no' => $supplierPriceNumber2,
            'supplier3_part_no' => $supplierPriceNumber3,
            'supplier4_part_no' => $supplierPriceNumber4,
            'supplier5_part_no' => $supplierPriceNumber5,
            'file' => "",
            //'file' => $this->input->post('file_link'),
        );

        if ($type == "combo") {
            $comboProducts = json_decode($data->comboProducts);
            foreach ($comboProducts as $prod) {
                $items[] = array(
                    'item_code' => $prod->comboCode,
                    'quantity' => $prod->comboQty,
                    'unit_price' => $prod->comboPrice,
                );
            }
        }

        $is_save = $this->products_model_ap->addProduct($productData, $items, $warehouse_qty, NULL, $photos);
        $product_saved->is_save = $is_save;
        echo json_encode($product_saved);
    }

    public function update()
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        $id = $data->id;
        if (!$this->auth_app_model->login($username, $password)) {

            return true;
        }

        $warehouses = $this->site->getAllWarehouses();

        $img = $data->img;
        $type = $data->type;
        $name = $data->name;
        $code = $data->code;
        $barcodeSymbol = $data->barcodeSymbol;
        $brand = $data->brand;
        $unit = $data->unit;
        $category = $data->category;
        $subCategory = $data->subCategory;
        $warehouseDetails = $data->warehouseDetails;
        $detailsInvoice = $data->detailsInvoice;
        $defaultSale = $data->defaultSale;
        $defaultPurchase = $data->defaultPurchase;
        $cost = $data->cost;
        $price = $data->price;
        $tax = $data->tax;
        $taxMethod = $data->taxMethod;
        $details = $data->details;
        $quantity = $data->quantity;
        $productQuantity = $data->productQuantity;
        $trackQuantity = $data->trackQuantity;
        $productCustomField1 = $data->productCustomField1;
        $productCustomField2 = $data->productCustomField2;
        $productCustomField3 = $data->productCustomField3;
        $productCustomField4 = $data->productCustomField4;
        $productCustomField5 = $data->productCustomField5;
        $productCustomField6 = $data->productCustomField6;
        $promotionPrice = $data->promotionPrice;
        $promotion = $data->promotion;
        $promotionStartDate = $data->promotionStartDate;
        $promotionEndDate = $data->promotionEndDate;
        $supplierName1 = $data->supplierName1;
        $supplierName2 = $data->supplierName2;
        $supplierName3 = $data->supplierName3;
        $supplierName4 = $data->supplierName4;
        $supplierName5 = $data->supplierName5;
        $supplierPartNo1 = $data->supplierPartNo1;
        $supplierPartNo2 = $data->supplierPartNo2;
        $supplierPartNo3 = $data->supplierPartNo3;
        $supplierPartNo4 = $data->supplierPartNo4;
        $supplierPartNo5 = $data->supplierPartNo5;
        $supplierPriceNumber1 = $data->supplierPriceNumber1;
        $supplierPriceNumber2 = $data->supplierPriceNumber2;
        $supplierPriceNumber3 = $data->supplierPriceNumber3;
        $supplierPriceNumber4 = $data->supplierPriceNumber4;
        $supplierPriceNumber5 = $data->supplierPriceNumber5;

        $array = json_decode($warehouseDetails, true);
        foreach ($array as $key) {
            $warehouse_qty[] = array(
                'warehouse_id' => $key["id"],
                'quantity' => $key["quantity"],
                'rack' => $key["rack"] ? $key["rack"] : NULL
            );
        }
        $photo = "no_image.jpg";
        if ($img != "" && $img != "imageNotUpdate") {
            $this->load->library('upload');
            $config['upload_path'] = $this->upload_path;
            $config['allowed_types'] = $this->image_types;
            $config['file_name'] = $this->upload->generateRandomString(32);
            $config['max_size'] = $this->allowed_file_size;
            $config['max_width'] = $this->Settings->iwidth;
            $config['max_height'] = $this->Settings->iheight;
            $config['overwrite'] = FALSE;
            $config['max_filename'] = 25;
            $config['encrypt_name'] = TRUE;
            $this->upload->initialize($config);
            $photo = $this->upload->file_name . '.jpg';
            $this->load->library('image_lib');
            $config['image_library'] = 'gd2';
            $config['source_image'] = './' . $this->upload_path . $photo;
            $config['new_image'] = './' . $this->thumbs_path . $photo;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = $this->Settings->twidth;
            $config['height'] = $this->Settings->theight;

            $img = str_replace('data:image/jpg;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $img = base64_decode($img);
            $file = $config['source_image'];
            file_put_contents($file, $img);
            $img = base64_decode($img);
            $source_img = imagecreatefromstring($img);
            $rotated_img = imagerotate($source_img, 90, 0);
            imagejpeg($rotated_img, $file, 10);
            $magicianObj = new imageLib($file);
            $magicianObj->resizeImage(50, 50);
            $magicianObj->saveImage('./assets/uploads/thumbs/' . $config['file_name'] . '.jpg', 100);
            imagedestroy($source_img);
        } elseif ($img == "imageNotUpdate") {
            $photo = $this->products_model_ap->getPhotoById($id);
        }

        $productData = array(
            'code' => $code,
            'barcode_symbology' => $barcodeSymbol,
            'name' => $name,
            'type' => $type,
            'brand' => $brand,
            'category_id' => $category,
            'subcategory_id' => $subCategory ? $subCategory : NULL,
            'cost' => $this->sma->formatDecimal($cost),
            'price' => $this->sma->formatDecimal($price),
            'unit' => $unit,
            'sale_unit' => $unit,
            'purchase_unit' => $unit,
            'tax_rate' => $tax,
            'tax_method' => $taxMethod,
            'alert_quantity' => $quantity,
            'track_quantity' => $trackQuantity ? $trackQuantity : '0',
            'details' => $detailsInvoice,
            'product_details' => $details,
            'supplier1' => $supplierName1,
            'supplier1price' => $this->sma->formatDecimal($supplierPartNo1),
            'quantity' => $productQuantity,
            'image' => $photo,
            'supplier2' => $supplierName2,
            'supplier2price' => $this->sma->formatDecimal($supplierPartNo2),
            'supplier3' => $supplierName3,
            'supplier3price' => $this->sma->formatDecimal($supplierPartNo3),
            'supplier4' => $supplierName4,
            'supplier4price' => $this->sma->formatDecimal($supplierPartNo4),
            'supplier5' => $supplierName5,
            'supplier5price' => $this->sma->formatDecimal($supplierPartNo5),
            'cf1' => $productCustomField1,
            'cf2' => $productCustomField2,
            'cf3' => $productCustomField3,
            'cf4' => $productCustomField4,
            'cf5' => $productCustomField5,
            'cf6' => $productCustomField6,
            'promotion' => $promotion,
            'promo_price' => $this->sma->formatDecimal($promotionPrice),
            'start_date' => $promotionStartDate ? $this->sma->fsd($promotionStartDate) : NULL,
            'end_date' => $promotionStartDate ? $this->sma->fsd($promotionEndDate) : NULL,
            'supplier1_part_no' => $supplierPriceNumber1,
            'supplier2_part_no' => $supplierPriceNumber2,
            'supplier3_part_no' => $supplierPriceNumber3,
            'supplier4_part_no' => $supplierPriceNumber4,
            'supplier5_part_no' => $supplierPriceNumber5,
            'file' => "",
            //'file' => $this->input->post('file_link'),
        );
        $photos[] = $photo;
        if ($type == "combo") {
            $comboProducts = json_decode($data->comboProducts);
            if ($comboProducts == null) {
                $this->products_model_ap->deleteComboItem($id);
            } else {
                foreach ($comboProducts as $prod) {
                    $items[] = array(
                        'item_code' => $prod->comboCode,
                        'quantity' => $prod->comboQty,
                        'unit_price' => $prod->comboPrice,
                    );
                }
            }
        }

        $photos[] = $photo;
        $is_save = $this->products_model_ap->updateProduct($id, $productData, $items, $warehouse_qty, null, $photos, null);

        $product_save = "";
        $product_save->is_save = $is_save;

        echo json_encode($product_save);
    }

    function suggestions()
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        $term = $data->term;
        if (!$this->auth_app_model->login($username, $password)) {
            return false;
        }
        $rows = $this->products_model_ap->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1);
            }
        }
        echo json_encode($pr);

    }

    /* -------------------------------------------------------- */

    function edit($id = NULL)
    {


        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        $id = $data->id;
        if (!$this->auth_app_model->login($username, $password)) {

            return true;
        }

        $warehouses = $this->site->getAllWarehouses();
        $warehouses_products = $this->products_model->getAllWarehousesWithPQ($id);
        $product = $this->site->getProductByID($id);

        $product_encode = json_encode($product);
        $product_decode = json_decode($product_encode, true);

        $key_tax = "tax_rate";
        $tax_rate_id = $product_decode[$key_tax];
        $tax_rate = $this->site->getTaxRateByID($tax_rate_id);

        $key_category = "category_id";
        $category_id = $product_decode[$key_category];
        $category = $this->site->getCategoryByID($category_id);

        $key_brand = "brand";
        $brand_id = $product_decode[$key_brand];
        $brand = $this->site->getBrandByID($brand_id);

        $key_unit = "unit";
        $unit_id = $product_decode[$key_unit];
        $unit = $this->site->getUnitByID($unit_id);

        $get_all_categories = $this->site->getAllCategories();
        $get_all_tax_rates = $this->site->getAllTaxRates();
        $get_all_brands = $this->site->getAllBrands();
        $get_all_base_units = $this->site->getAllBaseUnits();
        $get_all_suppliers = $this->site->getAllSuppliers();

        $product_data = "";
        $product_data->product = $product;
        $product_data->warehouses = $warehouses;
        $product_data->warehouses_products = $warehouses_products;
        $product_data->taxRate = $tax_rate;
        $product_data->category = $category;
        $product_data->brand = $brand;
        $product_data->unit = $unit;

        $product_data->suppliers = $get_all_suppliers;
        $product_data->categories = $get_all_categories;
        $product_data->taxRtes = $get_all_tax_rates;
        $product_data->brands = $get_all_brands;
        $product_data->baseUnits = $get_all_base_units;
        $product_data->warehouses = $warehouses;

        echo json_encode($product_data);
        return;
        if (!$id || !$product) {
            echo json_encode("PRODUCT NOT FOUND");
            return;
        }
        $this->form_validation->set_rules('category', lang("category"), 'required|is_natural_no_zero');
        if ($this->input->post('type') == 'standard') {
            $this->form_validation->set_rules('cost', lang("product_cost"), 'required');
            $this->form_validation->set_rules('unit', lang("product_unit"), 'required');
        }
        $this->form_validation->set_rules('code', lang("product_code"), 'alpha_dash');
        if ($this->input->post('code') !== $product->code) {
            $this->form_validation->set_rules('code', lang("product_code"), 'is_unique[products.code]');
        }
        if ($this->input->post('barcode_symbology') == 'ean13') {
            $this->form_validation->set_rules('code', lang("product_code"), 'min_length[13]|max_length[13]');
        }
        //  $this->form_validation->set_rules('product_image', lang("product_image"), 'xss_clean');
        //  $this->form_validation->set_rules('digital_file', lang("digital_file"), 'xss_clean');
        //  $this->form_validation->set_rules('userfile', lang("product_gallery_images"), 'xss_clean');

        if ($this->form_validation->run('products/add') == true) {

            $product_data = array('code' => $this->input->post('code'),
                'barcode_symbology' => $this->input->post('barcode_symbology'),
                'name' => $this->input->post('name'),
                'type' => $this->input->post('type'),
                'brand' => $this->input->post('brand'),
                'category_id' => $this->input->post('category'),
                'subcategory_id' => $this->input->post('subcategory') ? $this->input->post('subcategory') : NULL,
                'cost' => $this->sma->formatDecimal($this->input->post('cost')),
                'price' => $this->sma->formatDecimal($this->input->post('price')),
                'unit' => $this->input->post('unit'),
                'sale_unit' => $this->input->post('default_sale_unit'),
                'purchase_unit' => $this->input->post('default_purchase_unit'),
                'tax_rate' => $this->input->post('tax_rate'),
                'tax_method' => $this->input->post('tax_method'),
                'alert_quantity' => $this->input->post('alert_quantity'),
                'track_quantity' => $this->input->post('track_quantity') ? $this->input->post('track_quantity') : '0',
                'details' => $this->input->post('details'),
                'product_details' => $this->input->post('product_details'),
                'supplier1' => $this->input->post('supplier'),
                'supplier1price' => $this->sma->formatDecimal($this->input->post('supplier_price')),
                'supplier2' => $this->input->post('supplier_2'),
                'supplier2price' => $this->sma->formatDecimal($this->input->post('supplier_2_price')),
                'supplier3' => $this->input->post('supplier_3'),
                'supplier3price' => $this->sma->formatDecimal($this->input->post('supplier_3_price')),
                'supplier4' => $this->input->post('supplier_4'),
                'supplier4price' => $this->sma->formatDecimal($this->input->post('supplier_4_price')),
                'supplier5' => $this->input->post('supplier_5'),
                'supplier5price' => $this->sma->formatDecimal($this->input->post('supplier_5_price')),
                'cf1' => $this->input->post('cf1'),
                'cf2' => $this->input->post('cf2'),
                'cf3' => $this->input->post('cf3'),
                'cf4' => $this->input->post('cf4'),
                'cf5' => $this->input->post('cf5'),
                'cf6' => $this->input->post('cf6'),
                'promotion' => $this->input->post('promotion'),
                'promo_price' => $this->sma->formatDecimal($this->input->post('promo_price')),
                'start_date' => $this->input->post('start_date') ? $this->sma->fsd($this->input->post('start_date')) : NULL,
                'end_date' => $this->input->post('end_date') ? $this->sma->fsd($this->input->post('end_date')) : NULL,
                'supplier1_part_no' => $this->input->post('supplier_part_no'),
                'supplier2_part_no' => $this->input->post('supplier_2_part_no'),
                'supplier3_part_no' => $this->input->post('supplier_3_part_no'),
                'supplier4_part_no' => $this->input->post('supplier_4_part_no'),
                'supplier5_part_no' => $this->input->post('supplier_5_part_no'),
            );
            $this->load->library('upload');
            if ($this->input->post('type') == 'standard') {
                if ($product_variants = $this->products_model->getProductOptions($id)) {
                    foreach ($product_variants as $pv) {
                        $update_variants[] = array(
                            'id' => $this->input->post('variant_id_' . $pv->id),
                            'name' => $this->input->post('variant_name_' . $pv->id),
                            'cost' => $this->input->post('variant_cost_' . $pv->id),
                            'price' => $this->input->post('variant_price_' . $pv->id),
                        );
                    }
                } else {
                    $update_variants = NULL;
                }
                for ($s = 2; $s > 5; $s++) {
                    $product_data['suppliers' . $s] = $this->input->post('supplier_' . $s);
                    $product_data['suppliers' . $s . 'price'] = $this->input->post('supplier_' . $s . '_price');
                }
                foreach ($warehouses as $warehouse) {
                    $warehouse_qty[] = array(
                        'warehouse_id' => $this->input->post('wh_' . $warehouse->id),
                        'rack' => $this->input->post('rack_' . $warehouse->id) ? $this->input->post('rack_' . $warehouse->id) : NULL
                    );
                }

                if ($this->input->post('attributes')) {
                    $a = sizeof($_POST['attr_name']);
                    for ($r = 0; $r <= $a; $r++) {
                        if (isset($_POST['attr_name'][$r])) {
                            if ($product_variatnt = $this->products_model->getPrductVariantByPIDandName($id, trim($_POST['attr_name'][$r]))) {
                                $this->form_validation->set_message('required', lang("product_already_has_variant") . ' (' . $_POST['attr_name'][$r] . ')');
                                $this->form_validation->set_rules('new_product_variant', lang("new_product_variant"), 'required');
                            } else {
                                $product_attributes[] = array(
                                    'name' => $_POST['attr_name'][$r],
                                    'warehouse_id' => $_POST['attr_warehouse'][$r],
                                    'quantity' => $_POST['attr_quantity'][$r],
                                    'price' => $_POST['attr_price'][$r],
                                );
                            }
                        }
                    }

                } else {
                    $product_attributes = NULL;
                }

            } else {
                $warehouse_qty = NULL;
                $product_attributes = NULL;
            }

            if ($this->input->post('type') == 'service') {
                $product_data['track_quantity'] = 0;
            } elseif ($this->input->post('type') == 'combo') {
                $total_price = 0;
                $c = sizeof($_POST['combo_item_code']) - 1;
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r]) && isset($_POST['combo_item_price'][$r])) {
                        $items[] = array(
                            'item_code' => $_POST['combo_item_code'][$r],
                            'quantity' => $_POST['combo_item_quantity'][$r],
                            'unit_price' => $_POST['combo_item_price'][$r],
                        );
                    }
                    $total_price += $_POST['combo_item_price'][$r] * $_POST['combo_item_quantity'][$r];
                }
                if ($this->sma->formatDecimal($total_price) != $this->sma->formatDecimal($this->input->post('price'))) {
                    $this->form_validation->set_rules('combo_price', 'combo_price', 'required');
                    $this->form_validation->set_message('required', lang('pprice_not_match_ciprice'));
                }
                $product_data['track_quantity'] = 0;
            } elseif ($this->input->post('type') == 'digital') {
                if ($this->input->post('file_link')) {
                    $product_data['file'] = $this->input->post('file_link');
                }
                if ($_FILES['digital_file']['size'] > 0) {
                    $config['upload_path'] = $this->digital_upload_path;
                    $config['allowed_types'] = $this->digital_file_types;
                    $config['max_size'] = $this->allowed_file_size;
                    $config['overwrite'] = FALSE;
                    $config['encrypt_name'] = TRUE;
                    $config['max_filename'] = 25;
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('digital_file')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        redirect("products/add");
                    }
                    $file = $this->upload->file_name;
                    $product_data['file'] = $file;
                }
                $config = NULL;
                $product_data['track_quantity'] = 0;
            }
            if (!isset($items)) {
                $items = NULL;
            }
            if ($_FILES['product_image']['size'] > 0) {

                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('product_image')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products/edit/" . $id);
                }
                $photo = $this->upload->file_name;
                $product_data['image'] = $photo;
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = $this->upload_path . $photo;
                $config['new_image'] = $this->thumbs_path . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = $this->Settings->twidth;
                $config['height'] = $this->Settings->theight;
                $this->image_lib->clear();
                $this->image_lib->initialize($config);
                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                if ($this->Settings->watermark) {
                    $this->image_lib->clear();
                    $wm['source_image'] = $this->upload_path . $photo;
                    $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                    $wm['wm_type'] = 'text';
                    $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                    $wm['quality'] = '100';
                    $wm['wm_font_size'] = '16';
                    $wm['wm_font_color'] = '999999';
                    $wm['wm_shadow_color'] = 'CCCCCC';
                    $wm['wm_vrt_alignment'] = 'top';
                    $wm['wm_hor_alignment'] = 'right';
                    $wm['wm_padding'] = '10';
                    $this->image_lib->initialize($wm);
                    $this->image_lib->watermark();
                }
                $this->image_lib->clear();
                $config = NULL;
            }

            if ($_FILES['userfile']['name'][0] != "") {

                $config['upload_path'] = $this->upload_path;
                $config['allowed_types'] = $this->image_types;
                $config['max_size'] = $this->allowed_file_size;
                $config['max_width'] = $this->Settings->iwidth;
                $config['max_height'] = $this->Settings->iheight;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $files = $_FILES;
                $cpt = count($_FILES['userfile']['name']);
                for ($i = 0; $i < $cpt; $i++) {

                    $_FILES['userfile']['name'] = $files['userfile']['name'][$i];
                    $_FILES['userfile']['type'] = $files['userfile']['type'][$i];
                    $_FILES['userfile']['tmp_name'] = $files['userfile']['tmp_name'][$i];
                    $_FILES['userfile']['error'] = $files['userfile']['error'][$i];
                    $_FILES['userfile']['size'] = $files['userfile']['size'][$i];

                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload()) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        redirect("products/edit/" . $id);
                    } else {

                        $pho = $this->upload->file_name;

                        $photos[] = $pho;

                        $this->load->library('image_lib');
                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $this->upload_path . $pho;
                        $config['new_image'] = $this->thumbs_path . $pho;
                        $config['maintain_ratio'] = TRUE;
                        $config['width'] = $this->Settings->twidth;
                        $config['height'] = $this->Settings->theight;

                        $this->image_lib->initialize($config);

                        if (!$this->image_lib->resize()) {
                            echo $this->image_lib->display_errors();
                        }

                        if ($this->Settings->watermark) {
                            $this->image_lib->clear();
                            $wm['source_image'] = $this->upload_path . $pho;
                            $wm['wm_text'] = 'Copyright ' . date('Y') . ' - ' . $this->Settings->site_name;
                            $wm['wm_type'] = 'text';
                            $wm['wm_font_path'] = 'system/fonts/texb.ttf';
                            $wm['quality'] = '100';
                            $wm['wm_font_size'] = '16';
                            $wm['wm_font_color'] = '999999';
                            $wm['wm_shadow_color'] = 'CCCCCC';
                            $wm['wm_vrt_alignment'] = 'top';
                            $wm['wm_hor_alignment'] = 'right';
                            $wm['wm_padding'] = '10';
                            $this->image_lib->initialize($wm);
                            $this->image_lib->watermark();
                        }

                        $this->image_lib->clear();
                    }
                }
                $config = NULL;
            } else {
                $photos = NULL;
            }
            $data['quantity'] = isset($wh_total_quantity) ? $wh_total_quantity : 0;
            // $this->sma->print_arrays($data, $warehouse_qty, $update_variants, $product_attributes, $photos, $items);
        }

        if ($this->form_validation->run() == true && $this->products_model->updateProduct($id, $data, $items, $warehouse_qty, $product_attributes, $photos, $update_variants)) {
            $this->session->set_flashdata('message', lang("product_updated"));
            redirect('products');
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));

            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['tax_rates'] = $this->site->getAllTaxRates();
            $this->data['brands'] = $this->site->getAllBrands();
            $this->data['base_units'] = $this->site->getAllBaseUnits();
            $this->data['warehouses'] = $warehouses;
            $this->data['warehouses_products'] = $warehouses_products;
            $this->data['product'] = $product;
            $this->data['variants'] = $this->products_model->getAllVariants();
            $this->data['subunits'] = $this->site->getUnitsByBUID($product->unit);
            $this->data['product_variants'] = $this->products_model->getProductOptions($id);
            $this->data['combo_items'] = $product->type == 'combo' ? $this->products_model->getProductComboItems($product->id) : NULL;
            $this->data['product_options'] = $id ? $this->products_model->getProductOptionsWithWH($id) : NULL;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('edit_product')));
            $meta = array('page_title' => lang('edit_product'), 'bc' => $bc);
            $this->page_construct('products/edit', $meta, $this->data);
        }
    }


    function get_suggestions()
    {
        $term = $this->input->get('term', TRUE);
        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
        }

        $rows = $this->products_model->getProductsForPrinting($term);
        if ($rows) {
            foreach ($rows as $row) {
                $variants = $this->products_model->getProductOptions($row->id);
                $pr[] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => 1, 'variants' => $variants);
            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    function addByAjax()
    {
        if (!$this->mPermissions('add')) {
            exit(json_encode(array('msg' => lang('access_denied'))));
        }
        if ($this->input->get('token') && $this->input->get('token') == $this->session->userdata('user_csrf') && $this->input->is_ajax_request()) {
            $product = $this->input->get('product');
            if (!isset($product['code']) || empty($product['code'])) {
                exit(json_encode(array('msg' => lang('product_code_is_required'))));
            }
            if (!isset($product['name']) || empty($product['name'])) {
                exit(json_encode(array('msg' => lang('product_name_is_required'))));
            }
            if (!isset($product['category_id']) || empty($product['category_id'])) {
                exit(json_encode(array('msg' => lang('product_category_is_required'))));
            }
            if (!isset($product['unit']) || empty($product['unit'])) {
                exit(json_encode(array('msg' => lang('product_unit_is_required'))));
            }
            if (!isset($product['price']) || empty($product['price'])) {
                exit(json_encode(array('msg' => lang('product_price_is_required'))));
            }
            if (!isset($product['cost']) || empty($product['cost'])) {
                exit(json_encode(array('msg' => lang('product_cost_is_required'))));
            }
            if ($this->products_model->getProductByCode($product['code'])) {
                exit(json_encode(array('msg' => lang('product_code_already_exist'))));
            }
            if ($row = $this->products_model->addAjaxProduct($product)) {
                $tax_rate = $this->site->getTaxRateByID($row->tax_rate);
                $pr = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'qty' => 1, 'cost' => $row->cost, 'name' => $row->name, 'tax_method' => $row->tax_method, 'tax_rate' => $tax_rate, 'discount' => '0');
                $this->sma->send_json(array('msg' => 'success', 'result' => $pr));
            } else {
                exit(json_encode(array('msg' => lang('failed_to_add_product'))));
            }
        } else {
            json_encode(array('msg' => 'Invalid token'));
        }

    }

    /* ---------------------------------------------------------------- */


    function update_price()
    {
        $this->sma->checkPermissions('csv');
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {

            if (DEMO) {
                $this->session->set_flashdata('message', lang("disabled_in_demo"));
                redirect('welcome');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = TRUE;
                $config['encrypt_name'] = TRUE;
                $config['max_filename'] = 25;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products");
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);

                $keys = array('code', 'price');

                $final = array();

                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                $rw = 2;
                foreach ($final as $csv_pr) {
                    if (!$this->products_model->getProductByCode(trim($csv_pr['code']))) {
                        $this->session->set_flashdata('message', lang("check_product_code") . " (" . $csv_pr['code'] . "). " . lang("code_x_exist") . " " . lang("line_no") . " " . $rw);
                        redirect("products");
                    }
                    $rw++;
                }
            }

        } elseif ($this->input->post('update_price')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect("system_settings/group_product_prices/" . $group_id);
        }

        if ($this->form_validation->run() == true && !empty($final)) {
            $this->products_model->updatePrice($final);
            $this->session->set_flashdata('message', lang("price_updated"));
            redirect('products');
        } else {

            $this->data['userfile'] = array('name' => 'userfile',
                'id' => 'userfile',
                'type' => 'text',
                'value' => $this->form_validation->set_value('userfile')
            );
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'products/update_price', $this->data);

        }
    }

    /* ------------------------------------------------------------------------------- */

    function delete($id = NULL)
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        $id = $data->id;
        if (!$this->auth_app_model->login($username, $password)) {

            return true;
        }
        $delete = $this->products_model_ap->deleteProduct($id);
        echo json_encode($delete);
    }

    /* ----------------------------------------------------------------------------- */

    function quantity_adjustments($warehouse_id = NULL)
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;

        if (!$this->auth_app_model->login($username, $password)) {

            return true;
        }
        //$this->sma->checkPermissions('adjustments');

        //       if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
        $this->data['warehouses'] = $this->site->getAllWarehouses();
        $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : null;
//        }
//        else {
//            $this->data['warehouses'] = null;
//            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : null;
//        }

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('quantity_adjustments')));
        $meta = array('page_title' => lang('quantity_adjustments'), 'bc' => $bc);
        $this->page_construct('products/quantity_adjustments', $meta, $this->data);

        echo json_encode($this->data);
    }

    function getadjustments($warehouse_id = NULL)
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;

        if (!$this->auth_app_model->login($username, $password)) {

            return true;
        }
        $adjustments = $this->products_model_ap->getAdjustments();
        echo $adjustments;
    }

    public function view_adjustment($id)
    {
        $this->sma->checkPermissions('adjustments', TRUE);

        $adjustment = $this->products_model->getAdjustmentByID($id);
        if (!$id || !$adjustment) {
            $this->session->set_flashdata('error', lang('adjustment_not_found'));
            $this->sma->md();
        }

        $this->data['inv'] = $adjustment;
        $this->data['rows'] = $this->products_model->getAdjustmentItems($id);
        $this->data['created_by'] = $this->site->getUser($adjustment->created_by);
        $this->data['updated_by'] = $this->site->getUser($adjustment->updated_by);
        $this->data['warehouse'] = $this->site->getWarehouseByID($adjustment->warehouse_id);
        $this->load->view($this->theme . 'products/view_adjustment', $this->data);
    }

    function add_adjustment($count_id = NULL)
    {
        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        $productList = $data->adjustmentData;
        if (!$this->auth_app_model->login($username, $password)) {

            return true;
        }

        $decodeProductList = json_decode($productList, true);
        $timeDate = $decodeProductList['timeDate'];
        $warehouse_id = $decodeProductList['warehouse'];
        $note = $decodeProductList['note'];
        $reference = $decodeProductList['reference'];
        $product_details = $decodeProductList['productList'];
        for ($i = 0; $i < count($product_details); $i++) {
            if ($product_details[$i]["type"] == "-") {
                $type = "subtraction";
            } else {
                $type = "addition";
            }
            $products[] = array(
                'product_id' => $product_details[$i]["id"],
                'type' => $type,
                'quantity' => $product_details[$i]["quantity"],
                'warehouse_id' => $warehouse_id,
                'option_id' => $product_details[$i]["variant"],
                'serial_no' => $decodeProductList['serialNumber'],
            );

        }
        $date = date_create_from_format('H:i d/m/Y', $timeDate);
        $format_date = date_format($date, 'Y-m-d H:i');

        $adjustment_data = array(
            'date' => $format_date,
            'reference_no' => $reference ? $reference : $this->site->getReference('qa'),
            'warehouse_id' => $warehouse_id,
            'note' => $note,
            'created_by' => $this->session->userdata('user_id'),
            'count_id' => NULL,
        );

        $adjustment= $this->products_model_ap->addAdjustment($adjustment_data, $products);
        echo json_encode($adjustment);
    }

    function update_adjustment(){

        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        $adjustmentId = $data->adjustmentId;
        $productList = $data->adjustmentData;
        if (!$this->auth_app_model->login($username, $password)) {

            return true;
        }
        $decodeProductList = json_decode($productList, true);
        $timeDate = $decodeProductList['timeDate'];
        $warehouse_id = $decodeProductList['warehouse'];
        $note = $decodeProductList['note'];
        $reference = $decodeProductList['reference'];
        $product_details = $decodeProductList['productList'];
        for ($i = 0; $i < count($product_details); $i++) {
            if ($product_details[$i]["type"] == "-") {
                $type = "subtraction";
            } else {
                $type = "addition";
            }
            $products[] = array(
                'product_id' => $product_details[$i]["id"],
                'type' => $type,
                'quantity' => $product_details[$i]["quantity"],
                'warehouse_id' => $warehouse_id,
                'option_id' => $product_details[$i]["variant"],
                'serial_no' => $decodeProductList['serialNumber'],
            );

        }
        $date = date_create_from_format('H:i d/m/Y', $timeDate);
        $format_date = date_format($date, 'Y-m-d H:i');

        $adjustment_data = array(
            'date' => $format_date,
            'reference_no' => $reference ? $reference : $this->site->getReference('qa'),
            'warehouse_id' => $warehouse_id,
            'note' => $note,
            'created_by' => $this->session->userdata('user_id'),
            'count_id' => NULL,
        );
        $adjustment=  $this->products_model_ap->updateAdjustment($adjustmentId, $adjustment_data, $products);
        echo json_encode($adjustment);

    }

    function edit_adjustment($id, $adjustment_date)
    {

        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        $adjustmentId = $data->adjustmentId;
        if (!$this->auth_app_model->login($username, $password)) {

            return false;
        }
        $adjustment = $this->products_model_ap->getAdjustmentByID($adjustmentId);
        $inv_items = $this->products_model_ap->getAdjustmentItems($adjustmentId);
        krsort($inv_items);
        $c = rand(100000, 9999999);

        foreach ($inv_items as $item) {
            $product = $this->site->getProductByID($item->product_id);
            $row = json_decode('{}');
            $row->id = $item->product_id;
            $row->code = $product->code;
            $row->name = $product->name;
            $row->qty = $item->quantity;
            $row->type = $item->type;
            $options = $this->products_model->getProductOptions($product->id);
            $row->option = $item->option_id ? $item->option_id : 0;
            $row->serial = $item->serial_no ? $item->serial_no : '';
            $ri = $this->Settings->item_addition ? $product->id : $c;
            $prod[] = $product;
            $pr[$ri] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                'row' => $row, 'options' => $options);
            $c++;
        }

        $adjustment_date -> adjustment= $adjustment;
        $adjustment_date -> products= $prod;
        echo json_encode($adjustment_date);

    }

    function delete_adjustment($id = NULL)
    {

        $data = json_decode(file_get_contents("php://input"));
        $username = $data->username;
        $password = $data->password;
        $id = $data->id;
        if (!$this->auth_app_model->login($username, $password)) {

            return false;
        }
        $delete = $this->products_model->deleteAdjustment($id);
        echo json_encode($delete);
        //   $this->sma->checkPermissions('delete', TRUE);

        if ($this->products_model->deleteAdjustment($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("adjustment_deleted")));
        }

    }

    /* --------------------------------------------------------------------------------------------- */

    function view($id = NULL)
    {
        $this->sma->checkPermissions('index');

        $pr_details = $this->products_model->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['barcode'] = "<img src='" . site_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->products_model->getProductPhotos($id);
        $this->data['category'] = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses'] = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants'] = $this->products_model->getProductOptions($id);
        $this->data['sold'] = $this->products_model->getSoldQty($id);
        $this->data['purchased'] = $this->products_model->getPurchasedQty($id);

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => $pr_details->name));
        $meta = array('page_title' => $pr_details->name, 'bc' => $bc);
        $this->page_construct('products/view', $meta, $this->data);
    }

    function pdf($id = NULL, $view = NULL)
    {
        $this->sma->checkPermissions('index');

        $pr_details = $this->products_model->getProductByID($id);
        if (!$id || !$pr_details) {
            $this->session->set_flashdata('error', lang('prduct_not_found'));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->data['barcode'] = "<img src='" . site_url('products/gen_barcode/' . $pr_details->code . '/' . $pr_details->barcode_symbology . '/40/0') . "' alt='" . $pr_details->code . "' class='pull-left' />";
        if ($pr_details->type == 'combo') {
            $this->data['combo_items'] = $this->products_model->getProductComboItems($id);
        }
        $this->data['product'] = $pr_details;
        $this->data['unit'] = $this->site->getUnitByID($pr_details->unit);
        $this->data['brand'] = $this->site->getBrandByID($pr_details->brand);
        $this->data['images'] = $this->products_model->getProductPhotos($id);
        $this->data['category'] = $this->site->getCategoryByID($pr_details->category_id);
        $this->data['subcategory'] = $pr_details->subcategory_id ? $this->site->getCategoryByID($pr_details->subcategory_id) : NULL;
        $this->data['tax_rate'] = $pr_details->tax_rate ? $this->site->getTaxRateByID($pr_details->tax_rate) : NULL;
        $this->data['popup_attributes'] = $this->popup_attributes;
        $this->data['warehouses'] = $this->products_model->getAllWarehousesWithPQ($id);
        $this->data['options'] = $this->products_model->getProductOptionsWithWH($id);
        $this->data['variants'] = $this->products_model->getProductOptions($id);

        $name = $pr_details->code . '_' . str_replace('/', '_', $pr_details->name) . ".pdf";
        if ($view) {
            $this->load->view($this->theme . 'products/pdf', $this->data);
        } else {
            $html = $this->load->view($this->theme . 'products/pdf', $this->data, TRUE);
            if (!$this->Settings->barcode_img) {
                $html = preg_replace("'\<\?xml(.*)\?\>'", '', $html);
            }
            $this->sma->generate_pdf($html, $name);
        }
    }

    function getSubCategories($category_id = NULL)
    {
        if ($rows = $this->products_model->getSubCategories($category_id)) {
            $data = json_encode($rows);
        } else {
            $data = false;
        }
        echo $data;
    }


    function product_actions($wh = NULL)
    {

        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'sync_quantity') {

                    foreach ($_POST['val'] as $id) {
                        $this->site->syncQuantity(NULL, NULL, NULL, $id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("products_quantity_sync"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'delete') {

                    $this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->products_model->deleteProduct($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("products_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'labels') {

                    foreach ($_POST['val'] as $id) {
                        $row = $this->products_model->getProductByID($id);
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }

                    $this->data['items'] = isset($pr) ? json_encode($pr) : false;
                    $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
                    $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('print_barcodes')));
                    $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
                    $this->page_construct('products/print_barcodes', $meta, $this->data);

                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('Products');
                    if ($this->Settings->user_language == "hebrew" && $this->input->post('form_action') == 'export_pdf') {
                        $this->excel->getActiveSheet()->SetCellValue('W1', lang('name'));
                        $this->excel->getActiveSheet()->SetCellValue('V1', lang('code'));
                        $this->excel->getActiveSheet()->SetCellValue('U1', lang('barcode_symbology'));
                        $this->excel->getActiveSheet()->SetCellValue('T1', lang('brand'));
                        $this->excel->getActiveSheet()->SetCellValue('S1', lang('category_code'));
                        $this->excel->getActiveSheet()->SetCellValue('R1', lang('unit_code'));
                        $this->excel->getActiveSheet()->SetCellValue('Q1', lang('sale') . ' ' . lang('unit_code'));
                        $this->excel->getActiveSheet()->SetCellValue('P1', lang('purchase') . ' ' . lang('unit_code'));
                        $this->excel->getActiveSheet()->SetCellValue('O1', lang('cost'));
                        $this->excel->getActiveSheet()->SetCellValue('N1', lang('price'));
                        $this->excel->getActiveSheet()->SetCellValue('M1', lang('alert_quantity'));
                        $this->excel->getActiveSheet()->SetCellValue('L1', lang('tax_rate'));
                        $this->excel->getActiveSheet()->SetCellValue('K1', lang('tax_method'));
                        $this->excel->getActiveSheet()->SetCellValue('J1', lang('image'));
                        $this->excel->getActiveSheet()->SetCellValue('I1', lang('subcategory_code'));
                        $this->excel->getActiveSheet()->SetCellValue('H1', lang('product_variants'));
                        $this->excel->getActiveSheet()->SetCellValue('G1', lang('pcf1'));
                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('pcf2'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('pcf3'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('pcf4'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('pcf5'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('pcf6'));
                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('quantity'));
                    } else {
                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('name'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('code'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('barcode_symbology'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('brand'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('category_code'));
                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('unit_code'));
                        $this->excel->getActiveSheet()->SetCellValue('G1', lang('sale') . ' ' . lang('unit_code'));
                        $this->excel->getActiveSheet()->SetCellValue('H1', lang('purchase') . ' ' . lang('unit_code'));
                        $this->excel->getActiveSheet()->SetCellValue('I1', lang('cost'));
                        $this->excel->getActiveSheet()->SetCellValue('J1', lang('price'));
                        $this->excel->getActiveSheet()->SetCellValue('K1', lang('alert_quantity'));
                        $this->excel->getActiveSheet()->SetCellValue('L1', lang('tax_rate'));
                        $this->excel->getActiveSheet()->SetCellValue('M1', lang('tax_method'));
                        $this->excel->getActiveSheet()->SetCellValue('N1', lang('image'));
                        $this->excel->getActiveSheet()->SetCellValue('O1', lang('subcategory_code'));
                        $this->excel->getActiveSheet()->SetCellValue('P1', lang('product_variants'));
                        $this->excel->getActiveSheet()->SetCellValue('Q1', lang('pcf1'));
                        $this->excel->getActiveSheet()->SetCellValue('R1', lang('pcf2'));
                        $this->excel->getActiveSheet()->SetCellValue('S1', lang('pcf3'));
                        $this->excel->getActiveSheet()->SetCellValue('T1', lang('pcf4'));
                        $this->excel->getActiveSheet()->SetCellValue('U1', lang('pcf5'));
                        $this->excel->getActiveSheet()->SetCellValue('V1', lang('pcf6'));
                        $this->excel->getActiveSheet()->SetCellValue('W1', lang('quantity'));
                    }


                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $product = $this->products_model->getProductDetail($id);
                        $brand = $this->site->getBrandByID($product->brand);
                        if ($units = $this->site->getUnitsByBUID($product->unit)) {
                            foreach ($units as $u) {
                                if ($u->id == $product->unit) {
                                    $base_unit = $u->code;
                                }
                                if ($u->id == $product->sale_unit) {
                                    $sale_unit = $u->code;
                                }
                                if ($u->id == $product->purchase_unit) {
                                    $purchase_unit = $u->code;
                                }
                            }
                        } else {
                            $base_unit = '';
                            $sale_unit = '';
                            $purchase_unit = '';
                        }
                        $variants = $this->products_model->getProductOptions($id);
                        $product_variants = '';
                        if ($variants) {
                            foreach ($variants as $variant) {
                                $product_variants .= trim($variant->name) . '|';
                            }
                        }
                        $quantity = $product->quantity;
                        if ($wh) {
                            if ($wh_qty = $this->products_model->getProductQuantity($id, $wh)) {
                                $quantity = $wh_qty['quantity'];
                            } else {
                                $quantity = 0;
                            }
                        }
                        if ($this->Settings->user_language == "hebrew" && $this->input->post('form_action') == 'export_pdf') {
                            $this->excel->getActiveSheet()->SetCellValue('W' . $row, $product->name);
                            $this->excel->getActiveSheet()->SetCellValue('V' . $row, $product->code);
                            $this->excel->getActiveSheet()->SetCellValue('U' . $row, $product->barcode_symbology);
                            $this->excel->getActiveSheet()->SetCellValue('T' . $row, ($brand ? $brand->name : ''));
                            $this->excel->getActiveSheet()->SetCellValue('S' . $row, $product->category_code);
                            $this->excel->getActiveSheet()->SetCellValue('R' . $row, $base_unit);
                            $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $sale_unit);
                            $this->excel->getActiveSheet()->SetCellValue('P' . $row, $purchase_unit);
                            if ($this->Owner || $this->Admin || $this->session->userdata('show_cost')) {
                                $this->excel->getActiveSheet()->SetCellValue('O' . $row, $product->cost);
                            }
                            if ($this->Owner || $this->Admin || $this->session->userdata('show_price')) {
                                $this->excel->getActiveSheet()->SetCellValue('N' . $row, $product->price);
                            }
                            $this->excel->getActiveSheet()->SetCellValue('M' . $row, $product->alert_quantity);
                            $this->excel->getActiveSheet()->SetCellValue('L' . $row, $product->tax_rate_name);
                            $this->excel->getActiveSheet()->SetCellValue('K' . $row, $product->tax_method ? lang('exclusive') : lang('inclusive'));
                            $this->excel->getActiveSheet()->SetCellValue('J' . $row, $product->image);
                            $this->excel->getActiveSheet()->SetCellValue('I' . $row, $product->subcategory_code);
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $product_variants);
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $product->cf1);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $product->cf2);
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $product->cf3);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $product->cf4);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $product->cf5);
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $product->cf6);
                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $quantity);
                        } else {
                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $product->name);
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $product->code);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $product->barcode_symbology);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, ($brand ? $brand->name : ''));
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $product->category_code);
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $base_unit);
                            $this->excel->getActiveSheet()->SetCellValue('G' . $row, $sale_unit);
                            $this->excel->getActiveSheet()->SetCellValue('H' . $row, $purchase_unit);
                            if ($this->Owner || $this->Admin || $this->session->userdata('show_cost')) {
                                $this->excel->getActiveSheet()->SetCellValue('I' . $row, $product->cost);
                            }
                            if ($this->Owner || $this->Admin || $this->session->userdata('show_price')) {
                                $this->excel->getActiveSheet()->SetCellValue('J' . $row, $product->price);
                            }
                            $this->excel->getActiveSheet()->SetCellValue('K' . $row, $product->alert_quantity);
                            $this->excel->getActiveSheet()->SetCellValue('L' . $row, $product->tax_rate_name);
                            $this->excel->getActiveSheet()->SetCellValue('M' . $row, $product->tax_method ? lang('exclusive') : lang('inclusive'));
                            $this->excel->getActiveSheet()->SetCellValue('N' . $row, $product->image);
                            $this->excel->getActiveSheet()->SetCellValue('O' . $row, $product->subcategory_code);
                            $this->excel->getActiveSheet()->SetCellValue('P' . $row, $product_variants);
                            $this->excel->getActiveSheet()->SetCellValue('Q' . $row, $product->cf1);
                            $this->excel->getActiveSheet()->SetCellValue('R' . $row, $product->cf2);
                            $this->excel->getActiveSheet()->SetCellValue('S' . $row, $product->cf3);
                            $this->excel->getActiveSheet()->SetCellValue('T' . $row, $product->cf4);
                            $this->excel->getActiveSheet()->SetCellValue('U' . $row, $product->cf5);
                            $this->excel->getActiveSheet()->SetCellValue('V' . $row, $product->cf6);
                            $this->excel->getActiveSheet()->SetCellValue('W' . $row, $quantity);
                        }
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
                    $this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
                    if ($this->Settings->user_language == "hebrew" && $this->input->post('form_action') == 'export_pdf') {
                        $this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(30);
                        $this->excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    }
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                    $filename = 'products_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php");
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_product_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    public function delete_image($id = NULL)
    {
        $this->sma->checkPermissions('edit', true);
        if ($id && $this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            $this->db->delete('product_photos', array('id' => $id));
            $this->sma->send_json(array('error' => 0, 'msg' => lang("image_deleted")));
        }
        $this->sma->send_json(array('error' => 1, 'msg' => lang("ajax_error")));
    }

    public function getSubUnits($unit_id)
    {
        $unit = $this->site->getUnitByID($unit_id);
        if ($units = $this->site->getUnitsByBUID($unit_id)) {
            array_push($units, $unit);
        } else {
            $units = array($unit);
        }
        $this->sma->send_json($units);
    }

    public function qa_suggestions()
    {
        $term = $this->input->get('term', true);

        if (strlen($term) < 1 || !$term) {
            die("<script type='text/javascript'>setTimeout(function(){ window.top.location.href = '" . site_url('welcome') . "'; }, 10);</script>");
        }

        $analyzed = $this->sma->analyze_term($term);
        $sr = $analyzed['term'];
        $option_id = $analyzed['option_id'];

        $rows = $this->products_model->getQASuggestions($sr);
        if ($rows) {
            foreach ($rows as $row) {
                $row->qty = 1;
                $options = $this->products_model->getProductOptions($row->id);
                $row->option = $option_id;
                $row->serial = '';

                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")",
                    'row' => $row, 'options' => $options);

            }
            $this->sma->send_json($pr);
        } else {
            $this->sma->send_json(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    function adjustment_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {

                    $this->sma->checkPermissions('delete');
                    foreach ($_POST['val'] as $id) {
                        $this->products_model->deleteAdjustment($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line("adjustment_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);

                } elseif ($this->input->post('form_action') == 'export_excel' || $this->input->post('form_action') == 'export_pdf') {

                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle('quantity_adjustments');
                    if ($this->Settings->user_language == "hebrew" && $this->input->post('form_action') == 'export_pdf') {

                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('date'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('reference_no'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('warehouse'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('created_by'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('note'));
                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('items'));

                    } else {

                        $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                        $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference_no'));
                        $this->excel->getActiveSheet()->SetCellValue('C1', lang('warehouse'));
                        $this->excel->getActiveSheet()->SetCellValue('D1', lang('created_by'));
                        $this->excel->getActiveSheet()->SetCellValue('E1', lang('note'));
                        $this->excel->getActiveSheet()->SetCellValue('F1', lang('items'));
                    }


                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $adjustment = $this->products_model->getAdjustmentByID($id);
                        $created_by = $this->site->getUser($adjustment->created_by);
                        $warehouse = $this->site->getWarehouseByID($adjustment->warehouse_id);
                        $items = $this->products_model->getAdjustmentItems($id);
                        $products = '';
                        if ($items) {
                            foreach ($items as $item) {
                                $products .= $item->product_name . '(' . $this->sma->formatQuantity($item->type == 'subtraction' ? -$item->quantity : $item->quantity) . ')' . "\n";
                            }
                        }
                        if ($this->Settings->user_language == "hebrew" && $this->input->post('form_action') == 'export_pdf') {
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $this->sma->hrld($adjustment->date));
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $adjustment->reference_no);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $warehouse->name);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $created_by->first_name . ' ' . $created_by->last_name);
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $this->sma->decode_html($adjustment->note));
                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $products);
                        } else {
                            $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($adjustment->date));
                            $this->excel->getActiveSheet()->SetCellValue('B' . $row, $adjustment->reference_no);
                            $this->excel->getActiveSheet()->SetCellValue('C' . $row, $warehouse->name);
                            $this->excel->getActiveSheet()->SetCellValue('D' . $row, $created_by->first_name . ' ' . $created_by->last_name);
                            $this->excel->getActiveSheet()->SetCellValue('E' . $row, $this->sma->decode_html($adjustment->note));
                            $this->excel->getActiveSheet()->SetCellValue('F' . $row, $products);
                        }

                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
                    $this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                    if ($this->Settings->user_language == "hebrew" && $this->input->post('form_action') == 'export_pdf') {
                        $this->excel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    }
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $filename = 'quantity_adjustments_' . date('Y_m_d_H_i_s');
                    if ($this->input->post('form_action') == 'export_pdf') {
                        $styleArray = array('borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
                        $this->excel->getDefaultStyle()->applyFromArray($styleArray);
                        $this->excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        require_once(APPPATH . "third_party" . DIRECTORY_SEPARATOR . "MPDF" . DIRECTORY_SEPARATOR . "mpdf.php");
                        $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
                        $rendererLibrary = 'MPDF';
                        $rendererLibraryPath = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . $rendererLibrary;
                        if (!PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath)) {
                            die('Please set the $rendererName: ' . $rendererName . ' and $rendererLibraryPath: ' . $rendererLibraryPath . ' values' .
                                PHP_EOL . ' as appropriate for your directory structure');
                        }

                        header('Content-Type: application/pdf');
                        header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'PDF');
                        return $objWriter->save('php://output');
                    }
                    if ($this->input->post('form_action') == 'export_excel') {
                        $this->excel->getActiveSheet()->getStyle('E2:E' . $row)->getAlignment()->setWrapText(true);
                        $this->excel->getActiveSheet()->getStyle('F2:F' . $row)->getAlignment()->setWrapText(true);
                        header('Content-Type: application/vnd.ms-excel');
                        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
                        header('Cache-Control: max-age=0');

                        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
                        return $objWriter->save('php://output');
                    }

                    redirect($_SERVER["HTTP_REFERER"]);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

    function stock_counts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('stock_count');

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        if ($this->Owner || $this->Admin || !$this->session->userdata('warehouse_id')) {
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['warehouse_id'] = $warehouse_id;
            $this->data['warehouse'] = $warehouse_id ? $this->site->getWarehouseByID($warehouse_id) : NULL;
        } else {
            $this->data['warehouses'] = NULL;
            $this->data['warehouse_id'] = $this->session->userdata('warehouse_id');
            $this->data['warehouse'] = $this->session->userdata('warehouse_id') ? $this->site->getWarehouseByID($this->session->userdata('warehouse_id')) : NULL;
        }

        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('stock_counts')));
        $meta = array('page_title' => lang('stock_counts'), 'bc' => $bc);
        $this->page_construct('products/stock_counts', $meta, $this->data);
    }

    function getCounts($warehouse_id = NULL)
    {
        $this->sma->checkPermissions('stock_count', TRUE);

        if ((!$this->Owner || !$this->Admin) && !$warehouse_id) {
            $user = $this->site->getUser();
            $warehouse_id = $user->warehouse_id;
        }
        $detail_link = anchor('products/view_count/$1', '<label class="label label-primary pointer">' . lang('details') . '</label>', 'class="tip" title="' . lang('details') . '" data-toggle="modal" data-target="#myModal"');

        $this->load->library('datatables');
        $this->datatables
            ->select("{$this->db->dbprefix('stock_counts')}.id as id, date, reference_no, {$this->db->dbprefix('warehouses')}.name as wh_name, type, brand_names, category_names, initial_file, final_file")
            ->from('stock_counts')
            ->join('warehouses', 'warehouses.id=stock_counts.warehouse_id', 'left');
        if ($warehouse_id) {
            $this->datatables->where('warehouse_id', $warehouse_id);
        }

        $this->datatables->add_column('Actions', '<div class="text-center">' . $detail_link . '</div>', "id");
        echo $this->datatables->generate();
    }

    function view_count($id)
    {
        $this->sma->checkPermissions('stock_count', TRUE);
        $stock_count = $this->products_model->getStouckCountByID($id);
        if (!$stock_count->finalized) {
            $this->sma->md('products/finalize_count/' . $id);
        }

        $this->data['stock_count'] = $stock_count;
        $this->data['stock_count_items'] = $this->products_model->getStockCountItems($id);
        $this->data['warehouse'] = $this->site->getWarehouseByID($stock_count->warehouse_id);
        $this->data['adjustment'] = $this->products_model->getAdjustmentByCountID($id);
        $this->load->view($this->theme . 'products/view_count', $this->data);
    }

    function count_stock($page = NULL)
    {
        $this->sma->checkPermissions('stock_count');
        $this->form_validation->set_rules('warehouse', lang("warehouse"), 'required');
        $this->form_validation->set_rules('type', lang("type"), 'required');

        if ($this->form_validation->run() == true) {

            $warehouse_id = $this->input->post('warehouse');
            $type = $this->input->post('type');
            $categories = $this->input->post('category') ? $this->input->post('category') : NULL;
            $brands = $this->input->post('brand') ? $this->input->post('brand') : NULL;
            $this->load->helper('string');
            $name = random_string('md5') . '.csv';
            $products = $this->products_model->getStockCountProducts($warehouse_id, $type, $categories, $brands);
            $pr = 0;
            $rw = 0;
            foreach ($products as $product) {
                if ($variants = $this->products_model->getStockCountProductVariants($warehouse_id, $product->id)) {
                    foreach ($variants as $variant) {
                        $items[] = array(
                            'product_code' => $product->code,
                            'product_name' => $product->name,
                            'variant' => $variant->name,
                            'expected' => $variant->quantity,
                            'counted' => ''
                        );
                        $rw++;
                    }
                } else {
                    $items[] = array(
                        'product_code' => $product->code,
                        'product_name' => $product->name,
                        'variant' => '',
                        'expected' => $product->quantity,
                        'counted' => ''
                    );
                    $rw++;
                }
                $pr++;
            }
            if (!empty($items)) {
                $csv_file = fopen('./files/' . $name, 'w');
                fputcsv($csv_file, array(lang('product_code'), lang('product_name'), lang('variant'), lang('expected'), lang('counted')));
                foreach ($items as $item) {
                    fputcsv($csv_file, $item);
                }
                // file_put_contents('./files/'.$name, $csv_file);
                // fwrite($csv_file, $txt);
                fclose($csv_file);
            } else {
                $this->session->set_flashdata('error', lang('no_product_found'));
                redirect($_SERVER["HTTP_REFERER"]);
            }

            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld($this->input->post('date'));
            } else {
                $date = date('Y-m-d H:s:i');
            }
            $category_ids = '';
            $brand_ids = '';
            $category_names = '';
            $brand_names = '';
            if ($categories) {
                $r = 1;
                $s = sizeof($categories);
                foreach ($categories as $category_id) {
                    $category = $this->site->getCategoryByID($category_id);
                    if ($r == $s) {
                        $category_names .= $category->name;
                        $category_ids .= $category->id;
                    } else {
                        $category_names .= $category->name . ', ';
                        $category_ids .= $category->id . ', ';
                    }
                    $r++;
                }
            }
            if ($brands) {
                $r = 1;
                $s = sizeof($brands);
                foreach ($brands as $brand_id) {
                    $brand = $this->site->getBrandByID($brand_id);
                    if ($r == $s) {
                        $brand_names .= $brand->name;
                        $brand_ids .= $brand->id;
                    } else {
                        $brand_names .= $brand->name . ', ';
                        $brand_ids .= $brand->id . ', ';
                    }
                    $r++;
                }
            }
            $data = array(
                'date' => $date,
                'warehouse_id' => $warehouse_id,
                'reference_no' => $this->input->post('reference_no'),
                'type' => $type,
                'categories' => $category_ids,
                'category_names' => $category_names,
                'brands' => $brand_ids,
                'brand_names' => $brand_names,
                'initial_file' => $name,
                'products' => $pr,
                'rows' => $rw,
                'created_by' => $this->session->userdata('user_id')
            );

        }

        if ($this->form_validation->run() == true && $this->products_model->addStockCount($data)) {
            $this->session->set_flashdata('message', lang("stock_count_intiated"));
            redirect('products/stock_counts');

        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['warehouses'] = $this->site->getAllWarehouses();
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['brands'] = $this->site->getAllBrands();
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('count_stock')));
            $meta = array('page_title' => lang('count_stock'), 'bc' => $bc);
            $this->page_construct('products/count_stock', $meta, $this->data);

        }

    }

    function finalize_count($id)
    {
        $this->sma->checkPermissions('stock_count');
        $stock_count = $this->products_model->getStouckCountByID($id);
        if (!$stock_count || $stock_count->finalized) {
            $this->session->set_flashdata('error', lang("stock_count_finalized"));
            redirect('products/stock_counts');
        }

        $this->form_validation->set_rules('count_id', lang("count_stock"), 'required');

        if ($this->form_validation->run() == true) {

            if ($_FILES['csv_file']['size'] > 0) {
                $note = $this->sma->clear_tags($this->input->post('note'));
                $data = array(
                    'updated_by' => $this->session->userdata('user_id'),
                    'updated_at' => date('Y-m-d H:s:i'),
                    'note' => $note
                );

                $this->load->library('upload');
                $config['upload_path'] = $this->digital_upload_path;
                $config['allowed_types'] = 'csv';
                $config['max_size'] = $this->allowed_file_size;
                $config['overwrite'] = false;
                $config['encrypt_name'] = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('csv_file')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }

                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen($this->digital_upload_path . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 5000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                $titles = array_shift($arrResult);
                $keys = array('product_code', 'product_name', 'product_variant', 'expected', 'counted');
                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }
                //$this->sma->print_arrays($final);
                $rw = 2;
                $differences = 0;
                $matches = 0;
                foreach ($final as $pr) {
                    if ($product = $this->products_model->getProductByCode(trim($pr['product_code']))) {
                        $pr['counted'] = !empty($pr['counted']) ? $pr['counted'] : 0;
                        if ($pr['expected'] == $pr['counted']) {
                            $matches++;
                        } else {
                            $pr['stock_count_id'] = $id;
                            $pr['product_id'] = $product->id;
                            $pr['cost'] = $product->cost;
                            $pr['product_variant_id'] = empty($pr['product_variant']) ? NULL : $this->products_model->getProductVariantID($pr['product_id'], $pr['product_variant']);
                            $products[] = $pr;
                            $differences++;
                        }
                    } else {
                        $this->session->set_flashdata('error', lang('check_product_code') . ' (' . $pr['product_code'] . '). ' . lang('product_code_x_exist') . ' ' . lang('line_no') . ' ' . $rw);
                        redirect('products/finalize_count/' . $id);
                    }
                    $rw++;
                }

                $data['final_file'] = $csv;
                $data['differences'] = $differences;
                $data['matches'] = $matches;
                $data['missing'] = $stock_count->rows - ($rw - 2);
                $data['finalized'] = 1;
            }

            // $this->sma->print_arrays($data, $products);
        }

        if ($this->form_validation->run() == true && $this->products_model->finalizeStockCount($id, $data, $products)) {
            $this->session->set_flashdata('message', lang("stock_count_finalized"));
            redirect('products/stock_counts');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['stock_count'] = $stock_count;
            $this->data['warehouse'] = $this->site->getWarehouseByID($stock_count->warehouse_id);
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('products')), array('link' => site_url('products/stock_counts'), 'page' => lang('stock_counts')), array('link' => '#', 'page' => lang('finalize_count')));
            $meta = array('page_title' => lang('finalize_count'), 'bc' => $bc);
            $this->page_construct('products/finalize_count', $meta, $this->data);

        }

        function set_rack($product_id = NULL, $warehouse_id = NULL)
        {
            $this->sma->checkPermissions('edit', true);

            $this->form_validation->set_rules('rack', lang("rack_location"), 'trim|required');

            if ($this->form_validation->run() == true) {
                $data = array('rack' => $this->input->post('rack'),
                    'product_id' => $product_id,
                    'warehouse_id' => $warehouse_id,
                );
            } elseif ($this->input->post('set_rack')) {
                $this->session->set_flashdata('error', validation_errors());
                redirect("products");
            }

            if ($this->form_validation->run() == true && $this->products_model->setRack($data)) {
                $this->session->set_flashdata('message', lang("rack_set"));
                redirect("products/" . $warehouse_id);
            } else {
                $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
                $this->data['warehouse_id'] = $warehouse_id;
                $this->data['product'] = $this->site->getProductByID($product_id);
                $wh_pr = $this->products_model->getProductQuantity($product_id, $warehouse_id);
                $this->data['rack'] = $wh_pr['rack'];
                $this->data['modal_js'] = $this->site->modal_js();
                $this->load->view($this->theme . 'products/set_rack', $this->data);

            }
        }
    }

    function product_barcode($product_code = NULL, $bcs = 'code128', $height = 60)
    {
        // if ($this->Settings->barcode_img) {
        return "<img src='" . site_url('products/gen_barcode/' . $product_code . '/' . $bcs . '/' . $height) . "' alt='{$product_code}' class='bcimg' />";
        // } else {
        //     return $this->gen_barcode($product_code, $bcs, $height);
        // }
    }

    function barcode($product_code = NULL, $bcs = 'code128', $height = 60)
    {
        return site_url('products/gen_barcode/' . $product_code . '/' . $bcs . '/' . $height);
    }

    function gen_barcode($product_code = NULL, $bcs = 'code128', $height = 60, $text = 1)
    {
        $drawText = ($text != 1) ? FALSE : TRUE;
        $this->load->library('zend');
        $this->zend->load('Zend/Barcode');
        $barcodeOptions = array('text' => $product_code, 'barHeight' => $height, 'drawText' => $drawText, 'factor' => 1.0);
        if ($this->Settings->barcode_img) {
            $rendererOptions = array('imageType' => 'jpg', 'horizontalPosition' => 'center', 'verticalPosition' => 'middle');
            $imageResource = Zend_Barcode::render($bcs, 'image', $barcodeOptions, $rendererOptions);
            return $imageResource;
        } else {
            $rendererOptions = array('renderer' => 'svg', 'horizontalPosition' => 'center', 'verticalPosition' => 'middle');
            $imageResource = Zend_Barcode::render($bcs, 'svg', $barcodeOptions, $rendererOptions);
            header("Content-Type: image/svg+xml");
            echo $imageResource;
        }
    }

    function print_barcodes($product_id = NULL)
    {
        $this->sma->checkPermissions('barcode', true);

        $this->form_validation->set_rules('style', lang("style"), 'required');

        if ($this->form_validation->run() == true) {

            $style = $this->input->post('style');
            $bci_size = ($style == 10 || $style == 12 ? 50 : ($style == 14 || $style == 18 ? 30 : 20));
            $currencies = $this->site->getAllCurrencies();
            $s = isset($_POST['product']) ? sizeof($_POST['product']) : 0;
            if ($s < 1) {
                $this->session->set_flashdata('error', lang('no_product_selected'));
                redirect("products/print_barcodes");
            }
            for ($m = 0; $m < $s; $m++) {
                $pid = $_POST['product'][$m];
                $quantity = $_POST['quantity'][$m];
                $product = $this->products_model->getProductWithCategory($pid);
                $product->price = $this->input->post('check_promo') ? ($product->promotion ? $product->promo_price : $product->price) : $product->price;
                if ($variants = $this->products_model->getProductOptions($pid)) {
                    foreach ($variants as $option) {
                        if ($this->input->post('vt_' . $product->id . '_' . $option->id)) {
                            $barcodes[] = array(
                                'site' => $this->input->post('site_name') ? $this->Settings->site_name : FALSE,
                                'name' => $this->input->post('product_name') ? $product->name . ' - ' . $option->name : FALSE,
                                'image' => $this->input->post('product_image') ? $product->image : FALSE,
                                'barcode' => $this->product_barcode($product->code . $this->Settings->barcode_separator . $option->id, 'code128', $bci_size),
                                'price' => $this->input->post('price') ? $this->sma->formatMoney($option->price != 0 ? $option->price : $product->price) : FALSE,
                                'unit' => $this->input->post('unit') ? $product->unit : FALSE,
                                'category' => $this->input->post('category') ? $product->category : FALSE,
                                'currencies' => $this->input->post('currencies'),
                                'variants' => $this->input->post('variants') ? $variants : FALSE,
                                'quantity' => $quantity
                            );
                        }
                    }
                } else {
                    $barcodes[] = array(
                        'site' => $this->input->post('site_name') ? $this->Settings->site_name : FALSE,
                        'name' => $this->input->post('product_name') ? $product->name : FALSE,
                        'image' => $this->input->post('product_image') ? $product->image : FALSE,
                        'barcode' => $this->product_barcode($product->code, $product->barcode_symbology, $bci_size),
                        'price' => $this->input->post('price') ? $this->sma->formatMoney($product->price) : FALSE,
                        'unit' => $this->input->post('unit') ? $product->unit : FALSE,
                        'category' => $this->input->post('category') ? $product->category : FALSE,
                        'currencies' => $this->input->post('currencies'),
                        'variants' => FALSE,
                        'quantity' => $quantity
                    );
                }

            }
            $this->data['barcodes'] = $barcodes;
            $this->data['currencies'] = $currencies;
            $this->data['style'] = $style;
            $this->data['items'] = false;
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('print_barcodes')));
            $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
            $this->page_construct('products/print_barcodes', $meta, $this->data);

        } else {

            if ($this->input->get('purchase') || $this->input->get('transfer')) {
                if ($this->input->get('purchase')) {
                    $purchase_id = $this->input->get('purchase', TRUE);
                    $items = $this->products_model->getPurchaseItems($purchase_id);
                } elseif ($this->input->get('transfer')) {
                    $transfer_id = $this->input->get('transfer', TRUE);
                    $items = $this->products_model->getTransferItems($transfer_id);
                }
                if ($items) {
                    foreach ($items as $item) {
                        if ($row = $this->products_model->getProductByID($item->product_id)) {
                            $selected_variants = false;
                            if ($variants = $this->products_model->getProductOptions($row->id)) {
                                foreach ($variants as $variant) {
                                    $selected_variants[$variant->id] = isset($pr[$row->id]['selected_variants'][$variant->id]) && !empty($pr[$row->id]['selected_variants'][$variant->id]) ? 1 : ($variant->id == $item->option_id ? 1 : 0);
                                }
                            }
                            $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $item->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                        }
                    }
                    $this->data['message'] = lang('products_added_to_list');
                }
            }

            if ($product_id) {
                if ($row = $this->site->getProductByID($product_id)) {

                    $selected_variants = false;
                    if ($variants = $this->products_model->getProductOptions($row->id)) {
                        foreach ($variants as $variant) {
                            $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                        }
                    }
                    $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);

                    $this->data['message'] = lang('product_added_to_list');
                }
            }

            if ($this->input->get('category')) {
                if ($products = $this->products_model->getCategoryProducts($this->input->get('category'))) {
                    foreach ($products as $row) {
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }
                    $this->data['message'] = lang('products_added_to_list');
                } else {
                    $pr = array();
                    $this->session->set_flashdata('error', lang('no_product_found'));
                }
            }

            if ($this->input->get('subcategory')) {
                if ($products = $this->products_model->getSubCategoryProducts($this->input->get('subcategory'))) {
                    foreach ($products as $row) {
                        $selected_variants = false;
                        if ($variants = $this->products_model->getProductOptions($row->id)) {
                            foreach ($variants as $variant) {
                                $selected_variants[$variant->id] = $variant->quantity > 0 ? 1 : 0;
                            }
                        }
                        $pr[$row->id] = array('id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'code' => $row->code, 'name' => $row->name, 'price' => $row->price, 'qty' => $row->quantity, 'variants' => $variants, 'selected_variants' => $selected_variants);
                    }
                    $this->data['message'] = lang('products_added_to_list');
                } else {
                    $pr = array();
                    $this->session->set_flashdata('error', lang('no_product_found'));
                }
            }

            $this->data['items'] = isset($pr) ? json_encode($pr) : false;
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('print_barcodes')));
            $meta = array('page_title' => lang('print_barcodes'), 'bc' => $bc);
            $this->page_construct('products/print_barcodes', $meta, $this->data);

        }
    }
}
