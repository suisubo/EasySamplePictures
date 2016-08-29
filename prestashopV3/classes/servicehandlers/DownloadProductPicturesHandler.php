<?php


class DownloadProductPicturesHandlerCore extends AbstractHandler
{
	public function processUIInputs($context_inputs, &$outputs, $service_parameters, &$error_info)
	{
		return AbstractHandler::PROCESS_SUCCESS;
	}
	public function getReadableStatusString($context_inputs, $service_parameters, $lang)
	{
		return '';
	}
	public function getAdditionalUIElements($service_parameters)
	{
		return '';
	}
}