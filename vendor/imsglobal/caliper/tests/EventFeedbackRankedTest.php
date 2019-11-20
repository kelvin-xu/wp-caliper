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
use IMSGlobal\Caliper\events\FeedbackEvent;
use IMSGlobal\Caliper\entities\feedback\Rating;
use IMSGlobal\Caliper\entities\question\RatingScaleQuestion;
use IMSGlobal\Caliper\entities\scale\LikertScale;
use IMSGlobal\Caliper\entities\feedback\Comment;
use IMSGlobal\Caliper\entities\DigitalResource;
use IMSGlobal\Caliper\entities\DigitalResourceCollection;


/**
 * @requires PHP 5.6.28
 */
class EventFeedbackRankedTest extends CaliperTestCase {
    function setUp() {
        parent::setUp();

        $this->setTestObject(
            (new FeedbackEvent('urn:uuid:a502e4fc-24c1-11e9-ab14-d663bd873d93'))
                ->setActor(
                    (new Person('https://example.edu/users/554433'))
                )
                ->setProfile(
                    new Profile(Profile::FEEDBACK))
                ->setAction(
                    new Action(Action::RANKED))
                ->setObject(
                    (new DigitalResource('https://example.edu/terms/201801/courses/7/sections/1/resources/1/syllabus.pdf'))
                        ->setName('Course Syllabus')
                        ->setMediaType('application/pdf')
                        ->setIsPartOf((new DigitalResourceCollection('https://example.edu/terms/201801/courses/7/sections/1/resources/1'))
                            ->setName('Course Assets')
                            ->setIsPartOf(new CourseSection('https://example.edu/terms/201801/courses/7/sections/1'))
                        )
                        ->setDateCreated(new \DateTime('2018-08-02T11:32:00.000Z'))
                )
                ->setGenerated(
                    (new Rating('https://example.edu/users/554433/rating/1'))
                        ->setRater(
                            (new Person('https://example.edu/users/554433'))
                        )
                        ->setRated(
                            (new DigitalResource('https://example.edu/terms/201801/courses/7/sections/1/resources/1/syllabus.pdf'))
                                ->setName('Course Syllabus')
                                ->setMediaType('application/pdf')
                                ->setIsPartOf((new DigitalResourceCollection('https://example.edu/terms/201801/courses/7/sections/1/resources/1'))
                                    ->setName('Course Assets')
                                    ->setIsPartOf(new CourseSection('https://example.edu/terms/201801/courses/7/sections/1'))
                                )
                                ->setDateCreated(new \DateTime('2018-08-02T11:32:00.000Z'))
                        )
                        ->setQuestion(
                            (new RatingScaleQuestion('https://example.edu/question/2'))
                                ->setQuestionPosed('Do you agree with the opinion presented?')
                                ->setScale(
                                    (new LikertScale('https://example.edu/scale/2'))
                                        ->setScalePoints(4)
                                        ->setItemLabels(['Strongly Disagree', 'Disagree', 'Agree', 'Strongly Agree'])
                                        ->setItemValues(['-2', '-1', '1', '2'])
                                )
                        )
                        ->setSelections(['1'])
                        ->setRatingComment(
                            (new Comment('https://example.edu/terms/201801/courses/7/sections/1/assess/1/items/6/users/665544/responses/1/comment/1'))
                                ->setCommenter(
                                    (new Person('https://example.edu/users/554433'))
                                )
                                ->setCommentedOn(
                                    (new DigitalResource('https://example.edu/terms/201801/courses/7/sections/1/resources/1/syllabus.pdf'))
                                        ->setName('Course Syllabus')
                                        ->setMediaType('application/pdf')
                                        ->setIsPartOf((new DigitalResourceCollection('https://example.edu/terms/201801/courses/7/sections/1/resources/1'))
                                            ->setName('Course Assets')
                                            ->setIsPartOf(new CourseSection('https://example.edu/terms/201801/courses/7/sections/1'))
                                        )
                                        ->setDateCreated(new \DateTime('2018-08-02T11:32:00.000Z'))
                                )
                                ->setValue('I like what you did here but you need to improve on...')
                                ->setDateCreated(new \DateTime('2018-08-01T06:00:00.000Z'))
                        )
                        ->setDateCreated(new \DateTime('2018-08-01T06:00:00.000Z'))
                )
                ->setEventTime(
                    new \DateTime('2018-11-15T10:05:00.000Z'))
                ->setEdApp(
                    (new SoftwareApplication('https://example.edu'))->makeReference())
                ->setGroup(
                    (new CourseSection('https://example.edu/terms/201801/courses/7/sections/1'))
                        ->setCourseNumber(
                            'CPS 435-01'
                        )
                        ->setAcademicSession(
                            'Fall 2018'
                        )
                )
                ->setMembership(
                    (new Membership('https://example.edu/terms/201801/courses/7/sections/1/rosters/1'))
                        ->setMember(
                            (new Person('https://example.edu/users/554433'))->makeReference())
                        ->setOrganization(
                            (new Organization('https://example.edu/terms/201801/courses/7/sections/1'))->makeReference())
                        ->setRoles(
                            [new Role(Role::LEARNER)])
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
