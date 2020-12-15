<?php echo $header; ?>

<div id="modal-import" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel"><?php echo $text_import_products; ?></h4>
				</div>
				<div class="modal-body">
					<div id="import-popup-progress">
						<div class="row">
							<span class="name"><?php echo $text_part; ?></span>
							<span class="value" id="import-stats-part"></span>
						</div>
						<div class="row">
							<span class="name"><?php echo $text_checked_products; ?></span>
							<span class="value" id="import-stats-checked-products"></span>
						</div>
						<div class="row">
							<span class="name"><?php echo $text_inserted_products; ?></span>
							<span class="value" id="import-stats-inserted-products"></span>
						</div>
						<div class="row">
							<span class="name"><?php echo $text_updated_products; ?></span>
							<span class="value" id="import-stats-updated-products"></span>
						</div>
						<div class="row">
							<span class="name"><?php echo $text_progress; ?></span>
							<span class="value" id="import-stats-progress"></span>
						</div>
						<div class="hr"></div>
						
						<p id="success-done"><?php echo $text_successfully_imported; ?></p>
						
						<div id="block-progress">
							<span id="progress-percentage"></span>
							<div id="progressbar"><div id="progress"></div></div>
						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn" data-dismiss="modal"><?php echo $text_close; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="modal-add-import" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel"><?php echo $text_new_import; ?></h4>
				</div>
				<div class="modal-body">
					<label for="import_name"><?php echo $text_import_name; ?></label><br>
					<input type="text" name="import_name" value="" />
				</div>
				<div class="modal-footer">
					<button type="button" class="btn" data-dismiss="modal"><?php echo $text_close; ?></button>
					<button type="submit" name="add_import" class="btn btn-success"><?php echo $text_add_import; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php echo $column_left; ?>

<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid" id="import">
		<?php if ($error_warning) { ?>
	    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
	      <button type="button" class="close" data-dismiss="alert">&times;</button>
	    </div>
	    <?php } ?>
	    <?php if ($success) { ?>
	    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
	      <button type="button" class="close" data-dismiss="alert">&times;</button>
	    </div>
	    <?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-download"></i> <?php echo $heading_title; ?></h3>
				
				<div class="heading-actions">
					<?php if ($imports){ ?>
						<label for="feed_type"><b><?php echo $text_select_import; ?>:</b></label>&nbsp;
						<select name="feed_type" id="feed_type" onChange="redirectToFeedSetting(this.value);">
							<option value="0"><?php echo $text_select; ?></option>
							<?php foreach($imports as $import){?>
							<option value="<?php echo $import['import_id'];?>"<?php if ($import_id == $import['import_id']){ echo ' selected="selected"'; } ?>><?php echo $import['name'];?></option>
							<?php } ?>
						</select>
					<?php } ?>
					<input type="button" class="btn btn-small<?php if (!isset($_GET['import_id'])){echo ' importButton';} ?>" id="addImport" value="<?php echo $text_add_new_import; ?>" data-toggle="modal" data-target="#modal-add-import" />
					<?php if (isset($_GET['import_id'])){ ?><input type="button" class="btn btn-small btn-success" onClick="$('#import-form').submit();" value="<?php echo $text_save_import; ?>" /><?php } ?>
				</div>
			</div>
			<div class="panel-body">
	 
		<?php if (isset($_GET['import_id'])){ ?>
			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data" id="import-form">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab-xml_setting" data-toggle="tab"><span class="tab-name"><?php echo $text_import_setting; ?></span><i><?php echo $text_import_setting_i; ?></i></a></li>
					<li><a href="#tab-xml_tag" data-toggle="tab"><span class="tab-name"><?php echo $text_tags_setting; ?></span><i><?php echo $text_tags_setting_i; ?></i></a></li>
					<li><a href="#tab-import" data-toggle="tab"><span class="tab-name"><?php echo $text_import_or_cron; ?></span><i><?php echo $text_import_or_cron_i; ?></i></a></li>
				</ul>
				<div class="clear"></div>
				<div class="tab-content">

				<div class="tab-pane active" id="tab-xml_setting">
					<div class="left">
					
						<div class="box-content">
							<table class="form">
								<tr>
									<td><label for="feed_url"><b><?php echo $text_xml_link; ?></b></label><br /><small><?php echo $text_xml_link_i; ?></small></td>
									<td>
										<input type="text" name="feed_data[xml_url]" id="feed_url" value="<?php if (isset($feed_data['xml_url'])){echo $feed_data['xml_url'];} ?>" />
										<input type="button" class="btn btn-info" id="download_xml" value="<?php echo $text_download; ?>" />
										<span id="xml_url_result"<?php if (isset($xml_info_icon)){echo ' class="icon-result-'.$xml_info_icon.'"';}?>></span>
										<span id="xml_url_result_text"><?php echo $xml_info_text; ?></span>
										<span class="help tooltip tooltip-download" title="<?php echo $text_tooltip_xml_link; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_download_image_1"><b><?php echo $text_download_images; ?></b></label><br /><small><?php echo $text_download_images_i; ?></small></td>
									<td>
										<input type="radio" name="feed_data[download_image]" id="feed_download_image_1" value="1"<?php if (isset($feed_data['download_image']) AND $feed_data['download_image'] == 1){echo ' checked="checked"';} ?> /> <label for="feed_download_image_1"><?php echo $text_yes; ?></label>
										<input type="radio" name="feed_data[download_image]" id="feed_download_image_0" value="0"<?php if (!isset($feed_data['download_image']) || $feed_data['download_image'] == 0){echo ' checked="checked"';} ?> /> <label for="feed_download_image_0"><?php echo $text_no; ?></label>
										<span class="help tooltip" title="<?php echo $text_tooltip_download_images; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_category_only"><b><?php echo $text_category_only; ?></b></label><br /><small><?php echo $text_category_only_i; ?></small></td>
									<td>
										<input type="radio" name="feed_data[category_only]" id="feed_category_only_1" value="1"<?php if (isset($feed_data['category_only']) AND $feed_data['category_only'] == 1){echo ' checked="checked"';} ?> /> <label for="feed_category_only_1"><?php echo $text_yes; ?></label>
										<input type="radio" name="feed_data[category_only]" id="feed_category_only_0" value="0"<?php if (!isset($feed_data['category_only']) || $feed_data['category_only'] == 0){echo ' checked="checked"';} ?> /> <label for="feed_category_only_0"><?php echo $text_no; ?></label>
										<span class="help tooltip" title="<?php echo $text_tooltip_category_only; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_product_in_parent_category"><b><?php echo $text_product_in_parent_category; ?></b></label><br /><small><?php echo $text_product_in_parent_category_i; ?></small></td>
									<td>
										<input type="radio" name="feed_data[product_in_parent_category]" id="feed_product_in_parent_category_1" value="1"<?php if (isset($feed_data['product_in_parent_category']) AND $feed_data['product_in_parent_category'] == 1){echo ' checked="checked"';} ?> /> <label for="feed_product_in_parent_category_1"><?php echo $text_yes; ?></label>
										<input type="radio" name="feed_data[product_in_parent_category]" id="feed_product_in_parent_category_0" value="0"<?php if (!isset($feed_data['product_in_parent_category']) || $feed_data['product_in_parent_category'] == 0){echo ' checked="checked"';} ?> /> <label for="feed_product_in_parent_category_0"><?php echo $text_no; ?></label>
										<span class="help tooltip" title="<?php echo $text_tooltip_product_in_parent_category; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_product_only_old_update"><b><?php echo $text_product_only_old_update; ?></b></label><br /><small><?php echo $text_product_only_old_update_i; ?></small></td>
									<td>
										<input type="radio" name="feed_data[product_only_old_update]" id="feed_product_only_old_update_1" value="1"<?php if (isset($feed_data['product_only_old_update']) AND $feed_data['product_only_old_update'] == 1){echo ' checked="checked"';} ?> /> <label for="feed_product_only_old_update_1"><?php echo $text_yes; ?></label>
										<input type="radio" name="feed_data[product_only_old_update]" id="feed_product_only_old_update_0" value="0"<?php if (!isset($feed_data['product_only_old_update']) || $feed_data['product_only_old_update'] == 0){echo ' checked="checked"';} ?> /> <label for="feed_product_only_old_update_0"><?php echo $text_no; ?></label>
										<span class="help tooltip" title="<?php echo $text_tooltip_product_only_old_update; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_parts"><b><?php echo $text_import_parts; ?></b></label><br /><small><?php echo $text_import_parts_i; ?></small></td>
									<td>
										<select name="feed_data[parts]" id="feed_parts" onChange="chageCronLinks(this.value);">
											<?php
												$part = 1;
												while($part <= 10){
													$selected = '';
													if ($feed_data['parts'] == $part){$selected = ' selected="selected"';}
													echo ' <option value="'.$part.'"'.$selected.'>'.$part.' часть</option>';
													$part++;
												}
											?>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_import_parts; ?>"></span>
									</td>
								</tr>

								<tr>
									<td><label for="feed_product_tag"><b><?php echo $text_product_tag; ?></b></label><br /><small><?php echo $text_product_tag_i; ?></small></td>
									<td>
										<select name="feed_data[product_tag]" id="feed_product_tag">
											<?php
												if (isset($xml_tags)){
													foreach($xml_tags as $tag){
														$selected = '';
														if ($feed_data['product_tag'] == $tag['tag_key']){$selected = ' selected="selected"';}
														echo ' <option value="'.$tag['tag_key'].'"'.$selected.'>'.str_replace(';',' &gt; ',$tag['tag_key']).'</option>'; //NNEEWW
													}
												}
											?>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_product_tag; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_primary_key"><b><?php echo $text_primary_key; ?></b></label><br /><small><?php echo $text_primary_key_i; ?></small></td>
									<td>
										<select name="feed_data[primary_key]" id="feed_primary_key">
											<option value="product_id"<?php if (isset($feed_data['primary_key']) AND $feed_data['primary_key'] == 'product_id'){echo ' selected="selected"';} ?>><?php echo $text_product_id; ?></option>
											<option value="model"<?php if (isset($feed_data['primary_key']) AND $feed_data['primary_key'] == 'model'){echo ' selected="selected"';} ?>><?php echo $text_product_model; ?></option>
											<option value="sku"<?php if (isset($feed_data['primary_key']) AND $feed_data['primary_key'] == 'sku'){echo ' selected="selected"';} ?>><?php echo $text_product_sku; ?></option>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_primary_key; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_global_language_id"><b><?php echo $text_global_language_id; ?></b></label><br /><small><?php echo $text_global_language_id_i; ?></small></td>
									<td>
										<select name="feed_data[global_language_id]" id="feed_data_global_language_id">
											<?php
												if ($languages){
													foreach($languages as $language){
														$selected = '';
														if ($language['language_id'] == $feed_data['global_language_id']){$selected = ' selected="selected"';}
														echo '<option value="'.(int)$language['language_id'].'"'.$selected.'>'.$language['name'].'</option>';
													}
												}
											?>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_global_language_id; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_stock_status_id"><b><?php echo $text_stock_status; ?></b></label><br /><small><?php echo $text_stock_status_i; ?></small></td>
									<td>
										<select name="feed_data[stock_status_id]" id="feed_data_stock_status_id">
											<?php
												if ($stock_statuses){
													foreach($stock_statuses as $stock_status){
														$selected = '';
														if ($stock_status['stock_status_id'] == $feed_data['stock_status_id']){$selected = ' selected="selected"';}
														echo '<option value="'.(int)$stock_status['stock_status_id'].'"'.$selected.'>'.$stock_status['name'].'</option>';
													}
												}
											?>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_stock_status; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_tax_class_id"><b><?php echo $text_tax_class; ?></b></label><br /><small><?php echo $text_tax_class_i; ?></small></td>
									<td>
										<select name="feed_data[tax_class_id]" id="feed_data_tax_class_id">
											<option value="0"><?php echo $text_none; ?></option>
											<?php
												if ($tax_classes){
													foreach($tax_classes as $tax_class){
														$selected = '';
														if ($tax_class['tax_class_id'] == $feed_data['tax_class_id']){$selected = ' selected="selected"';}
														echo '<option value="'.(int)$tax_class['tax_class_id'].'"'.$selected.'>'.$tax_class['title'].'</option>';
													}
												}
											?>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_tax_class; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_length_class_id"><b><?php echo $text_length_class; ?></b></label><br /><small><?php echo $text_length_class_i; ?></small></td>
									<td>
										<select name="feed_data[length_class_id]" id="feed_data_length_class_id">
											<?php
												if ($length_classes){
													foreach($length_classes as $length_class){
														$selected = '';
														if ($length_class['length_class_id'] == $feed_data['length_class_id']){$selected = ' selected="selected"';}
														echo '<option value="'.(int)$length_class['length_class_id'].'"'.$selected.'>'.$length_class['title'].'</option>';
													}
												}
											?>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_length_class; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_weight_class_id"><b><?php echo $text_weight_class; ?></b></label><br /><small><?php echo $text_weight_class_i; ?></small></td>
									<td>
										<select name="feed_data[weight_class_id]" id="feed_data_weight_class_id">
											<?php
												if ($weight_classes){
													foreach($weight_classes as $weight_class){
														$selected = '';
														if ($weight_class['weight_class_id'] == $feed_data['weight_class_id']){$selected = ' selected="selected"';}
														echo '<option value="'.(int)$weight_class['weight_class_id'].'"'.$selected.'>'.$weight_class['title'].'</option>';
													}
												}
											?>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_weight_class; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_manufacturer_id"><b><?php echo $text_default_manufacturer; ?></b></label><br /><small><?php echo $text_default_manufacturer_i; ?></small></td>
									<td>
										<select name="feed_data[manufacturer_id]" id="feed_data_manufacturer_id">
											<option value="0"><?php echo $text_select; ?></option>
											<?php
												if ($manufacturers){
													foreach($manufacturers as $manufacturer){
														$selected = '';
														if ($manufacturer['manufacturer_id'] == $feed_data['manufacturer_id']){$selected = ' selected="selected"';}
														echo '<option value="'.(int)$manufacturer['manufacturer_id'].'"'.$selected.'>'.$manufacturer['name'].'</option>';
													}
												}
											?>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_default_manufacturer; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_attribute_group_name"><b><?php echo $text_default_attribute_group_name; ?></b></label><br /><small><?php echo $text_default_attribute_group_name_i; ?></small></td>
									<td>
										<input type="text" name="feed_data[attribute_group_name]" value="<?php echo $feed_data['attribute_group_name'];?>" id="feed_data_attribute_group_name" />
										<span class="help tooltip" title="<?php echo $text_tooltip_attribute_group; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_option_group_name"><b><?php echo $text_default_option_group_name; ?></b></label><br /><small><?php echo $text_default_option_group_name_i; ?></small></td>
									<td>
										<input type="text" name="feed_data[option_group_name]" value="<?php echo $feed_data['option_group_name'];?>" id="feed_data_option_group_name" />
										<span class="help tooltip" title="<?php echo $text_tooltip_option_group; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_option_quantity"><b><?php echo $text_default_option_quantity; ?></b></label><br /><small><?php echo $text_default_option_quantity_i; ?></small></td>
									<td>
										<input type="text" name="feed_data[option_quantity]" value="<?php echo $feed_data['option_quantity'];?>" id="feed_data_option_quantity" />
										<span class="help tooltip" title="<?php echo $text_tooltip_option_quantity; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_option_type"><b><?php echo $text_default_option_type; ?></b></label><br /><small><?php echo $text_default_option_type_i; ?></small></td>
									<td>
										<select name="feed_data[option_type]" id="feed_data_option_type">
											<optgroup label="Choose">
												<option value="select"<?php if ($feed_data['option_type'] == "select"){echo ' selected="selected"';}?>>Select</option>
												<option value="radio"<?php if ($feed_data['option_type'] == "radio"){echo ' selected="selected"';}?>>Radio</option>
												<option value="checkbox"<?php if ($feed_data['option_type'] == "checkbox"){echo ' selected="selected"';}?>>Checkbox</option>
												<option value="image"<?php if ($feed_data['option_type'] == "image"){echo ' selected="selected"';}?>>Image</option>
											</optgroup>
											<optgroup label="Input">
												<option value="text"<?php if ($feed_data['option_type'] == "text"){echo ' selected="selected"';}?>>Text</option>
												<option value="textarea"<?php if ($feed_data['option_type'] == "textarea"){echo ' selected="selected"';}?>>Textarea</option>
											</optgroup>
											<optgroup label="File">
												<option value="file"<?php if ($feed_data['option_type'] == "file"){echo ' selected="selected"';}?>>File</option>
											</optgroup>
											<optgroup label="Date">
												<option value="date"<?php if ($feed_data['option_type'] == "date"){echo ' selected="selected"';}?>>Date</option>
												<option value="time"<?php if ($feed_data['option_type'] == "time"){echo ' selected="selected"';}?>>Time</option>
												<option value="datetime"<?php if ($feed_data['option_type'] == "datetime"){echo ' selected="selected"';}?>>Date &amp; Time</option>
											</optgroup>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_option_type; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_option_subtract_1"><b><?php echo $text_default_option_subtract; ?></b></label><br /><small><?php echo $text_default_option_subtract_i; ?></small></td>
									<td>
										<input type="radio" name="feed_data[option_subtract]" id="feed_option_subtract_1" value="1"<?php if (isset($feed_data['option_subtract']) AND $feed_data['option_subtract'] == 1){echo ' checked="checked"';} ?> /> <label for="feed_option_subtract_1"><?php echo $text_yes; ?></label>
										<input type="radio" name="feed_data[option_subtract]" id="feed_option_subtract_0" value="0"<?php if (!isset($feed_data['option_subtract']) || $feed_data['option_subtract'] == 0){echo ' checked="checked"';} ?> /> <label for="feed_option_subtract_0"><?php echo $text_no; ?></label>
										<span class="help tooltip" title="<?php echo $text_tooltip_option_subtract; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_option_required_1"><b><?php echo $text_default_option_required; ?></b></label><br /><small><?php echo $text_default_option_required_i; ?></small></td>
									<td>
										<input type="radio" name="feed_data[option_required]" id="feed_option_required_1" value="1"<?php if (isset($feed_data['option_required']) AND $feed_data['option_required'] == 1){echo ' checked="checked"';} ?> /> <label for="feed_option_subtract_1"><?php echo $text_yes; ?></label>
										<input type="radio" name="feed_data[option_required]" id="feed_option_required_0" value="0"<?php if (!isset($feed_data['option_required']) || $feed_data['option_required'] == 0){echo ' checked="checked"';} ?> /> <label for="feed_option_subtract_0"><?php echo $text_no; ?></label>
										<span class="help tooltip" title="<?php echo $text_tooltip_option_required; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_seo_keyword_product"><b><?php echo $text_seo_keyword_product; ?></b></label><br /><small><?php echo $text_friendly_url; ?></small></td>
									<td>
										<select name="feed_data[seo_keyword_product]" id="feed_data_seo_keyword_product">
											<option value=""<?php if ($feed_data['seo_keyword_product'] == ""){echo ' selected="selected"';}?>><?php echo $text_skip; ?></option>
											<option value="id-name"<?php if ($feed_data['seo_keyword_product'] == "id-name"){echo ' selected="selected"';}?>><?php echo $text_seo_id_name; ?></option>
											<option value="name"<?php if ($feed_data['seo_keyword_product'] == "name"){echo ' selected="selected"';}?>><?php echo $text_seo_name; ?></option>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_seo_keyword; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_seo_keyword_category"><b><?php echo $text_seo_keyword_category; ?></b></label><br /><small><?php echo $text_friendly_url; ?></small></td>
									<td>
										<select name="feed_data[seo_keyword_category]" id="feed_data_seo_keyword_category">
											<option value=""<?php if ($feed_data['seo_keyword_category'] == ""){echo ' selected="selected"';}?>><?php echo $text_skip; ?></option>
											<option value="id-name"<?php if ($feed_data['seo_keyword_category'] == "id-name"){echo ' selected="selected"';}?>><?php echo $text_seo_id_name; ?></option>
											<option value="name"<?php if ($feed_data['seo_keyword_category'] == "name"){echo ' selected="selected"';}?>><?php echo $text_seo_name; ?></option>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_seo_keyword; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_seo_keyword_manufacturer"><b><?php echo $text_seo_keyword_manufacturer; ?></b></label><br /><small><?php echo $text_friendly_url; ?></small></td>
									<td>
										<select name="feed_data[seo_keyword_manufacturer]" id="feed_data_seo_keyword_manufacturer">
											<option value=""<?php if ($feed_data['seo_keyword_manufacturer'] == ""){echo ' selected="selected"';}?>><?php echo $text_skip; ?></option>
											<option value="id-name"<?php if ($feed_data['seo_keyword_manufacturer'] == "id-name"){echo ' selected="selected"';}?>><?php echo $text_seo_id_name; ?></option>
											<option value="name"<?php if ($feed_data['seo_keyword_manufacturer'] == "name"){echo ' selected="selected"';}?>><?php echo $text_seo_name; ?></option>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_seo_keyword; ?>"></span>
									</td>
								</tr>                
								<tr>
									<td><label for="feed_data_category_separator"><b><?php echo $text_category_separator; ?></b></label><br /><small><?php echo $text_category_separator_i; ?></small></td>
									<td>
									<?php
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
									?>
										<select name="feed_data[category_separator]" id="feed_data_category_separator">
										<?php foreach($separators as $index => $separator){ ?>
											<option value="<?php echo $index; ?>"<?php if ((int)$feed_data['category_separator'] == $index){echo ' selected="selected"';}?>><?php 
												$option_text = '';
												$option_text .= '"'.$separator.'"';
												$option_text .= ' (пример: ';
												$option_text .= implode($separator,array("Категория А","Категория Б","Категория В"));
												$option_text .= ')';
												echo htmlspecialchars($option_text);
											 ?></option>
										<?php } ?>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_category_separator; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_old_product_action"><b><?php echo $text_old_product; ?></b></label><br /><small><?php echo $text_old_product_i; ?></small></td>
									<td>
										<select name="feed_data[old_product_action]" id="feed_data_old_product_action">
											<option value="nothing"<?php if ($feed_data['old_product_action'] == "nothing"){echo ' selected="selected"';}?>><?php echo $text_do_nothing; ?></option>
											<option value="delete"<?php if ($feed_data['old_product_action'] == "delete"){echo ' selected="selected"';}?>><?php echo $text_delete; ?></option>
											<option value="disable"<?php if ($feed_data['old_product_action'] == "disable"){echo ' selected="selected"';}?>><?php echo $text_disable; ?></option>
											<option value="zero_quantity"<?php if ($feed_data['old_product_action'] == "zero_quantity"){echo ' selected="selected"';}?>><?php echo $text_set_zero_quantity; ?></option>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_old_product; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_product_status_1"><b><?php echo $text_product_status; ?></b></label><br /><small><?php echo $text_product_status_i; ?></small></td>
									<td>
										<input type="radio" name="feed_data[product_status]" id="feed_product_status_1" value="1"<?php if (isset($feed_data['product_status']) AND $feed_data['product_status'] == 1){echo ' checked="checked"';} ?> /> <label for="feed_product_status_1"><?php echo $text_enabled; ?></label>
										<input type="radio" name="feed_data[product_status]" id="feed_product_status_0" value="0"<?php if (!isset($feed_data['product_status']) || $feed_data['product_status'] == 0){echo ' checked="checked"';} ?> /> <label for="feed_product_status_0"><?php echo $text_disabled; ?></label>
										<span class="help tooltip" title="<?php echo $text_tooltip_product_status; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_product_subtract_1"><b><?php echo $text_subtract; ?></b></label><br /><small><?php echo $text_subtract_i; ?></small></td>
									<td>
										<input type="radio" name="feed_data[product_subtract]" id="feed_data_product_subtract_1" value="1"<?php if (isset($feed_data['product_subtract']) AND $feed_data['product_subtract'] == 1){echo ' checked="checked"';} ?> /> <label for="feed_data_product_subtract_1"><?php echo $text_yes; ?></label>
										<input type="radio" name="feed_data[product_subtract]" id="feed_data_product_subtract_0" value="0"<?php if (!isset($feed_data['product_subtract']) || $feed_data['product_subtract'] == 0){echo ' checked="checked"';} ?> /> <label for="feed_data_product_subtract_0"><?php echo $text_no; ?></label>
										<span class="help tooltip" title="<?php echo $text_tooltip_product_subtract; ?>"></span>
									</td>
								</tr>
								

								<tr>
									<td><label for="feed_product_shipping_1"><b><?php echo $text_product_shipping; ?></b></label><br /><small><?php echo $text_product_shipping_i; ?></small></td>
									<td>
										<input type="radio" name="feed_data[product_shipping]" id="feed_product_shipping_1" value="1"<?php if (isset($feed_data['product_shipping']) AND $feed_data['product_shipping'] == 1){echo ' checked="checked"';} ?> /> <label for="feed_product_shipping_1"><?php echo $text_yes; ?></label>
										<input type="radio" name="feed_data[product_shipping]" id="feed_product_shipping_0" value="0"<?php if (!isset($feed_data['product_shipping']) || $feed_data['product_shipping'] == 0){echo ' checked="checked"';} ?> /> <label for="feed_product_shipping_0"><?php echo $text_no; ?></label>
										<span class="help tooltip" title="<?php echo $text_tooltip_product_shipping; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_product_quantity"><b><?php echo $text_default_quantity; ?></b></label><br /><small><?php echo $text_default_quantity_i; ?></small></td>
									<td>
										<input type="text" name="feed_data[product_quantity]" value="<?php if (isset($feed_data['product_quantity'])){echo $feed_data['product_quantity'];}else{echo '0';} ?>" id="feed_data_product_quantity" />
										<span class="help tooltip" title="<?php echo $text_tooltip_default_quantity; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_special_price_customer_group_id"><b><?php echo $text_special_price_group; ?></b></label><br /><small><?php echo $text_special_price_group_i; ?></small></td>
									<td>
										<select name="feed_data[special_price_customer_group_id]" id="feed_data_special_price_customer_group_id">
										<?php foreach($customer_groups as $customer_group){ ?>
											<option value="<?php echo (int)$customer_group['customer_group_id']; ?>"<?php if (isset($feed_data['special_price_customer_group_id']) AND $feed_data['special_price_customer_group_id'] == $customer_group['customer_group_id']){echo ' selected="selected"';} ?>><?php echo $customer_group['name']; ?></option>
										<?php } ?>
										</select>
										<span class="help tooltip" title="<?php echo $text_tooltip_special_price_group; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_price_edit"><b><?php echo $text_price_edit; ?></b></label><br /><small><?php echo $text_price_edit_i; ?></small></td>
									<td>
										<input type="text" name="feed_data[price_edit]" value="<?php if (isset($feed_data['price_edit'])){if ($feed_data['price_edit'] > 0){echo '+';}echo (float)$feed_data['price_edit'];}else{echo '0';} ?>" id="feed_data_price_edit" />
										<select name="feed_data[price_edit_type]" id="feed_data_price_edit_type">
											<option value="percentage"<?php if (isset($feed_data['price_edit_type']) AND $feed_data['price_edit_type'] == "percentage"){echo ' selected="selected"';} ?>><?php echo $text_percent; ?></option>
											<option value="fixed"<?php if (isset($feed_data['price_edit_type']) AND $feed_data['price_edit_type'] == "fixed"){echo ' selected="selected"';} ?>><?php echo $text_fixed; ?></option>
										</select>
										<input type="checkbox" name="feed_data[price_edit_options]" id="feed_data_price_edit_options" value="1"<?php if (isset($feed_data['price_edit_options']) AND $feed_data['price_edit_options'] == 1){echo ' checked="checked"';} ?> /> <label for="feed_data_price_edit_options"><?php echo $text_include_option_price; ?></label>
										<span class="help tooltip" title="<?php echo $text_tooltip_price_edit; ?>"></span>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_store_id_0"><b><?php echo $text_store; ?></b></label><br /><small><?php echo $text_store_i; ?></small></td>
									<td>
										<div id="store-items" class="well well-sm" style="height: 100px; overflow: auto;">
											<?php
												if ($stores){$i = 0;foreach($stores as $store){$i++;
												$checked = '';
												if (isset($feed_data['store_id']) AND is_array($feed_data['store_id'])){
													foreach($feed_data['store_id'] as $store_id){
														if ($store['store_id'] == $store_id){$checked = ' checked="checked"';}
													}
												}
												if (!$feed_data['store_id'] AND $i == 1){$checked = ' checked="checked"';}
											?>
											<div class="<?php if ($i % 2){echo 'even';}else{echo 'odd';}?>">
												<input name="feed_data[store_id][]" id="feed_data_store_id_<?php echo (int)$store['store_id']; ?>" value="<?php echo (int)$store['store_id']; ?>"<?php echo $checked;?> type="checkbox" /><label for="feed_data_store_id_<?php echo (int)$store['store_id']; ?>"><?php echo $store['name']; ?></label>
											</div>
										 <?php }} ?>
										</div>
									</td>
								</tr>
								<tr>
									<td><label for="feed_data_update-product_name"><b><?php echo $text_update; ?></b></label><br /><small><?php echo $text_update_i; ?></small></td>
									<td>
										<div id="update-items" class="well well-sm" style="height: 180px; overflow: auto;">
											<?php 
												$i = 0;
												foreach($update_items as $option_key => $option_title){
												$i++;
												$checked = '';
												if (isset($feed_data['update_items']) and is_array($feed_data['update_items'])){
													foreach($feed_data['update_items'] as $update_item){
														if ($update_item == $option_key){
															$checked = ' checked="checked"';
														}
													}
												}
											?>
											<div class="<?php if ($i % 2){echo 'even';}else{echo 'odd';}?>">
												<input type="checkbox" name="feed_data[update_items][]" value="<?php echo $option_key;?>" id="feed_data_update-<?php echo $option_key;?>"<?php echo $checked;?> /><label for="feed_data_update-<?php echo $option_key;?>"><?php echo $option_title;?></label>
											</div>
											<?php } ?>
										</div>
									</td>
								</tr>
								<tr>
									<td><label for="feed_delete"><b><?php echo $text_delete_feed; ?></b></label><br /><small><?php echo $text_delete_feed_i; ?></small></td>
									<td>
										<a href="<?php echo $link_delete_import;?>" id="delete-import-link" class="btn btn-small btn-danger" onClick="return confirm('<?php echo $text_delete_feed_confirm; ?>');"><?php echo $text_delete_feed_button; ?></a>
										<input type="checkbox" id="delete-import-products" value="1" onCLick="switchDeleteLink($(this).prop('checked'));">
										<label for="delete-import-products"><?php echo $text_delete_feed_products; ?></label>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="right">
						<div class="box-heading icon icon-info"><?php echo $text_information; ?></div>
							<div class="box-content">
								<div id="xml-import-info">
									<table cellpadding="0" cellspacing="0">
										<tbody>
											<?php if ($feed_data['import_info'] != ""){foreach(unserialize($feed_data['import_info']) as $key => $value){ ?>
											<tr>
												<td class="name"><?php echo $text_import_info[$key]; ?></td>
												<td><?php echo $value; ?></td>
											</tr>
											<?php }}?>
										</tbody>
									</table>
								</div>
							</div>
					</div>
				</div>
				<div class="tab-pane" id="tab-xml_tag">
					<div class="left">
						<div class="box-heading icon icon-xml"><?php echo $text_tag_preview; ?><span class="button-refresh" onClick="getXMLStructure();"></span></div>
						<div class="box-content">
							<div id="xml_structure">
							<?php if ($xml_structure){ $tag_cache_number = 0;?>
								<?php foreach($xml_structure as $tag_cache){$tag_cache_number++; ?>
									<?php if ($tag_cache['tag_content'] == ""){?>
										<div class="row row-open">
											<input name="tag_cache[<?php echo (int)$tag_cache_number;?>][tag_name]" value="<?php echo $tag_cache['tag_name']; ?>" type="hidden">
											<input name="tag_cache[<?php echo (int)$tag_cache_number;?>][tag_content]" value="" type="hidden">
											<input name="tag_cache[<?php echo (int)$tag_cache_number;?>][tag_key]" value="<?php echo $tag_cache['tag_key']; ?>" type="hidden">
											<input name="tag_cache[<?php echo (int)$tag_cache_number;?>][level]" value="<?php echo $tag_cache['level']; ?>" type="hidden">
											<span class="level level-<?php echo $tag_cache['level']; ?>"><i>&lt;</i><span class="tag_name"><?php echo $tag_cache['tag_name']; ?></span><i>&gt;</i></span>
										</div>
									<?php }else{ ?>
										<div class="row">
											<input name="tag_cache[<?php echo (int)$tag_cache_number;?>][tag_name]" value="<?php echo $tag_cache['tag_name']; ?>" type="hidden">
											<input name="tag_cache[<?php echo (int)$tag_cache_number;?>][tag_content]" value="<?php echo $tag_cache['tag_content']; ?>" type="hidden">
											<input name="tag_cache[<?php echo (int)$tag_cache_number;?>][tag_key]" value="<?php echo $tag_cache['tag_key']; ?>" type="hidden">
											<input name="tag_cache[<?php echo (int)$tag_cache_number;?>][level]" value="<?php echo $tag_cache['level']; ?>" type="hidden">
											<span class="level level-<?php echo $tag_cache['level']; ?>"><i>&lt;</i><span class="tag_name"><?php echo $tag_cache['tag_name']; ?></span><i>&gt;</i><?php echo $tag_cache['tag_content']; ?><i>&lt;/</i><span class="tag_name"><?php echo $tag_cache['tag_name']; ?></span><i>&gt;</i></span>
											<div class="tag_content"><?php echo $text_content; ?>:&nbsp;&nbsp;
												<select name="tag[<?php echo $tag_cache['tag_key']; ?>]" onchange="changeXMLContent();" data-name="<?php echo $tag_cache['tag_name']; ?>" data-value="<?php echo $tag_cache['tag_content']; ?>">
													<option value="-"><?php echo $text_skip; ?></option>
													<?php foreach($tag_options as $option_key => $option_title){ ?>
													<option value="<?php echo $option_key;?>"<?php if ($tag_cache['tag_key_content'] == $option_key){echo ' selected="selected"';}?>><?php echo $option_title;?></option>
													<?php } ?>
												</select>
											</div>
										</div>
									<?php } ?>
								<?php } ?>
							<?php } ?>
							</div>
						</div>
					</div>
					<div class="right">
						<div class="box-heading icon icon-info"><?php echo $text_product_preview; ?></div>
						<div class="box-content">
						<div id="product-preview">
							<table cellpadding="0" cellspacing="0">
								<thead>
									<tr>
										<td class="name"><?php echo $text_type; ?></td>
										<td><?php echo $text_value; ?></td>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
				<div class="tab-pane" id="tab-import">
					<p><?php echo $text_importing_info; ?></p>
					<input type="button" id="start-import" class="btn btn-success" value="<?php echo $text_import_now; ?>" data-toggle="modal" data-target="#modal-import" />
					<br /><br />
					<label for="cron-links"><?php echo $text_cron_link; ?>:</label>
					<br /> 
					<textarea id="cron-links"></textarea>
				</div>        
				
				</div>
			</form>
			<?php } ?>
	
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="view/javascript/jquery/jquery.tooltipster.js"></script>
<script type="text/javascript"><!--

//selecting import:
function redirectToFeedSetting(import_id){
	if (import_id != 0){
		if (<?php echo (int)$import_id;?> != import_id){
			window.location.href='<?php echo htmlspecialchars_decode($link_redirect_link); ?>&import_id='+import_id;
		}
	}
	return true;
}

<?php if (isset($_GET['import_id'])){ ?>
//import edit:
$(document).ready(function() {
	$('.tooltip').tooltipster();
});

function switchDeleteLink(delete_products){
	if (delete_products == true){
		$('#delete-import-link').attr('href','<?php echo htmlspecialchars_decode($link_delete_import_include_products);?>');
	}else{
		$('#delete-import-link').attr('href','<?php echo htmlspecialchars_decode($link_delete_import);?>');
	}
}

function changeXMLContent(){
	var product_preview = '';
	var tag_name        = '';
	var tag_value       = '';
	$("#xml_structure select").each(function( index ) {
		if ($(this).val() != "-"){
			tag_name = $(this).find("option:selected").text();
			tag_value = $(this).attr('data-value');
			var tag_value_length = tag_value.length;
			if (tag_value_length > 100){
				tag_value = tag_value.substring(0,100)+".."; 
			}
			product_preview += '<tr><td class="name">'+tag_name+'</td><td>'+tag_value+'</td></tr>';
		}
	});
	return $('#product-preview table tbody').html(product_preview);
}

$('#download_xml').click(function(){
	$('#xml_structure').html('');
	$('#xml_structure').addClass('full-loading');
	$('#xml_url_result').removeClass('icon-result-loading');
	$('#xml_url_result').removeClass('icon-result-error');
	$('#xml_url_result').removeClass('icon-result-success');
	$('#xml_url_result').removeClass('icon-result-info');
	$('#xml_url_result_text').text('<?php echo $text_loading; ?>');
	$('#xml_url_result').addClass('icon-result-loading');

	$.post("<?php echo htmlspecialchars_decode($download_xml_url);?>", {
	 'download_xml': true, 'xml_url': $('#feed_url').val(), 'import_id': <?php echo $import_id; ?>
	}, function(data) {
		 var result = data.split('|');
		 $('#xml_url_result').removeClass('icon-result-loading');
		 $('#xml_url_result').addClass('icon-result-'+result[0]);
		 $('#xml_url_result_text').html(result[1]);
		 getXMLStructure();
		 getImportInfo();

	 //change XML link - load actual Product Tag options
		 $.post("<?php echo htmlspecialchars_decode($link_product_tags);?>", {'import_id': <?php echo $import_id; ?>}, function(data) {$('#feed_product_tag').html(data);});
	});
 
});

function getXMLStructure(){
	$('#xml_structure').html('');
	$('#xml_structure').addClass('full-loading');
	$('#xml_structure').removeClass('xml-error');
	$.post("<?php echo htmlspecialchars_decode($xml_structure_url);?>", {'getXMLStructure': true}, function(data) {
		if (data == "xml-error-structure"){
			$('#xml_structure').addClass('xml-error');
			$('#xml_structure').html('<?php echo $text_xml_file_damaged; ?>');
		}else{
			$('#xml_structure').html(data);
			$('#xml_structure').removeClass('full-loading');
			changeXMLContent();
		 }
	});
	return false;
}


function getImportInfo(){
	$.post("<?php echo htmlspecialchars_decode($link_import_info);?>", {'getImportInfo': true}, function(data) {
		 $('#xml-import-info tbody').html(data);
	});
	return true;
}

function chageCronLinks(total_parts){
	var part = 1;
	var links = '';
	while(part <= total_parts){
		links += '<?php echo HTTP_SERVER;?>import.php?import_id=<?php echo (int)$_GET["import_id"];?>&part='+part+'_'+total_parts+"\n";
		part++;
	}
	$('#cron-links').val(links);
	return true;
}

chageCronLinks(<?php echo (int)$feed_data['parts']; ?>);
changeXMLContent();

//import popup:
var actual_part    = 1;
var imported_parts = 0;
var progressInterval; //timer

function updateStats(total_products){
	var d = new Date();
	var url = 'import-stats.dat?update='+d.getTime();
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function process(){
		if (xhr.readyState == 4) {
			var stats = xhr.responseText
			stats = stats.split('|');
			var total_parts    = $('#feed_parts').val();
			var total_inserted = stats[0];
			var total_updated  = stats[1];
			var total_checked  = stats[2];
			var percentage = Math.round(100/total_products*total_checked);
			if (percentage == "NaN"){percentage = 0;}
			$('#import-stats-part').html(imported_parts+"/"+total_parts);
			$('#import-stats-checked-products').html(total_checked+' / '+total_products);
			$('#import-stats-inserted-products').html(total_inserted);
			$('#import-stats-updated-products').html(total_updated);
			$('#import-stats-progress').html(percentage+' %');
			$('#progress').css("width",percentage+"%");
			if (total_checked >= total_products){
				clearInterval(progressInterval);
				$('#block-progress').css('display','none');
				$('#success-done').fadeIn();
				console.log('End of import.');
			}
		}
	};
	xhr.open("GET", url, true);
	xhr.send();
}

//start - import
$('#start-import').click(function(){

actual_part    = 1;
imported_parts = 0;

//save form data before start uploading
	//if (saveAndStay()){
		$('#import-stats-part').html('<span class="mini-loading"></span>');
		$('#import-stats-checked-products').html('<span class="mini-loading"></span>');
		$('#import-stats-inserted-products').html('<span class="mini-loading"></span>');
		$('#import-stats-updated-products').html('<span class="mini-loading"></span>');
		$('#import-stats-progress').html('<span class="mini-loading"></span>');
		$('#block-progress').css('display','block');
		$('#success-done').css('display','none');

	$.when(saveAndStay()).done(function(){
	//loading icon
		
		var total_products = 0;
		var total_inserted = 0;
		var total_updated  = 0;
		var total_checked  = 0;
		var part           = '???';
		var total_parts    = $('#feed_parts').val();
		var importing      = true; //true = still importing; false = done - complete done !
		var stats_cleared  = false;
		
	//clear stats
		$('#import-popup-main').css("display","none");
		$('#import-popup-cron-links').css("display","none");
		$('#import-popup-progress').fadeIn();
		$('#modal-import .modal-header h4').text("<?php echo $text_importing; ?>..");
		
	//clear stats:
		$.ajax({
			url: 'import.php?import_id=<?php echo (int)$_GET["import_id"];?>&action=clearInsertedAndUpdated',
			dataType: 'json',
			success: function(json) {
				console.log('Stats - cleared');
				stats_cleared = true;
			}
		});
		
	//get total products in xml and clear inserted or updated:
		$.ajax({
			url: 'import.php?import_id=<?php echo (int)$_GET["import_id"];?>&action=getTotalProductsInXML',
			dataType: 'json',
			success: function(json) {
				total_products = json;
				console.log('Get total products finished - '+total_products);
			}
		});
		
		var importing_interval_number = 0;
		progressInterval              = setInterval(function(){
		
		
	//import:
		if (total_products > 0){
			if (stats_cleared){updateStats(total_products);}
			if (((actual_part-1) == imported_parts && total_parts > 1) || (actual_part == 1 && total_parts == 1)){
				console.log('Start importing part '+actual_part);
				$.ajax({
					url: 'import.php?import_id=<?php echo (int)$_GET["import_id"];?>&import_from_admin=true&part='+actual_part+'_'+total_parts,
					dataType: 'json',
					complete: function(json){
						imported_parts++;
					}
					});
					actual_part++;
			}
			importing_interval_number++;
		}
		
		}, 1000);
	});
});

function saveAndStay(){
	return $.ajax({
		type: "POST",
		url: '<?php echo htmlspecialchars_decode($_SERVER["REQUEST_URI"]); ?>',
		data: $('#import-form').serialize(),
		success: function(){
			console.log('Import saved');
			return true;
		}
	});
}

$('#modal-import').modal({backdrop: 'static'})  
$('#modal-import').modal('hide');  

<?php } ?>

//--></script>
<?php echo $footer; ?>