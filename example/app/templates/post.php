<!-- Atomik:Model(name="Post" var="post") -->
<!-- Atomik:Reference(model="Post" has-many="Comment" as="comments" var="comment") -->
<!-- Atomik:Reference(model="Post" has-one="User" as="user") -->

<div>
	<!-- Atomik:Field(name="Title" type="text") -->
	<h1><?php echo $post->title ?></h1>
	<span>
		<?php echo $post->user->name ?>
	</span>
	<p>
		<?php echo $post->body ?>
	</p>
	<ul>
		<?php foreach ($post->comments as $comment): ?>
			<li>
				<h3><?php echo $comment->title ?></h3>
				<p><?php echo $comment->text ?></p>
			</li>
		<?php endforeach; ?>
	</ul>
</div>