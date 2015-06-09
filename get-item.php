<?php
	require_once '../../views/_secureHead.php';
	require_once 'views/backButton.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$ITEM_ID = request_isset ('item_id');

		ReaderManager::setViewed ($ITEM_ID);
		$article = ReaderManager::getArticle ($ITEM_ID);

		$page_title = $article['title'] . ' | Reader';
		$alt_menu = BackButtonView::render(null);

		$views_to_load = array();
		$views_to_load[] = ' ' . $article['description'];

		include $relative_base_path . 'views/_generic.php';
	}