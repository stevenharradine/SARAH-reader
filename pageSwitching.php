<?php
	$ITEM_ID = request_isset ('item_id');
	$label = request_isset ('label');
	$maxItemId = request_isset ('max_item_id');

	switch ($page_action) {
		case ('favoriteToggle') :
			ReaderManager::favoriteToggle ($ITEM_ID);
			break;
		case ('markAllRead') :
			ReaderManager::markAllRead ($label, $maxItemId);
			break;
		case ('readFullArticle') :
			header ('location: ' . ReaderManager::getArticle ($ITEM_ID)['url']);
			break;
	}