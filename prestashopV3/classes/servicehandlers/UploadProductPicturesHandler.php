<?php

class UploadProductPicturesHandlerCore extends AbstractHandler
{
	public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info)
	{
		foreach($service_parameters as $service_parameter)
		{
			if($service_parameter['param_name'] == 'sample_num')
			{
				$num_example = (int)$service_parameter['param_value'];
		
				for($i = 1; $i <= $num_example; $i++)
				{
					$num_files = count($_FILES['sample_photo'.$i]['name']);
					for($j = 0; $j < $num_files; $j++)
					{
						$current_work_dir = getcwd();
						$target_dir = $current_work_dir."/sample_pics/".$_POST["transaction_id"].'/'.'sample_'.$i.'_photos/';
						$target_thumb_dir = $current_work_dir."/sample_pics/".$_POST["transaction_id"].'/'.'sample_'.$i.'_photos/thumb/';
						mkdir($target_dir, '0777', true);
						mkdir($target_thumb_dir, '0777', true);
						$target_file = $target_dir.'picture_'.$j.'.'.pathinfo($_FILES['sample_photo'.$i]["name"][$j], PATHINFO_EXTENSION);
						$target_thumb_file = $target_thumb_dir.'picture_'.$j.'.'.pathinfo($_FILES['sample_photo'.$i]["name"][$j], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['sample_photo'.$i]["tmp_name"][$j], $target_file);
						$this->makeThumbnails($target_file, $target_thumb_file);
					}
				}				
			}
		}
		
	}
	public function getReadableStatusString($context_inputs, $service_parameters, $lang)
	{
		
	}
	public function getAdditionalUIElements($service_parameters)
	{
		foreach($service_parameters as $service_parameter)
		{
			if($service_parameter['param_name'] == 'sample_num')
			{
				$num_example = (int)$service_parameter['param_value'];
		
				for($i = 1; $i <= $num_example; $i++)
				{
					$input['ui_element_type'] = 'file';
					$input['ui_element_name'] = 'sample_photo'.$i;
					$input['ui_element_accept'] = 'image/*';
					$input['ui_element_label'] = 'Photos for Sample '.$i;
		
					$ui_list[] = $input;
				}
		
				return $ui_list;
			}
		}
	}
	
	function makeThumbnails($img_origin, $img_thumb)
	{
		$thumbnail_width = 134;
		$thumbnail_height = 189;
		
		$arr_image_details = getimagesize($img_origin); // pass id to thumb name
		$original_width = $arr_image_details[0];
		$original_height = $arr_image_details[1];
		if ($original_width > $original_height) {
			$new_width = $thumbnail_width;
			$new_height = intval($original_height * $new_width / $original_width);
		} else {
			$new_height = $thumbnail_height;
			$new_width = intval($original_width * $new_height / $original_height);
		}
		$dest_x = intval(($thumbnail_width - $new_width) / 2);
		$dest_y = intval(($thumbnail_height - $new_height) / 2);
		if ($arr_image_details[2] == 1) {
			$imgt = "ImageGIF";
			$imgcreatefrom = "ImageCreateFromGIF";
		}
		if ($arr_image_details[2] == 2) {
			$imgt = "ImageJPEG";
			$imgcreatefrom = "ImageCreateFromJPEG";
		}
		if ($arr_image_details[2] == 3) {
			$imgt = "ImagePNG";
			$imgcreatefrom = "ImageCreateFromPNG";
		}
		if ($imgt) {
			$old_image = $imgcreatefrom($img_origin);
			$new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
			imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
			$imgt($new_image, $img_thumb);
		}
	}
}
