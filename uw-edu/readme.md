# PHP Exercise

This project contains three class files with incomplete code. 

* src/CourseSectionArea.php
* src/FacultyArea.php
* src/MeetingMinutes.php (bonus)

Your task is to complete the code so that the provided automated testing passes.

## Hint and Scale of Solution

As with most programming tasks, 95% of the work is understanding the domain and 
planning your approach.

Each class only needs a small block of code to work correctly. If you find yourself 
writing much more than 20 lines of code per task, you are probably off track.

Your tasks will mean writing code so that provided automated testing passes.

Don't change the test files. When you send me your code I will use the unchanged 
versions to test your implementations.

However, you should read the tests and understand what they are checking for.
Test are a great way to provide requirements and specification.


## Setup

You will need to provide an environment where you can write and run PHP code.

* git, to grab a working copy of this project
* PHP 7.2 or better
* Composer to install vendor packages (PhpUnit)

You will need to work out the specifics of your environment, OS, directories, etc.
But roughly you will do something like this.

    $ cd /my/local/working/directory
    $ git clone git@bitbucket.org:hanisko/php-exercise.git
    $ cd php-exercise
    $ composer install
    
`composer install` will install PhpUnit and its dependencies. As you work on the 
tasks you can run PhpUnit to see how you are progressing.

    $ vendor/bin/phpunit

If you run it right away, you will see lots of failures, because the code being 
tested is not implemented.


## Domain

This project provides some mock data based on actual web service API available 
at University of Washington.

In the "data" directory is a file `data/uw-course-section-service-mock.json` 
containing JSON data that would be returned from the UW web service. 

Imagine that file is the result of a query against the web service asking:

> What courses are offered in 2020 Autumn within the EDUC curriculum?

The web service response returns an array of results. Each array item is a JSON 
object representing one Course Section that a student could register for. 

> Course Sections are courses offered in a specific quarter. They are uniquely 
> identified by Year + Quarter + Curriculum + Course Number + Section letter. 
> For example: 2020 Autumn EDUC 369 C.


### Meetings

Each Course Section record has a Meetings property. This is an array of meetings 
records (there can be 0-3 meetings per Section). Each Meeting record has a Start 
Time and End Time and can happen multiple days per week. For example one Meeting 
could be Tuesdays and Thursdays 9 AM - 10:50 AM.

If Meetings occur at different times on different days, that must be represented 
as separate Meeting records. So if your class meets Monday 9 AM - 10:50 AM and 
Wednesday 1:00 PM to 1:50 PM you need two meeting records.

    Meeting 1 = Days: [ Mon ], StartTime: 09:00, EndTime: 10:50
    Meeting 2 = Days: [ Wed ], StartTime: 13:00, EndTime: 13:50 


### Instructors

Instructors are listed within a Meeting record in the web service. So each 
Meeting has a list of Instructors.

One Instructor can be listed multiple times within a Course Section if they 
are an Instructor for multiple Meetings. Commonly when a Course has multiple 
Meetings the same Instructors are listed in each.

Instructors have RegID values that are unique identifiers within UW data. (The
RegIDs in this sample data are made up, but you can assume they are unique 
identifiers of a person.)


### College Areas

The College of Education has Areas that are subject-matter-based divisions 
of the college. Each of our faculty is associated with one college Area.

College Areas and faculty association is local College of Education data. 
It does not show up in the central course data available in the UW web service.

In many cases we need to consume UW institutional data and combine it with local
college records.

This project includes a file `data/area-faculty.php`. That file provides a PHP
array structure with:

* Section for each Area. Area abbreviation is the top array key.
* List of faculty RegIDs that belong to the given Area. 


## Exercises

### 1. FacultyArea

In the __src__ directory find the file __FacultyArea.php__.

This file contains a PHP class with one method to be implemented: `areaOf()`

The `areaOf()` method should accept a string argument containing a RegID. 

When the automated test are run this class will be instantiated with its 
`$areaRoster` configuration. See the file `data/area-faculty.php` for an example 
area roster.

It should search through its `$areaRoster` and figure out what area the faculty 
with that RegID belongs to. It should return the string name (abbreviation) of 
the college Area.

Faculty are just associated with a single Area, provided `$areaRoster` will
follow that rule.

If the RegID does not show up in the `$areaRoster`, `areaOf()` should return 
NULL.

Your task is to implement the method so that the provided automated tests pass.

    vendor/bin/phpunit --filter FacultyAreaTest


### 2. CourseSectionArea

Next file to look at is `src/CourseSectionArea.php`.

In this file your task is to implement the `areasFor()` method.

The purpose of `areasFor()` is to take a Course Section record from the web 
service data and figure out what College of Education Areas are related to 
it through the Instructors.

Since a Course Section can have multiple Instructors, it can be related to 
multiple Areas. So `areasFor()` must return an array.

The standard way to process a string of JSON data in PHP is to use the standard
PHP function `json_decode()` to convert the string into a structure of arrays 
and objects, specifically PHP stdClass() objects. __You don't need to do this,
this is just to help explain the input your method will receive.__

`areasFor()` will get a stdClass argument (we are calling it `$section`) that
will follow the data structure you find in `src/uw-course-section-service-mock.json`.

The `CourseSectionArea` class will also have an instance of `FacultyArea` that 
you worked on in Task 1. You can use this to get Areas based on Instructor's 
RegIDs.

Your task is to implement the `areasFor()` method so that the provided automated 
tests pass.

    vendor/bin/phpunit --filter CourseSectionAreaTest


### 3. MeetingMinutes (bonus)

I would like you to spend no more than an hour on this whole set of exercises.
If you've reached your limit it's 100% fine to stop here.

If you are breezing through and want an extra challenge, look at 
`src/MeetingMinutes.php`.

In this file your task is to implement the `countOneMeeting()` method.

The purpose of this class is to look at the Meeting data in a Course Section 
record and total up the number of meeting minutes in a week.

Reminder each Course Section record has an array of Meetings (0-3). Each Meeting
can meet on multiple days. But each Meeting record has a common Start Time and 
End Time.

The method `countWeeklyMeetingMinutes()` is already implemented. It will receive 
the full Course Section object. It takes care of calling your implementation of 
`countOneMeeting()` and summing up the results.

`countOneMeeting()` will receive a single Meeting item from the web service data.
Look at `src/uw-course-section-service-mock.json` to see the structure.

Your task is to implement the `countOneMeeting()` method so that the provided 
automated tests pass.

    vendor/bin/phpunit --filter MeetingMinutes


## Turning Results In

I'm flexible about how you provide me your results.

If you fork the repository you can send me a link to your repo. You can send me a 
pull request to look at. Or you can just email me the files from your `src` directory.