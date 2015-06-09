<?php
	require_once ('../../views/ViewModel.php');

	class SearchFieldView extends View {
		public function render ($data) {
			$searchField = '<input type="text" value="' . ($data['searchQuery'] != null || $data['searchQuery'] != '' ? $data['searchQuery'] : '') . '" name="searchQuery" id="searchQuery" autofocus="autofocus" />';
			$labelField = $data['label'] != null ? '<input type="hidden" name="label" value="' . $data['label'] . '" />' : '';

			$output = '';

			$output .= <<<EOD
	<form action="#" method="POST" class="search">
		$labelField
		<label for="searchQuery">Search</label>
		$searchField
		<input type="submit" value="Search" />
	</form>
EOD;

			return $output;
		}
	}