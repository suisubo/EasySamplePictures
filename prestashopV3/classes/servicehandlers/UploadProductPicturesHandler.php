<?php

class UploadProductPicturesHandlerCore extends AbstractHandler
{
	public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info)
	{
		$current_work_dir = getcwd();
		$zip = new ZipArchive();
		$zip->open($current_work_dir."/sample_pics/".$_POST["transaction_id"]."/sample_photos.zip", ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
		foreach($service_parameters as $service_parameter)
		{
			if($service_parameter['param_name'] == 'sample_num')
			{
				$num_example = (int)$service_parameter['param_value'];
		
				for($i = 1; $i <= $num_example; $i++)
				{
					$num_files = count($_FILES['sample_photo'.$i]['name']);
					if($num_files == 0 || $num_files == 1 && $_FILES['sample_photo'.$i]['name'][0] == '')
						continue;
					
					$outputs['sample_'.$i.'_pic_num'] = $num_files;
					for($j = 0; $j < $num_files; $j++)
					{						
						$target_dir = $current_work_dir."/sample_pics/".$_POST["transaction_id"].'/'.'sample_'.$i.'_photos/';
						$target_thumb_dir = $current_work_dir."/sample_pics/".$_POST["transaction_id"].'/'.'sample_'.$i.'_photos/thumb/';
						mkdir($target_dir, '0777', true);
						mkdir($target_thumb_dir, '0777', true);
						$target_file = $target_dir.'picture_'.$j.'.jpg';//.pathinfo($_FILES['sample_photo'.$i]["name"][$j], PATHINFO_EXTENSION);
						$target_thumb_file = $target_thumb_dir.'picture_'.$j.'.jpg';
						move_uploaded_file($_FILES['sample_photo'.$i]["tmp_name"][$j], $target_file);
						$this->makeThumbnails($target_file, $target_thumb_file);
						$zip->addFile($target_file, 'sample_'.$i.'_photos/'.basename($target_file));
					}
				}				
			}
		}
		
		$zip->close();
		
		return AbstractHandler::PROCESS_SUCCESS;
		
	}
	public function getReadableStatusString($context_inputs, $service_parameters, $lang)
	{
		return 'waiting for the photos being uploaded';
	}
	public function getAdditionalInputUIElements($context_inputs, $service_parameters)
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
					$input['ui_element_label'] = 'Photos for Sample '.$context_inputs['sample_tag'.$i];
		
					$ui_list[] = $input;
				}
		
				return $ui_list;
			}
		}
	}
	
	public function getAdditionalStatusUIElements($context_inputs, $service_parameters){
		return null;
	}
	
	function makeThumbnails($img_origin, $img_thumb)
	{
		$thumbnail_width = 256;
		$thumbnail_height = 256;
		
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
			$imgcreatefrom = "ImageCreateFromGIF";
		}
		if ($arr_image_details[2] == 2) {			
			$imgcreatefrom = "ImageCreateFromJPEG";
		}
		if ($arr_image_details[2] == 3) {			
			$imgcreatefrom = "ImageCreateFromPNG";
		}
		
		$imgt = "ImageJPEG";
		
		if ($imgt) {
			$old_image = $imgcreatefrom($img_origin);
			$new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
			// set background to white
			$white = imagecolorallocate($new_image, 255, 255, 255);
			imagefill($new_image, 0, 0, $white);
			imagecopyresized($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
			$imgt($new_image, $img_thumb);
		}
	}
}
