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
						mkdir($target_dir, '0777', true);
						$target_file = $target_dir.'picture_'.$j.'.'.pathinfo($_FILES['sample_photo'.$i]["name"][$j], PATHINFO_EXTENSION);
						move_uploaded_file($_FILES['sample_photo'.$i]["tmp_name"][$j], $target_file);
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
}
