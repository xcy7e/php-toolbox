<?php

declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

use Imagick;
use ImagickException;
use Imagine\Imagick\Imagine;
use Imagine\Image\Box;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Image manipulation utilities.
 */
class ImageTool
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
	 * **Overrides the file `$file`**
	 *
	 * @param File $file Absolute path to file
	 * @param int  $maxWidth
	 * @param int  $maxHeight
	 * @return bool                Indicates if the file was changed
	 * @throws ImagickException
	 */
	public static function reduceResolutionIfNecessary(File $file, int $maxWidth, int $maxHeight): bool
	{
		$image = new Imagick($file->getPathname());
		if ($image->getImageWidth() > $image->getImageHeight()) {
			// Landscape
			if ($image->getImageWidth() > $maxWidth) {
				ImageTool::resize($file->getPathname(), $maxWidth, $maxHeight);
				return true;
			}
		} elseif ($image->getImageWidth() < $image->getImageHeight()) {
			// Portrait
			if ($image->getImageHeight() > $maxWidth) {
				ImageTool::resize($file->getPathname(), $maxHeight, $maxWidth);
				return true;
			}
		}
		return false;
	}

}