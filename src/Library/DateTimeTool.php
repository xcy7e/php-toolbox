<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

use DateTimeInterface;
use Exception;
use JetBrains\PhpStorm\ArrayShape;

/**
 * DateTime utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class DateTimeTool
{

	/**
	 * Evaluates the diff between `$date` and *now* in **minutes**
	 *
	 * @param string|DateTimeInterface $date
	 * @return int
	 * @throws Exception
	 */
	public static function getMinutesDiff(string|DateTimeInterface $date): int
	{
		$now = new \DateTime();
		if ($date instanceof DateTimeInterface === false) {
			$date = new \DateTime(date("d.m.Y H:i:s", strtotime($date)));
		}
		$diff = $now->diff($date);

		return ($diff->d * 24) + ($diff->h * 60) + $diff->i;
	}

	/**
	 * Evaluates the diff between `$date` and *now* in **days**
	 *
	 * @param string|DateTimeInterface $date
	 * @return int|null
	 * @throws Exception
	 */
	public static function getDaysDiff(string|DateTimeInterface $date): ?int
	{
		if ($date instanceof DateTimeInterface === false) {
			$date = new \DateTime(date("d.m.Y H:i:s", strtotime($date)));
		}
		$now = (new \DateTime())->setTime(0, 0);

		return (int)$now->diff($date->setTime(0, 0))->format('%R%a') - 1;
	}

	/**
	 * Translates `$date` in a readable text, if the date is within 8 days close to *now* (prior or past)
	 * e.g. "yesterday", "in 2 days", "last week", etc.
	 *
	 * @param string|DateTimeInterface|null $date      (null = *now*)
	 * @param bool                          $informalDate
	 * @param string                        $dateFormat
	 * @param int                           $rangeDays (max: 8) Max distance in days to apply humanized translations
	 *                                                 (both past and future).
	 * @param string                        $locale    Locale for translations, defaults to English ("en_US").
	 *                                                 Examples: "de_DE", "fr_FR".
	 * @return string
	 * @throws Exception
	 */
	public static function translateDateDiff(
		null|string|DateTimeInterface $date = null,
		bool                          $informalDate = true,
		string                        $dateFormat = 'd.m.Y',
		int                           $rangeDays = 8,
		string                        $locale = 'en_US'
	): string
	{
		$date = $date ?? new \DateTime();    // fallback: now

		if ($date instanceof DateTimeInterface === false) {
			$date = new \DateTime(date('d.m.Y H:i:s', strtotime($date)));
		}

		// Locale-aware formatter (defaults to English)
		$formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
		$formatter->setPattern($dateFormat);

		$now  = (new \DateTime())->setTime(0, 0, 0);
		$date = $date->setTime(0, 0, 0);

		$diffDays = (int)$now->diff($date)->format('%R%a'); // signed day difference

		// If outside configured range, return formatted date only
		if (abs($diffDays) > $rangeDays) {
			return $formatter->format($date);
		}

		// Localized phrases with graceful fallback to English
		$dict = self::getRelativeDateDictionary($locale);

		// Build string according to diff
		return match ($diffDays) {
			-8, -7, -6, -5 => $informalDate ? $dict['last_week'] : $dict['since_1_week'],
			-4 => $informalDate ? $formatter->format($date) : $dict['since_n_days'](4),
			-3 => $informalDate ? $formatter->format($date) : $dict['since_n_days'](3),
			-2 => $informalDate ? $dict['day_before_yesterday'] : $dict['since_n_days'](2),
			-1 => $informalDate ? $dict['yesterday'] : $dict['since_yesterday'],
			0 => $dict['today'],
			1 => $dict['tomorrow'],
			2 => $informalDate ? $dict['day_after_tomorrow'] : $dict['in_n_days'](2),
			3 => $informalDate ? $formatter->format($date) : $dict['in_n_days'](3),
			4 => $informalDate ? $formatter->format($date) : $dict['in_n_days'](4),
			5, 6, 7, 8 => $informalDate ? $dict['next_week'] : $dict['in_1_week'],
			default => $formatter->format($date),
		};
	}

	/**
	 * Returns a small i18n dictionary for relative date phrases.
	 * Falls back to English when a key is missing.
	 *
	 * @param string $locale           BCP-47/ICU locale like "en_US", "de-DE", etc.
	 *                                 (natively supported: `en`, `de`, `fr`, `es`, `it`)
	 *                                 if unsupported or unsuitable, you can provide your own `$translations`
	 * @param null|array{
	 *    last_week: string,
	 *    since_1_week: string,
	 *    since_n_days: callable(int): string,
	 *    day_before_yesterday: string,
	 *    yesterday: string,
	 *    since_yesterday: string,
	 *    today: string,
	 *    tomorrow: string,
	 *    day_after_tomorrow: string,
	 *    in_n_days: callable(int): string,
	 *    next_week: string,
	 *    in_1_week: string
	 *  }            $translations     custom translations (fallback EN)
	 * @return array<string, mixed>
	 */
	#[ArrayShape([
		'last_week'            => 'string',
		'since_1_week'         => 'string',
		'since_n_days'         => 'callable(int):string',
		'day_before_yesterday' => 'string',
		'yesterday'            => 'string',
		'since_yesterday'      => 'string',
		'today'                => 'string',
		'tomorrow'             => 'string',
		'day_after_tomorrow'   => 'string',
		'in_n_days'            => 'callable(int):string',
		'next_week'            => 'string',
		'in_1_week'            => 'string',
	])]
	private static function getRelativeDateDictionary(string $locale = 'en', ?array $translations = null): array
	{
		// Native default translations
		$en = [
			'last_week'            => 'last week',
			'since_1_week'         => 'since 1 week',
			'since_n_days'         => fn(int $n) => "since $n days",
			'day_before_yesterday' => 'the day before yesterday',
			'yesterday'            => 'yesterday',
			'since_yesterday'      => 'since yesterday',
			'today'                => 'today',
			'tomorrow'             => 'tomorrow',
			'day_after_tomorrow'   => 'the day after tomorrow',
			'in_n_days'            => fn(int $n) => "in $n days",
			'next_week'            => 'next week',
			'in_1_week'            => 'in 1 week',
		];

		$de = [
			'last_week'            => 'letzte Woche',
			'since_1_week'         => 'seit 1 Woche',
			'since_n_days'         => fn(int $n) => "seit {$n} Tagen",
			'day_before_yesterday' => 'Vorgestern',
			'yesterday'            => 'Gestern',
			'since_yesterday'      => 'seit gestern',
			'today'                => 'Heute',
			'tomorrow'             => 'Morgen',
			'day_after_tomorrow'   => 'Übermorgen',
			'in_n_days'            => fn(int $n) => "in {$n} Tagen",
			'next_week'            => 'nächste Woche',
			'in_1_week'            => 'in 1 Woche',
		];

		$fr = [
			'last_week'            => 'la semaine dernière',
			'since_1_week'         => 'depuis 1 semaine',
			'since_n_days'         => fn(int $n) => "depuis {$n} jours",
			'day_before_yesterday' => 'avant-hier',
			'yesterday'            => 'hier',
			'since_yesterday'      => 'depuis hier',
			'today'                => 'aujourd’hui',
			'tomorrow'             => 'demain',
			'day_after_tomorrow'   => 'après-demain',
			'in_n_days'            => fn(int $n) => "dans {$n} jours",
			'next_week'            => 'la semaine prochaine',
			'in_1_week'            => 'dans 1 semaine',
		];

		$es = [
			'last_week'            => 'la semana pasada',
			'since_1_week'         => 'desde hace 1 semana',
			'since_n_days'         => fn(int $n) => "desde hace {$n} días",
			'day_before_yesterday' => 'anteayer',
			'yesterday'            => 'ayer',
			'since_yesterday'      => 'desde ayer',
			'today'                => 'hoy',
			'tomorrow'             => 'mañana',
			'day_after_tomorrow'   => 'pasado mañana',
			'in_n_days'            => fn(int $n) => "en {$n} días",
			'next_week'            => 'la próxima semana',
			'in_1_week'            => 'en 1 semana',
		];

		$it = [
			'last_week'            => 'la settimana scorsa',
			'since_1_week'         => 'da 1 settimana',
			'since_n_days'         => fn(int $n) => "da {$n} giorni",
			'day_before_yesterday' => "l'altro ieri",
			'yesterday'            => 'ieri',
			'since_yesterday'      => 'da ieri',
			'today'                => 'oggi',
			'tomorrow'             => 'domani',
			'day_after_tomorrow'   => 'dopodomani',
			'in_n_days'            => fn(int $n) => "tra {$n} giorni",
			'next_week'            => 'la prossima settimana',
			'in_1_week'            => 'tra 1 settimana',
		];

		// Override translations
		if (is_array($translations) && in_array($locale, ['en', 'en_US', 'en_GB']))
			$en = $translations;    // overwrite English translations
		if (is_array($translations) && in_array($locale, ['de', 'de_DE']))
			$de = $translations;    // overwrite German translations
		if (is_array($translations) && in_array($locale, ['fr', 'fr_FR']))
			$fr = $translations;    // overwrite French translations
		if (is_array($translations) && in_array($locale, ['es', 'es_ES']))
			$es = $translations;    // overwrite Spanish translations
		if (is_array($translations) && in_array($locale, ['it', 'it_IT']))
			$it = $translations;    // overwrite Italian translations


		$byLocale = [
			'en'    => $en,
			'en_US' => $en,
			'en_GB' => $en,
			'de'    => $de,
			'de_DE' => $de,
			'fr'    => $fr,
			'fr_FR' => $fr,
			'es'    => $es,
			'es_ES' => $es,
			'it'    => $it,
			'it_IT' => $it,
		];
		if (is_array($translations) && !isset($byLocale[$locale])) {
			$byLocale[$locale] = $translations;    // custom translation
		}

		$base = $byLocale[$locale] ?? $byLocale[DateTimeTool::normalizeLocale($locale)] ?? $en;

		// Missing keys will be set to English
		return array_replace($en, $base);
	}

	/**
	 * Normalize locale like "de-DE" -> "de_DE", return primary language code if needed.
	 */
	private static function normalizeLocale(string $locale): string
	{
		$l     = str_replace('-', '_', $locale);
		$parts = explode('_', $l);

		return strtolower($parts[0]);
	}

}