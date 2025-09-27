<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use Xcy7e\PhpToolbox\Library\DateTimeTool;
use PHPUnit\Framework\TestCase;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class DateTimeToolTest extends TestCase
{

	public function testGetMinutesDiff()
	{
		$beforeTwoMinutes = new \DateTime('now - 2 minutes');
		$diffMin          = DateTimeTool::getMinutesDiff($beforeTwoMinutes);

		$this->assertEquals(2, $diffMin);
	}

	public function testGetDaysDiff()
	{
		$beforeTwoDays = new \DateTime('now - 2 days');
		$inTwoDays = new \DateTime('now + 2 days');
		$diffDaysBefore      = DateTimeTool::getDaysDiff($beforeTwoDays);
		$diffDaysIn      = DateTimeTool::getDaysDiff($inTwoDays);

		$this->assertEquals(-2, $diffDaysBefore);
		$this->assertEquals(2, $diffDaysIn);
	}

	public function testTranslateDateDiffWithDefaults()
	{
		$lastWeek         = new \DateTime('now -1 week');
		$beforeYesterday  = new \DateTime('now -2 days');
		$yesterday        = new \DateTime('now -1 day');
		$today            = new \DateTime('now');
		$tomorrow         = new \DateTime('now +1 day');
		$dayAfterTomorrow = new \DateTime('now +2 days');
		$nextWeek         = new \DateTime('now +1 week');

		// lastWeek (en)
		$this->assertEquals('last week', DateTimeTool::translateDateDiff($lastWeek));
		$this->assertEquals('since 1 week', DateTimeTool::translateDateDiff($lastWeek, false));
		// beforeYesterday (en)
		$this->assertEquals('the day before yesterday', DateTimeTool::translateDateDiff($beforeYesterday));
		$this->assertEquals('since 2 days', DateTimeTool::translateDateDiff($beforeYesterday, false));
		// yesterday (en)
		$this->assertEquals('yesterday', DateTimeTool::translateDateDiff($yesterday));
		$this->assertEquals('since yesterday', DateTimeTool::translateDateDiff($yesterday, false));
		// today (en)
		$this->assertEquals('today', DateTimeTool::translateDateDiff($today));
		$this->assertEquals('today', DateTimeTool::translateDateDiff($today, false));
		// tomorrow (en)
		$this->assertEquals('tomorrow', DateTimeTool::translateDateDiff($tomorrow));
		$this->assertEquals('tomorrow', DateTimeTool::translateDateDiff($tomorrow, false));
		// dayAfterTomorrow (en)
		$this->assertEquals('the day after tomorrow', DateTimeTool::translateDateDiff($dayAfterTomorrow));
		$this->assertEquals('in 2 days', DateTimeTool::translateDateDiff($dayAfterTomorrow, false));
		// nextWeek (en)
		$this->assertEquals('next week', DateTimeTool::translateDateDiff($nextWeek));
		$this->assertEquals('in 1 week', DateTimeTool::translateDateDiff($nextWeek, false));
	}


	public function testTranslateDateDiffWithCustomTranslations()
	{
		$lastWeek         = new \DateTime('now -1 week');
		$beforeYesterday  = new \DateTime('now -2 days');
		$yesterday        = new \DateTime('now -1 day');
		$today            = new \DateTime('now');
		$tomorrow         = new \DateTime('now +1 day');
		$dayAfterTomorrow = new \DateTime('now +2 days');
		$nextWeek         = new \DateTime('now +1 week');

		$customTranslations = [
			'last_week'            => 'LAST WEEK!',
			'since_1_week'         => 'SINCE ONE WEEK!',
			'since_n_days'         => fn(int $n) => "SINCE {$n} DAYS",
			'day_before_yesterday' => 'DAY BEFORE YESTERDAY',
			'yesterday'            => 'YESTERDAY',
			'since_yesterday'      => 'SINCE YESTERDAY',
			'today'                => 'TODAY',
			'tomorrow'             => 'TOMORROW',
			'day_after_tomorrow'   => 'DAY AFTER TOMORROW',
			'in_n_days'            => fn(int $n) => "IN {$n} DAYS",
			'next_week'            => 'NEXT WEEK',
			'in_1_week'            => 'IN ONE WEEK',
		];

		// lastWeek (custom)
		$this->assertEquals('LAST WEEK!', DateTimeTool::translateDateDiff($lastWeek, true, 'd.m.Y', 8, 'en_US', $customTranslations));
		$this->assertEquals('SINCE ONE WEEK!', DateTimeTool::translateDateDiff($lastWeek, false, 'd.m.Y', 8, 'en_US', $customTranslations));
		// beforeYesterday (custom)
		$this->assertEquals('DAY BEFORE YESTERDAY', DateTimeTool::translateDateDiff($beforeYesterday, true, 'd.m.Y', 8, 'en_US', $customTranslations));
		$this->assertEquals('SINCE 2 DAYS', DateTimeTool::translateDateDiff($beforeYesterday, false, 'd.m.Y', 8, 'en_US', $customTranslations));
		// yesterday (custom)
		$this->assertEquals('YESTERDAY', DateTimeTool::translateDateDiff($yesterday, true, 'd.m.Y', 8, 'en_US', $customTranslations));
		$this->assertEquals('SINCE YESTERDAY', DateTimeTool::translateDateDiff($yesterday, false, 'd.m.Y', 8, 'en_US', $customTranslations));
		// today (custom)
		$this->assertEquals('TODAY', DateTimeTool::translateDateDiff($today, true, 'd.m.Y', 8, 'en_US', $customTranslations));
		$this->assertEquals('TODAY', DateTimeTool::translateDateDiff($today, false, 'd.m.Y', 8, 'en_US', $customTranslations));
		// tomorrow (custom)
		$this->assertEquals('TOMORROW', DateTimeTool::translateDateDiff($tomorrow, true, 'd.m.Y', 8, 'en_US', $customTranslations));
		$this->assertEquals('TOMORROW', DateTimeTool::translateDateDiff($tomorrow, false, 'd.m.Y', 8, 'en_US', $customTranslations));
		// dayAfterTomorrow (custom)
		$this->assertEquals('DAY AFTER TOMORROW', DateTimeTool::translateDateDiff($dayAfterTomorrow, true, 'd.m.Y', 8, 'en_US', $customTranslations));
		$this->assertEquals('IN 2 DAYS', DateTimeTool::translateDateDiff($dayAfterTomorrow, false, 'd.m.Y', 8, 'en_US', $customTranslations));
		// nextWeek (custom)
		$this->assertEquals('NEXT WEEK', DateTimeTool::translateDateDiff($nextWeek, true, 'd.m.Y', 8, 'en_US', $customTranslations));
		$this->assertEquals('IN ONE WEEK', DateTimeTool::translateDateDiff($nextWeek, false, 'd.m.Y', 8, 'en_US', $customTranslations));
	}
}