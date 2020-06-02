<?php declare(strict_types=1);

use App\FacultyArea;
use PHPUnit\Framework\TestCase;

class FacultyAreaTest extends TestCase
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

    public function test_it_instantiates()
    {
        $it = new FacultyArea($this->testConfig);

        $this->assertInstanceOf(FacultyArea::class, $it);
    }

    public function test_it_returns_an_area_from_a_regid()
    {
        $it = new FacultyArea($this->testConfig);

        $this->assertSame('FOO', $it->areaOf('A001'));
    }

    public function test_it_returns_null_when_regid_is_not_found()
    {
        $it = new FacultyArea($this->testConfig);

        $this->assertNull($it->areaOf('X099'), 'argument is X099, unknown RegID');
    }

    public function test_it_returns_area_other_than_first()
    {
        $it = new FacultyArea($this->testConfig);

        $this->assertSame('BAR', $it->areaOf('B003'));
    }

    public function test_it_matches_regid_other_than_first()
    {
        $it = new FacultyArea($this->testConfig);

        $this->assertSame('BAR', $it->areaOf('B005'));
    }

    public function test_it_works_with_college_area_configuration()
    {
        $it = new FacultyArea(include __DIR__ . '/../data/area-faculty.php');

        $this->assertSame('S3', $it->areaOf('A87D00000000000077722'));
        $this->assertSame('TLC', $it->areaOf('A87D00000000000077712'));
        $this->assertSame('EFLP', $it->areaOf('A87D00000000000077725'));

        $this->assertNull($it->areaOf('A87D00000000000099999'), 'Response for unknown RegID');
    }
}
