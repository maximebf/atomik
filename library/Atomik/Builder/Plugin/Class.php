<?php echo '<?php'; ?>


class <?php echo ucfirst($this->name) ?>Plugin
{
	/**
	 * Plugin's configuration
	 *
	 * @var array
	 */
	public static $config = array(
		<?php 
			$config = array();
			foreach ($this->config as $key => $value) {
				$config[] = sprintf("'%s' => %s", $key, $value);
			}
			echo implode(",\n\t\t", $config);
		?>
	
	);
	
	/**
	 * Plugin initialization
	 *
	 * @param array $config Plugin's configuration
	 */
	public static function start($config = array())
	{
		// merging default and user specified configuration 
		self::$config = array_merge(self::$config, $config);
	}
	<?php foreach ($this->listeners as $listener => $spec): ?>
	
	/**
	 * <?php echo $listener ?> handler
	 *
	 * <?php echo trim($spec['node']->description) ?>

	 *
	 <?php foreach ($spec['node']->children() as $name => $param): ?>
<?php if($name == 'description') continue; ?>
* @param <?php echo $param['type'] ?> <?php echo $param['name'] ?> <?php echo $param ?>

	 <?php endforeach; ?>
*/
	public static function on<?php echo str_replace('::', '', $listener) ?>(<?php echo $spec['args'] ?>)
	{
		throw new Exception('Not implemeted');
	}
	<?php endforeach; ?>

}