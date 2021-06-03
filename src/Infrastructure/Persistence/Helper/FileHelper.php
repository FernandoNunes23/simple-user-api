<?php

namespace App\Infrastructure\Persistence\Helper;

class FileHelper
{
	public function transformContentInArray(string $fileContent)
	{
		$fileContentAsArray = eval('return ' . $fileContent .';');

		return $fileContentAsArray ?? [];
	}

	public function transformArrayToString(array $data)
	{
		$dataAsString = var_export($data, true);

		return $dataAsString;
	}
}