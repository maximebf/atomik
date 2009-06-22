<?php

class Atomik_Backend_Models
{
	public static function getModelBuilder($name)
	{
		if (!class_exists($name, false)) {
			$modelFile = Atomik::path($name . '.php', DbPlugin::$config['model_dirs']);
			include $modelFile;
		}
		
		return Atomik_Model_Builder_Factory::get($name);
	}
	
	public static function getModels()
	{
		$models = array();
		$modelsDirs = (array) DbPlugin::$config['model_dirs'];
		
		foreach ($modelsDirs as $dir) {
			if (file_exists($dir)) {
				$models = array_merge($models, self::getModelsFromDir($dir));
			}
		}
		
		return $models;
	}
	
	public static function getModelsFromDir($dir, $prefix = '')
	{
		$models = array();
		foreach (new DirectoryIterator($dir) as $file) {
			if (substr($file->getFilename(), 0, 1) == '.') {
				continue;
			}
			
			if ($file->isDir()) {
				$models[] = array(
					'name' => $file->getFilename(),
					'models' => self::getModelsFromDir($file->getPathname(), $file->getFilename() . '_')
				);
				continue;
			}
			
			$filename = $file->getFilename();
			$extension = strtolower(substr($filename, strrpos($filename, '.') + 1));
			
			if ($extension != 'php') {
				continue;
			}
			
			$name = $prefix . substr($filename, 0, -strlen($extension) - 1);
			$builder = self::getModelBuilder($name);
			if ($builder->getOption('admin-ignore', false)) {
				continue;
			}
			
			$models[] = array(
				'name' => $name,
				'path' => $file->getPathname()
			);
		}
		return $models;
	}
}