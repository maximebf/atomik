<?= $this->form() ?>
    <dl>
    	<?php foreach ($fields as $field): ?>
        <dt><?= $this->modelFormLabel($field) ?></dt>
        <dd><?= $this->modelFormInput($field, $model) ?></dd>
    	<?php endforeach; ?>
        <dt></dt>
        <dd><?= $this->formButtons($buttonLabel) ?></dd>
    </dl>
</form>