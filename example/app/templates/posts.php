<!-- Atomik:Page(name="Home" var="page") -->
<!-- Atomik:ModelSet(name="Post" var="posts") -->

<h1><?php echo $page->title ?></h1>

<ul>
	<?php foreach ($posts as $post): ?>
		<li>
			<h1>
				<a href="post/<?php echo $post->id ?>">
					<span><?php echo $post->title ?></span>
				</a>
			</h1>
		</li>
	<?php endforeach; ?>
</ul>