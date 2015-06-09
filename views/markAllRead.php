<?php
	require_once ($relative_base_path . 'views/ViewModel.php');
/*
		$data = array (
			'showFavorites' => $showFavorites,
			'label' => $_REQUEST['label']
		);
*/
	class MarkAllReadView extends View {
		public function render ($data) {
			if (!$data['showFavorites']) {
				$labelAttr = (isset ($data['label']) && $data['label'] != 'All') ? '&amp;label=' . $data['label'] : '';

				$maxItemId = ReaderManager::getAllArticlesMaxId(
					$data['showFavorites'],
					$data['label']
				);

				$icon = IconView::render( new IconModel ('empty-trash', 'Mark all as read'));

				return  "<a class=\"mark-as-read button\" href=\"./?action=markAllRead$labelAttr&amp;max_item_id=$maxItemId\">$icon</a>";
			}

			return '';
		}
	}