<?php declare(strict_types=1);

use App\MeetingMinutes;
use PHPUnit\Framework\TestCase;

class MeetingMinutesTest extends TestCase
{
    public function test_it_instantiates()
    {
        $it = new MeetingMinutes();

        $this->assertInstanceOf(MeetingMinutes::class, $it);
    }

    public function test_it_counts_minutes_of_one_meeting_on_one_day()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addMeeting(['Monday'], '09:00', '09:50')
            ->getSection();

        $it = new MeetingMinutes();

        $this->assertSame(50, $it->countWeeklyMeetingMinutes($section));
    }

    public function test_it_counts_minutes_that_cross_into_new_hour()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addMeeting(['Monday'], '09:00', '11:50')
            ->getSection();

        $it = new MeetingMinutes();

        $this->assertSame(170, $it->countWeeklyMeetingMinutes($section));
    }

    public function test_it_multiplies_meeting_minutes_by_number_of_days()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addMeeting(['Tuesday', 'Thursday'], '09:00', '11:50')
            ->getSection();

        $it = new MeetingMinutes();

        $this->assertSame(340, $it->countWeeklyMeetingMinutes($section));
    }

    public function test_it_sums_minutes_from_multiple_meetings()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addMeeting(['Tuesday', 'Thursday'], '09:00', '11:50')
            ->toSection()
            ->addMeeting(['Friday'], '13:00', '13:50')
            ->getSection();

        $it = new MeetingMinutes();

        $this->assertSame(390, $it->countWeeklyMeetingMinutes($section));
    }

    public function test_it_returns_zero_when_start_time_is_empty()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addMeeting(['Friday'], '', '11:50')
            ->getSection();

        $it = new MeetingMinutes();

        $this->assertSame(0, $it->countWeeklyMeetingMinutes($section));
    }

    public function test_it_returns_zero_when_end_time_is_empty()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addMeeting(['Friday'], '09:00', '')
            ->getSection();

        $it = new MeetingMinutes();

        $this->assertSame(0, $it->countWeeklyMeetingMinutes($section));
    }

    public function test_it_returns_zero_when_meeting_has_no_days()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addMeeting([], '09:00', '11:50')
            ->getSection();

        $it = new MeetingMinutes();

        $this->assertSame(0, $it->countWeeklyMeetingMinutes($section));
    }

    public function test_it_works_with_example_data_set()
    {
        $exampleDataSet = json_decode(file_get_contents(__DIR__ . '/../data/uw-course-section-service-mock.json'));

        $it = new MeetingMinutes();

        $section = $exampleDataSet[2];
        $this->assertSame(170, $it->countWeeklyMeetingMinutes($section), $this->sectionLabel($section));

        $section = $exampleDataSet[0];
        $this->assertSame(280, $it->countWeeklyMeetingMinutes($section), $this->sectionLabel($section));

        $section = $exampleDataSet[38];
        $this->assertSame(160, $it->countWeeklyMeetingMinutes($section), $this->sectionLabel($section));

    }

    private function sectionLabel($section)
    {
        return "EDUC {$section->Course->CourseNumber} {$section->SectionID}";
    }
    
    private function makeStubSection($CourseNumber, $SectionID)
    {
        return new StubSectionGenerator($CourseNumber, $SectionID);
    }
}
