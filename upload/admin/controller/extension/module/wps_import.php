<?php
class ControllerExtensionModuleWpsImport extends Controller {
	private $error      = array(); 
	private $tag_number = 0; 

	public function index() {
		$this->language->load('extension/module/wps_import');
		$this->document->setTitle($this->language->get('heading_title'));

  //texts:
    $data['heading_title']                       = $this->language->get('heading_title');
    $data['text_new_import']                     = $this->language->get('text_new_import');
    $data['text_import_name']                    = $this->language->get('text_import_name');
    $data['text_close']                          = $this->language->get('text_close');
    $data['text_add_import']                     = $this->language->get('text_add_import');
    $data['text_select_import']                  = $this->language->get('text_select_import');
    $data['text_select']                         = $this->language->get('text_select');
    $data['text_add_new_import']                 = $this->language->get('text_add_new_import');
    $data['text_save_import']                    = $this->language->get('text_save_import');
    $data['text_tags_setting']                   = $this->language->get('text_tags_setting');
    $data['text_import_setting']                 = $this->language->get('text_import_setting');
    $data['text_import_preview']                 = $this->language->get('text_import_preview');
    $data['text_main_setting']                   = $this->language->get('text_main_setting');
    $data['text_xml_link']                       = $this->language->get('text_xml_link');
    $data['text_xml_link_i']                     = $this->language->get('text_xml_link_i');
    $data['text_download']                       = $this->language->get('text_download');
    $data['text_download_images']                = $this->language->get('text_download_images');
    $data['text_download_images_i']              = $this->language->get('text_download_images_i');
    $data['text_yes']                            = $this->language->get('text_yes');
    $data['text_no']                             = $this->language->get('text_no');
    $data['text_product_tag']                    = $this->language->get('text_product_tag');
    $data['text_product_tag_i']                  = $this->language->get('text_product_tag_i');
    $data['text_primary_key']                    = $this->language->get('text_primary_key');
    $data['text_primary_key_i']                  = $this->language->get('text_primary_key_i');
    $data['text_product_id']                     = $this->language->get('text_product_id');
    $data['text_product_sku']                    = $this->language->get('text_product_sku');
    $data['text_product_model']                  = $this->language->get('text_product_model');
    $data['text_stock_status']                   = $this->language->get('text_stock_status');
    $data['text_stock_status_i']                 = $this->language->get('text_stock_status_i');
    $data['text_tax_class']                      = $this->language->get('text_tax_class');
    $data['text_tax_class_i']                    = $this->language->get('text_tax_class_i');
    $data['text_default_manufacturer']           = $this->language->get('text_default_manufacturer');
    $data['text_default_manufacturer_i']         = $this->language->get('text_default_manufacturer_i');
    $data['text_old_product']                    = $this->language->get('text_old_product');
    $data['text_old_product_i']                  = $this->language->get('text_old_product_i');
    $data['text_do_nothing']                     = $this->language->get('text_do_nothing');
    $data['text_delete']                         = $this->language->get('text_delete');
    $data['text_disable']                        = $this->language->get('text_disable');
    $data['text_set_zero_quantity']              = $this->language->get('text_set_zero_quantity');
    $data['text_product_status']                 = $this->language->get('text_product_status');
    $data['text_product_status_i']               = $this->language->get('text_product_status_i');
    $data['text_enabled']                        = $this->language->get('text_enabled');
    $data['text_disabled']                       = $this->language->get('text_disabled');
    $data['text_subtract']                       = $this->language->get('text_subtract');
    $data['text_subtract_i']                     = $this->language->get('text_subtract_i');
    $data['text_default_quantity']               = $this->language->get('text_default_quantity');
    $data['text_default_quantity_i']             = $this->language->get('text_default_quantity_i');
    $data['text_store']                          = $this->language->get('text_store');
    $data['text_store_i']                        = $this->language->get('text_store_i');
    $data['text_update']                         = $this->language->get('text_update');
    $data['text_update_i']                       = $this->language->get('text_update_i');
    $data['text_tooltip_xml_link']               = $this->language->get('text_tooltip_xml_link');
    $data['text_delete_feed']                    = $this->language->get('text_delete_feed');
    $data['text_delete_feed_i']                  = $this->language->get('text_delete_feed_i');
    $data['text_delete_feed_button']             = $this->language->get('text_delete_feed_button');
    $data['text_delete_feed_confirm']            = $this->language->get('text_delete_feed_confirm');
    $data['text_delete_feed_products']           = $this->language->get('text_delete_feed_products');
    $data['text_tag_preview']                    = $this->language->get('text_tag_preview');
    $data['text_product_preview']                = $this->language->get('text_product_preview');
    $data['text_type']                           = $this->language->get('text_type');
    $data['text_value']                          = $this->language->get('text_value');
    $data['text_category_only']                  = $this->language->get('text_category_only');
    $data['text_category_only_i']                = $this->language->get('text_category_only_i');
    $data['text_tooltip_category_only']          = $this->language->get('text_tooltip_category_only');
    $data['text_product_in_parent_category']                  = $this->language->get('text_product_in_parent_category');
    $data['text_product_in_parent_category_i']                = $this->language->get('text_product_in_parent_category_i');
    $data['text_tooltip_product_in_parent_category']          = $this->language->get('text_tooltip_product_in_parent_category');
    $data['text_product_only_old_update']                  = $this->language->get('text_product_only_old_update');
    $data['text_product_only_old_update_i']                = $this->language->get('text_product_only_old_update_i');
    $data['text_tooltip_product_only_old_update']          = $this->language->get('text_tooltip_product_only_old_update');
    $data['text_global_language_id']             = $this->language->get('text_global_language_id');
    $data['text_global_language_id_i']           = $this->language->get('text_global_language_id_i');
    $data['text_tooltip_global_language_id']     = $this->language->get('text_tooltip_global_language_id');
    $data['text_loading']                        = $this->language->get('text_loading');
    $data['text_length_class']                   = $this->language->get('text_length_class');
    $data['text_length_class_i']                 = $this->language->get('text_length_class_i');
    $data['text_weight_class']                   = $this->language->get('text_weight_class');
    $data['text_weight_class_i']                 = $this->language->get('text_weight_class_i');
    $data['text_default_attribute_group_name']   = $this->language->get('text_default_attribute_group_name');
    $data['text_default_attribute_group_name_i'] = $this->language->get('text_default_attribute_group_name_i');
    $data['text_default_option_group_name']      = $this->language->get('text_default_option_group_name');
    $data['text_default_option_group_name_i']    = $this->language->get('text_default_option_group_name_i');
    $data['text_default_option_type']            = $this->language->get('text_default_option_type');
    $data['text_default_option_type_i']          = $this->language->get('text_default_option_type_i');
    $data['text_default_option_quantity']        = $this->language->get('text_default_option_quantity');
    $data['text_default_option_quantity_i']      = $this->language->get('text_default_option_quantity_i');
    $data['text_default_option_subtract']        = $this->language->get('text_default_option_subtract');
    $data['text_default_option_subtract_i']      = $this->language->get('text_default_option_subtract_i');
    $data['text_default_option_required']        = $this->language->get('text_default_option_required');
    $data['text_default_option_required_i']      = $this->language->get('text_default_option_required_i');
    $data['text_category_separator']             = $this->language->get('text_category_separator');
    $data['text_category_separator_i']           = $this->language->get('text_category_separator_i');
    $data['text_seo_keyword_product']            = $this->language->get('text_seo_keyword_product');
    $data['text_seo_keyword_category']           = $this->language->get('text_seo_keyword_category');
    $data['text_seo_keyword_manufacturer']       = $this->language->get('text_seo_keyword_manufacturer');
    $data['text_friendly_url']                   = $this->language->get('text_friendly_url');
    $data['text_seo_id_name']                    = $this->language->get('text_seo_id_name');
    $data['text_seo_name']                       = $this->language->get('text_seo_name');
    $data['text_skip']                           = $this->language->get('text_skip');
    $data['text_content']                        = $this->language->get('text_content');
    $data['text_import_parts']                   = $this->language->get('text_import_parts');
    $data['text_import_parts_i']                 = $this->language->get('text_import_parts_i');
    $data['text_importing_info']                 = $this->language->get('text_importing_info');
    $data['text_import_now']                     = $this->language->get('text_import_now');
    $data['text_cron_link']                      = $this->language->get('text_cron_link');
    $data['text_part']                           = $this->language->get('text_part');
    $data['text_checked_products']               = $this->language->get('text_checked_products');
    $data['text_inserted_products']              = $this->language->get('text_inserted_products');
    $data['text_updated_products']               = $this->language->get('text_updated_products');
    $data['text_progress']                       = $this->language->get('text_progress');
    $data['text_import_products']                = $this->language->get('text_import_products');
    $data['text_import_or_cron']                 = $this->language->get('text_import_or_cron');
    $data['text_importing']                      = $this->language->get('text_importing');
    $data['text_information']                    = $this->language->get('text_information');
    $data['text_none']                           = $this->language->get('text_none');
    $data['text_tooltip_download_images']        = $this->language->get('text_tooltip_download_images');
    $data['text_tooltip_import_parts']           = $this->language->get('text_tooltip_import_parts');
    $data['text_tooltip_product_tag']            = $this->language->get('text_tooltip_product_tag');
    $data['text_tooltip_primary_key']            = $this->language->get('text_tooltip_primary_key');
    $data['text_tooltip_tax_class']              = $this->language->get('text_tooltip_tax_class');
    $data['text_tooltip_length_class']           = $this->language->get('text_tooltip_length_class');
    $data['text_tooltip_weight_class']           = $this->language->get('text_tooltip_weight_class');
    $data['text_tooltip_default_manufacturer']   = $this->language->get('text_tooltip_default_manufacturer');
    $data['text_tooltip_attribute_group']        = $this->language->get('text_tooltip_attribute_group');
    $data['text_tooltip_option_group']           = $this->language->get('text_tooltip_option_group');
    $data['text_tooltip_option_quantity']        = $this->language->get('text_tooltip_option_quantity');
    $data['text_tooltip_option_type']            = $this->language->get('text_tooltip_option_type');
    $data['text_tooltip_option_subtract']        = $this->language->get('text_tooltip_option_subtract');
    $data['text_tooltip_option_required']        = $this->language->get('text_tooltip_option_required');
    $data['text_tooltip_seo_keyword']            = $this->language->get('text_tooltip_seo_keyword');
    $data['text_tooltip_category_separator']     = $this->language->get('text_tooltip_category_separator');
    $data['text_tooltip_old_product']            = $this->language->get('text_tooltip_old_product');
    $data['text_tooltip_product_status']         = $this->language->get('text_tooltip_product_status');
    $data['text_tooltip_product_subtract']       = $this->language->get('text_tooltip_product_subtract');
    $data['text_tooltip_default_quantity']       = $this->language->get('text_tooltip_default_quantity');
    $data['text_tooltip_stock_status']           = $this->language->get('text_tooltip_stock_status');
    $data['text_successfully_imported']          = $this->language->get('text_successfully_imported');
    $data['text_import_setting_i']               = $this->language->get('text_import_setting_i');
    $data['text_tags_setting_i']                 = $this->language->get('text_tags_setting_i');
    $data['text_import_or_cron_i']               = $this->language->get('text_import_or_cron_i');
    $data['text_xml_file_damaged']               = $this->language->get('text_xml_file_damaged');
    $data['text_price_edit']                     = $this->language->get('text_price_edit');
    $data['text_price_edit_i']                   = $this->language->get('text_price_edit_i');
    $data['text_tooltip_price_edit']             = $this->language->get('text_tooltip_price_edit');
    $data['text_percent']                        = $this->language->get('text_percent');
    $data['text_fixed']                          = $this->language->get('text_fixed');
    $data['text_include_option_price']           = $this->language->get('text_include_option_price');
		$data['button_cancel']                       = $this->language->get('button_cancel');
		$data['text_special_price_group']            = $this->language->get('text_special_price_group');
		$data['text_special_price_group_i']          = $this->language->get('text_special_price_group_i');
		$data['text_tooltip_special_price_group']    = $this->language->get('text_tooltip_special_price_group');

		$data['text_product_shipping']               = $this->language->get('text_product_shipping');
		$data['text_product_shipping_i']             = $this->language->get('text_product_shipping_i');
		$data['text_tooltip_product_shipping']       = $this->language->get('text_tooltip_product_shipping');


    $data['update_items'] = array(
      'quantity'     => $this->language->get('text_update_item_product_quantity'),
      'price'        => $this->language->get('text_update_item_product_price'),
      'description'  => $this->language->get('text_update_item_product_description'),
      'attribute'    => $this->language->get('text_update_item_attribute'),
      'option'       => $this->language->get('text_update_item_option'),
      'category'     => $this->language->get('text_update_item_category'),
      'category_id'  => $this->language->get('text_update_item_category_id'),
      'image'        => $this->language->get('text_update_item_image'),
      'manufacturer' => $this->language->get('text_update_item_manufacturer'),
      'status' => "Product status",
    );

		$this->load->model('setting/setting');
		$this->load->model('tool/wps_import');
    $this->model_tool_wps_import->checkDatabasePrepare();
    
		if(isset($this->error['warning'])){
			$data['error_warning'] = $this->error['warning'];
		}else{
			$data['error_warning'] = '';
		}

		if(isset($this->session->data['success'])){
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}else{
			$data['success'] = '';
		}



    $data['feed_data']     = array();
    $data['xml_structure'] = false;
    $import_id = false;
    if(isset($_GET['import_id'])){
      $import_id = (int)$_GET['import_id'];
    }
    $data['import_id'] = $import_id;
    $data['imports']   = $this->model_tool_wps_import->getImports();
    
    
    $data['stock_statuses']   = $this->model_tool_wps_import->getStockStatuses();
    $data['tax_classes']      = $this->model_tool_wps_import->getTaxClasses();
    $data['length_classes']   = $this->model_tool_wps_import->getLengthClasses();
    $data['weight_classes']   = $this->model_tool_wps_import->getWeightClasses();
    $data['manufacturers']    = $this->model_tool_wps_import->getManufacturers();
    $data['stores']           = $this->model_tool_wps_import->getStores();
    $data['languages']        = $this->model_tool_wps_import->getLanguages();
    $data['customer_groups']  = $this->model_tool_wps_import->getCustomerGroups();


    if(isset($_POST['add_import'])){
      $import_name = htmlspecialchars($_POST['import_name']);
      $import_id = $this->model_tool_wps_import->addImport($import_name);
			$this->session->data['success'] = $this->language->get('text_insert_import_success');
			$this->response->redirect($this->url->link('extension/module/wps_import', 'token=' . $this->session->data['token'] . '&import_id='.(int)$import_id, true));
    }
    
    if(isset($_GET['action']) AND $_GET['action'] == "deleteImport"){
      $include_products = htmlspecialchars($_GET['include_products']);
      $delete_products = false;
      if($include_products == "true"){$delete_products = true;}
      $this->model_tool_wps_import->deleteImport($import_id,$include_products);
			$this->session->data['success'] = $this->language->get('text_delete_success');
			$this->response->redirect($this->url->link('extension/module/wps_import', 'token=' . $this->session->data['token'], true));
    }

		if(isset($_POST['feed_data'])) {
      $store_id     = array();
      $update_items = array();
      $tags         = false;
      $product_shipping = 0;
      
      if(isset($_POST['feed_data']['store_id'])){$store_id = $_POST['feed_data']['store_id'];}
      if(isset($_POST['feed_data']['update_items'])){$update_items = $_POST['feed_data']['update_items'];}
      if(isset($_POST['tag'])){$tags = $_POST['tag'];}
      if(!isset($_POST['feed_data']['price_edit_options'])){$_POST['feed_data']['price_edit_options'] = 0;}
      if(isset($_POST['feed_data']['product_shipping']) AND (int)$_POST['feed_data']['product_shipping'] == 1){$product_shipping = 1;}
      
      $feed_data = array(
        'download_image'           => (int)$_POST['feed_data']['download_image'],
        'stock_status_id'          => (int)$_POST['feed_data']['stock_status_id'],
        'tax_class_id'             => (int)$_POST['feed_data']['tax_class_id'],
        'length_class_id'          => (int)$_POST['feed_data']['length_class_id'],
        'weight_class_id'          => (int)$_POST['feed_data']['weight_class_id'],
        'manufacturer_id'          => (int)$_POST['feed_data']['manufacturer_id'],
        'category_only'            => (int)$_POST['feed_data']['category_only'],
        'product_in_parent_category' => (int)$_POST['feed_data']['product_in_parent_category'],
        'product_only_old_update' => (int)$_POST['feed_data']['product_only_old_update'],
        'global_language_id'       => (int)$_POST['feed_data']['global_language_id'],
        'product_status'           => (int)$_POST['feed_data']['product_status'],
        'option_subtract'          => (int)$_POST['feed_data']['option_subtract'],
        'option_required'          => (int)$_POST['feed_data']['option_required'],
        'option_quantity'          => (int)$_POST['feed_data']['option_quantity'],
        'old_product_action'       => htmlspecialchars($_POST['feed_data']['old_product_action']),
        'xml_url'                  => $_POST['feed_data']['xml_url'],
        'product_tag'              => isset($_POST['feed_data']['product_tag']) ? $_POST['feed_data']['product_tag'] : '',
        'primary_key'              => htmlspecialchars($_POST['feed_data']['primary_key']),
        'product_subtract'         => htmlspecialchars($_POST['feed_data']['product_subtract']),
        'product_quantity'         => htmlspecialchars($_POST['feed_data']['product_quantity']),
        'attribute_group_name'     => htmlspecialchars($_POST['feed_data']['attribute_group_name']),
        'option_group_name'        => htmlspecialchars($_POST['feed_data']['option_group_name']),
        'option_type'              => htmlspecialchars($_POST['feed_data']['option_type']),
        'category_separator'       => htmlspecialchars($_POST['feed_data']['category_separator']),
        'seo_keyword_product'      => htmlspecialchars($_POST['feed_data']['seo_keyword_product']),
        'seo_keyword_category'     => htmlspecialchars($_POST['feed_data']['seo_keyword_category']),
        'seo_keyword_manufacturer' => htmlspecialchars($_POST['feed_data']['seo_keyword_manufacturer']),
        'price_edit_type'          => htmlspecialchars($_POST['feed_data']['price_edit_type']),
        'store_id'                 => $store_id,
        'update_items'             => $update_items,
        'tags'                     => $tags,
        'tag_cache'                => isset($_POST['tag_cache']) ? $_POST['tag_cache'] : '',
        'price_edit'               => (float)$_POST['feed_data']['price_edit'],
        'parts'                    => (int)$_POST['feed_data']['parts'],
        'price_edit_options'       => (int)$_POST['feed_data']['price_edit_options'],
        'special_price_customer_group_id' => (int)$_POST['feed_data']['special_price_customer_group_id'],
        'product_shipping'                => $product_shipping,
      );
      
      $this->model_tool_wps_import->updateImport($import_id,$feed_data);
      $this_import = $this->model_tool_wps_import->getImport($import_id);
			$this->session->data['success'] = sprintf($this->language->get('text_success_update'),$this_import['name']);
			$this->response->redirect($this->url->link('extension/module/wps_import', 'token=' . $this->session->data['token'].'&import_id='.$import_id, true));
		}
    
    
//links
    $data['download_xml_url']                    = $this->url->link('extension/module/wps_import/download_xml', 'import_id='.$import_id.'&token=' . $this->session->data['token'], true);
    $data['xml_structure_url']                   = $this->url->link('extension/module/wps_import/getXMLStructure', 'import_id='.$import_id.'&token=' . $this->session->data['token'], true);
    $data['link_redirect_link']                  = $this->url->link('extension/module/wps_import', 'token=' . $this->session->data['token'], true);
    $data['link_delete_import']                  = $this->url->link('extension/module/wps_import', 'token=' . $this->session->data['token'] . '&action=deleteImport&include_products=false&import_id='.$import_id, true);
    $data['link_delete_import_include_products'] = $this->url->link('extension/module/wps_import', 'token=' . $this->session->data['token'] . '&action=deleteImport&include_products=true&import_id='.$import_id, true);
    $data['link_product_tags']                   = $this->url->link('extension/module/wps_import/getProductTagsOptions', 'token=' . $this->session->data['token'], true);
    $data['link_import_info']                    = $this->url->link('extension/module/wps_import/getXMLInfo', 'import_id='.$import_id.'&token=' . $this->session->data['token'], true);
		$data['cancel']                              = $this->url->link('extension/extension', 'token=' . $this->session->data['token'].'&type=module', true);
    $data['xml_created_file']                    = false;
    $data['feed_data']                           = array();
    




    if($import_id){
        if(isset($this->session->data['success'])){
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }else{
            $data['success'] = '';
        }
      $data['feed_data'] = $this->model_tool_wps_import->getImport($import_id);
      $import_info = unserialize($data['feed_data']['import_info']);
      if($import_info){
        foreach($import_info as $key => $value){
          $data['text_import_info'][$key]  = $this->language->get('text_import_info_'.$key);
        }
      }
      

      $xml_file = '../system/download/xml/feed_'.$import_id.'.xml';
      if(VERSION >= '2.1.0.1'){
        $xml_file = '../system/storage/download/xml/feed_'.$import_id.'.xml';
      }


      $xml_structure = $this->model_tool_wps_import->getTagsCache($import_id);
      
      if(file_exists($xml_file)){
        $data['xml_info_icon'] = "success";
        $data['xml_info_text'] = sprintf($this->language->get('text_last_download'),date("d.m.Y H:i:s", filemtime($xml_file)));
      }else{
        $data['xml_info_icon'] = "info";
        $data['xml_info_text'] = $this->language->get('text_xml_unsaved');
      }
      
      $data['xml_structure'] = array();
      $data['total_levels']  = false;
      
      $total_levels = 0;
      if($xml_structure){
        foreach($xml_structure as $tag){
          if($total_levels < $tag['level']){$total_levels = (int)$tag['level'];}
        }
        $data['xml_structure'] = $xml_structure;
        $data['xml_tags']      = $this->model_tool_wps_import->getXmlTags($import_id);
        $data['total_levels']  = $total_levels;
      }
      $data['tag_options'] = $this->getTagOptions();
    }



		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], true),
			'separator' => false
		);
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], true),
			'separator' => ' :: '
		);
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/wps_import', 'token=' . $this->session->data['token'], true),
			'separator' => ' :: '
		);


		$data['token']       = $this->session->data['token'];
		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/module/wps_import', $data));
	}
  
  
  

	public function getProductTagsOptions() {
		$import_id = (int)$_POST['import_id'];
    if($import_id){
  		$this->load->model('tool/wps_import');

      $xml_file      = '../system/download/xml/feed_'.$import_id.'.xml';

      if(VERSION >= '2.1.0.1'){
        $xml_file = '../system/storage/download/xml/feed_'.$import_id.'.xml';
      }


      $xml_structure = $this->model_tool_wps_import->getXMLStructure($xml_file);
      $this_import   = $this->model_tool_wps_import->getImport($import_id);
      $html          = '';
      if($xml_structure){
        foreach($xml_structure as $tag){
          $selected = '';
          $full_tag_name = str_replace('>',';',$tag['full_tag_name']);
          if($this_import['product_tag'] == $full_tag_name){$selected = ' selected="selected"';}
          $html .= ' <option value="'.str_replace('>',';',$tag['full_tag_name']).'"'.$selected.'>'.str_replace('>',' &gt; ',$tag['full_tag_name']).'</option>'; //NNEEWW
        }
      }
      echo $html;
    }
    die();
	}
  
  



	public function getXMLInfo(){
    $import_id = (int)$_GET['import_id'];
		$this->language->load('extension/module/wps_import');
 		$this->load->model('tool/wps_import');
    $import_data = $this->model_tool_wps_import->getImport($import_id);
    $import_info = unserialize($import_data['import_info']);
    $return = '';
    if($import_info){
      foreach($import_info as $key => $value){
        if($value == ""){$value = '-';}
        $return .= '<tr>';
        $return .= '  <td class="name">'.$this->language->get('text_import_info_'.$key).'</td>';
        $return .= '  <td>'.$value.'</td>';
        $return .= '</tr>';
      }
    }
    echo $return;
    die();
  }
  
  public function download_xml() {
		$this->language->load('extension/module/wps_import');
    if(isset($_POST['download_xml'])){
      $xml_url   = $_POST['xml_url'];
      $import_id = htmlspecialchars($_POST['import_id']);
   		$this->load->model('tool/wps_import');
      $download = $this->model_tool_wps_import->downloadXML($import_id,$xml_url);
      if(!$download){echo 'error|'.$this->language->get('text_incorrect_xml_link');}
      else{echo 'success|'.$this->language->get('text_xml_successfully_downloaded');}
    }else{
      echo 'error|Error number 1870';
    }
    die();
  }
  
	public function getTagOptions(){
		$this->language->load('extension/module/wps_import');
		$this->load->model('tool/wps_import');
    $languages                 = $this->model_tool_wps_import->getLanguages();
    $tag_options               = array();
    $tag_options['product_id'] = $this->language->get('text_product_id');

    foreach($languages as $language){
      $tag_options['product_name['.$language['language_id']."]"] = $this->language->get('text_product_name')." (".$language['code'].")";
    }
    
    foreach($languages as $language){
      $tag_options['product_description['.$language['language_id']."]"] = $this->language->get('text_product_description')." (".$language['code'].")";
    }
    
    foreach($languages as $language){
      $tag_options['product_meta_description['.$language['language_id']."]"] = $this->language->get('text_product_meta_description')." (".$language['code'].")";
    }
    
    foreach($languages as $language){
      $tag_options['product_meta_keyword['.$language['language_id']."]"] = $this->language->get('text_product_meta_keyword')." (".$language['code'].")";
    }
    
    if(VERSION >= '1.5.4'){
      foreach($languages as $language){
        $tag_options['product_tag['.$language['language_id']."]"] = $this->language->get('text_product_tag')." (".$language['code'].")";
      }
    }
    
    $tag_options['model']             = $this->language->get('text_product_model');
    $tag_options['sku']               = $this->language->get('text_product_sku');
    $tag_options['upc']               = $this->language->get('text_product_upc');

    if(VERSION >= '1.5.4'){
      $tag_options['status']               = "Product status (Enabled/Disabled)"; 
      $tag_options['jan']               = $this->language->get('text_product_jan'); 
      $tag_options['isbn']              = $this->language->get('text_product_isbn');
      $tag_options['mpn']               = $this->language->get('text_product_mpn');
    }

    $tag_options['quantity']          = $this->language->get('text_product_quantity');
    $tag_options['main_image']        = $this->language->get('text_product_main_image');
    $tag_options['image']             = $this->language->get('text_product_image');



    foreach($languages as $language){
      $tag_options['product_category_name['.$language['language_id']."]"] = $this->language->get('text_product_category_name')." (".$language['code'].")";
    }
    

    ///for upload category only
    $tag_options['category_parent']     = $this->language->get('text_category_parent');
    $tag_options['category_key_id']     = $this->language->get('text_category_key_id');
    //for other
    $tag_options['product_url'] = $this->language->get('text_product_url');
    $tag_options['product_category_id'] = $this->language->get('text_product_category_id');
    $tag_options['product_3d']          = $this->language->get('text_product_3d');
    $tag_options['manufacturer_name'] = $this->language->get('text_product_manufacturer_name');
    $tag_options['price']             = $this->language->get('text_product_price');
    $tag_options['special']           = $this->language->get('text_product_special'); //NNEEWW
    $tag_options['weight']            = $this->language->get('text_product_weight');
    $tag_options['length']            = $this->language->get('text_product_length');
    $tag_options['width']             = $this->language->get('text_product_width');
    $tag_options['height']            = $this->language->get('text_product_height');
    $tag_options['minimum']           = $this->language->get('text_product_minimum');
    $tag_options['product_option_price'] = $this->language->get('text_product_option_price');
    $tag_options['product_option_quantity'] = $this->language->get('text_product_option_quantity');

    foreach($languages as $language){
      $tag_options['product_option_name['.$language['language_id']."]"] = $this->language->get('text_product_option_name')." (".$language['code'].")";
    }

    foreach($languages as $language){
      $tag_options['product_option_value['.$language['language_id']."]"] = $this->language->get('text_product_option_value')." (".$language['code'].")";
    }

    foreach($languages as $language){
      $tag_options['product_attribute_group['.$language['language_id']."]"] = $this->language->get('text_product_attribute_group')." (".$language['code'].")";
    }

    foreach($languages as $language){
      $tag_options['product_attribute_name['.$language['language_id']."]"] = $this->language->get('text_product_attribute_name')." (".$language['code'].")";
    }

    foreach($languages as $language){
      $tag_options['product_attribute_value['.$language['language_id']."]"] = $this->language->get('text_product_attribute_value')." (".$language['code'].")";
    }

    foreach($languages as $language){
      $tag_options['product_attribute_value_second['.$language['language_id']."]"] = $this->language->get('text_product_attribute_value_second')." (".$language['code'].")";
    }

    foreach($languages as $language){
      $tag_options['product_attribute_value_third['.$language['language_id']."]"] = $this->language->get('text_product_attribute_value_third')." (".$language['code'].")";
    }

    foreach($languages as $language){
      $tag_options['product_attribute_type['.$language['language_id']."]"] = $this->language->get('text_product_attribute_type')." (".$language['code'].")";
    }

    foreach($languages as $language){
      $tag_options['product_attribute_format['.$language['language_id']."]"] = $this->language->get('text_product_attribute_format')." (".$language['code'].")";
    }

    foreach($languages as $language){
      $tag_options['product_attribute_extra['.$language['language_id']."]"] = $this->language->get('text_product_attribute_extra')." (".$language['code'].")";
    }

    return $tag_options;
  }


  public function haveTagChild($xml_structure,$level,$tag_name){
    $exists = false;
    foreach($xml_structure as $structure){
      if($structure['level'] == ($level+1) AND $structure['parent_tag'] == $tag_name){
        $exists = true;
      }
    }
    return $exists;
  }
  



// XML structure
        public function getTagOpenOrCloseTag($type,$level,$tag_name,$xml_structure){
          $row = "";
          $this->tag_number++;
          
          
          if($type == 'open'){
            
          
          $have_child = $this->haveTagChild($xml_structure,$level,$tag_name);
          if($have_child){
            $row = '<div class="row row-open"><span class="level level-'.$level.'"><i>&lt;</i><span class="tag_name">'.$tag_name.'</span><i>&gt;</i></span></div>';}
          }else{
            $row = '<div class="row row-open"><span class="level level-'.$level.'"><i>&lt;</i><span class="tag_name">'.$tag_name.'</span><i>&gt;</i><i>&lt;</i><span class="tag_name">'.$tag_name.'</span><i>&gt;</i></span></div>';
          }


          if($type == 'close'){
          $have_child = $this->haveTagChild($xml_structure,$level,$tag_name);
          if($have_child){
            $row = '<div class="row"><span class="level level-'.$level.'"><i>&lt;/</i><span class="tag_name">'.$tag_name.'</span><i>&gt;</i></span></div>';
          }
          
          
          
          }
          
          
          
          

          $tag_cache = '
          <input type="hidden" name="tag_cache['.$this->tag_number.'][tag_name]" value="'.$tag_name.'" />
          <input type="hidden" name="tag_cache['.$this->tag_number.'][tag_content]" value="" />
          <input type="hidden" name="tag_cache['.$this->tag_number.'][tag_key]" value="'.$tag_name.'" />
          <input type="hidden" name="tag_cache['.$this->tag_number.'][level]" value="'.$level.'" />
          ';
          
          
          return $tag_cache.$row;
        }
        
        
        public function getTagRow($import_id,$level,$tag_name,$tag_value,$tag_options,$tag_path = array()){
          $this->tag_number++;

	      	$this->language->load('extension/module/wps_import');

	       	$this->load->model('tool/wps_import');
          
          
          $row = '';
          
          
          $tag_name_start = '<i>&lt;</i><span class="tag_name">'.$tag_name.'</span><i>&gt;</i>';
          $tag_name_stop  = '<i>&lt;/</i><span class="tag_name">'.$tag_name.'</span><i>&gt;</i>';
          
                    
          
          
          $row .= '<div class="row">';
          $row .= '<span class="level level-'.$level.'">'.$tag_name_start;
          
          mb_internal_encoding("UTF-8");
          $display_limit = 35;

          $tag_value = htmlspecialchars_decode($tag_value);
          $tag_value = strip_tags($tag_value);
          $tag_value = htmlspecialchars($tag_value);

          $value_length  = strlen($tag_value);
          if($value_length > $display_limit){
            $display_value = mb_substr($tag_value,0,$display_limit)."..";
          }else{
            $display_value = $tag_value;
          }
          
          $row .= htmlspecialchars($display_value);
          $row .= $tag_name_stop;
          $row .= '</span>';
          
          
          
          
          
          $row .= '<div class="tag_content">'.$this->language->get('text_content').':&nbsp;&nbsp;';
 

          //DEBUG
          //$row .= "<font color='red'>".implode(';',$tag_path)."</font>";
          
          
    
    
          $row .= '<select name="tag['.implode(';',$tag_path).']" onChange="changeXMLContent();" data-name="'.$tag_name.'" data-value="'.$tag_value.'">';
          $row .= '<option value="-">'.$this->language->get('text_skip').'</option>';
          if(isset($tag_options)){
            foreach($tag_options as $option_key => $option_title){
              
              $selected = '';
              $have_assign_content = $this->model_tool_wps_import->getTagAssignContent($import_id,implode(';',$tag_path));
              if($have_assign_content == $option_key){
                $selected = ' selected="selected"';
              }
            
              $row .= '<option value="'.$option_key.'"'.$selected.'>'.$option_title.'</option>';
            }
          }
          $row .= '</select>';




          $tag_cache = '
          <input type="hidden" name="tag_cache['.$this->tag_number.'][tag_name]" value="'.$tag_name.'" />
          <input type="hidden" name="tag_cache['.$this->tag_number.'][tag_content]" value="'.htmlspecialchars($display_value).'" />
          <input type="hidden" name="tag_cache['.$this->tag_number.'][tag_key]" value="'.implode(';',$tag_path).'" />
          <input type="hidden" name="tag_cache['.$this->tag_number.'][level]" value="'.$level.'" />
          ';
          
          
          
          $row .= $tag_cache.'</div>';


          $row .= '</div>';
          
          
          return $row;                                                                                                                                                                                           
        }
        
        
  
  
  
	public function getXMLStructure() {
    
    
    if(isset($_GET['import_id'])){
      $import_id = (int)$_GET['import_id'];
    }else{
      $import_id = false;
    }
    
    
    if((int)$import_id == 0){
      echo 'Error number 4778';
      die();
    }
    
    
		$this->load->model('tool/wps_import');
  
  
    $xml_file = '../system/download/xml/feed_'.$import_id.'.xml';

    if(VERSION >= '2.1.0.1'){
      $xml_file = '../system/storage/download/xml/feed_'.$import_id.'.xml';
    }
    
      
    $xml_structure = $this->model_tool_wps_import->getXMLStructure($xml_file);

    
    $last_tag_name = false;
    $last_tag_level = false;
    
    $tag_options = $this->getTagOptions();
    
    $return_structure = '';
    
    
    if($xml_structure){
    foreach($xml_structure as $tag_0){
      if($tag_0['level'] == 0 AND strlen($tag_0['value']) == 0){
          $return_structure .= $this->getTagOpenOrCloseTag('open',$tag_0['level'],$tag_0['tag_name'],$xml_structure);



            foreach($xml_structure as $tag_1){
              if($tag_1['level'] == 1 AND $tag_1['parent_tag'] == $tag_0['tag_name'] AND strlen($tag_1['value']) == 0){
                  $return_structure .= $this->getTagOpenOrCloseTag('open',$tag_1['level'],$tag_1['tag_name'],$xml_structure);



                    foreach($xml_structure as $tag_2){
                      if($tag_2['level'] == 2 AND $tag_2['parent_tag'] == $tag_1['tag_name'] AND strlen($tag_2['value']) == 0){
                          $return_structure .= $this->getTagOpenOrCloseTag('open',$tag_2['level'],$tag_2['tag_name'],$xml_structure);



                            foreach($xml_structure as $tag_3){
                              if($tag_3['level'] == 3 AND $tag_3['parent_tag'] == $tag_2['tag_name'] AND strlen($tag_3['value']) == 0){
                                  $return_structure .= $this->getTagOpenOrCloseTag('open',$tag_3['level'],$tag_3['tag_name'],$xml_structure);
                


                                    foreach($xml_structure as $tag_4){
                                      if($tag_4['level'] == 4 AND $tag_4['parent_tag'] == $tag_3['tag_name'] AND strlen($tag_4['value']) == 0){
                                          $return_structure .= $this->getTagOpenOrCloseTag('open',$tag_4['level'],$tag_4['tag_name'],$xml_structure);




                                            foreach($xml_structure as $tag_5){
                                              if($tag_5['level'] == 5 AND $tag_5['parent_tag'] == $tag_4['tag_name'] AND strlen($tag_5['value']) == 0){
                                                  $return_structure .= $this->getTagOpenOrCloseTag('open',$tag_5['level'],$tag_5['tag_name'],$xml_structure);

                                                      foreach($xml_structure as $tag_6){
                                                          if($tag_6['level'] == 6 AND $tag_6['parent_tag'] == $tag_5['tag_name'] AND strlen($tag_6['value']) == 0){
                                                              $return_structure .= $this->getTagOpenOrCloseTag('open',$tag_6['level'],$tag_6['tag_name'],$xml_structure);

                                                                  foreach($xml_structure as $tag_7){
                                                                      if($tag_7['level'] == 7 AND $tag_7['parent_tag'] == $tag_6['tag_name'] AND strlen($tag_7['value']) == 0){
                                                                          $return_structure .= $this->getTagOpenOrCloseTag('open',$tag_7['level'],$tag_7['tag_name'],$xml_structure);


                                                                          foreach($xml_structure as $tag_8){
                                                                              if($tag_8['level'] == 8 AND $tag_8['parent_tag'] == $tag_7['tag_name'] AND strlen($tag_8['value']) == 0){
                                                                                  $return_structure .= $this->getTagOpenOrCloseTag('open',$tag_8['level'],$tag_8['tag_name'],$xml_structure);


                                                                                  foreach($xml_structure as $tag_9){
                                                                                      if($tag_9['level'] == 9 AND $tag_9['parent_tag'] == $tag_8['tag_name'] AND strlen($tag_9['value']) == 0){
                                                                                          $return_structure .= $this->getTagOpenOrCloseTag('open',$tag_9['level'],$tag_9['tag_name'],$xml_structure);
                                                                                          //only for 10 levels ! 0-7
                                                                                          $return_structure .= $this->getTagOpenOrCloseTag('close',$tag_9['level'],$tag_9['tag_name'],$xml_structure);
                                                                                      }elseif($tag_9['level'] == 9 AND $tag_9['parent_tag'] == $tag_8['tag_name'] AND strlen($tag_9['value']) > 0){
                                                                                          $return_structure .= $this->getTagRow($import_id,$tag_9['level'],$tag_9['tag_name'],$tag_9['value'],$tag_options,array($tag_0['tag_name'],$tag_1['tag_name'],$tag_2['tag_name'],$tag_3['tag_name'],$tag_4['tag_name'],$tag_5['tag_name'],$tag_6['tag_name'],$tag_7['tag_name'],$tag_8['tag_name'],$tag_9['tag_name']));
                                                                                      }
                                                                                  }


                                                                                  $return_structure .= $this->getTagOpenOrCloseTag('close',$tag_8['level'],$tag_8['tag_name'],$xml_structure);
                                                                              }elseif($tag_8['level'] == 8 AND $tag_8['parent_tag'] == $tag_7['tag_name'] AND strlen($tag_8['value']) > 0){
                                                                                  $return_structure .= $this->getTagRow($import_id,$tag_8['level'],$tag_8['tag_name'],$tag_8['value'],$tag_options,array($tag_0['tag_name'],$tag_1['tag_name'],$tag_2['tag_name'],$tag_3['tag_name'],$tag_4['tag_name'],$tag_5['tag_name'],$tag_6['tag_name'],$tag_7['tag_name'],$tag_8['tag_name']));
                                                                              }
                                                                          }

                                                                          $return_structure .= $this->getTagOpenOrCloseTag('close',$tag_7['level'],$tag_7['tag_name'],$xml_structure);
                                                                      }elseif($tag_7['level'] == 7 AND $tag_7['parent_tag'] == $tag_6['tag_name'] AND strlen($tag_7['value']) > 0){
                                                                          $return_structure .= $this->getTagRow($import_id,$tag_7['level'],$tag_7['tag_name'],$tag_7['value'],$tag_options,array($tag_0['tag_name'],$tag_1['tag_name'],$tag_2['tag_name'],$tag_3['tag_name'],$tag_4['tag_name'],$tag_5['tag_name'],$tag_6['tag_name'],$tag_7['tag_name']));
                                                                      }
                                                                  }


                                                              $return_structure .= $this->getTagOpenOrCloseTag('close',$tag_6['level'],$tag_6['tag_name'],$xml_structure);
                                                          }elseif($tag_6['level'] == 6 AND $tag_6['parent_tag'] == $tag_5['tag_name'] AND strlen($tag_6['value']) > 0){
                                                              $return_structure .= $this->getTagRow($import_id,$tag_6['level'],$tag_6['tag_name'],$tag_6['value'],$tag_options,array($tag_0['tag_name'],$tag_1['tag_name'],$tag_2['tag_name'],$tag_3['tag_name'],$tag_4['tag_name'],$tag_5['tag_name'],$tag_6['tag_name']));
                                                          }
                                                      }

                                                  $return_structure .= $this->getTagOpenOrCloseTag('close',$tag_5['level'],$tag_5['tag_name'],$xml_structure);
                                              }elseif($tag_5['level'] == 5 AND $tag_5['parent_tag'] == $tag_4['tag_name'] AND strlen($tag_5['value']) > 0){
                                                $return_structure .= $this->getTagRow($import_id,$tag_5['level'],$tag_5['tag_name'],$tag_5['value'],$tag_options,array($tag_0['tag_name'],$tag_1['tag_name'],$tag_2['tag_name'],$tag_3['tag_name'],$tag_4['tag_name'],$tag_5['tag_name']));
                                              }
                                            }
                                            
                                            
                                            
                                            
                                          $return_structure .= $this->getTagOpenOrCloseTag('close',$tag_4['level'],$tag_4['tag_name'],$xml_structure);
                                      }elseif($tag_4['level'] == 4 AND $tag_4['parent_tag'] == $tag_3['tag_name'] AND strlen($tag_4['value']) > 0){
                                        $return_structure .= $this->getTagRow($import_id,$tag_4['level'],$tag_4['tag_name'],$tag_4['value'],$tag_options,array($tag_0['tag_name'],$tag_1['tag_name'],$tag_2['tag_name'],$tag_3['tag_name'],$tag_4['tag_name']));
                                      }
                                    }
                                    
                                    
                                  $return_structure .= $this->getTagOpenOrCloseTag('close',$tag_3['level'],$tag_3['tag_name'],$xml_structure);
                              }elseif($tag_3['level'] == 3 AND $tag_3['parent_tag'] == $tag_2['tag_name'] AND strlen($tag_3['value']) > 0){
                                $return_structure .= $this->getTagRow($import_id,$tag_3['level'],$tag_3['tag_name'],$tag_3['value'],$tag_options,array($tag_0['tag_name'],$tag_1['tag_name'],$tag_2['tag_name'],$tag_3['tag_name']));
                              }
                            }


                          $return_structure .= $this->getTagOpenOrCloseTag('close',$tag_2['level'],$tag_2['tag_name'],$xml_structure);
                      }elseif($tag_2['level'] == 2 AND $tag_2['parent_tag'] == $tag_1['tag_name'] AND strlen($tag_2['value']) > 0){
                        $return_structure .= $this->getTagRow($import_id,$tag_2['level'],$tag_2['tag_name'],$tag_2['value'],$tag_options,array($tag_0['tag_name'],$tag_1['tag_name'],$tag_2['tag_name']));
                      }
                    }


                  $return_structure .= $this->getTagOpenOrCloseTag('close',$tag_1['level'],$tag_1['tag_name'],$xml_structure);
              }elseif($tag_1['level'] == 1 AND $tag_1['parent_tag'] == $tag_0['tag_name'] AND strlen($tag_1['value']) > 0){
                $return_structure .= $this->getTagRow($import_id,$tag_1['level'],$tag_1['tag_name'],$tag_1['value'],$tag_options,array($tag_0['tag_name'],$tag_1['tag_name']));
              }
            }


          $return_structure .= $this->getTagOpenOrCloseTag('close',$tag_0['level'],$tag_0['tag_name'],$xml_structure);
      }elseif($tag_0['level'] == 0 AND strlen($tag_0['value']) > 0){
        $return_structure .= $this->getTagRow($import_id,$tag_0['level'],$tag_0['tag_name'],$tag_0['value'],$tag_options,array($tag_0['tag_name']));
      }
    }
    }

    echo $return_structure;//.'<script type="text/javascript">changeXMLContent();</script>';
  }
  
  
}
?>