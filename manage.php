<?php
	require_once '../../views/_secureHead.php';
	require_once 'views/backButton.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$id = request_isset ('id');
		$name = request_isset ('name');
		$label = request_isset ('label');
		$rss = request_isset ('rss');

		switch ($page_action) {
			case ('update_by_id') :
				$db_update_success = ReaderManager::updateFeed ($id, $name, $label, $rss);
				break;
			case ('add_feed') :
				$db_update_success = ReaderManager::addFeed ($name, $label, $rss);
				break;
			case ('delete_by_id') :
				$db_delete_success = ReaderManager::deleteFeed ($id);
				break;
		}

		$feed_data = ReaderManager::getFeeds();

		$feedTableModel = new TableModel ('Feeds');

		while (($feed_row = mysql_fetch_array( $feed_data )) != null) {
			$feedTableModel->addRow ( array (
				TableView2::createCell ('name', $feed_row['name']),
				TableView2::createCell ('label', $feed_row['label']),
				TableView2::createCell ('rss', $feed_row['rss']),
				TableView2::createEdit ($feed_row['FEED_ID'])
			));
		}

		$page_title = 'Manage | Reader';
		
		$alt_menu = getAddButton() . BackButtonView::render(null);

		$addModel = new AddModel('Add', 'add_feed');
		$addModel->addRow ('name', 'Feed name');
		$addModel->addRow ('rss', 'RSS');
		$addModel->addRow ('label', 'Label');
	
		$views_to_load = array();
		$views_to_load[] = ' ' . AddView2::render($addModel);
		$views_to_load[] = ' ' . TableView2::render($feedTableModel);
		
		include $relative_base_path . 'views/_generic.php';
	}