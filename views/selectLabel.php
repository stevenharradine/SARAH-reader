<?php
	require_once ($relative_base_path . 'views/ViewModel.php');
/*
		$data = array (
			'showFavorites' => $showFavorites,
			'feed_labels_data' => $feed_labels_data,
			'label' => $_REQUEST['label']
		);
*/
	class SelectLabelView extends View {
		public function render ($data) {
			global $link;
			$selectedAttribute = $data['showFavorites'] ? ' selected="selected"' : '';

			$optionList = '';
			for ($i = 0; $i < count($data['feed_labels_data']); $i++) {
				$label = $data['feed_labels_data'][$i];

				$count = ReaderManager::getCount($label);
				
				$optionList .= '		<option value="' . $label . '"' . ( (isset ($data['label']) && $label == $data['label']) ? ' selected="selected"' : '') . '>' . $label . ' (' . $count . ')</option>';
			}

			return <<<EOD
	<select id="choose-label">
		<option>All</option>
		<option value="favorite" $selectedAttribute>Favorites</option>
		<option value="reload">Reload</option>
		<option disabled="disabled">------</option>
		$optionList
	</select>
EOD;
		}
	}