<a href="<?php echo Atomik::url('posts') ?>">Back to posts</a>

<div>
	<h1><?php echo $post->title ?></h1>
	<p>
		<?php echo $post->body ?>
	</p>
	
	<hr />
	<h3>Comments</h3>
	
	<ul>
		<?php foreach ($post->comments as $comment): ?>
			<li>
				<p><?php echo $comment->message ?></p>
			</li>
		<?php endforeach; ?>
	</ul>
	
	<hr />
	<h3>Add a comment:</h3>
	<?php echo $form ?>
</div>