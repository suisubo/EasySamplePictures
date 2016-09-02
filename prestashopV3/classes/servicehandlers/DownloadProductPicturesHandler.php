<?php


class DownloadProductPicturesHandlerCore extends AbstractHandler
{
	public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info)
	{
		return AbstractHandler::PROCESS_SUCCESS;
	}
	public function getReadableStatusString($context_inputs, $service_parameters, $lang)
	{
		return null;
	}
	
	public function getAdditionalInputUIElements($context_inputs, $service_parameters){
		$photo_url = __PS_BASE_URI__."/sample_pics/".$context_inputs["transaction_id"]."/sample_photos.zip";
		$gallery = '<a href="'.$photo_url.'" class="transaction_button transaction_blue">Download Photos<span></span></a><br><br><br>';	
		
		$input_sample_tag['ui_element_type'] = 'custom';
		$input_sample_tag['ui_element_custom_content'] = $gallery;
		
		$ui_list[] = $input_sample_tag;
		
		return $ui_list;
	}
	
	public function getAdditionalStatusUIElements($context_inputs, $service_parameters)
	{
		foreach($service_parameters as $service_parameter)
		{
			if($service_parameter['param_name'] == 'sample_num')
			{
				$num_example = (int)$service_parameter['param_value'];
				
				for($i = 1; $i <= $num_example; $i++)
				{
					if(!isset($context_inputs['sample_'.$i.'_pic_num'])) continue;
					
					$gallery= $gallery.'<label for="sample_gallery_'.$i.'">'.$context_inputs['sample_tag'.$i].'</label>';
					$gallery = $gallery.'<div class="fotorama" data-nav="thumbs" id="sample_gallery_'.$i.'" data-width="700" data-ratio="700/467" data-max-width="100%">';
					$num_pic = (int)$context_inputs['sample_'.$i.'_pic_num'];
					$sample_pic_dir = __PS_BASE_URI__.'sample_pics/'.$context_inputs["transaction_id"].'/sample_'.$i.'_photos/';
					$sample_pic_thum_dir = __PS_BASE_URI__.'sample_pics/'.$context_inputs["transaction_id"].'/sample_'.$i.'_photos/thumb/';
					for($j = 0; $j < $num_pic; $j++)
					{						
						$gallery = $gallery.'<a href="'.$sample_pic_dir.'picture_'.$j.'.jpg'
								   .'"><img src="'.$sample_pic_thum_dir.'picture_'.$j.'.jpg'.'"></a>';
						
						//$gallery = $gallery.'<img src="'.$sample_pic_dir.'picture_'.$j.'.jpg'.'">';
						
					}
					
					$gallery = $gallery.'</div>';
				}
			}
		}
		
		$input_sample_tag['ui_element_type'] = 'custom';
		$input_sample_tag['ui_element_custom_content'] = $gallery;

		$ui_list[] = $input_sample_tag;
		
		return $ui_list;
	}
}