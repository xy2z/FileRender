<?php

	namespace xy2z\FileRender;

	/**
	 * FileRender
	 *
	 * @author Alexander Pedersen <xy2z@protonmail.com>
	 *
	 * Gets files from a dir and presents them in the browser.
	 * This can be used to have an alias 'dir' that gets sent to php, and then PHP fetches the file via this script.
	 *
	 * Then files can be stored in a non-public dir, to make better security in case some files requires permissions.
	 *
	 */
	class FileRender {

		/**
		 * File path
		 *
		 * @var string
		 */
		protected $path;

		/**
		 * Path info like extension, basename, etc.
		 * See PHP's pathinfo()
		 *
		 * @var array
		 */
		protected $pathinfo;

		/**
		 * Known extensions
		 * These extensions will be rendered in the browser (like images and text)
		 * If the extension is not in this array, the file will be downloaded.
		 *
		 * @var array
		 */
		protected static $extensions = array();

		/**
		 * Force download file
		 *
		 * @var boolean
		 */
		public $force_download = false;

		/**
		 * Overwrite filename when downloading
		 *
		 * @var null
		 */
		public $download_filename = null;

		/**
		 * Constructor
		 *
		 * @param string $path Full/relative file path.
		 */
		public function __construct(string $path) {
			$this->path = $path;

			if (!file_exists($path)) {
				throw new \Exception('File not found: ' . $path);
			}

			$this->pathinfo = pathinfo($this->path);

			// Set extensions.
			self::$extensions = [
				# content-type => extensions
				'image' => ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'ico', 'tif', 'tiff', 'webp', 'svg'],
				'application/pdf' => ['pdf'],

				# Text
				'text/html' => ['htm', 'html'],
				'text/plain' => ['txt', 'info', 'json', 'map'],
				'text/css' => ['css'],
				'application/javascript' => ['js'],

				# Audio
				'audio/mpeg' => ['mp3'],
				'audio/wav' => ['wav'],
				'audio/flac' => ['flac'],
				'video/ogg' => ['ogg'],

				# Video
				'video/webm' => ['webm'],
				'video/mp4' => ['mp4']
			];
		}

		/**
		 * Render or download the file.
		 *
		 */
		public function render() {
			header('Content-Length: ' . filesize($this->path));

			// Check if the file is a known extension, that should be rendered (instead of downloaded)
			if (!$this->force_download) {
				foreach (self::$extensions as $type => $type_extensions) {
					if (in_array(strtolower($this->pathinfo['extension']), $type_extensions)) {
						header('Content-Disposition: inline; filename="' . $this->pathinfo['basename'] . '"');
						header('Content-type: ' . $this->get_mime_type($type));
						readfile($this->path);
						return;
					}
				}
			}

			// Not a known filetype, so it should be downloaded.
			$this->download();
		}

		/**
		 * Download the file
		 *
		 */
		protected function download() {
			$filename = $this->download_filename ?? $this->pathinfo['basename'];
			header('Content-Type: application/octet-stream');
			header('Content-Transfer-Encoding: Binary');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			readfile($this->path);
		}

		/**
		 * Get the MIME type for the file
		 *
		 * @param string $type File type, the key from the $extensions array
		 *
		 * @return string The content-type used in header().
		 */
		protected function get_mime_type(string $type) : string {
			if ($type == 'image') {
				// Image. Get mime type from PHP.
				return image_type_to_mime_type(exif_imagetype($this->path));
			}

			// Not image.
			return $type;
		}

	}
