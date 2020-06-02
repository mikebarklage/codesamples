<?php

class StubMeetingGenerator
{
    private $obj;
    private $days = [];
    private $section;

    public function __construct($days, $start, $end, StubSectionGenerator $section)
    {
        $this->section = $section;

        $this->obj = new stdClass();
        $this->obj->DaysOfWeek = new stdClass();
        $this->obj->DaysOfWeek->Days = [];
        foreach ($days as $day) {
            $this->days[] = strtolower($day);
            $dayObject = new stdClass();
            $dayObject->Name = $day;
            $this->obj->DaysOfWeek->Days[] = $dayObject;
        }
        $this->obj->StartTime = $start;
        $this->obj->EndTime = $end;
        $this->obj->Instructors = [];
    }

    public function addInstructor($RegID, $Name)
    {
        $person = new stdClass();
        $person->Name = $Name;
        $person->RegID = $RegID;

        $instructor = new stdClass();
        $instructor->PercentInvolve = '100';
        $instructor->Person = $person;

        $this->obj->Instructors[] = $instructor;

        return $this;
    }

    public function toList(): StubWebServiceGenerator
    {
        return $this->section->toList();
    }

    public function toSection(): StubSectionGenerator
    {
        return $this->section;
    }

    public function getList()
    {
        return $this->section->toList()->getList();
    }

    public function getSection()
    {
        return $this->section->getSection();
    }

    public function getMeeting()
    {
        $this->obj->DaysOfWeek->Text = $this->meetingDaysText();
        return $this->obj;
    }

    private function meetingDaysText()
    {
        if (count($this->days) === 0) {
            return "to be arranged";
        }
        $out = [
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
        ];
        if (in_array('monday', $this->days)) {
            $out[0] = 'M';
        }
        if (in_array('tuesday', $this->days)) {
            $out[1] = 'T';
        }
        if (in_array('wednesday', $this->days)) {
            $out[2] = 'W';
        }
        if (in_array('thursday', $this->days)) {
            $out[3] = 'T';
        }
        if (in_array('friday', $this->days)) {
            $out[4] = 'F';
        }
        if (in_array('saturday', $this->days)) {
            $out[5] = 'S';
        }
        if (in_array('sunday', $this->days)) {
            $out[6] = 'S';
        }
        return implode('', $out);
    }
}