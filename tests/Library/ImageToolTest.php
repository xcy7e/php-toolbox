<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
use Xcy7e\PhpToolbox\Library\ImageTool;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class ImageToolTest extends TestCase
{
    private function createTinyPng(string $path): void
    {
        // 1x1 transparent PNG
		$image = imagecreatetruecolor(1, 1);
		imagealphablending($image, false);
		imagesavealpha($image, true);
		$transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
		imagefill($image, 0, 0, $transparent);
		imagepng($image, $path);
    }

    public function testReduceResolutionIfNecessaryNoChangeOnSmallImage()
    {
        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick not available');
        }
        $tmp = tempnam(sys_get_temp_dir(), 'img');
		$png = $tmp . '.png';
        $this->createTinyPng($png);
        $file = new File($png);
        try {
            $changed = ImageTool::reduceResolutionIfNecessary($file, 100, 100);
            $this->assertFalse($changed, '1x1 image should not be resized');
        } finally {
            @unlink($png);
        }
    }

    public function testConvertImageToJpgSkipsIfImagineMissing()
    {
        if (!class_exists('Imagick')) {
            $this->markTestSkipped('Imagick not available');
        }
        $src = tempnam(sys_get_temp_dir(), 'img');
		$png = $src . '.png';
        $dst = $src . '.jpg';
        $this->createTinyPng($png);
		$file = new File($png);
        try {
            ImageTool::convertImageToJpg($file->getPathname(), $dst, 80);
            $this->assertFileExists($dst);
        } finally {
            @unlink($png);
            if (is_file($dst)) @unlink($dst);
        }
    }
}