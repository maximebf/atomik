<!-- Atomik:ListModel(name="Post" var="posts") -->

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

<form action="" method="post">
	<div>
		<div>Title:</div>
		<input type="text" name="post[title]" />
	</div>
	<div>
		<div>Body:</div>
		<textarea rows="5" cols="100" name="post[body]"></textarea>
	</div>
	<div>
		<input type="submit" value="Add" />
	</div>
</form>