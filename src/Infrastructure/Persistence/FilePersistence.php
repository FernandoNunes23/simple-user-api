<?php


namespace App\Infrastructure\Persistence;


use App\Infrastructure\Persistence\Helper\FileHelper;

class FilePersistence
{
	const PERSISTENCE_FILE_PATH = __DIR__ . '/../../../var/data/';
	const INDEX_KEY_FILE = __DIR__ . '/../../../var/data/key.index';
	const KEY_FIELD = "key";
	const EMAIL_FIELD = "email";

	private $fileHelper;
	private $indexesToRollback;

	public function __construct(FileHelper $fileHelper)
	{
		$this->fileHelper = $fileHelper;
	}

	private function getLastIndexKey()
	{
		if (!file_exists(self::INDEX_KEY_FILE)) {
			return 0;
		}

		$fileContent = file_get_contents(self::INDEX_KEY_FILE);

		$indexFileArray = $this->fileHelper->transformContentInArray($fileContent);

		return array_key_last($indexFileArray);
	}

	private function getIndex($field, $key)
	{
		$indexFileName = $field . ".index";
		$indexFilePath = self::PERSISTENCE_FILE_PATH . $indexFileName;

		if (!file_exists($indexFilePath)) {
			return false;
		}

		$fileContent = file_get_contents($indexFilePath);

		$indexFileArray = $this->fileHelper->transformContentInArray($fileContent);

		$index = null;

		if (!empty($indexFileArray[$key])) {
			$index = $indexFileArray[$key];
		}

		return $index;
	}

	private function saveIndex($field, $key, $value, $operation="insert", $actualObject = null)
	{
		$indexFileName = $field . ".index";
		$indexFilePath = self::PERSISTENCE_FILE_PATH . $indexFileName;

		if (file_exists($indexFilePath)) {
			$fileContent = file_get_contents($indexFilePath);
		}

		$indexFileArray = [];

		if (!empty($fileContent)) {
			$indexFileArray = $this->fileHelper->transformContentInArray($fileContent);
		}

		if (!empty($indexFileArray[$key]) && $operation == "insert") {
			throw new \Exception("Index '{$field}' already exists with the same value.");
		}

		if ($operation == "update") {
			$this->deleteIndex(self::EMAIL_FIELD, $actualObject->getEmail());
			$fileContent = file_get_contents($indexFilePath);
			$indexFileArray = $this->fileHelper->transformContentInArray($fileContent);
		}

		$indexFileArray[$key] = $value;

		$this->indexesToRollback[$field] = 1;

		file_put_contents($indexFilePath, $this->fileHelper->transformArrayToString($indexFileArray));
	}

	private function deleteIndex($field, $key)
	{
		$indexFileName = $field . ".index";
		$indexFilePath = self::PERSISTENCE_FILE_PATH . $indexFileName;

		if (!file_exists($indexFilePath)) {
			return;
		}

		$fileContent = file_get_contents($indexFilePath);

		$indexFileArray = [];

		if (!empty($fileContent)) {
			$indexFileArray = $this->fileHelper->transformContentInArray($fileContent);
		}

		unset($indexFileArray[$key]);

		file_put_contents($indexFilePath, $this->fileHelper->transformArrayToString($indexFileArray));
	}

	private function rollback($object)
	{
		$this->delete($object, "id", "rollback");

		if (!empty($this->indexesToRollback[self::KEY_FIELD])) {
			$this->deleteIndex(self::KEY_FIELD, $object->getId());
		}

		if (!empty($this->indexesToRollback[self::EMAIL_FIELD])) {
			$this->deleteIndex(self::EMAIL_FIELD, $object->getEmail());
		}
	}

	public function delete($object, $field = "id", $operation = "delete")
	{
		if ($field === "id") {
			$filePath = $this->getIndex(self::KEY_FIELD, $object->getId());
		}else if ($field === "email") {
			$filePath = $this->getIndex(self::EMAIL_FIELD, $object->getEmail());
		} else {
			throw new \Exception("Cannot delete by field {$field}");
		}

		if ($operation == "delete") {
			$this->deleteIndex(self::KEY_FIELD, $object->getId());
			$this->deleteIndex(self::EMAIL_FIELD, $object->getEmail());
		}

		unlink($filePath);

		return true;
	}

	public function update($object)
	{
		if (!file_exists(self::INDEX_KEY_FILE)) {
			return [];
		}

		$fileContent = file_get_contents(self::INDEX_KEY_FILE);

		$indexFileArray = $this->fileHelper->transformContentInArray($fileContent);

		if ($this->getIndex(self::EMAIL_FIELD, $object->getEmail())) {
			$objectFoundIndex = $this->getIndex(self::EMAIL_FIELD, $object->getEmail());
			$objectFound = unserialize(file_get_contents($objectFoundIndex));

			if ($objectFound->getId() != $object->getId()) {
				throw new \Exception("Index email already exists.");
			}
		}

		$actualObject = unserialize(file_get_contents($indexFileArray[$object->getId()]));

		if ($actualObject->getEmail() != $object->getEmail()) {
			$this->saveIndex(self::EMAIL_FIELD, $object->getEmail(), $indexFileArray[$object->getId()], "update", $actualObject);
		}

		file_put_contents($indexFileArray[$object->getId()], serialize($object));
	}

	public function save($object)
	{
		$uniqueKey = $this->getLastIndexKey() + 1;

		if(empty($object->getEmail())) {
			throw new \InvalidArgumentException("Field 'email' is a index and cannot be null.");
		}

		$object->setId($uniqueKey);
		$row = serialize($object);

		$objectFileName = self::PERSISTENCE_FILE_PATH . $uniqueKey . ".txt";

		try {
			file_put_contents($objectFileName, $row);
			$this->saveIndex(self::KEY_FIELD, $uniqueKey, $objectFileName);
			$this->saveIndex(self::EMAIL_FIELD, $object->getEmail(), $objectFileName);
		} catch (\Exception $e) {
			$this->rollback($object);
			throw $e;
		}

		return $object;
	}

	public function getByEmail($email)
	{
		$indexFileName =  "email.index";
		$indexFilePath = self::PERSISTENCE_FILE_PATH . $indexFileName;

		if (!file_exists($indexFilePath)) {
			return [];
		}

		$fileContent = file_get_contents($indexFilePath);

		$indexFileArray = $this->fileHelper->transformContentInArray($fileContent);

		if (empty($indexFileArray[$email])) {
			return [];
		}

		return unserialize(file_get_contents($indexFileArray[$email]));
	}

	public function get($key)
	{
		if (!file_exists(self::INDEX_KEY_FILE)) {
			return [];
		}

		$fileContent = file_get_contents(self::INDEX_KEY_FILE);

		$indexFileArray = $this->fileHelper->transformContentInArray($fileContent);

		if (empty($indexFileArray[$key])) {
			return [];
		}

		return unserialize(file_get_contents($indexFileArray[$key]));
	}

	public function getAll()
	{
		if (!file_exists(self::INDEX_KEY_FILE)) {
			return [];
		}

		$fileContent = file_get_contents(self::INDEX_KEY_FILE);

		$indexFileArray = $this->fileHelper->transformContentInArray($fileContent);

		$data = [];

		foreach($indexFileArray as $dataFile) {
			$data[] = unserialize(file_get_contents($dataFile));
		}

		return $data;
	}
}