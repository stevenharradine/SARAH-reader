<?php
	require_once ('../../views/ViewModel.php');

	class BackButtonView extends View {
		public function render ($data) {
			$labelUrlVariable = isset ($data['label']) ? '?label=' . $data['label'] : '';

			return ButtonView::render ( new ButtonModel(
				IconView::render( new IconModel ('arrow-left', 'Back')),	// label
				'./' . $labelUrlVariable,									// link
				'reader'													// class
			));
		}
	}