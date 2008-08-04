<!-- Atomik:Model(name="Post" var="post") -->
<!-- Atomik:Reference(model="Post" has-many="Comment" as="comments" var="comment") -->
<!-- Atomik:Reference(model="Post" has-one="User" as="user") -->

<a href="<?php echo Atomik::url('posts') ?>">Back to posts</a>

<div>
	<!-- Atomik:Field(name="Title" type="text") -->
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
	
	<form action="" method="post">
		Message: <textarea rows="2" cols="100" name="comment[message]"></textarea>
		<input type="submit" value="Add" />
	</form>
</div>