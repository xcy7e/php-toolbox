<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

use Imagine\Imagick\Imagine;
use Imagine\Image\Box;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Image manipulation utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class ImageTool
{

	/**
	 * Scale an image, keeping its aspect-ratio
	 *
	 * **Overrides the file `$filename`**
	 *
	 * @param string $filename  Absolute path to file
	 * @param int    $maxWidth  Portrait-Image: set `maxWidth`
	 * @param int    $maxHeight Landscape-Image: set `maxHeight`
	 */
	public static function resize(string $filename, int $maxWidth, int $maxHeight): void
	{
		$imagine = new Imagine();

		list($iwidth, $iheight) = getimagesize($filename);
		$ratio  = $iwidth / $iheight;
		$width  = $maxWidth;
		$height = $maxHeight;
		if ($width / $height > $ratio) {
			$width = $height * $ratio;
		} else {
			$height = $width / $ratio;
		}

		$photo = $imagine->open($filename);
		$photo->resize(new Box($width, $height))->save($filename);
	}

	/**
	 * Converts any image to JPG
	 * **Reduces its quality and optimizes its compression**
	 *
	 * @param string $filename            Absolute path to source file
	 * @param string $newFilename         Absolute path to new file
	 * @param int    $imageConvertQuality From 0 to 100
	 * @return void
	 */
	public static function convertImageToJpg(string $filename, string $newFilename, int $imageConvertQuality = 100): void
	{
		$imagine = new Imagine();
		$photo   = $imagine->open($filename);

		$imageConvertQuality = min(max($imageConvertQuality, 0), 100);    // 0-100

		// Save with adjusted quality/compression
		$photo
			->resize(
				new Box(
					$photo->getSize()->getWidth(),
					$photo->getSize()->getHeight()
				)
			)
			->save($newFilename, [
				'jpeg_quality'          => $imageConvertQuality,
				'png_compression_level' => 9,
				'webp_quality'          => $imageConvertQuality,
			]);
	}

	/**
	 * Scales an image down if necessary, according to `$maxWidth` and `$maxHeight`
	 *
	 * Overrides the file `$file` when resizing occurs.
	 *
	 * @param File $file      Absolute path to file
	 * @param int  $maxWidth  Maximum allowed width
	 * @param int  $maxHeight Maximum allowed height
	 * @return bool           Indicates if the file was changed (resized)
	 */
	public static function reduceResolutionIfNecessary(File $file, int $maxWidth, int $maxHeight): bool
	{
		$path = $file->getPathname();
		$size = @getimagesize($path);
		if (!is_array($size) || !isset($size[0], $size[1])) {
			// Unable to determine size (possibly invalid image); do nothing gracefully
			return false;
		}

		[$width, $height] = [$size[0], $size[1]];

		// If already within bounds, nothing to do
		if ($width <= $maxWidth && $height <= $maxHeight) {
			return false;
		}

		// Resize within the bounding box
		ImageTool::resize($path, $maxWidth, $maxHeight);
		return true;
	}

}