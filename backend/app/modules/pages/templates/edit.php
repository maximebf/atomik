<div id="main-wrapper">
	<div id="main">
		<form action="<?php echo Atomik::url('pages/save'); ?>" method="post" class="form form-content">
    		<h1 id="page-title">
    		    <span id="page-title-span"><?php echo __($page->getName()); ?></span>
    		    <a href="javascript:;" id="edit-title">edit</a>
    			<input type="hidden" name="name" id="page-name" value="<?php echo $page->getName(); ?>" />
    		</h1>
    		<div id="content">
    				<input type="hidden" name="id" value="<?php echo $page->getId(); ?>" />
    				<input type="hidden" name="version" value="<?php echo $page->getVersion(); ?>" />
    				<input type="hidden" name="lang" value="<?php echo $page->getLanguage(); ?>" />
    				<dl>
    					<?php foreach ($page->getFields() as $field): ?>
    						<dt>
    							<?php echo __($field['label']); ?>
    						</dt>
    						<dd>
    							<?php
    								/* prints the input associated to the field type */
    								switch ($field['type']) {
    									case 'input':
    										echo '<input name="fields[' . $field['id'] . ']" type="text" value="' 
    											. $field['value'] . '" class="form-input" />';
    										break;
    									
    									case 'textarea':
    										echo '<textarea name="fields[' . $field['id'] . ']" class="form-textarea">' 
    											. $field['value'] . '</textarea>';
    										break;
    								}
    							?>
    						</dd>
    					<?php endforeach; ?>
    					<dt></dt>
    					<dd class="buttons">
    						<input type="submit" value="<?php echo __('Save'); ?>" class="form-button-big" />
    						<?php echo __('or'); ?> 
    						<a href="<?php echo Atomik::url('pages/index'); ?>" class="form-link-button">
    							<?php echo __('Cancel'); ?>
    						</a>
    					</dd>
    				</dl>
    		</div>
		</form>
	</div>
</div>
<div id="sidebar-wrapper">
	<div id="sidebar">
	
		<a class="sidebar-action" href="javascript:;" id="action-create-version">
			<img src="<?php echo Atomik::url('images/page.png') ?>" />
			<?php echo __('Create a new version') ?>
		</a>
		<div class="sidebar-block" style="display:none">
			<form action="<?php echo Atomik::url('pages/createVersion'); ?>" method="post" style="text-align:right">
				<input type="hidden" name="id" value="<?php echo $page->getId(); ?>" />
				<div style="text-align: left"><?php echo __('Notes for the new version') ?>:</div>
				<textarea name="note" class="form-textarea" style="width: 299px; height: 50px"></textarea>
				<a href="javascript:;" class="form-link-button" id="create-version-cancel">
					<?php echo __('Cancel'); ?>
				</a> <?php echo __('or') ?> 
				<input type="submit" value="<?php echo __('Create'); ?>" class="form-button" />
			</form>
		</div>
		
		<div class="sidebar-block">
			<h2><?php echo __('Status'); ?></h2>
			<select style="width: 100%">
				<option selected="selected">Published</option>
				<option>Unpublished</option>
			</select>
		</div>
		
		<div class="sidebar-block">
			<h2><?php echo __('Versions'); ?></h2>
			<strong><?php echo __('You are currently editing version') ?>:</strong>
			<ul>
				<li>
					<a href="<?php echo Atomik::url('pages/edit?id=' . $page->getId() . '&version=' . $page->getVersion()) ?>">
				        <?php echo $page->getVersion() . ': ' . $page->getVersionInfo() ?>
				    </a>
				</li>
			</ul>
			<?php if (count($versions = $page->getVersions()) - 1 > 0): ?>
    			<?php echo __('Other version of this page') ?>:
    			<ul>
    				<?php
                        foreach ($versions as $version) {
                            if ($version['version'] != $page->getVersion()) {
                                echo '<li><a href="' . Atomik::url('pages/edit?id=' . $page->getId() 
                                   . '&version=' . $version['version']) . '">' . $version['version'] 
                                   . ': ' . $version['note'] . '</a></li>';
                            }
                        }
    				?>
    			</ul>
			<?php endif; ?>
		</div>
		
		<div class="sidebar-block">
			<h2><?php echo __('Language'); ?></h2>
			<?php echo __('You are currently editing this page in') ?>:
			<ul>
				<li>
					<a href="<?php echo Atomik::url('pages/edit?id=' . $page->getId() . '&version=' . $page->getVersion() . '&lang=' . $page->getLanguage()) ?>">
					    <?php echo $page->getLanguage() ?>
					</a>
				</li>
			</ul>
			<?php if (count($pageLangs) - 1 > 0): ?>
				<?php echo __('This page also exist in') ?>:
    			<ul>
    				<?php
                        foreach ($pageLangs as $lang) {
                            if ($lang != $page->getLanguage()) {
                                echo '<li><a href="' . Atomik::url('pages/edit?id=' . $page->getId() 
                                   . '&version=' . $page->getVersion() . '&lang=' . $lang) . '">' 
                                   . $lang . '</a></li>';
                            }
                        }
    				?>
    			</ul>
			<?php endif; ?>
			<?php echo __('Edit this page in a new language') ?>: 
			<select id="page-new-lang">
				<option selected="selected"></option>
				<?php
                    foreach (LangPlugin::getDefinedLanguages($langDirs) as $lang) {
                        if (!in_array($lang, $pageLangs)) {
                            echo '<option value="' . $lang . '">' . $lang . '</option>';
                        }
                    }
				?>
			</select>
		</div>
		
		<?php Atomik::fireEvent('Backend::Pages::Sidebar', array($page)); ?>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
	
		$('#edit-title').click(function() {
			var el = $(this);
			var title = $('#page-title-span');
			
			var input = $('<input type="text" value="' + title.text() + '" />');
			var link = $('<a href="javascript:;">ok</a>');
			
			link.click(function() {
				if (input.val().length == 0) {
					Atomik.addMessage('The page name can\'t be empty', 'error');
					return;
				}
				
				$('#page-name').val(input.val());
				title.html(input.val());
				el.show();
			});
			
			el.hide();
			title.empty();
			title.append(input);
			title.append(link);
		});
		
    	$('#action-create-version').click(function() {
    		$(this).next().slideDown('slow');
    	});
    	
    	$('#create-version-cancel').click(function() {
    		$(this).parent().parent().slideUp('slow');
    	});
		
		$('#page-new-lang').change(function() {
			if ($(this).val() != '') {
				document.location = '<?php echo Atomik::url('pages/edit?id=' . $page->getId() . '&version=' . $page->getVersion() . '&lang=') ?>' + $(this).val();
			}
		});
		
    });
</script>
