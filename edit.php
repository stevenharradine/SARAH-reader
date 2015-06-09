<?php
	require_once '../../views/_secureHead.php';
	require_once $relative_base_path . 'models/edit.php';

	if( isset ($sessionManager) && $sessionManager->isAuthorized () ) {
		$id = request_isset ('id');

		$record = ReaderManager::getFeed ($id);

		$page_title = 'Edit | Bookmarks';

		// build edit view
		$editModel = new EditModel ('Edit', 'update_by_id', $id, 'manage.php');
		$editModel->addRow ('name', 'Name', $record['name'] );
		$editModel->addRow ('label', 'Label', $record['label'] );
		$editModel->addRow ('rss', 'RSS', $record['rss'] );

		$views_to_load = array();
		$views_to_load[] = ' ' . EditView2::render($editModel);

		include $relative_base_path . 'views/_generic.php';
	}
?>