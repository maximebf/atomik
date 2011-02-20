<?php echo '<?php'; ?>


$config = array_merge(array(
	<?php 
		$config = array();
		foreach ($this->config as $key => $value) {
			$config[] = sprintf("'%s' => %s", $key, $value);
		}
		echo implode(",\n\t", $config);
	?>

), $config);

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
function <?php echo $funcName = $this->name . '_on' . str_replace('::', '', $listener) ?>(<?php echo $spec['args'] ?>)
{
	throw new Exception('Not implemeted');
}
Atomik::listenEvent('<?php echo $listener ?>', '<?php echo $funcName ?>');

<?php endforeach; ?>