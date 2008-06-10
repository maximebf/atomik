<?php content_page('Accueil') ?>

<h1><?php echo content_input('title', 'Title') ?></h1>

<p id="intro" class="editable toto">
	<?php echo content_textarea('intro', 'Intro text') ?>
</p>

<p>
	<?php echo content_input('author', 'Auteur') ?>
</p>

<?php echo __('hello world'); ?><br />
<?php echo __('hello %s', 'toto'); ?>
hello

<script type="text/javascript">
	$(document).ready(function() {
		$('#button').click(function() {
			$.getJSON('index.php?action=index', function(data) {
		
			});
		});
	});
</script>
<button id="button">clicl</button>
