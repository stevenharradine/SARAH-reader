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
			$selectedAttribute = $data['showFavorites'] ? ' selected="selected"' : '';

			$optionList = '';
			while ( ( $row = mysql_fetch_array ( $data['feed_labels_data'] ) ) != null ) {
				$label = $row['label'];

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