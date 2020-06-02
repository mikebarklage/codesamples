<?php

class StubWebServiceGenerator
{
    /**
     * @var StubSectionGenerator[]
     */
    private $sections = [];

    public function addSection($CourseNumber, $SectionID)
    {
        $section = new StubSectionGenerator($CourseNumber, $SectionID, $this);
        $this->sections[] = $section;
        return $section;
    }

    public function getList()
    {
        $out = [];
        foreach ($this->sections as $section) {
            $out[] = $section->getSection();
        }

        return $out;
    }
}