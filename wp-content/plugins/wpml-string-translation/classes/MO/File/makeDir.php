<?php

namespace WPML\ST\MO\File;

trait makeDir {

	/**
	 * @var \WP_Filesystem_Direct
	 */
	protected $filesystem;

	/** @return bool */
	public function maybeCreateSubdir() {
		$subdir = $this->getSubdir();

		if ( $this->filesystem->is_dir( $subdir ) && $this->filesystem->is_writable( $subdir ) ) {
			return true;
		}

		$chmod = defined( 'FS_CHMOD_DIR' ) ? FS_CHMOD_DIR : 0755;
		return $this->filesystem->mkdir( $subdir, $chmod );
	}

	/**
	 * This declaration throws a "Strict standards" warning in PHP 5.6.
	 * @todo: Remove the comment when we drop support for PHP 5.6.
	 */
	//abstract public static function getSubdir();
}

