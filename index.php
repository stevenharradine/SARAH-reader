<?php
	require_once '../../views/_secureHead.php';
	require_once 'views/reader.php';
	require_once 'views/selectLabel.php';
	require_once 'views/markAllRead.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$showFavorites = request_isset ('showFavorites');

		require 'pageSwitching.php';

		$page_title = 'Reader';

		$allArticles = ReaderManager::getAllArticles (
			$showFavorites,
			$label
		);
		
		// build out app specific menu
		$alt_menu = SelectLabelView::render( array (
			'feed_labels_data' => ReaderManager::getLabels(),	// all labels
			'label' => $label,									// the selected label
			'showFavorites' => $showFavorites					// is the favorite option selected
		)) . (count ($allArticles) > 0 ? MarkAllReadView::render( array (
			'showFavorites' => $showFavorites,					// is the favorite option selected
			'label' => $label									// the selected label
		)) : '') . ButtonView::render ( new ButtonModel(
			IconView::render( new IconModel ('archive', 'Manage RSS')),
			'manage.php',										// button link
			'manage'											// button class
		)) . ButtonView::render ( new ButtonModel(
			IconView::render( new IconModel ('search', 'Search')),
			'search.php' .										// button link (ammending either label or favorite, only one can be selected)
				($label != null && $label != 'All' ? "?label=$label" : '') . 
				($showFavorites != null && $showFavorites == '1' ? "?label=Favorites" : ''),
			'search'											// button class
		));

		// create data model for add new feed
		$addModel = new AddModel('Add', 'add_feed');
		$addModel->addRow ('feed_name', 'Feed name');
		$addModel->addRow ('rss', 'RSS');
		$addModel->addRow ('label', 'Label');
	
		// load views
		$views_to_load = array();
		$views_to_load[] = ' ' . AddView2::render($addModel);
		$views_to_load[] = ' ' . ReaderView::render($allArticles);
		
		// render the page
		include $relative_base_path . 'views/_generic.php';
	}
