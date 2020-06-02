<?php declare(strict_types=1);

use App\CourseSectionArea;
use App\FacultyArea;
use PHPUnit\Framework\TestCase;

class CourseSectionAreaTest extends TestCase
{
    private $testConfig = [
        'FOO' => [
            'A001',
            'A002',
        ],
        'BAR' => [
            'B003',
            'B004',
            'B005',
            'B006',
        ],
        'BAZ' => [
            'C007',
            'C008',
        ],
    ];

    /**
     * @var CourseSectionArea
     */
    private $it;

    public function setUp(): void
    {
        $this->it = new CourseSectionArea(new FacultyArea($this->testConfig));
    }

    public function test_it_instantiates()
    {
        $this->assertInstanceOf(CourseSectionArea::class, $this->it);
    }

    public function test_it_maps_course_section_to_an_area()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addDefaultMeeting()
            ->addInstructor('B003', 'Bob')
            ->getSection();

        $this->assertSame(['BAR'], $this->it->areasFor($section));
    }

    public function test_it_returns_multiple_areas()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addDefaultMeeting()
            ->addInstructor('B003', 'Bob')
            ->addInstructor('C008', 'Carly')
            ->getSection();

        $this->assertSame(['BAR', 'BAZ'], $this->it->areasFor($section));
    }

    public function test_it_returns_areas_in_alphabetical_order()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addDefaultMeeting()
            ->addInstructor('A002', 'Allen')
            ->addInstructor('C008', 'Carly')
            ->addInstructor('B003', 'Bob')
            ->getSection();

        $this->assertSame(['BAR', 'BAZ', 'FOO'], $this->it->areasFor($section));
    }

    public function test_it_returns_an_area_only_once()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addDefaultMeeting()
            ->addInstructor('A002', 'Allen')
            ->addInstructor('A001', 'Annie')
            ->addInstructor('B003', 'Brenda')
            ->addInstructor('B004', 'Bob')
            ->addInstructor('B005', 'Berty')
            ->getSection();

        $this->assertSame(['BAR', 'FOO'], $this->it->areasFor($section));
    }

    public function test_it_returns_an_empty_array_when_regid_not_found()
    {
        $section = $this->makeStubSection('310', 'A')
            ->addDefaultMeeting()
            ->addInstructor('D099', 'Douglas')
            ->getSection();

        $this->assertSame([], $this->it->areasFor($section));
    }

    public function test_it_works_with_example_data_set()
    {
        $exampleDataSet = json_decode(file_get_contents(__DIR__ . '/../data/uw-course-section-service-mock.json'));
        $it = new CourseSectionArea(new FacultyArea(include __DIR__ . '/../data/area-faculty.php'));

        $section = $exampleDataSet[0];
        $this->assertSame(['EFLP'], $it->areasFor($section), $this->sectionLabel($section));

        $section = $exampleDataSet[11];
        $this->assertSame(['S3'], $it->areasFor($section), $this->sectionLabel($section));

        $section = $exampleDataSet[27];
        $this->assertSame(['EFLP', 'LSHD'], $it->areasFor($section), $this->sectionLabel($section));

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
