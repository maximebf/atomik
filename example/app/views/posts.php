<h1>Posts</h1>
<ul>
	<?php foreach ($posts as $post): ?>
		<li>
			<a href="<?php echo Atomik::url('post', array('id' => $post->id)) ?>">
				<span><?php echo $post->title ?></span>
			</a>
		</li>
	<?php endforeach; ?>
</ul>

<hr />
<h2>Add a post</h2>
<?php echo $form ?>