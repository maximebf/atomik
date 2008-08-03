<h1><?php echo $post->title ?></h1>
<p>
	<?php echo $post->body ?>
</p>
<hr />
<h3>Comments</h3>
<ul>
	<?php foreach ($post->comments as $comment): ?>
		<li>
			<?php echo $comment->message ?>
		</li>
	<?php endforeach; ?>
</ul>