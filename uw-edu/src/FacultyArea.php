<?php

namespace App;

/**
 * Provides a college area for a faculty
 */
class FacultyArea
{
    private $areaRosters;

    /**
     * This will receive a configuration array that lists RegIDs of faculty associated
     * with each college area.
     *
     * See the file data/area-faculty.php for an example area roster configuration.
     *
     * Each faculty can be assigned to only one area.
     *
     * @param array $areaRosters
     */
    public function __construct(array $areaRosters)
    {
        $this->areaRosters = $areaRosters;
    }

    /**
     * Receives the RegID identifier of a faculty and returns the college area that
     * faculty is associated with.
     *
     * If RegID is not included in any area roster, returns null.
     *
     * @param string $RegID
     * @return string|null
     */
    public function areaOf($RegID)
    {

		foreach ($this->areaRosters as $key => $valueArray) {
			if (in_array($RegID, $valueArray)) {
				return $key;
			}
		}

        return null;
    }
}
