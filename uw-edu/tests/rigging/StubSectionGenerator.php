<?php

class StubSectionGenerator
{
    private $obj;
    private $list;
    /**
     * @var StubMeetingGenerator[]
     */
    private $meetings;

    public function __construct($CourseNumber, $SectionID, ?StubWebServiceGenerator $list = null)
    {
        $course = new stdClass();
        $course->Href =  "/student/v5/course/2020,autumn,EDUC,{$CourseNumber}.json";
        $course->Year = 2020;
        $course->Quarter = "autumn";
        $course->CurriculumAbbreviation = "EDUC";
        $course->CourseNumber = $CourseNumber;

        $this->obj = new stdClass();
        $this->obj->Course = $course;
        $this->obj->CourseNumber = $CourseNumber;
        $this->obj->CourseTitle = "STUB {$CourseNumber}";
        $this->obj->CourseTitleLong = "Stub Course Section Num {$CourseNumber}";
        $this->obj->SectionID = $SectionID;

        $this->obj->Meetings = [];
        $this->list = $list;
    }

    public function addMeeting($days, $start, $end)
    {
        $meeting = new StubMeetingGenerator($days, $start, $end, $this);
        $this->meetings[] = $meeting;

        return $meeting;
    }

    public function addDefaultMeeting()
    {
        return $this->addMeeting(['Monday'], '10:30', '11:50');
    }

    public function toList(): StubWebServiceGenerator
    {
        return $this->list;
    }

    public function getList()
    {
        return $this->list->getList();
    }

    public function getSection()
    {
        foreach ($this->meetings as $meeting) {
            $this->obj->Meetings[] = $meeting->getMeeting();
        }
        return $this->obj;
    }
}