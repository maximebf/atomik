<?php

class FormButtonsHelper
{
	public function formButtons($cancelUrl = 'javascript:history.back(-1)', $submitLabel = 'Save', $cancelLabel = 'Cancel')
	{
		$html = '<input type="submit" value="' . __($submitLabel) . '" />';
		if ($cancelUrl) {
			$html .= '<a href="' . $cancelUrl . '" class="form-link-button">' . __($cancelLabel) . '</a>';
		}
		return $html;
	}
}