<?php
	require_once '../../views/_secureHead.php';
	require_once 'views/backButton.php';
	require_once 'views/searchField.php';
	require_once 'views/reader.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$searchQuery = request_isset ('searchQuery');

		require 'pageSwitching.php';

		$page_title = 'Search | Reader';
		
		$alt_menu = BackButtonView::render(
			$label != null ?			// if label is passed into the page
			array ('label' => $label) :	// add it as a url parameter
			null						// otherwise do nothing
		);
	
		$views_to_load = array();

		$views_to_load[] = ' ' . SearchFieldView::render( array (	// load the search form
			'searchQuery' => $searchQuery,	// the search term from the url
			'label' => $label				// the selected label from the dropdown
		));

		if ($searchQuery != null) {							// if a search query is defined
			$views_to_load[] = ' ' . ReaderView::render(	// load the articles
				ReaderManager::getArticlesBySearch (		// perform a search
					$searchQuery,	// the search term from the url
					$label			// the selected label from the dropdown
				)
			);
		}
		
		include $relative_base_path . 'views/_generic.php';
	}