<?php

if($import_id == 1){
  $product_data['images'] = array();
  if(isset($product->IMAGES)){
    $images = $product->IMAGES;
    if(isset($images->IMAGE_1)){$product_data['images'][] = (string)$images->IMAGE_1;}
    if(isset($images->IMAGE_2)){$product_data['images'][] = (string)$images->IMAGE_2;}
    if(isset($images->IMAGE_3)){$product_data['images'][] = (string)$images->IMAGE_3;}
    if(isset($images->IMAGE_4)){$product_data['images'][] = (string)$images->IMAGE_4;}
    if(isset($images->IMAGE_5)){$product_data['images'][] = (string)$images->IMAGE_5;}
    if(isset($images->IMAGE_6)){$product_data['images'][] = (string)$images->IMAGE_6;}
    if(isset($images->IMAGE_7)){$product_data['images'][] = (string)$images->IMAGE_7;}
    if(isset($images->IMAGE_8)){$product_data['images'][] = (string)$images->IMAGE_8;}
    if(isset($images->IMAGE_9)){$product_data['images'][] = (string)$images->IMAGE_9;}
    if(isset($images->IMAGE_10)){$product_data['images'][] = (string)$images->IMAGE_10;}
    if(isset($images->IMAGE_11)){$product_data['images'][] = (string)$images->IMAGE_11;}
    if(isset($images->IMAGE_12)){$product_data['images'][] = (string)$images->IMAGE_12;}
    if(isset($images->IMAGE_13)){$product_data['images'][] = (string)$images->IMAGE_13;}
    if(isset($images->IMAGE_14)){$product_data['images'][] = (string)$images->IMAGE_14;}
    if(isset($images->IMAGE_15)){$product_data['images'][] = (string)$images->IMAGE_15;}
    if(isset($images->IMAGE_16)){$product_data['images'][] = (string)$images->IMAGE_16;}
    if(isset($images->IMAGE_17)){$product_data['images'][] = (string)$images->IMAGE_17;}
    if(isset($images->IMAGE_18)){$product_data['images'][] = (string)$images->IMAGE_18;}
    if(isset($images->IMAGE_19)){$product_data['images'][] = (string)$images->IMAGE_19;}
    if(isset($images->IMAGE_20)){$product_data['images'][] = (string)$images->IMAGE_20;}
  }
}

$product_clear_images = array();
foreach($product_data['images'] as $image){
  
  $clear_image = $image;
  $clear_image = str_replace("\n","",$clear_image);
  $clear_image = str_replace('				','',$clear_image);
  $product_clear_images[] = $clear_image;
}

$product_data['images'] = $product_clear_images;


?>