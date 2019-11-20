<?php
require_once 'CaliperTestCase.php';

use IMSGlobal\Caliper\profiles\Profile;
use IMSGlobal\Caliper\actions\Action;
use IMSGlobal\Caliper\entities\agent\Organization;
use IMSGlobal\Caliper\entities\agent\Person;
use IMSGlobal\Caliper\entities\agent\SoftwareApplication;
use IMSGlobal\Caliper\entities\lis\CourseSection;
use IMSGlobal\Caliper\entities\lis\Membership;
use IMSGlobal\Caliper\entities\lis\Role;
use IMSGlobal\Caliper\entities\lis\Status;
use IMSGlobal\Caliper\entities\session\Session;
use IMSGlobal\Caliper\entities\DigitalResource;
use IMSGlobal\Caliper\entities\DigitalResourceCollection;
use IMSGlobal\Caliper\events\ResourceManagementEvent;


/**
 * @requires PHP 5.6.28
 */
class EventResourceManagementPrintedTest extends CaliperTestCase {
    function setUp() {
        parent::setUp();

        $this->setTestObject(
            (new ResourceManagementEvent('urn:uuid:d3543a73-e307-4190-a755-5ce7b3187bc5'))
                ->setActor(
                    (new Person('https://example.edu/users/554433'))
                )
                ->setProfile(
                    new Profile(Profile::RESOURCE_MANAGEMENT))
                ->setAction(
                    new Action(Action::PRINTED))
                ->setObject(
                    (new DigitalResource('https://example.edu/terms/201801/courses/7/sections/1/resources/1/syllabus.pdf'))
                        ->setName('Course Syllabus')
                        ->setMediaType('application/pdf')
                        ->setCreators(
                            [
                                (new Person('https://example.edu/users/554433'))
                            ]
                        )
                        ->setIsPartOf(
                            (new DigitalResourceCollection('https://example.edu/terms/201801/courses/7/sections/1/resources/1'))
                                ->setName('Course Assets')
                                ->setIsPartOf(
                                    (new CourseSection('https://example.edu/terms/201801/courses/7/sections/1'))
                                )
                        )
                        ->setDateCreated(
                            new \DateTime('2018-08-02T11:32:00.000Z'))
                )
                ->setEventTime(
                    new \DateTime('2018-11-15T10:05:00.000Z'))
                ->setEdApp(
                    (new SoftwareApplication('https://example.edu'))->makeReference())
                ->setGroup(
                    (new CourseSection('https://example.edu/terms/201801/courses/7/sections/1'))
                        ->setCourseNumber('CPS 435-01')
                        ->setAcademicSession('Fall 2018')
                )
                ->setMembership(
                    (new Membership('https://example.edu/terms/201801/courses/7/sections/1/rosters/1'))
                        ->setMember(
                            (new Person('https://example.edu/users/554433'))->makeReference())
                        ->setOrganization(
                            (new Organization('https://example.edu/terms/201801/courses/7/sections/1'))->makeReference())
                        ->setRoles(
                            [new Role(Role::INSTRUCTOR)])
                        ->setStatus(
                            new Status(Status::ACTIVE))
                        ->setDateCreated(
                            new \DateTime('2018-08-01T06:00:00.000Z'))
                )
                ->setSession(
                    (new Session('https://example.edu/sessions/1f6442a482de72ea6ad134943812bff564a76259'))
                        ->setStartedAtTime(
                            new \DateTime('2018-11-15T10:00:00.000Z'))
                )
        );
    }
}
