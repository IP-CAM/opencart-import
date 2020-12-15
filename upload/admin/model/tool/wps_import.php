<?php
class ModelToolWpsImport extends Model {

  public function getCustomerGroups(){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		return $query->rows;
  }
  
  
  public function getXmlTags($import_id){
    $return = array();
    $query = $this->db->query("SELECT distinct(tag_name),tag_key,level FROM `" . DB_PREFIX . "wps_xml_import_tags_cache` WHERE import_id = '".(int)$import_id."' ORDER by tag_cache_id ASC");
    if($query->rows){
      $main_tag_name = '';
      $prev_tag_name = '';
      foreach($query->rows as $tag){
        if($tag['level'] == 0){
          $tag_key       = $tag['tag_name'];
          $main_tag_name = $tag['tag_name'];
        }elseif($tag['level'] == 1){
          $tag_key = $main_tag_name.';'.$tag['tag_name'];
        }elseif(!strpos($tag['tag_key'],';') AND $tag['level'] != 0){
          $tag_key = $prev_tag_name.';'.$tag['tag_key'];
        }else{
          $tag_key = $tag['tag_key'];
        }
        $return[] = array(
          'level'    => $tag['level'], 
          'tag_name' => $tag_key, 
          'tag_key'  => $tag_key
        );
        $prev_tag_name = $tag_key;
      }
    }
    return $return;
  }
 


  public function checkDatabasePrepare(){
    $result = false;
    $table_exists = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "wps_xml_import'");
    if(!$table_exists->row){
      $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wps_xml_import` (`import_id` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(255) CHARACTER SET utf8 NOT NULL,`xml_url` text CHARACTER SET utf8 NOT NULL,`import_info` text CHARACTER SET utf8 NOT NULL,`parts` int(2) NOT NULL DEFAULT '1',`download_image` tinyint(1) NOT NULL DEFAULT '1',`product_tag` varchar(255) CHARACTER SET utf8 NOT NULL,`primary_key` varchar(255) CHARACTER SET utf8 NOT NULL,`stock_status_id` int(11) NOT NULL,`tax_class_id` int(11) NOT NULL,`length_class_id` int(11) NOT NULL,`weight_class_id` int(11) NOT NULL,`manufacturer_id` int(11) NOT NULL,`attribute_group_name` varchar(255) CHARACTER SET utf8 NOT NULL,`option_group_name` varchar(255) CHARACTER SET utf8 NOT NULL,`option_type` varchar(255) CHARACTER SET utf8 NOT NULL,`option_quantity` int(4) NOT NULL,`option_subtract` tinyint(1) NOT NULL DEFAULT '0',`option_required` tinyint(1) NOT NULL DEFAULT '1',`global_language_id` int(11) NOT NULL,`category_separator` varchar(255) CHARACTER SET utf8 NOT NULL,`seo_keyword_product` varchar(255) CHARACTER SET utf8 NOT NULL,`seo_keyword_category` varchar(255) CHARACTER SET utf8 NOT NULL,`seo_keyword_manufacturer` varchar(255) CHARACTER SET utf8 NOT NULL,`old_product_action` varchar(255) CHARACTER SET utf8 NOT NULL,`product_status` tinyint(1) NOT NULL DEFAULT '1',`product_subtract` tinyint(1) NOT NULL DEFAULT '1',`product_quantity` int(4) NOT NULL DEFAULT '0',`price_edit` decimal(10,4) NOT NULL,`price_edit_type` varchar(255) CHARACTER SET utf8 NOT NULL,`price_edit_options` tinyint(1) NOT NULL DEFAULT '0',`store_id` text CHARACTER SET utf8 NOT NULL,`update_items` text CHARACTER SET utf8 NOT NULL,`date_last_import` datetime NOT NULL,`date_added` datetime NOT NULL,`date_changed` datetime NOT NULL,PRIMARY KEY (`import_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "wps_xml_import` ADD `special_price_customer_group_id` INT(11) NOT NULL DEFAULT '1' AFTER `product_quantity`;");
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "wps_xml_import` ADD `product_shipping` TINYINT(1) NOT NULL DEFAULT '1' AFTER `product_subtract`; ");
      $this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD `feed_product_id` INT(11) NOT NULL AFTER `product_id`, ADD `import_id` INT(11) NOT NULL AFTER `feed_product_id`, ADD `import_active_product` TINYINT(1) NOT NULL DEFAULT '0' AFTER `import_id`;");
      $result = true;
    }
    $table_exists = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "wps_xml_import_tags'");
    if(!$table_exists->row){
      $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wps_xml_import_tags` (`tag_id` int(11) NOT NULL AUTO_INCREMENT,`import_id` int(11) NOT NULL,`tag_name` varchar(255) CHARACTER SET utf8 NOT NULL,`tag_content` varchar(255) CHARACTER SET utf8 NOT NULL,PRIMARY KEY (`tag_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
    }
    $table_exists = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "wps_xml_import_tags_cache'");
    if(!$table_exists->row){
      $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wps_xml_import_tags_cache` (`tag_cache_id` int(11) NOT NULL AUTO_INCREMENT,`import_id` int(11) NOT NULL,`tag_name` varchar(255) CHARACTER SET utf8 NOT NULL,`tag_content` text CHARACTER SET utf8 NOT NULL,`tag_key` varchar(255) CHARACTER SET utf8 NOT NULL,`tag_key_content` varchar(255) CHARACTER SET utf8 NOT NULL,`level` int(10) NOT NULL,PRIMARY KEY (`tag_cache_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
    }
    return $result;
  }

    public function oldProductAction($import_id)
    {
        $feed_data = $this->getImport($import_id);
        if ($feed_data['old_product_action'] != 'nothing') {
            $this->load->model('catalog/product');
            $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE import_id = '" . (int)$import_id . "' AND import_active_product = '0'");
            if ($query->rows) {
                foreach ($query->rows as $product) {
                    if ($feed_data['old_product_action'] == 'delete') {
                        $this->model_catalog_product->deleteProduct($product['product_id']);
                    }
                    if ($feed_data['old_product_action'] == 'disable') {
                        $this->db->query("UPDATE " . DB_PREFIX . "product SET status = '0' WHERE product_id = '" . (int)$product['product_id'] . "'");
                    }
                    if ($feed_data['old_product_action'] == 'zero_quantity') {
                        $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '0' WHERE product_id = '" . (int)$product['product_id'] . "'");
                    }
                }
            }
        }
    }


	public function setAllProductsInActive($import_id){
    return $this->db->query("UPDATE " . DB_PREFIX . "product SET import_active_product = '0' WHERE import_id = '".(int)$import_id."'");
  }


	public function clearCacheLogStats(){
    $file = fopen('import-stats.dat', "w+");
    fwrite($file, '0|0|0'); //inserted|updated|checked
    fclose($file);
    return true;
  }
	public function logCacheStats($type){
	 
    $file = fopen('import-stats.dat', "r");
    $stats = fread($file, filesize('import-stats.dat'));
    fclose($file);
    
    $stats_e  = explode("|",$stats);
    
    if(isset($stats_e[0])){$inserted = (int)$stats_e[0];}
    else{$inserted = 0;}
    
    if(isset($stats_e[1])){$updated = (int)$stats_e[1];}
    else{$updated = 0;}
    
    if(isset($stats_e[2])){$checked = (int)$stats_e[2];}
    else{$checked = 0;}
    
        
    if($type == "insert"){
      $inserted++;
    }
    if($type == "update"){
      $updated++;
    }
    
    $new_stats = $inserted.'|'.$updated.'|'.($inserted+$updated);
    
    
    $file = fopen('import-stats.dat', "w+");
    fwrite($file, $new_stats);
    fclose($file);
    
    return true;
  }




	public function saveImportStart($import_id){
    $feed_data = $this->getImport($import_id);
  	$import_info = unserialize($feed_data['import_info']);
    $import_info['import_start'] = date("Y-m-d H:i:s");
    $this->db->query("UPDATE " . DB_PREFIX . "wps_xml_import SET import_info = '".serialize($import_info)."' WHERE import_id = '".(int)$import_id."'");
  }
	public function saveImportEnd($import_id){
    $feed_data = $this->getImport($import_id);
  	$import_info = unserialize($feed_data['import_info']);
    $import_info['import_end'] = date("Y-m-d H:i:s");
    $import_time = strtotime($import_info['import_end'])-strtotime($import_info['import_start']);
    $import_info['import_time'] = gmdate("H:i:s",$import_time);
    $import_info['total_imports'] = $import_info['total_imports']+1;
    $this->db->query("UPDATE " . DB_PREFIX . "wps_xml_import SET import_info = '".serialize($import_info)."' WHERE import_id = '".(int)$import_id."'");
  }





	public function downloadXML($import_id,$xml_url = false){
  	$feed_data = $this->getImport($import_id);
  	
  	if($xml_url){
      $feed_data['xml_url'] = $xml_url;
    }

  	
    $xml_dir = '../system/download/xml/';

    if(VERSION >= '2.1.0.1'){
      $xml_dir = '../system/storage/download/xml/';
    }



    if(!is_dir($xml_dir)){
      mkdir($xml_dir);
    }
    
    $feed_data['xml_url'] = htmlspecialchars_decode($feed_data['xml_url']);
    
    
    $save_xml_name = $xml_dir.'feed_'.$import_id.'.xml';

    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $feed_data['xml_url']);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);

    if($content == false){
      $content = file_get_contents($feed_data['xml_url']);
    }


//fix for last tags inline - file damaged:
    $last_chars = substr($content, -1000);
    $last_tag = explode('</',$last_chars);
    $last_tag = $last_tag[count($last_tag)-1];
    $last_tag = '</'.$last_tag;
    $content = str_replace($last_tag,"\n".$last_tag,$content);
    file_put_contents($save_xml_name, $content);
    
    
    
  //import info - update
    $import_info_old = unserialize($feed_data['import_info']);
    
    $total_products = 0;
    if($feed_data['product_tag']){
      $xml = simplexml_load_file($save_xml_name);
      if($xml){
        $products = $xml->{$feed_data['product_tag']};
        $total_products = count($products);
      }
    }
    
    if(!isset($import_info_old['total_imports'])){$import_info_old['total_imports'] = 0;}
    if(!isset($import_info_old['last_import_date'])){$import_info_old['last_import_date'] = '';}
    if(!isset($import_info_old['import_time'])){$import_info_old['import_time'] = '';}
    if(!isset($import_info_old['import_start'])){$import_info_old['import_start'] = '';}
    if(!isset($import_info_old['import_end'])){$import_info_old['import_end'] = '';}
    if(!isset($import_info_old['import_time'])){$import_info_old['import_time'] = '';}

    $import_info_new = array(
      'total_imports'    => $import_info_old['total_imports'],
      'xml_size'         => round((filesize($save_xml_name)/1024/1024),2)." MB",
      'total_products'   => $total_products,
      'import_time'      => $import_info_old['import_time'],
      'import_start'     => $import_info_old['import_start'],
      'import_end'       => $import_info_old['import_end']
    );
    $this->db->query("UPDATE " . DB_PREFIX . "wps_xml_import SET import_info = '".serialize($import_info_new)."' WHERE import_id = '".(int)$import_id."'");

    return true;
  }

	public function deleteImport($import_id, $delete_products){
    $this->db->query("DELETE FROM " . DB_PREFIX . "wps_xml_import WHERE import_id = '".(int)$import_id."'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "wps_xml_import_tags WHERE import_id = '".(int)$import_id."'");
    $this->db->query("DELETE FROM " . DB_PREFIX . "wps_xml_import_tags_cache WHERE import_id = '".(int)$import_id."'");
    if($delete_products){
	  	$this->load->model('catalog/product');
      $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE import_id = '".(int)$import_id."'");
      foreach($query->rows as $product){
        $this->model_catalog_product->deleteProduct($product['product_id']);
      }
    }
    return true;
  }







	public function getImports(){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "wps_xml_import ORDER by name ASC");
    if(!$query->rows){
      return false;
    }else{
      return $query->rows;
    }
  }
  
  
  
  


	public function getImportTags($import_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "wps_xml_import_tags WHERE import_id = '".(int)$import_id."'");
    if(!$query->rows){
      return array();
    }else{
      return $query->rows;
    }
  }






	public function getImport($import_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "wps_xml_import WHERE import_id = '".(int)$import_id."'");
    if(!$query->row){
      return false;
    }else{
    
      $feed_data = $query->row;
    
      $return_feed_data                 = $feed_data;
      
      
      if(unserialize($feed_data['store_id'])){
        $return_feed_data['store_id']     = unserialize($feed_data['store_id']);
      }else{
       $return_feed_data['store_id']     = false;
      }
      
      
      
      
      
      if(unserialize($feed_data['update_items'])){
        $return_feed_data['update_items']     = unserialize($feed_data['update_items']);
      }else{
       $return_feed_data['update_items']     = false;
      }
      
      
      return $return_feed_data;
      
    }
  }



	public function addImport($import_name){
		$this->db->query("INSERT INTO " . DB_PREFIX . "wps_xml_import SET name = '".$this->db->escape($import_name)."', date_added = NOW()");
    return $this->getLastImportId();
  }
  
  
  
  
  



	public function updateImport($import_id, $feed_data){
		$this->db->query("UPDATE " . DB_PREFIX . "wps_xml_import SET 
xml_url = '".$this->db->escape($feed_data['xml_url'])."', 
download_image = '".(int)$feed_data['download_image']."', 
category_only = '".(int)$feed_data['category_only']."', 
product_in_parent_category = '".(int)$feed_data['product_in_parent_category']."', 
product_only_old_update = '".(int)$feed_data['product_only_old_update']."', 
product_tag = '".$this->db->escape($feed_data['product_tag'])."', 
primary_key = '".$this->db->escape($feed_data['primary_key'])."', 
stock_status_id = '".(int)$feed_data['stock_status_id']."', 
tax_class_id = '".(int)$feed_data['tax_class_id']."', 
length_class_id = '".(int)$feed_data['length_class_id']."', 
weight_class_id = '".(int)$feed_data['weight_class_id']."', 
manufacturer_id = '".(int)$feed_data['manufacturer_id']."', 
global_language_id = '".(int)$feed_data['global_language_id']."', 
old_product_action = '".$this->db->escape($feed_data['old_product_action'])."', 
product_status = '".(int)$feed_data['product_status']."', 
product_subtract = '".(int)$feed_data['product_subtract']."', 
product_quantity = '".(int)$feed_data['product_quantity']."', 
store_id = '".serialize($feed_data['store_id'])."', 
update_items = '".serialize($feed_data['update_items'])."', 
date_changed = NOW(),
attribute_group_name = '".$this->db->escape($feed_data['attribute_group_name'])."', 
option_group_name = '".$this->db->escape($feed_data['option_group_name'])."', 
option_type = '".$this->db->escape($feed_data['option_type'])."', 
option_quantity = '".(int)$feed_data['option_quantity']."',
option_subtract = '".(int)$feed_data['option_subtract']."',
option_required = '".(int)$feed_data['option_required']."',
category_separator = '".$this->db->escape($feed_data['category_separator'])."',
seo_keyword_product = '".$this->db->escape($feed_data['seo_keyword_product'])."',
seo_keyword_category = '".$this->db->escape($feed_data['seo_keyword_category'])."',
seo_keyword_manufacturer = '".$this->db->escape($feed_data['seo_keyword_manufacturer'])."', 
parts = '".(int)$feed_data['parts']."', 


price_edit = '".(float)$feed_data['price_edit']."', 
price_edit_type = '".$this->db->escape($feed_data['price_edit_type'])."', 
price_edit_options = '".(int)$feed_data['price_edit_options']."', 
special_price_customer_group_id = '".(int)$feed_data['special_price_customer_group_id']."', 
product_shipping = '".(int)$feed_data['product_shipping']."'

 WHERE import_id = '".(int)$import_id."'");
 
 
 
    
		$this->db->query("DELETE FROM " . DB_PREFIX . "wps_xml_import_tags WHERE import_id = '".(int)$import_id."'");

    foreach($feed_data['tags'] as $tag_name => $tag_content){
      if($tag_content != "-"){
	     	$this->db->query("INSERT INTO " . DB_PREFIX . "wps_xml_import_tags SET import_id = '".(int)$import_id."', tag_name = '".$this->db->escape($tag_name)."', tag_content = '".$this->db->escape($tag_content)."'");
      }
    }


  //cache - saved only if is refresh of xml structure
    if($feed_data['tag_cache']){
    	$this->db->query("DELETE FROM " . DB_PREFIX . "wps_xml_import_tags_cache WHERE import_id = '".(int)$import_id."'");
      foreach($feed_data['tag_cache'] as $index => $tag_cache_data){
      //get tag_key_content - like product_id, model etc..
        $tag_key_content = '';
        foreach($feed_data['tags'] as $tag_name => $tag_content){if($tag_name == $tag_cache_data['tag_key']){$tag_key_content = $tag_content;}}
        $this->db->query("INSERT INTO " . DB_PREFIX . "wps_xml_import_tags_cache SET import_id = '".(int)$import_id."', tag_name = '".$this->db->escape($tag_cache_data['tag_name'])."', tag_content = '".$this->db->escape($tag_cache_data['tag_content'])."', tag_key = '".$this->db->escape($tag_cache_data['tag_key'])."', level = '".(int)$tag_cache_data['level']."', tag_key_content = '".$this->db->escape($tag_key_content)."'");
      }
    }

    return $import_id;
    
    
    
  }
  
  
  
  public function getTagsCache($import_id){
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "wps_xml_import_tags_cache WHERE import_id = '".(int)$import_id."' ORDER by tag_cache_id ASC");
    return $query->rows;
  }  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
	public function getLastImportId(){
		$query = $this->db->query("SELECT import_id FROM " . DB_PREFIX . "wps_xml_import ORDER by import_id DESC LIMIT 1");
    return $query->row['import_id'];
  }





	public function getStockStatuses(){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "stock_status WHERE language_id = '".(int)$this->config->get('config_language_id')."' ORDER by name ASC");
    return $query->rows;
  }


	public function getTaxClasses(){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "tax_class ORDER by title ASC");
    return $query->rows;
  }
  
  
  
  
  


	public function getLengthClasses(){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class_description WHERE language_id = '".(int)$this->config->get('config_language_id')."'");
    return $query->rows;
  }

	public function getWeightClasses(){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class_description WHERE language_id = '".(int)$this->config->get('config_language_id')."'");
    return $query->rows;
  }


	public function getManufacturers(){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer ORDER by name ASC");
    return $query->rows;
  }


	public function getStores(){
		$stores[] = array(
			'store_id' => 0,
			'name'     => $this->config->get('config_name') . $this->language->get('text_default')
	  );
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");
	  if($query->rows){
      foreach($query->rows as $store){
        $stores[] = array(
          'store_id' => (int)$store['store_id'],
          'name'     => $store['name']
        );
      }
    }
		return $stores;
  }
  
  
  
  
  


	public function getTagAssignContent($import_id,$tag_name){
  
  
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "wps_xml_import_tags WHERE import_id = '".(int)$import_id."' AND tag_name = '".$this->db->escape($tag_name)."'");
	  if($query->row){
      return $query->row['tag_content'];
    }
		return false;
  }



  public function isTagEmpty($tag_content){

    $element_value = false;
    if(!$tag_content->children() || (string)$tag_content != ""){
      $value = (string)$tag_content;
      $value = urlencode($value);
      $value = str_replace("%0A","",$value);
      $value = urldecode($value);
      $value = nl2br($value);
      $value = str_replace(" ","",$value);
      $value = str_replace('<br />
',"",$value);
      $value = str_replace("\n","",$value);
      $value = str_replace("\t","",$value);
      $is_empty = strlen($value);

      if($is_empty > 0){
        $element_value = (string)$tag_content;
      }else{
        $element_value = false;
      }
    }

    if($element_value){
      $element_value = nl2br($element_value);
      $element_value = str_replace('<br />
',"",$element_value);
      $is_empty = strlen($element_value);
    
      if($is_empty == 0){
        return true;
      }else{
        return false;
      }
    }else{
      return true;
    }
  }







  
  public function getXMLStructure($xml_file){
    if(!file_exists($xml_file)){
      return false;
    }
    
//check xml file:
  libxml_use_internal_errors(TRUE);
  $xml_is_valid = simplexml_load_file($xml_file);  
  if($xml_is_valid == false){
    echo "xml-error-structure";
    die();
  }




  
  $xml_structure = array();
  $xml_values    = array();
  $xml           = simplexml_load_file($xml_file);  
  
  $main_xml_tag = $xml->getName();
  
  $structure_value = '0;;'.$main_xml_tag.';'.$main_xml_tag; //NNEEWW
  $xml_structure[] = $structure_value;
  $xml_values[$structure_value] = false;

  
  foreach($xml as $tag_name_1 => $tag_value_1){
    
    
    $structure_value = '1;'.$main_xml_tag.';'.$tag_name_1.';'.($main_xml_tag.'>'.$tag_name_1); //NNEEWW

    $tag_value = false;
//    if($xml->{$tag_name_1}->count() == 0){$tag_value = (string)$xml->{$tag_name_1};}
      if(!$this->isTagEmpty($tag_value_1)){$tag_value = (string)$tag_value_1;}

    if(!in_array($structure_value,$xml_structure)){$xml_structure[] = $structure_value;}
    if(!array_key_exists($structure_value, $xml_values) || $tag_value){$xml_values[$structure_value] = $tag_value;}
    
    
    
    
    
    if($tag_value_1){
      foreach($tag_value_1 as $tag_name_2 => $tag_value_2){

      $structure_value = '2;'.$tag_name_1.';'.$tag_name_2.';'.($main_xml_tag.'>'.$tag_name_1.'>'.$tag_name_2);
      
      $tag_value = false;
      if(!$this->isTagEmpty($tag_value_2)){$tag_value = (string)$tag_value_2;}

      if(!in_array($structure_value,$xml_structure)){$xml_structure[] = $structure_value;}
      if(!array_key_exists($structure_value, $xml_values) AND strlen($tag_value) > 0){$xml_values[$structure_value] = $tag_value;}
    
     
        if($tag_value_2){
          foreach($tag_value_2 as $tag_name_3 => $tag_value_3){
    
    
          $structure_value = '3;'.$tag_name_2.';'.$tag_name_3.';'.($main_xml_tag.'>'.$tag_name_1.'>'.$tag_name_2.'>'.$tag_name_3); //NNEEWW
      
          $tag_value = false;
          if(!$this->isTagEmpty($tag_value_3)){$tag_value = (string)$tag_value_3;}
          if(!in_array($structure_value,$xml_structure)){$xml_structure[] = $structure_value;}


          if(!array_key_exists($structure_value, $xml_values) AND strlen($tag_value) > 0){$xml_values[$structure_value] = $tag_value;}
          
            if($tag_value_3){
              foreach($tag_value_3 as $tag_name_4 => $tag_value_4){
    
                    
              $structure_value = '4;'.$tag_name_3.';'.$tag_name_4.';'.($main_xml_tag.'>'.$tag_name_1.'>'.$tag_name_2.'>'.$tag_name_3.'>'.$tag_name_4); //NNEEWW
          
              $tag_value = false;
              if(!$this->isTagEmpty($tag_value_4)){$tag_value = (string)$tag_value_4;}
              if(!in_array($structure_value,$xml_structure)){$xml_structure[] = $structure_value;}
              if(!array_key_exists($structure_value, $xml_values) AND strlen($tag_value) > 0){$xml_values[$structure_value] = $tag_value;}

//                echo $tag_name_4." = ".$tag_value."<br />";
 
                if($tag_value_4){
                  foreach($tag_value_4 as $tag_name_5 => $tag_value_5){
                  $structure_value = '5;'.$tag_name_4.';'.$tag_name_5.';'.($tag_name_1.'>'.$tag_name_2.'>'.$tag_name_3.'>'.$tag_name_4.'>'.$tag_name_5); //NNEEWW
                  $tag_value = false;
                  if(!$this->isTagEmpty($tag_value_5)){$tag_value = (string)$tag_value_5;}
                  if(!in_array($structure_value,$xml_structure)){$xml_structure[] = $structure_value;}
                  if(!array_key_exists($structure_value, $xml_values) AND strlen($tag_value) > 0){$xml_values[$structure_value] = $tag_value;}
                  
                    if($tag_value_5){
                      foreach($tag_value_5 as $tag_name_6 => $tag_value_6){
                      $structure_value = '6;'.$tag_name_5.';'.$tag_name_6.';'.($main_xml_tag.'>'.$tag_name_1.'>'.$tag_name_2.'>'.$tag_name_3.'>'.$tag_name_4.'>'.$tag_name_5.'>'.$tag_name_6); //NNEEWW
                      $tag_value = false;
                      if(!$this->isTagEmpty($tag_value_6)){$tag_value = (string)$tag_value_6;}
                      if(!in_array($structure_value,$xml_structure)){$xml_structure[] = $structure_value;}
                      if(!array_key_exists($structure_value, $xml_values) AND strlen($tag_value) > 0){$xml_values[$structure_value] = $tag_value;}

                          if($tag_value_6){
                              foreach($tag_value_6 as $tag_name_7 => $tag_value_7){
                                  $structure_value = '7;'.$tag_name_6.';'.$tag_name_7.';'.($main_xml_tag.'>'.$tag_name_1.'>'.$tag_name_2.'>'.$tag_name_3.'>'.$tag_name_4.'>'.$tag_name_5.'>'.$tag_name_6.'>'.$tag_name_7); //NNEEWW
                                  $tag_value = false;
                                  if(!$this->isTagEmpty($tag_value_7)){$tag_value = (string)$tag_value_7;}
                                  if(!in_array($structure_value,$xml_structure)){$xml_structure[] = $structure_value;}
                                  if(!array_key_exists($structure_value, $xml_values) AND strlen($tag_value) > 0){$xml_values[$structure_value] = $tag_value;}


                                  if($tag_value_7){
                                      foreach($tag_value_7 as $tag_name_8 => $tag_value_8){
                                          $structure_value = '8;'.$tag_name_7.';'.$tag_name_8.';'.($main_xml_tag.'>'.$tag_name_1.'>'.$tag_name_2.'>'.$tag_name_3.'>'.$tag_name_4.'>'.$tag_name_5.'>'.$tag_name_6.'>'.$tag_name_7.'>'.$tag_name_8); //NNEEWW
                                          $tag_value = false;
                                          if(!$this->isTagEmpty($tag_value_8)){$tag_value = (string)$tag_value_8;}
                                          if(!in_array($structure_value,$xml_structure)){$xml_structure[] = $structure_value;}
                                          if(!array_key_exists($structure_value, $xml_values) AND strlen($tag_value) > 0){$xml_values[$structure_value] = $tag_value;}

                                          if($tag_value_8){
                                              foreach($tag_value_8 as $tag_name_9 => $tag_value_9){
                                                  $structure_value = '9;'.$tag_name_8.';'.$tag_name_9.';'.($main_xml_tag.'>'.$tag_name_1.'>'.$tag_name_2.'>'.$tag_name_3.'>'.$tag_name_4.'>'.$tag_name_5.'>'.$tag_name_6.'>'.$tag_name_7.'>'.$tag_name_8.'>'.$tag_name_9); //NNEEWW
                                                  $tag_value = false;
                                                  if(!$this->isTagEmpty($tag_value_9)){$tag_value = (string)$tag_value_9;}
                                                  if(!in_array($structure_value,$xml_structure)){$xml_structure[] = $structure_value;}
                                                  if(!array_key_exists($structure_value, $xml_values) AND strlen($tag_value) > 0){$xml_values[$structure_value] = $tag_value;}

                                                  if($tag_value_9){
                                                      foreach($tag_value_9 as $tag_name_10 => $tag_value_10){
                                                          $structure_value = '10;'.$tag_name_9.';'.$tag_name_10.';'.($main_xml_tag.'>'.$tag_name_1.'>'.$tag_name_2.'>'.$tag_name_3.'>'.$tag_name_4.'>'.$tag_name_5.'>'.$tag_name_6.'>'.$tag_name_7.'>'.$tag_name_8.'>'.$tag_name_9.'>'.$tag_name_10); //NNEEWW
                                                          $tag_value = false;
                                                          if(!$this->isTagEmpty($tag_value_10)){$tag_value = (string)$tag_value_10;}
                                                          if(!in_array($structure_value,$xml_structure)){$xml_structure[] = $structure_value;}
                                                          if(!array_key_exists($structure_value, $xml_values) AND strlen($tag_value) > 0){$xml_values[$structure_value] = $tag_value;}

                                                      }
                                                  }


                                              }
                                          }

                                      }
                                  }

                              }
                          }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }





  $return_xml_structure = array();



  foreach($xml_structure as $structure){
    $data = explode(";",$structure);
    
    
    if(isset($xml_values[$structure])){
      $value = (string)$xml_values[$structure];
    }else{
      $value = false;
    }


    if($data[1] == ""){
      $parent_tag = $data[1];
    }
    
    
    
    $return_xml_structure[] = array(
      "level"         => (int)$data[0],
      "parent_tag"    => $data[1],
      "tag_name"      => $data[2],
      "full_tag_name" => $data[3], //NNEEWW
      "value"         => $value
    );


}


  return $return_xml_structure;




  }

    
    
    
    
    
  
  public function XMLToArray($xml) {
ini_set('memory_limit', '512M');


/*
    $parser = xml_parser_create('ISO-8859-1'); // For Latin-1 charset
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); // Dont mess with my cAsE sEtTings
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); // Dont bother with empty info
    xml_parse_into_struct($parser, $xml, $values);
    xml_parser_free($parser);
    */
    
    
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($xml), $values);
    xml_parser_free($parser);
    
    
    
    
    
   
    $return = array(); // The returned array
    $stack = array();  // tmp array used for stacking


    foreach($values as $val) {
      if($val['type'] == "open") {
        array_push($stack, $val['tag']);
      } elseif($val['type'] == "close") {
        array_pop($stack);
      } elseif($val['type'] == "complete") {

      if(isset($val['value'])){
        array_push($stack, $val['tag']);
        $this->setArrayValue($return, $stack, $val['value']);
        array_pop($stack);
      }



      }
    }
    return $return;
  }
   
  public function setArrayValue(&$array, $stack, $value) {
    if ($stack) {
      $key = array_shift($stack);
      $this->setArrayValue($array[$key], $stack, $value);
      return $array;
    } else {
      $array = $value;
    }//if-else
  }//function setArrayValue
  
  




























/****************************** IMPORT FUNCTIONS ******************************/
public function importProduct($product_data,$import_data){
 

  if(!isset($product_data['model'])){$product_data['model'] = false;}
  if(!isset($product_data['product_url'])){$product_data['product_url'] = false;}
  if(!isset($product_data['sku'])){$product_data['sku'] = false;}
  if(!isset($product_data['upc'])){$product_data['upc'] = false;}
  if(!isset($product_data['status'])){$product_data['status'] = 1;}
  if(!isset($product_data['jan'])){$product_data['jan'] = false;} 
  if(!isset($product_data['isbn'])){$product_data['isbn'] = false;}
  if(!isset($product_data['mpn'])){$product_data['mpn'] = false;}
  if(!isset($product_data['location'])){$product_data['location'] = false;}
  if(!isset($product_data['shipping'])){$product_data['shipping'] = false;}
  if(!isset($product_data['price'])){$product_data['price'] = false;}
  if(!isset($product_data['points'])){$product_data['points'] = false;}
  if(!isset($product_data['tax_class_id'])){$product_data['tax_class_id'] = false;}
  if(!isset($product_data['date_available'])){$product_data['date_available'] = false;}
  if(!isset($product_data['weight'])){$product_data['weight'] = false;}
  if(!isset($product_data['weight_class_id'])){$product_data['weight_class_id'] = false;}
  if(!isset($product_data['length'])){$product_data['length'] = false;}
  if(!isset($product_data['width'])){$product_data['width'] = false;}
  if(!isset($product_data['height'])){$product_data['height'] = false;}
  if(!isset($product_data['length_class_id'])){$product_data['length_class_id'] = false;}
  if(!isset($product_data['subtract'])){$product_data['subtract'] = false;}
  if(!isset($product_data['minimum'])){$product_data['minimum'] = false;}
  if(!isset($product_data['sort_order'])){$product_data['sort_order'] = false;}
  //if(!isset($product_data['status'])){$product_data['status'] = false;}
  if(!isset($product_data['date_added'])){$product_data['date_added'] = false;}
  if(!isset($product_data['date_modified'])){$product_data['date_modified'] = false;}
  if(!isset($product_data['viewed'])){$product_data['viewed'] = false;}

  if(!isset($product_data['main_image'])){$product_data['main_image'] = false;}
  if(!isset($product_data['images'])){$product_data['images'] = array();}


//price fix
  if(isset($product_data['price'])){
    $product_data['price'] = str_replace(" ","",$product_data['price']);
    $product_data['price'] = str_replace(",",".",$product_data['price']);
  }
  if(isset($product_data['special'])){
    $product_data['special'] = str_replace(" ","",$product_data['special']);
    $product_data['special'] = str_replace(",",".",$product_data['special']);
  }

  
  if($import_data['price_edit'] != 0){
    if($import_data['price_edit_type'] == "percentage"){
      $product_data['price'] = $product_data['price']+($product_data['price']/100*$import_data['price_edit']);
    }
    if($import_data['price_edit_type'] == "fixed"){
      $product_data['price'] = (float)$product_data['price']+$import_data['price_edit'];
    }
  }


//DATA FOR CATEGORY ONLY
    //categoryParent
        if(!isset($product_data['category_parent'])){$product_data['category_parent'] = 0;}
    //uniqCategoryId for add to category, to table category and field category_id
        if(!isset($product_data['category_key_id'])){$product_data['category_key_id'] = false;}
//DATA FOR OTHER
    //categoryID for add to product info, to table product to category
    if(!isset($product_data['category_id'])){$product_data['category_id'] = false;}
    //Product 3d for add in description end
    if(!isset($product_data['product_3d'])){$product_data['product_3d'] = false;}


//category
  $product_data['category'] = array();
  if(isset($product_data['categories'])){
    foreach($product_data['categories'] as $index => $category){
      $category_detail = array();
      foreach($this->getLanguages() as $language){
        $language_id = $language['language_id'];
        if(!isset($category[$language_id])){
            $category_detail[$language_id] = $category[$import_data['global_language_id']];
        }
        else{
            $category_detail[$language_id] = $category[$language_id];
        }
      }
      $product_data['category'][] = $category_detail;
    }
  }


//manufacturer
  $product_data['manufacturer_id'] = 0;
  if(isset($product_data['manufacturer_name']) AND $product_data['manufacturer_name'] != ""){
    if($import_data['store_id']){
      foreach($import_data['store_id'] as $store_id){$product_data['manufacturer_id'] = $this->getOrInsertManufacturerByName($product_data['manufacturer_name'],$store_id,$import_data);}
    }
  }else{
    $product_data['manufacturer_id'] = $import_data['manufacturer_id'];
  }


  
//product quantity
  if(!isset($product_data['quantity']) || $product_data['quantity'] == ""){
    $product_data['quantity'] = $import_data['product_quantity'];
  }




/************* GLOBAL VALUES FROM IMPORT SETTING **********/
//manufacturer
  $product_data['category_only'] = $import_data['category_only'];
  $product_data['product_in_parent_category'] = $import_data['product_in_parent_category'];
  $product_data['product_only_old_update'] = $import_data['product_only_old_update'];
  $product_data['stock_status_id'] = $import_data['stock_status_id'];
  $product_data['tax_class_id']    = $import_data['tax_class_id'];
  $product_data['length_class_id'] = $import_data['length_class_id'];
  $product_data['weight_class_id'] = $import_data['weight_class_id'];
  //$product_data['status']          = $import_data['product_status'];
  $product_data['subtract']        = $import_data['product_subtract'];


  if(!isset($product_data['product_descriptions'])){
    $product_data['product_descriptions'] = array();
  }else{
    foreach($this->getLanguages() as $language){
      $language_id = $language['language_id'];

/*
      if(!isset($product_data['product_descriptions'][$language_id])){
        $product_data['product_descriptions'][$language_id]['name']             = false;
        $product_data['product_descriptions'][$language_id]['description']      = false;
        $product_data['product_descriptions'][$language_id]['meta_description'] = false;
        $product_data['product_descriptions'][$language_id]['meta_keyword']     = false;
        $product_data['product_descriptions'][$language_id]['tag']              = false;
      }
*/
      
        if(!isset($product_data['product_descriptions'][$language_id]['name'])){
          if(isset($product_data['product_descriptions'][$import_data['global_language_id']]['name'])){
              $product_data['product_descriptions'][$language_id]['name'] = false;
              //$product_data['product_descriptions'][$language_id]['name'] = $product_data['product_descriptions'][$import_data['global_language_id']]['name'];
          }
          else{
              $product_data['product_descriptions'][$language_id]['name'] = false;
          }
        }
        if(!isset($product_data['product_descriptions'][$language_id]['description'])){
          if(isset($product_data['product_descriptions'][$import_data['global_language_id']]['description'])){
              $product_data['product_descriptions'][$language_id]['description'] = false;
              //$product_data['product_descriptions'][$language_id]['description'] = $product_data['product_descriptions'][$import_data['global_language_id']]['description'];
          }
          else{
              $product_data['product_descriptions'][$language_id]['description'] = false;
          }
        }else{
            if(!empty($product_data['product_3d'][0])){
                $product_data['product_descriptions'][$language_id]['description'] = $product_data['product_descriptions'][$language_id]['description'] . ' <iframe width="100%" height="500" src="'.$product_data['product_3d'][0].'" frameborder="0" allowfullscreen></iframe>';
            }
        }
        if(!isset($product_data['product_descriptions'][$language_id]['meta_description'])){
          if(isset($product_data['product_descriptions'][$import_data['global_language_id']]['meta_description'])){$product_data['product_descriptions'][$language_id]['meta_description'] = $product_data['product_descriptions'][$import_data['global_language_id']]['meta_description'];}
          else{$product_data['product_descriptions'][$language_id]['meta_description'] = false;}
        }
        if(!isset($product_data['product_descriptions'][$language_id]['meta_keyword'])){
          if(isset($product_data['product_descriptions'][$import_data['global_language_id']]['meta_keyword'])){$product_data['product_descriptions'][$language_id]['meta_keyword'] = $product_data['product_descriptions'][$import_data['global_language_id']]['meta_keyword'];}
          else{$product_data['product_descriptions'][$language_id]['meta_keyword'] = false;}
        }
        if(!isset($product_data['product_descriptions'][$language_id]['tag'])){
          if(isset($product_data['product_descriptions'][$import_data['global_language_id']]['tag'])){$product_data['product_descriptions'][$language_id]['tag'] = $product_data['product_descriptions'][$import_data['global_language_id']]['tag'];}
          else{$product_data['product_descriptions'][$language_id]['tag'] = false;}
        }
      
      

    }
  }



//attributes add other language
  if(!isset($product_data['product_attributes'])){
    $product_data['product_attributes'] = array();
  }else{  
    $i = 0;
    foreach($product_data['product_attributes'] as $attribute){
      foreach($this->getLanguages() as $language){
        $language_id = $language['language_id'];
        if(!isset($product_data['product_attributes'][$i][$language_id])){
  
          if(!isset($product_data['product_attributes'][$i][$language_id]['group'])){
            $group_name = $import_data['attribute_group_name'];
            //$group_name = $product_data['product_attributes'][$i][$import_data['global_language_id']]['group'];
            $groupId = (!isset($product_data['product_attributes'][$i][$import_data['global_language_id']]['groupId'])) ? '' : $product_data['product_attributes'][$i][$import_data['global_language_id']]['groupId'];
          }else{
            $group_name = $product_data['product_attributes'][$i][$import_data['global_language_id']]['group'];
            $groupId = $product_data['product_attributes'][$i][$import_data['global_language_id']]['groupId'];
          }

          $product_data['product_attributes'][$i][$language_id] = array(
            'group' => $group_name,
            'groupId' => $groupId,
            'name'  => (!isset($product_data['product_attributes'][$i][$language_id]['name'])) ? '' : $product_data['product_attributes'][$i][$language_id]['name'],
            'nameId'  => (!isset($product_data['product_attributes'][$i][$import_data['global_language_id']]['nameId'])) ? '' : $product_data['product_attributes'][$i][$import_data['global_language_id']]['nameId'],
            'text'  => (!isset($product_data['product_attributes'][$i][$import_data['global_language_id']]['text'])) ? '' : $product_data['product_attributes'][$i][$import_data['global_language_id']]['text'],
          );
        }else{
          if(!isset($product_data['product_attributes'][$i][$language_id]['group'])){$product_data['product_attributes'][$i][$language_id]['group'] = $import_data['attribute_group_name'];}
          if(!isset($product_data['product_attributes'][$i][$language_id]['name'])){$product_data['product_attributes'][$i][$language_id]['name'] = false;}
          if(!isset($product_data['product_attributes'][$i][$language_id]['text'])){$product_data['product_attributes'][$i][$language_id]['text'] = false;}
        }
      }
      $i++;
    }
  }

  
  
  
  
//options
  if(!isset($product_data['product_options'])){
    $product_data['product_options'] = array();
  }else{  
    $i = 0;
    foreach($product_data['product_options'] as $option){
      foreach($this->getLanguages() as $language){
        $language_id = $language['language_id'];
        if(!isset($product_data['product_options'][$i][$language_id]['name']) || !isset($product_data['product_options'][$i][$language_id]['value'])){
          
          
          if(!isset($product_data['product_options'][$i][$import_data['global_language_id']]['name'])){
            $group_name = $import_data['option_group_name'];
          }else{
            $group_name = $product_data['product_options'][$i][$import_data['global_language_id']]['name'];
          }

          
          if(!isset($product_data['product_options'][$i][$import_data['global_language_id']]['price'])){
            $option_price = 0;
          }else{
            $option_price = (float)$product_data['product_options'][$i][$import_data['global_language_id']]['price'];
          }
          
          
  
          if(isset($product_data['product_options'][$i][$import_data['global_language_id']]['quantity'])){
            $option_quantity = (int)$product_data['product_options'][$i][$import_data['global_language_id']]['quantity'];
          }else{
            $option_quantity = (int)$import_data['option_quantity'];
          }

        if(isset($product_data['product_options'][$i][$import_data['global_language_id']]['value'])){
          $option_value = $product_data['product_options'][$i][$import_data['global_language_id']]['value'];
        }else{
          $option_value = '';
        }
            

          $product_data['product_options'][$i][$language_id] = array(
            'name' => $group_name,
            'value'  => $option_value,
            'price'  => $option_price,
            'quantity' => (int)$option_quantity
          );
        }else{
          if(!isset($product_data['product_options'][$i][$language_id]['name'])){$product_data['product_options'][$i][$language_id]['name'] = $import_data['option_group_name'];}
          if(!isset($product_data['product_options'][$i][$language_id]['value'])){$product_data['product_options'][$i][$language_id]['value'] = false;}
          if(!isset($product_data['product_options'][$i][$language_id]['price'])){$product_data['product_options'][$i][$language_id]['price'] = false;}
        }

//price fix:
  $option_price = $product_data['product_options'][$i][$language_id]['price'];
  $option_price = str_replace(" ","",$option_price);
  $option_price = str_replace(",",".",$option_price);
  $product_data['product_options'][$i][$language_id]['price'] = $option_price;

          
  if($import_data['price_edit'] != 0 AND $import_data['price_edit_options'] == 1){
    if($import_data['price_edit_type'] == "percentage"){
      $option_price = $product_data['product_options'][$i][$language_id]['price'];
      $option_price = $option_price+($option_price/100*$import_data['price_edit']);
      $product_data['product_options'][$i][$language_id]['price'] = $option_price;
    }
    if($import_data['price_edit_type'] == "fixed"){
      $product_data['product_options'][$i][$language_id]['price'] = (float)$product_data['product_options'][$i][$language_id]['price']+$import_data['price_edit'];
    }
  }


        $product_data['product_options'][$i][$language_id]['type']     = $import_data['option_type'];
        $product_data['product_options'][$i][$language_id]['subtract'] = $import_data['option_subtract'];
        $product_data['product_options'][$i][$language_id]['required'] = $import_data['option_required'];


        if(isset($product_data['product_options'][$i][$import_data['global_language_id']]['quantity'])){
          $product_data['product_options'][$i][$language_id]['quantity'] = (int)$product_data['product_options'][$i][$import_data['global_language_id']]['quantity'];
        }else{
          $product_data['product_options'][$i][$language_id]['quantity'] = (int)$import_data['option_quantity'];
        }





      }
      $i++;
    }
  }
  
  






//default values
  $product_data['tax_class_id'] = $import_data['tax_class_id'];
  //$product_data['status']       = (int)$import_data['product_status'];



  if(isset($product_data[$import_data['primary_key']])){
    $product_exists = $this->productExists($import_data['primary_key'],$product_data[$import_data['primary_key']],$import_data['import_id']);
  }else{
    $product_exists = false;
  }

  if($product_data['category_only']!=0){
      $product_exists =  $this->categoryExists($product_data['category_key_id'][0]);
  }


  if($product_exists == false){
      if ($product_data['product_only_old_update'] != 1) {
          $this->insertProduct($product_data,$import_data);
          $this->logCacheStats('insert');
      }
  }else{
  // print_r($product_data);
    $this->updateProduct($product_exists,$product_data,$import_data); //$product_exists = product_id
   $this->logCacheStats('update');
  }





/*
  $import_data values:
  download_image : 1/2
  
  if not in product_data get from import_data!:)
  stock_status_id
  tax_class_id
  manufacturer_id
  
  //new imported products
  product_status
  product_subtract
  product_quantity
  
  
  //then:
  old_product_action
  
  //update
  update_items
*/
}




public function getLastProductId(){
  $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product ORDER by product_id DESC LIMIT 1");
  return $query->row['product_id'];
}

public function getLanguages(){
  $query = $this->db->query("SELECT language_id,code,name FROM " . DB_PREFIX . "language ORDER by language_id ASC");
  return $query->rows;
}






public function makeCategoryPatch($category_id,$parent_id){
	$level = 0;
  $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$parent_id . "' ORDER BY `level` ASC");
  foreach($query->rows as $result){	
  	$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");
		$level++;
	}
	$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', `level` = '" . (int)$level . "'");
  return true;
}


//дурнокод, нужно переделать рекурсивно
public function updateCategoryPatch(){
    $level = 0;
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category` ORDER BY `category_id` ASC");
    foreach($query->rows as $categoryItemsFirst){
        $queryShow = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$categoryItemsFirst['category_id'] . "' AND path_id =  '" . (int)$categoryItemsFirst['category_id'] . "'");
        $category_exists = $queryShow->row;
        if(!$category_exists){
            $this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$categoryItemsFirst['category_id'] . "', `path_id` = '" . (int)$categoryItemsFirst['category_id'] . "', `level` = '" . (int)$level . "'");
            if($categoryItemsFirst['parent_id']!=0){
                /*2 уровень вложенности*/
                $this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$categoryItemsFirst['category_id'] . "', `path_id` = '" . (int)$categoryItemsFirst['parent_id'] . "', `level` = '" . (int)$level . "'");
                $level++;
                $this->db->query("UPDATE ".DB_PREFIX."category_path SET `level` = '".$level."' WHERE category_id = '". (int)$categoryItemsFirst['category_id'] ."' AND path_id = '" . (int)$categoryItemsFirst['category_id'] . "'");
                /*3 уровень вложености*/
                $queryCategorySub = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category` WHERE category_id = '" . (int)$categoryItemsFirst['parent_id'] . "'");
                if($queryCategorySub->row['parent_id']!=0){
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$categoryItemsFirst['category_id'] . "', `path_id` = '" . (int)$queryCategorySub->row['parent_id'] . "', `level` = 0");
                    $this->db->query("UPDATE ".DB_PREFIX."category_path SET `level` = '1' WHERE category_id = '". (int)$categoryItemsFirst['category_id'] ."' AND path_id = '" . (int)$categoryItemsFirst['parent_id'] . "'");
                    $this->db->query("UPDATE ".DB_PREFIX."category_path SET `level` = '2' WHERE category_id = '". (int)$categoryItemsFirst['category_id'] ."' AND path_id = '" . (int)$categoryItemsFirst['category_id'] . "'");
                    /*4 уровень вложености*/
                    $queryCategorySubSub = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category` WHERE category_id = '" . (int)$queryCategorySub->row['parent_id'] . "'");
                    if($queryCategorySubSub->row['parent_id']!=0){
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$categoryItemsFirst['category_id'] . "', `path_id` = '" . (int)$queryCategorySubSub->row['parent_id'] . "', `level` = 0");
                        $this->db->query("UPDATE ".DB_PREFIX."category_path SET `level` = '1' WHERE category_id = '". (int)$categoryItemsFirst['category_id'] ."' AND path_id = '" . (int)$queryCategorySub->row['parent_id'] . "'");
                        $this->db->query("UPDATE ".DB_PREFIX."category_path SET `level` = '2' WHERE category_id = '". (int)$categoryItemsFirst['category_id'] ."' AND path_id = '" . (int)$categoryItemsFirst['parent_id'] . "'");
                        $this->db->query("UPDATE ".DB_PREFIX."category_path SET `level` = '3' WHERE category_id = '". (int)$categoryItemsFirst['category_id'] ."' AND path_id = '" . (int)$categoryItemsFirst['category_id'] . "'");
                        /*5 уровень вложености*/
                        $queryCategorySubSubSub = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category` WHERE category_id = '" . (int)$queryCategorySubSub->row['parent_id'] . "'");
                        if($queryCategorySubSubSub->row['parent_id']!=0){
                            $this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$categoryItemsFirst['category_id'] . "', `path_id` = '" . (int)$queryCategorySubSubSub->row['parent_id'] . "', `level` = 0");
                            $this->db->query("UPDATE ".DB_PREFIX."category_path SET `level` = '1' WHERE category_id = '". (int)$categoryItemsFirst['category_id'] ."' AND path_id = '" . (int)$queryCategorySubSub->row['parent_id'] . "'");
                            $this->db->query("UPDATE ".DB_PREFIX."category_path SET `level` = '2' WHERE category_id = '". (int)$categoryItemsFirst['category_id'] ."' AND path_id = '" . (int)$queryCategorySub->row['parent_id'] . "'");
                            $this->db->query("UPDATE ".DB_PREFIX."category_path SET `level` = '3' WHERE category_id = '". (int)$categoryItemsFirst['category_id'] ."' AND path_id = '" . (int)$categoryItemsFirst['parent_id'] . "'");
                            $this->db->query("UPDATE ".DB_PREFIX."category_path SET `level` = '4' WHERE category_id = '". (int)$categoryItemsFirst['category_id'] ."' AND path_id = '" . (int)$categoryItemsFirst['category_id'] . "'");
                        }
                    }
                }
            }
        }
    }
    return true;
}


public function getOrUpdateCategory($category,$parent,$idCat){
    $parent_id           = $parent;
     foreach ($category as $lang => $category_name){
         $query = $this->db->query("SELECT c.*,cd.* FROM ".DB_PREFIX."category_description AS cd INNER JOIN ".DB_PREFIX."category AS c ON cd.category_id=c.category_id WHERE c.category_id = '".(int)$idCat."' /*AND parent_id = '".$parent_id."'*/ LIMIT 1");
         $category_exists = $query->row;
         if($category_exists){
             $this->db->query("UPDATE ".DB_PREFIX."category SET `parent_id` = '".$this->db->escape($parent_id)."' WHERE category_id = '".(int)$idCat."'");
             $this->db->query("UPDATE ".DB_PREFIX."category_description SET `name` = '".$this->db->escape($category_name[0])."' WHERE category_id = '".(int)$idCat."' AND language_id = '".(int)$lang."'");
         }
     }
}

public function getOrInsertCategory($category,$import_data,$parent=0,$idCat=false){

  $separators = array(
    1  => ' | ',
    2  => '|',
    3  => ' > ',
    4  => '>',
    5  => ' ; ',
    6  => ';',
    7  => ' , ',
    8  => ',',
    9  => ' _ ',
    10 => '_'
  );
      
  $categories_array    = array();
  $separator           = $separators[(int)$import_data['category_separator']];

  $separated_category  = true; // Category 1 | Category 2 | Category 1 = true
  $primary_language_id = (int)$import_data['global_language_id'];
  $parent_id           = $parent;

  $category_primary_language = $category[$primary_language_id];
  $categories = explode($separator,$category_primary_language);

//isn't separated
  if(count($categories) == 1){
    $categories = array($category_primary_language);
    $separated_category = false;
  }
  
//insert this categories for primary language and get category_ids:  
	  foreach($categories as $category_name){
      $query = $this->db->query("SELECT c.*,cd.* FROM ".DB_PREFIX."category_description AS cd INNER JOIN ".DB_PREFIX."category AS c ON cd.category_id=c.category_id WHERE name = '".$this->db->escape($category_name)."' AND parent_id = '".$parent_id."' LIMIT 1");
      $category_exists = $query->row;
	    if(!$category_exists){
	        if($idCat   !=  false){
                $this->db->query("INSERT INTO " . DB_PREFIX . "category (category_id,parent_id,status,date_added) VALUES('" . (int)$idCat . "','" . (int)$parent_id . "','1',NOW())");
                $this->db->query("INSERT INTO ".DB_PREFIX."category_description (category_id,language_id,name) VALUES('".(int)$idCat."','".(int)$primary_language_id."','".$this->db->escape($category_name)."')");
                if($import_data['store_id']){
                    foreach($import_data['store_id'] as $store_id){
                        $this->db->query("INSERT INTO ".DB_PREFIX."category_to_store (category_id,store_id) VALUES('".(int)$idCat."','".(int)$store_id."')");
                    }
                }
                if(VERSION >= '1.5.5'){
                    $this->makeCategoryPatch($idCat,$parent_id);
                }
                $this_category_id = $idCat;
            }else {
                $this->db->query("INSERT INTO " . DB_PREFIX . "category (parent_id,status,date_added) VALUES('" . (int)$parent_id . "','1',NOW())");
                $this_category_query = $this->db->query("SELECT category_id FROM ".DB_PREFIX."category ORDER by category_id DESC LIMIT 1");
                $this_category_id = $this_category_query->row['category_id'];
                $this->db->query("INSERT INTO ".DB_PREFIX."category_description (category_id,language_id,name) VALUES('".(int)$this_category_id."','".(int)$primary_language_id."','".$this->db->escape($category_name)."')");
                if($import_data['store_id']){
                    foreach($import_data['store_id'] as $store_id){
                        $this->db->query("INSERT INTO ".DB_PREFIX."category_to_store (category_id,store_id) VALUES('".(int)$this_category_id."','".(int)$store_id."')");
                    }
                }
                if(VERSION >= '1.5.5'){
                    $this->makeCategoryPatch($this_category_id,$parent_id);
                }
            }
      }else{
        $this_category_id = $category_exists['category_id'];
      }
       $categories_array[] = $this_category_id;
	     $parent_id = $this_category_id;
	     $return_category_id[] = $this_category_id;
	  }
	  
	  
//set categories for another language descriptions:
  foreach($category as $language_id => $category_name){
    if($separated_category){$categories = explode($separator,$category_name);}
    else{$categories = array($category_name);}
    $i = 0;
    foreach($categories as $category_name){
      $category_id = $categories_array[$i];
    //url alias
      if($language_id == $primary_language_id){
        $seo_keyword = $import_data['seo_keyword_category'];
        if($seo_keyword != ""){
          $url_query = 'category_id='.$category_id;
          if($seo_keyword == "id-name"){
            $url_keyword = $category_id."-".$category_name;
          }
          if($seo_keyword == "name"){
            $url_keyword = $category_name;
          }
          $this->insertUrlAlias($url_query,$url_keyword);
        }
      }
      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_description WHERE category_id = '".(int)$category_id."' AND language_id = '".(int)$language_id."'");
      if(!$query->row){
        $this->db->query("INSERT INTO ".DB_PREFIX."category_description (category_id,language_id,name) VALUES('".(int)$category_id."','".(int)$language_id."','".$this->db->escape($category_name)."')");
      }
      $i++;
    }
  }
  
  return $categories_array;
}



public function insertUrlAlias($url_query,$url_keyword, $url=false){
  $url_keyword = ($url==true) ? $url_keyword : $this->createSEOKeyword($url_keyword);
  $query = $this->db->query("SELECT * FROM ".DB_PREFIX."url_alias WHERE `query` = '".$this->db->escape($url_query)."' LIMIT 1");
  if(!$query->row){
    $this->db->query("INSERT INTO ".DB_PREFIX."url_alias SET `query` = '".$this->db->escape($url_query)."', `keyword` = '".$this->db->escape($url_keyword)."'");
  }else{
    $this->db->query("UPDATE ".DB_PREFIX."url_alias SET `keyword` = '".$this->db->escape($url_keyword)."' WHERE url_alias_id = '".(int)$query->row['url_alias_id']."'");
  }
  return true;
}


public function getOrInsertManufacturerByName($manufacturer_name,$store_id,$import_data){
  $query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "manufacturer WHERE name = '".$this->db->escape($manufacturer_name)."'");
  if(!$query->row){
    $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '".$this->db->escape($manufacturer_name)."'");
    $query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "manufacturer ORDER by manufacturer_id DESC LIMIT 1");
    $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '".(int)$query->row['manufacturer_id']."', store_id = '".(int)$store_id."'");
    foreach(['1','3'] as $lang){
        $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_description SET manufacturer_id = '".(int)$query->row['manufacturer_id']."', 	language_id = '".(int)$lang."'");
    }
    $manufacturer_id = $query->row['manufacturer_id'];
  }else{
    $manufacturer_id = $query->row['manufacturer_id'];
  }
//url alias:
  $seo_keyword = $import_data['seo_keyword_manufacturer'];
  if($seo_keyword != ""){
    $url_query = 'manufacturer_id='.$manufacturer_id;
    if($seo_keyword == "id-name"){
      $url_keyword = $manufacturer_id."-".$manufacturer_name;
    }
    if($seo_keyword == "name"){
      $url_keyword = $manufacturer_name;
    }
    $this->insertUrlAlias($url_query,$url_keyword);
  }
  return $manufacturer_id;
}





public function getOptionIdFromOptionValueId($option_value_id){
  $query = $this->db->query("SELECT option_id FROM " . DB_PREFIX . "option_value WHERE option_value_id = '".(int)$option_value_id."'");
  if(!$query->row){
    return $query->row['option_id'];
  }else{
    return $query->row['option_id'];
  }
}








    public function getOrInsertAttributeId($attribute){


        $attribute_group_id = false;
        $attribute_id       = false;
        foreach($attribute as $language_id => $attribute_data){


//attribute group:
            if(!$attribute_group_id){
                $query = $this->db->query("SELECT attribute_group_id FROM " . DB_PREFIX . "attribute_group_description WHERE language_id = '".(int)$language_id."' AND name = '".$this->db->escape($attribute_data['group'])."'");
                if(!$query->row){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group SET sort_order = '0'");
                    $query = $this->db->query("SELECT attribute_group_id FROM " . DB_PREFIX . "attribute_group ORDER by attribute_group_id DESC LIMIT 1");
                    $attribute_group_id = $query->row['attribute_group_id'];
                    $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description SET attribute_group_id = '".(int)$attribute_group_id."', language_id = '".$language_id."', name = '".$this->db->escape($attribute_data['group'])."'");
                }else{
                    $attribute_group_id = $query->row['attribute_group_id'];
                }
            }else{
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_group_description WHERE language_id = '".(int)$language_id."' AND attribute_group_id = '".(int)$attribute_group_id."'");
                if(!$query->row){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description SET attribute_group_id = '".(int)$attribute_group_id."', language_id = '".$language_id."', name = '".$this->db->escape($attribute_data['group'])."'");
                }
            }


//attribute:
            if(!$attribute_id){
                $query = $this->db->query("SELECT a.attribute_id, ad.name FROM " . DB_PREFIX . "attribute AS a LEFT JOIN " . DB_PREFIX . "attribute_description AS ad ON a.attribute_id = ad.attribute_id WHERE language_id = '".(int)$language_id."' AND attribute_group_id = '".(int)$attribute_group_id."' AND name = '".$this->db->escape($attribute_data['name'])."'");
                if(!$query->row){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET attribute_group_id = '".(int)$attribute_group_id."'");
                    $query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "attribute ORDER by attribute_id DESC LIMIT 1");
                    $attribute_id = $query->row['attribute_id'];
                    $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '".(int)$attribute_id."', language_id = '".(int)$language_id."', name = '".$this->db->escape($attribute_data['name'])."'");
                }else{
                    $attribute_id = $query->row['attribute_id'];
                }
            }else{
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '".(int)$attribute_id."' AND language_id = '".(int)$language_id."'");
                if(!$query->row){
                    $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '".(int)$attribute_id."', language_id = '".(int)$language_id."', name = '".$this->db->escape($attribute_data['name'])."'");
                }
            }

        }


        return $attribute_id;
    }







public function getOrInsertOptionId($option){

$option_id       = false;
$option_value_id = false;
foreach($option as $language_id => $option_data){


//option:
if(!$option_id){
  $query = $this->db->query("SELECT option_id FROM " . DB_PREFIX . "option_description WHERE language_id = '".(int)$language_id."' AND name = '".$this->db->escape($option_data['name'])."'");
  if(!$query->row){
    $this->db->query("INSERT INTO `" . DB_PREFIX . "option` SET type = '".$option_data['type']."'");
    $query = $this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option` ORDER by option_id DESC LIMIT 1");
    $option_id = $query->row['option_id'];
    $this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id = '".(int)$option_id."', language_id = '".$language_id."', name = '".$this->db->escape($option_data['name'])."'");
  }else{
    $option_id = $query->row['option_id'];
  }
}else{
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_description WHERE language_id = '".(int)$language_id."' AND option_id = '".(int)$option_id."'");
  if(!$query->row){
    $this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id = '".(int)$option_id."', language_id = '".$language_id."', name = '".$this->db->escape($option_data['name'])."'");
  }
}
  
  
//value:
if(!$option_value_id){
  $query = $this->db->query("SELECT ov.option_id, ov.option_value_id, od.name FROM " . DB_PREFIX . "option_value AS ov LEFT JOIN " . DB_PREFIX . "option_value_description AS od ON ov.option_value_id = od.option_value_id WHERE od.language_id = '".(int)$language_id."' AND ov.option_id = '".(int)$option_id."' AND od.name = '".$this->db->escape($option_data['value'])."'");
  if(!$query->row){
    $this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '".(int)$option_id."'");
    $query = $this->db->query("SELECT option_value_id FROM " . DB_PREFIX . "option_value ORDER by option_value_id DESC LIMIT 1");
    $option_value_id = $query->row['option_value_id'];
    $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '".(int)$option_value_id."', option_id = '".(int)$option_id."', language_id = '".(int)$language_id."', name = '".$this->db->escape($option_data['value'])."'");
  }else{
    $option_value_id = $query->row['option_value_id'];
  }
}else{
  $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '".(int)$option_value_id."' AND language_id = '".(int)$language_id."' AND name = '".$this->db->escape($option_data['value'])."'");
  if(!$query->row){
    $query_1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '".(int)$option_value_id."' AND language_id = '".(int)$language_id."'");
    if(!$query_1->row){
      $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '".(int)$option_value_id."', option_id = '".(int)$option_id."', language_id = '".(int)$language_id."', name = '".$this->db->escape($option_data['value'])."'");
    }
  }
}

}


return $option_value_id;
}



public function save_image($inPath,$outPath)
{ //Download images from remote server
    $in=    fopen($inPath, "rb");
    $out=   @fopen($outPath, "wb");
    while ($chunk = fread($in,8192))
    {
        @fwrite($out, $chunk, 8192);
    }
    fclose($in);
    @fclose($out);
}



public function getImage($image_url,$import_data){

  $image_url = trim($image_url);
  $image_url = str_replace(" ","%20",$image_url);
  $image_name = explode("/",$image_url);
  $image_name = $image_name[count($image_name)-1];
  $image_name = md5(time().rand(11111,99999)).'.jpg';
  $image_name = trim($image_name);
  $image_file = $import_data['image_dir'].$image_name;
  
  
  if($import_data['download_image'] == 1){
    if(!file_exists($image_file)){

      $ch = curl_init();
	  $ports = array();
	  

	$image_url = str_replace(' ', '%20', $image_url);
	if (preg_match('/:(\d+)/', $image_url, $ports)) {
      $image_url = preg_replace('/:\d+/', '', $image_url);
     curl_setopt($ch, CURLOPT_PORT, (int)$ports[1]);
    }
	  
	  
      curl_setopt($ch, CURLOPT_URL, $image_url);
	  if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off') {
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
	  }
      
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      $image_content = curl_exec($ch);
  
      if($image_content == false){
        $image_content = file_get_contents($image_url);
      }
      file_put_contents($image_file, $image_content);

    }
  }
  
  $this->save_image($image_url,"/catalog/feed_".$import_data['import_id']."/".$image_name);
  
  return "/catalog/feed_".$import_data['import_id']."/".$image_name;
}


    public function updateProduct($product_id, $product_data, $import_data)
    {

        if ($product_data['category_only'] != 0) {
            if ($product_data['category']) {
                foreach ($product_data['category'] as $key => $category_detail) {
                    $this->getOrUpdateCategory($category_detail, $product_data['category_parent'][0], $product_data['category_key_id'][0]);
                }
                //$this->updateCategoryPatch();
            }
        } else {

            if (in_array("category_id", $import_data['update_items'])) {
                foreach ($product_data['category_id'] as $category_id) {
                    $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "', main_category = 1");
                }
            }

            //insert product to all parent category, 5 subcategory level
            if ($product_data['product_in_parent_category'] != 0) {
                foreach ($product_data['category_id'] as $category_id) {
                    $first = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
                    if($first->row['parent_id']){
                        $firstExist = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$first->row['parent_id'] . "'");
                        if(!$firstExist->row){
                            $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$first->row['parent_id'] . "'");
                        }
                        $second = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$first->row['parent_id'] . "'");
                        if($second->row['parent_id']){
                            $secondExist = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$second->row['parent_id'] . "'");
                            if(!$secondExist->row) {
                                $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$second->row['parent_id'] . "'");
                            }
                            $third = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$second->row['parent_id'] . "'");
                            if($third->row['parent_id']){
                                $thirdExist = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$third->row['parent_id'] . "'");
                                if(!$thirdExist->row) {
                                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$third->row['parent_id'] . "'");
                                }
                                $four = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$third->row['parent_id'] . "'");
                                if($four->row['parent_id']){
                                    $fourExist = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$four->row['parent_id'] . "'");
                                    if(!$fourExist->row) {
                                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$four->row['parent_id'] . "'");
                                    }
                                    $five = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$four->row['parent_id'] . "'");
                                    if($five->row['parent_id']){
                                        $fiveExist = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$five->row['parent_id'] . "'");
                                        if(!$fiveExist->row) {
                                            $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$five->row['parent_id'] . "'");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }



            if (!$import_data['update_items']) {
                $import_data['update_items'] = array();
            }

            $additional_product_sql = "";


            if (in_array("status", $import_data['update_items'])) {
                $additional_product_sql .= ", status = '" . (int)$product_data['status'] . "'";
            }


            if (in_array("quantity", $import_data['update_items'])) {
                $additional_product_sql .= ", quantity = '" . (int)$product_data['quantity'] . "'";
            }
            if (in_array("price", $import_data['update_items'])) {
                $additional_product_sql .= ", price = '" . (float)$product_data['price'] . "', tax_class_id = '" . (int)$product_data['tax_class_id'] . "'";

//special price
                $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
                if (isset($product_data['special']) AND (float)$product_data['special'] != 0) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$import_data['special_price_customer_group_id'] . "', priority = '1', price = '" . (float)$product_data['special'] . "'");
                }

            }
            if (in_array("manufacturer", $import_data['update_items'])) {
                $additional_product_sql .= ", manufacturer_id = '" . (int)$product_data['manufacturer_id'] . "'";
            }


            $this->db->query("UPDATE " . DB_PREFIX . "product SET shipping = '" . (int)$import_data['product_shipping'] . "', import_active_product = '1'" . $additional_product_sql . " WHERE product_id = '" . (int)$product_id . "'");


            if (in_array("description", $import_data['update_items'])) {
                foreach ($product_data['product_descriptions'] as $language_id => $product_description) {
                    $exists = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'");

                    $sql_opencart_version = "";
                    if (VERSION >= '1.5.4') {
                        $sql_opencart_version = ", tag = '" . $this->db->escape($product_description['tag']) . "'";
                    }

                    if ($exists->row) {
                        if ($product_description['name'] != false && $product_description['description'] != false) {
                            $this->db->query("UPDATE " . DB_PREFIX . "product_description SET name = '" . $this->db->escape($product_description['name']) . "', description = '" . $this->db->escape($product_description['description']) . "', meta_description = '" . $this->db->escape($product_description['meta_description']) . "', meta_keyword = '" . $this->db->escape($product_description['meta_keyword']) . "', meta_title = '" . $this->db->escape($product_description['name']) . "'" . $sql_opencart_version . " WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'");
                        }
                    } else {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($product_description['name']) . "', description = '" . $this->db->escape($product_description['description']) . "', meta_description = '" . $this->db->escape($product_description['meta_description']) . "', meta_keyword = '" . $this->db->escape($product_description['meta_keyword']) . "', meta_title = '" . $this->db->escape($product_description['name']) . "'" . $sql_opencart_version);
                    }
                    //url alias
                    if ($language_id == $import_data['global_language_id']) {
                        $seo_keyword = $import_data['seo_keyword_product'];
                        if ($seo_keyword != "") {
                            $url_query = 'product_id=' . $product_id;
                            if ($seo_keyword == "id-name") {
                                $url_keyword = $product_id . "-" . $product_description['name'];
                            }
                            if ($seo_keyword == "name") {
                                $url_keyword = $product_description['name'];
                            }
                            $this->insertUrlAlias($url_query, $url_keyword);
                        }
                    }
                }
            }

//работа с массивом атрибутов
            if (in_array("attribute", $import_data['update_items'])) {
                //print_r($product_data['product_attributes']);
                //удаляем все старые связи атрибута, товара и языка
                foreach ($product_data['product_attributes'] as $attribute) {
                    $attribute_id = $this->getOrInsertAttributeId($attribute);
                    foreach ($attribute as $language_id => $attribute_data) {
                        if (!empty($attribute_data['text'])) {
                            $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$attribute_id . "' AND language_id = '" . (int)$language_id . "'");
                        }
                    }
                }
                $previousText = '';//если значений атрибутов несолько, добавляем его в буфер и потом записываем все через запятую
                foreach ($product_data['product_attributes'] as $attribute) {
                    $attribute_id = $this->getOrInsertAttributeId($attribute, $product_data['category_id']);
                    foreach ($attribute as $language_id => $attribute_data) {
                        $extra =(!empty($attribute_data['extra'])) ? ' ['.$attribute_data['extra'].']' : '';
                        if (!empty($attribute_data['text'])) {
                            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$attribute_id . "' AND language_id = '" . (int)$language_id . "'");
                            if ($query->row) {
                                $atrText = ($previousText != $attribute_data['text']) ? $query->row['text'] . ', ' . $attribute_data['text'] : $attribute_data['text'];
                                $atrText =  str_replace($extra, "", $atrText);
                                if(!empty($attribute_data['format'])) {// если есть форматирование атрибута тогда делаем его
                                    $atrText = sprintf($attribute_data['format'], $atrText, $atrText);
                                }
                                $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$attribute_id . "' AND language_id = '" . (int)$language_id . "'");
                                $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "',  text = '" . $this->db->escape($atrText) . "'");
                                $previousText = $attribute_data['text'];
                            } else {
                                if(!empty($attribute_data['type'])) {// если тип атрибута bool и текст =1 тогда пишем плюс
                                    $atrText = ($attribute_data['type'] == 'TYPE_BOOLEAN' && $attribute_data['text'] == 1) ? '+' : $attribute_data['text'].$extra;
                                }else{
                                    $atrText = $attribute_data['text'].$extra;
                                }
                                if(!empty($attribute_data['format'])) {// если есть форматирование атрибута тогда делаем его
                                    $atrText = sprintf($attribute_data['format'], $atrText, $atrText);
                                }
                                $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "',  text = '" . $this->db->escape($atrText) . "'");
                                $previousText = $attribute_data['text'];
                            }
                        }
                    }
                }
            }


            if (in_array("option", $import_data['update_items'])) {

                $this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
                $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");


                foreach ($product_data['product_options'] as $option) {
                    $option_value_id = $this->getOrInsertOptionId($option);
                    $option_id = $this->getOptionIdFromOptionValueId($option_value_id);

                    //product_option_id
                    $query = $this->db->query("SELECT product_option_id FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "' AND option_id = '" . (int)$option_id . "'");
                    if (!$query->row) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', required = '" . (int)$import_data['option_required'] . "'");
                        $query = $this->db->query("SELECT product_option_id FROM " . DB_PREFIX . "product_option ORDER by product_option_id DESC LIMIT 1");
                        $product_option_id = $query->row['product_option_id'];
                    } else {
                        $product_option_id = $query->row['product_option_id'];
                    }


                    //product_option_value_id
                    $this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "' AND product_option_id = '" . (int)$product_option_id . "' AND option_id = '" . (int)$option_id . "' AND option_value_id = '" . (int)$option_value_id . "'");
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', option_value_id = '" . (int)$option_value_id . "', subtract = '" . (int)$import_data['option_subtract'] . "', price = '" . (float)$option[$import_data['global_language_id']]['price'] . "', quantity = '" . (int)$option[$import_data['global_language_id']]['quantity'] . "'");
                }
            }


            if (in_array("category", $import_data['update_items'])) {
                foreach ($product_data['category'] as $category_detail) {
                    $categories = $this->getOrInsertCategory($category_detail, $import_data);
                    $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
                    foreach ($categories as $category_id) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
                    }
                }
            }





            if (in_array("image", $import_data['update_items'])) {
                if ($product_data['main_image']) {
                    $image = $this->getImage($product_data['main_image'], $import_data);
                    $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($image) . "' WHERE product_id = '" . (int)$product_id . "'");
                }
                $images = array();
                if ($product_data['images']) {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image  WHERE product_id = '" . (int)$product_id . "'");
                    if(!$query->row){
                        $i = 0;
                        foreach ($product_data['images'] as $image_url) {
                            if($i<=2) {
                                //echo 'загружаем рисунок - '.$image_url;
                                $image = $this->getImage($image_url, $import_data);
                                if (!$product_data['main_image'] AND $i == 0) {
                                    echo 'update' . $image . '<br>';
                                    $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($image) . "' WHERE product_id = '" . (int)$product_id . "'");
                                } else {
                                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($image) . "'");
                                }
                                $i++;
                            }
                        }
                    }
                    //$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
                }
            }

        }
    }


public function insertProduct($product_data,$import_data){
  $import_id = $import_data['import_id'];

  if($product_data['category_only']!=0){
      if ($product_data['category']) {
          foreach ($product_data['category'] as $key => $category_detail) {
             $this->getOrInsertCategory($category_detail, $import_data, $product_data['category_parent'][0], $product_data['category_key_id'][0] );
          }
      }
  }else {

      $sql_opencart_version = "";
      if (VERSION >= '1.5.4') {
          $sql_opencart_version = " status = '" . $this->db->escape($product_data['status']) . "', jan = '" . $this->db->escape($product_data['jan']) . "', isbn = '" . $this->db->escape($product_data['isbn']) . "', mpn = '" . $this->db->escape($product_data['mpn']) . "', ";
      }

      $this->db->query("INSERT INTO " . DB_PREFIX . "product SET import_active_product = '1', import_id = '" . (int)$import_id . "', model = '" . $this->db->escape($product_data['model']) . "', sku = '" . $this->db->escape($product_data['sku']) . "', upc = '" . $this->db->escape($product_data['upc']) . "'," . $sql_opencart_version . " location = '" . $this->db->escape($product_data['location']) . "', quantity = '" . (int)$product_data['quantity'] . "', stock_status_id = '" . (int)$product_data['stock_status_id'] . "', manufacturer_id = '" . (int)$product_data['manufacturer_id'] . "', shipping = '" . (int)$import_data['product_shipping'] . "', price = '" . (float)$product_data['price'] . "', points = '" . (int)$product_data['points'] . "', tax_class_id = '" . (int)$product_data['tax_class_id'] . "', date_available = '" . $this->db->escape($product_data['date_available']) . "', weight = '" . (float)$product_data['weight'] . "', weight_class_id = '" . (int)$product_data['weight_class_id'] . "', length = '" . (float)$product_data['length'] . "', width = '" . (float)$product_data['width'] . "', height = '" . (float)$product_data['height'] . "', length_class_id = '" . (float)$product_data['length_class_id'] . "', subtract = '" . (int)$product_data['subtract'] . "', minimum = '" . (int)$product_data['minimum'] . "', sort_order = '" . (int)$product_data['sort_order'] . "', date_added = NOW(), date_modified = NOW(), viewed = '" . (int)$product_data['viewed'] . "'");

      $product_id = $this->getLastProductId();

//insert feed_product_id
      if (isset($product_data['product_id']) AND (int)$product_data['product_id'] != 0) {
          $this->db->query("UPDATE " . DB_PREFIX . "product SET feed_product_id = '" . (int)$product_data['product_id'] . "' WHERE product_id = '" . (int)$product_id . "'");
      }

      if (isset($product_data['special']) AND (float)$product_data['special'] != 0) {
          $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$import_data['special_price_customer_group_id'] . "', priority = '1', price = '" . (float)$product_data['special'] . "'");
      }

//----------------------------------------------------------------------- images
      if ($product_data['main_image']) {
          $image = $this->getImage($product_data['main_image'], $import_data);
          $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($image) . "' WHERE product_id = '" . (int)$product_id . "'");
      }
      $images = array();
      if ($product_data['images']) {
          $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
          $i = 0;
          foreach ($product_data['images'] as $image_url) {
              $image = $this->getImage($image_url, $import_data);
              if (!$product_data['main_image'] AND $i == 0) {
                  $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($image) . "' WHERE product_id = '" . (int)$product_id . "'");
              } else {
                  $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($image) . "'");
              }
              $i++;
          }
      }

//----------------------------------------------------------------------- stores
      if ($import_data['store_id']) {
          foreach ($import_data['store_id'] as $store_id) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
          }
      }

//------------------------------------------------------------------ description
      foreach ($product_data['product_descriptions'] as $language_id => $product_description) {

          $sql_opencart_version = "";
          if (VERSION >= '1.5.4') {
              $sql_opencart_version = ", tag = '" . $this->db->escape($product_description['tag']) . "'";
          }

          $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($product_description['name']) . "', description = '" . $this->db->escape($product_description['description']) . "', meta_description = '" . $this->db->escape($product_description['meta_description']) . "', meta_keyword = '" . $this->db->escape($product_description['meta_keyword']) . "', meta_title = '" . $this->db->escape($product_description['name']) . "'" . $sql_opencart_version);

          //url alias
          if ($language_id == $import_data['global_language_id']) {
              $seo_keyword = $import_data['seo_keyword_product'];
              if ($seo_keyword != "") {
                  $url_query = 'product_id=' . $product_id;
                  if ($seo_keyword == "id-name") {
                      $url_keyword = $product_id . "-" . $product_description['name'];
                  }
                  if ($seo_keyword == "name") {
                      $url_keyword = $product_description['name'];
                  }
                  $this->insertUrlAlias($url_query, $url_keyword);
              }
          }
      }

//------------------------------------------------------------------- Product URL
      if ($product_data['product_url']) {
          foreach ($product_data['product_url'] as $product_url) {
              $url_query = 'product_id=' . $product_id;
              $this->insertUrlAlias($url_query,$product_url, true);
              //$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $product_url . "'");
          }
      }
//------------------------------------------------------------------- categoryID
      if ($product_data['category_id']) {
          foreach ($product_data['category_id'] as $category_id) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
          }
      }
//------------------------------------------------------------------- categories
      if ($product_data['category']) {
          foreach ($product_data['category'] as $category_detail) {
              $categories = $this->getOrInsertCategory($category_detail, $import_data);
              foreach ($categories as $category_id) {
                  $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
              }
          }
      }
      //insert product to all parent category, 5 subcategory level
      if ($product_data['product_in_parent_category'] != 0) {
          foreach ($product_data['category_id'] as $category_id) {
              $first = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");
              if($first->row['parent_id']){
                  $firstExist = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$first->row['parent_id'] . "'");
                  if(!$firstExist->row){
                      $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$first->row['parent_id'] . "'");
                  }
                  $second = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$first->row['parent_id'] . "'");
                  if($second->row['parent_id']){
                      $secondExist = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$second->row['parent_id'] . "'");
                      if(!$secondExist->row) {
                          $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$second->row['parent_id'] . "'");
                      }
                      $third = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$second->row['parent_id'] . "'");
                      if($third->row['parent_id']){
                          $thirdExist = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$third->row['parent_id'] . "'");
                          if(!$thirdExist->row) {
                              $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$third->row['parent_id'] . "'");
                          }
                          $four = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$third->row['parent_id'] . "'");
                          if($four->row['parent_id']){
                              $fourExist = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$four->row['parent_id'] . "'");
                              if(!$fourExist->row) {
                                  $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$four->row['parent_id'] . "'");
                              }
                              $five = $this->db->query("SELECT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$four->row['parent_id'] . "'");
                              if($five->row['parent_id']){
                                  $fiveExist = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$five->row['parent_id'] . "'");
                                  if(!$fiveExist->row) {
                                      $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$five->row['parent_id'] . "'");
                                  }
                              }
                          }
                      }
                  }
              }
          }
      }

//--------------------------------------------------------add attributes when insert
/*
          $previousText = '';//если значений атрибутов несолько, добавляем его в буфер и потом записываем все через запятую
          foreach ($product_data['product_attributes'] as $attribute) {
              $attribute_id = $this->getOrInsertAttributeId($attribute);
              foreach ($attribute as $language_id => $attribute_data) {
                  $extra =(!empty($attribute_data['extra'])) ? ' ['.$attribute_data['extra'].']' : '';
                  if (!empty($attribute_data['text'])) {
                      $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$attribute_id . "' AND language_id = '" . (int)$language_id . "'");
                      if ($query->row) {
                          $atrText = ($previousText != $attribute_data['text']) ? $query->row['text'] . ', ' . $attribute_data['text'] : $attribute_data['text'];
                          $atrText =  str_replace($extra, "", $atrText);
                          $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$attribute_id . "' AND language_id = '" . (int)$language_id . "'");
                          $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "',  text = '" . $this->db->escape($atrText) . "'");
                          $previousText = $attribute_data['text'];
                      } else {
                          if(!empty($attribute_data['type'])) {// если тип атрибута bool и текст =1 тогда пишем плюс
                              $atrText = ($attribute_data['type'] == 'TYPE_BOOLEAN' && $attribute_data['text'] == 1) ? '+' : $attribute_data['text'].$extra;
                          }else{
                              $atrText = $attribute_data['text'].$extra;
                          }
                          $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "',  text = '" . $this->db->escape($atrText) . "'");
                          $previousText = $attribute_data['text'];
                      }
                  }
              }
          }
          */
print_r($product_data['product_attributes']);
           foreach ($product_data['product_attributes'] as $attribute) {
           $attribute_id = $this->getOrInsertAttributeId($attribute);
           foreach ($attribute as $language_id => $attribute_data) {
              $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$attribute_id . "' AND language_id = '" . (int)$language_id . "'");
              if (!$query->row) {
                  $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape($attribute_data['text']) . "'");
              }
          }
          }


//---------------------------------------------------------------------- options
      foreach ($product_data['product_options'] as $option) {
          $option_value_id = $this->getOrInsertOptionId($option);
          $option_id = $this->getOptionIdFromOptionValueId($option_value_id);

          //product_option_id
          $query = $this->db->query("SELECT product_option_id FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "' AND option_id = '" . (int)$option_id . "'");
          if (!$query->row) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', required = '" . (int)$import_data['option_required'] . "'");
              $query = $this->db->query("SELECT product_option_id FROM " . DB_PREFIX . "product_option ORDER by product_option_id DESC LIMIT 1");
              $product_option_id = $query->row['product_option_id'];
          } else {
              $product_option_id = $query->row['product_option_id'];
          }

          //product_option_value_id
          $query = $this->db->query("SELECT product_option_value_id FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "' AND product_option_id = '" . (int)$product_option_id . "' AND option_id = '" . (int)$option_id . "' AND option_value_id = '" . (int)$option_value_id . "'");
          if (!$query->row) {
              $this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$option_id . "', option_value_id = '" . (int)$option_value_id . "', subtract = '" . (int)$import_data['option_subtract'] . "', price = '" . (float)$option[$import_data['global_language_id']]['price'] . "', quantity = '" . (int)$option[$import_data['global_language_id']]['quantity'] . "'");
          }
      }

  }
}

















public function productExists($primary_key,$value,$import_id){
  if($primary_key == 'product_id'){$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE import_id = '".(int)$import_id."' AND feed_product_id = '".(int)$value."'");}
  if($primary_key == 'model'){$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE /*import_id = '".(int)$import_id."' AND*/ model = '".$this->db->escape($value)."'");}
  if($primary_key == 'sku'){$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE import_id = '".(int)$import_id."' AND sku = '".$this->db->escape($value)."'");}
  if($query->row){return $query->row['product_id'];}
  else{return false;}
}

public function categoryExists($catId){
 $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE category_id = '".(int)$catId."'");
 return ($query->row) ? true : false;
}









	public function createSEOKeyword($title,$options = array('transliterate')){


  // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    $title = mb_convert_encoding((string)$title, 'UTF-8', mb_list_encodings());
    $defaults = array(
    'delimiter' => '-',
    'limit' => null,
    'lowercase' => true,
    'replacements' => array(),
    'transliterate' => true,
    );
  
  // Merge options
    $options = array_merge($defaults, $options);
    $char_map = array(
  
  // Latin
    'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
    'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
    'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
    'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
    'ß' => 'ss',
    'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
    'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
    'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
    'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
    'ÿ' => 'y', 'ž' => 'z',
     
  // Latin symbols
    '©' => '(c)',
     
  // Greek
    'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
    'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
    'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
    'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
    'Ϋ' => 'Y',
    'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
    'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
    'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
    'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
    'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
     
  // Turkish
    'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
    'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
     
  // Russian
    'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
    'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
    'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
    'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
    'Я' => 'Ya',
    'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
    'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
    'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
    'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
    'я' => 'ya',
     
  // Ukrainian
    'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
    'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
     
  // Czech
    'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
    'Ž' => 'Z',
    'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
    'ž' => 'z',
     
  // Polish
    'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
    'Ż' => 'Z',
    'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
    'ż' => 'z',
     
  // Latvian
    'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
    'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
    'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
    'š' => 's', 'ū' => 'u', 'ž' => 'z',
    
  //special
    'Ľ' => 'L', 'ľ' => 'l',
    );
  
  // Make custom replacements
    $title = preg_replace(array_keys($options['replacements']), $options['replacements'], $title);
  
  // Transliterate characters to ASCII
    if ($options['transliterate']) {
      $title = str_replace(array_keys($char_map), $char_map, $title);
    }
  
  // Replace non-alphanumeric characters with our delimiter
    $title = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $title);
    
  // Remove duplicate delimiters
    $title = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $title);
    
  // Truncate slug to max. characters
    $title = mb_substr($title, 0, ($options['limit'] ? $options['limit'] : mb_strlen($title, 'UTF-8')), 'UTF-8');
  
  // Remove delimiter from ends
    $title = trim($title, $options['delimiter']);
   
    return $options['lowercase'] ? mb_strtolower($title, 'UTF-8') : $title;
}


}
?>