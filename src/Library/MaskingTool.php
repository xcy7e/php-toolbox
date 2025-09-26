<?php

declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

/**
 * String masking utilities.
 */
class MaskingTool
{

	/**
	 * Masks an IBAN
	 *
	 * @param string $iban
	 * @param bool   $grouping   Split IBAN into blocks
	 * @param int    $blockSize  Character count of each block
	 * @param int    $maskLength Number of characters to mask
	 * @param int    $maskOffset Position to start masking (equals number of unmasked characters)
	 * @return string
	 */
	public static function maskIban(string $iban, bool $grouping = true, int $blockSize = 4, int $maskLength = 14, int $maskOffset = 4): string
	{
		$iban = strtoupper($iban);

		// remove spaces
		$iban = preg_replace('/\040/', '', $iban);

		if ($maskLength) // mask the iban
			$iban = substr_replace($iban, str_repeat('*', $maskLength), $maskOffset, $maskLength);

		if ($grouping)    // split iban into groups
			$iban = trim(preg_replace('/(.{' . $blockSize . '})/', '$1 ', $iban));

		return $iban;
	}

}