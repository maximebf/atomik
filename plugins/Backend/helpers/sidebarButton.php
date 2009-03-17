<?php

class SidebarButtonHelper
{
	public function sidebarButton($label, $url, $imageUrl = null)
	{
		if ($imageUrl !== null) {
			$label = sprintf('<img src="%s" />', $imageUrl) . $label;
		}
		return sprintf('<a class="sidebar-action" href="%s">%s</a>', $url, $label);
	}
}