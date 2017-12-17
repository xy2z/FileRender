# FileRender

Presents a file in the browser from a non-public dir.

The file will be downloaded if not presentable (eg. `.dll`, `.exe` etc.)


## Requirements
- PHP 7.0 or above
- `php_exif` extension enabled (included in PHP)


## Installation
Install the latest version with [Composer](https://getcomposer.org).
```bash
$ composer require xy2z/file-render
```


## Basic Usage
```php
use xy2z\FileRender\FileRender;

$fr = new FileRender('/path/to/file.png');
$fr->render();
```


### Force Download
```php
$fr = new FileRender('/path/to/file.png');
$fr->force_download = true;
$fr->download_filename = 'renamed.png';
$fr->render();
```
