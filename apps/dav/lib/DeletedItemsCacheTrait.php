<?php
/**
 * @author Viktar Dubiniuk <dubiniuk@owncloud.com>
 *
 * @copyright Copyright (c) 2018, ownCloud GmbH
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */


namespace OCA\DAV;

use OCA\DAV\Connector\Sabre\Directory;
use OCA\DAV\Connector\Sabre\File;

/**
 * Class DeletedItemsCacheTrait
 *
 * Provides ability to store/retrieve deleted file ids by path
 *
 * @package OCA\DAV
 */
Trait DeletedItemsCacheTrait {

	/**
	 * @var int[]
	 */
	protected $deletedItems = [];

	/**
	 * Returns the INode object for the requested path
	 *
	 * @param string $path
	 *
	 * @return \Sabre\DAV\INode
	 */
	abstract public function getNodeForPath($path);

	/**
	 * Get fileId by path
	 *
	 * @param string $path
	 *
	 * @return int|false
	 */
	public function getDeletedItemFileId($path) {
		if (isset($this->deletedItems[$path])) {
			return $this->deletedItems[$path];
		}
		return false;
	}

	/**
	 * Store fileId before deletion
	 *
	 * @param string $path
	 *
	 * @return void
	 */
	public function beforeDelete($path) {
		try {
			$node = $this->getNodeForPath($path);
			if (($node instanceof Directory
				|| $node instanceof File)
				&& $node->getId()
			) {
				$this->deletedItems[$path] = $node->getId();
			}
		} catch (\Exception $e) {
			// do nothing, delete will throw the same exception anyway
		}
	}
}
