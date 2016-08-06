<?php

abstract class AbstractHandlerCore
{
	abstract public function getRequiredInputs();
	abstract public function processInputs($inputs);
	abstract public function getReadableStatusString($lang);
	abstract public function getOutputTypes();
}
