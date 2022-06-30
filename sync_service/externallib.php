<?php

use core_completion\progress;
require_once( __DIR__.'/../../config.php' );
require_once( $CFG->libdir.'/externallib.php' );
require_once( $CFG->dirroot.'/user/lib.php' );
require_once( $CFG->dirroot.'/course/lib.php' );

/**
 * Class which contains the implementations of the added functions.
 * 
 * @author Daniel Schröter
 */
class local_sync_service_external extends external_api {

    /**
     * Defines the necessary parameters to execute the request.
     */
    public static function add_new_course_module_url_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value( PARAM_TEXT, 'id of course' ),
                'sectionnum' => new external_value( PARAM_TEXT, 'relative number of the section' ),
                'urlname' => new external_value( PARAM_TEXT, 'displayed mod name' ),
                'url' => new external_value( PARAM_TEXT, 'url to insert' ),
                'beforemod' => new external_value( PARAM_TEXT, 'mod to set before', VALUE_DEFAULT, null ),
            )
        );
    }

    /**
     * This method implements the logic for the API-Call.
     * 
     * @param $courseid The course id.
     * @param $sectionnum The number of the section inside the course.
     * @param $urlname Displayname of the Module.
     * @param $url Url to publish.
     * @param $beforemod Optional parameter, a Module where the new Module should be placed before.
     * 
     * @return Message: Successful and $cmid of the new Module.
     */
    public static function add_new_course_module_url( $courseid, $sectionnum, $urlname, $url, $beforemod = null ) {
        global $DB, $CFG;
        require_once $CFG->dirroot . '/mod/' . '/url' . '/lib.php';

        // Parameter validation.
        $params = self::validate_parameters(
            self::add_new_course_module_url_parameters(),
            array(
                'courseid' => $courseid,
                'sectionnum' => $sectionnum,
                'urlname' => $urlname,
                'url' => $url,
                'beforemod' => $beforemod
            )
        );

        // Ensure the current user has required permission in this course.
        $context = context_course::instance( $params[ 'courseid' ] );
        self::validate_context( $context );

        //Required permissions
        require_capability( 'mod/url:addinstance', $context );

        $instance = new \stdClass();
        $instance->course = $params[ 'courseid' ];
        $instance->name = $params[ 'urlname' ];
        $instance->intro = null;
        $instance->introformat = \FORMAT_HTML;
        $instance->externalurl = $params[ 'url' ];
        $instance->id = url_add_instance( $instance, null );

        $cm->id = add_course_module( $cm );

        $modulename = 'url';

        $cm = new \stdClass();
        $cm->course     = $params[ 'courseid' ];
        $cm->module     = $DB->get_field( 'modules', 'id', array( 'name'=>$modulename ) );
        $cm->instance   = $instance->id;
        $cm->section    = $params[ 'sectionnum' ];

        $cm->id = add_course_module( $cm );
        $cmid = $cm->id;

        $section->id = course_add_cm_to_section( $params[ 'courseid' ], $cmid, $params[ 'sectionnum' ], $params[ 'beforemod' ] );

        $update = [
            'message'=>'Successful',
            'id' =>$cmid
        ];
        return $update;
    }

    /**
     * Obtains the Parameter which will be returned.
     */
    public static function add_new_course_module_url_returns() {
        return new external_single_structure(
            array(
                'message' => new external_value( PARAM_TEXT, 'if the execution was successful' ),
                'id' => new external_value( PARAM_TEXT, 'cmid of the new module' )
            )
        );
    }

    /**
     * Defines the necessary parameters to execute the request.
     */
    public static function add_new_course_module_resource_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value( PARAM_TEXT, 'id of course' ),
                'sectionnum' => new external_value( PARAM_TEXT, 'relative number of the section' ),
                'itemid' => new external_value( PARAM_TEXT, 'id of the upload' ),
                'displayname' => new external_value( PARAM_TEXT, 'displayed mod name' ),
                'beforemod' => new external_value( PARAM_TEXT, 'mod to set before', VALUE_DEFAULT, null ),
            )
        );
    }

    /**
     * This method implements the logic for the API-Call.
     * 
     * @param $courseid The course id.
     * @param $sectionnum The number of the section inside the course.
     * @param $displayname Displayname of the Module.
     * @param $itemid File to publish.
     * @param $beforemod Optional parameter, a Module where the new Module should be placed before.
     * 
     * @return Message: Successful and $cmid of the new Module.
     */
    public static function add_new_course_module_resource( $courseid, $sectionnum, $itemid, $displayname, $beforemod = null ) {
        global $DB, $CFG;
        require_once $CFG->dirroot . '/mod/' . '/resource' . '/lib.php';

        // Parameter validation.
        $params = self::validate_parameters(
            self::add_new_course_module_resource_parameters(),
            array(
                'courseid' => $courseid,
                'sectionnum' => $sectionnum,
                'itemid' => $itemid,
                'displayname' => $displayname,
                'beforemod' => $beforemod
            )
        );

        // Ensure the current user has required permission in this course.
        $context = context_course::instance( $params[ 'courseid' ] );
        self::validate_context( $context );

        //Required permissions
        require_capability( 'mod/resource:addinstance', $context );

        $modulename = 'resource';

        $cm = new \stdClass();
        $cm->course     = $params[ 'courseid' ];
        $cm->module     = $DB->get_field( 'modules', 'id', array( 'name'=>$modulename ) );
        $cm->section    = $params[ 'sectionnum' ];

        $cm->id = add_course_module( $cm );
        $cmid = $cm->id;

        $instance = new \stdClass();
        $instance->course = $params[ 'courseid' ];
        $instance->name = $params[ 'displayname' ];
        $instance->intro = null;
        $instance->introformat = \FORMAT_HTML;
        $instance->coursemodule = $cmid;

        $instance->files = $params[ 'itemid' ];
        $instance->id = resource_add_instance( $instance, null );

        $section->id = course_add_cm_to_section( $params[ 'courseid' ], $cmid, $params[ 'sectionnum' ], $params[ 'beforemod' ] );

        $update = [
            'message'=>'Successful',
            'id' =>$cmid
        ];
        return $update;
    }

    /**
     * Obtains the Parameter which will be returned.
     */
    public static function add_new_course_module_resource_returns() {
        return new external_single_structure(
            array(
                'message' => new external_value( PARAM_TEXT, 'if the execution was successful' ),
                'id' => new external_value( PARAM_TEXT, 'cmid of the new module' )
            )
        );
    }

    /**
     * Defines the necessary parameters to execute the request.
     */
    public static function move_module_to_specific_position_parameters() {
        return new external_function_parameters(
            array(
                'cmid' => new external_value( PARAM_TEXT, 'id of module' ),
                'sectionid' => new external_value( PARAM_TEXT, 'relative number of the section' ),
                'beforemod' => new external_value( PARAM_TEXT, 'mod to set before', VALUE_DEFAULT, null ),
            )
        );
    }

    /**
     * This method implements the logic for the API-Call.
     * 
     * @param $cmid The Module to move.
     * @param $sectionid The id of the section inside the course.
     * @param $beforemod Optional parameter, a Module where the new Module should be placed before.
     * 
     * @return Message: Successful and $cmid of the new Module.
     */
    public static function move_module_to_specific_position( $cmid, $sectionid, $beforemod = null ) {
        global $DB, $CFG;
        require_once $CFG->dirroot . '/course/' . '/lib.php';

        // Parameter validation.
        $params = self::validate_parameters(
            self::move_module_to_specific_position_parameters(),
            array(
                'cmid' => $cmid,
                'sectionid' => $sectionid,
                'beforemod' => $beforemod
            )
        );

        // Ensure the current user has required permission.
        $modcontext = context_module::instance( $params[ 'cmid' ] );
        self::validate_context( $modcontext );

        $cm = get_coursemodule_from_id( '', $params[ 'cmid' ] );

        // Ensure the current user has required permission in this course.
        $context = context_course::instance( $cm->course );
        self::validate_context( $context );

        //Required permissions
        require_capability( 'moodle/course:movesections', $context );

        $section = $DB->get_record( 'course_sections', array( 'id' => $params[ 'sectionid' ], 'course' => $cm->course ) );

        moveto_module( $cm, $section, $params[ 'beforemod' ] );

        $update = [
            'message'=>'Successful'
        ];
        return $update;
    }

    /**
     * Obtains the Parameter which will be returned.
     */
    public static function move_module_to_specific_position_returns() {
        return new external_single_structure(
            array(
                'message' => new external_value( PARAM_TEXT, 'if the execution was successful' )
            )
        );
    }

    /**
     * Defines the necessary parameters to execute the request.
     */
    public static function add_new_course_module_directory_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value( PARAM_TEXT, 'id of course' ),
                'sectionnum' => new external_value( PARAM_TEXT, 'relative number of the section' ),
                'itemid' => new external_value( PARAM_TEXT, 'id of the upload' ),
                'displayname' => new external_value( PARAM_TEXT, 'displayed mod name' ),
                'beforemod' => new external_value( PARAM_TEXT, 'mod to set before', VALUE_DEFAULT, null ),
            )
        );
    }

    /**
     * This method implements the logic for the API-Call.
     * 
     * @param $courseid The course id.
     * @param $sectionnum The number of the section inside the course.
     * @param $displayname Displayname of the Module.
     * @param $itemid Files in same draft area to upload.
     * @param $beforemod Optional parameter, a Module where the new Module should be placed before.
     * 
     * @return Message: Successful and $cmid of the new Module.
     */
    public static function add_new_course_module_directory( $courseid, $sectionnum, $itemid, $displayname, $beforemod = null ) {
        global $DB, $CFG;
        require_once $CFG->dirroot . '/mod/' . '/folder' . '/lib.php';

        // Parameter validation.
        $params = self::validate_parameters(
            self::add_new_course_module_directory_parameters(),
            array(
                'courseid' => $courseid,
                'sectionnum' => $sectionnum,
                'itemid' => $itemid,
                'displayname' => $displayname,
                'beforemod' => $beforemod
            )
        );

        // Ensure the current user has required permission in this course.
        $context = context_course::instance( $params[ 'courseid' ] );
        self::validate_context( $context );

        //Required permissions
        require_capability( 'mod/folder:addinstance', $context );

        $modulename = 'folder';

        $cm = new \stdClass();
        $cm->course     = $params[ 'courseid' ];
        $cm->module     = $DB->get_field( 'modules', 'id', array( 'name'=>$modulename ) );
        $cm->section    = $params[ 'sectionnum' ];

        $cm->id = add_course_module( $cm );
        $cmid = $cm->id;

        $instance = new \stdClass();
        $instance->course = $params[ 'courseid' ];
        $instance->name = $params[ 'displayname' ];
        $instance->coursemodule = $cmid;
        $instance->introformat = FORMAT_HTML;
        $instance->intro = '<p>'.$params[ 'displayname' ].'</p>';
        $instance->files = $params[ 'itemid' ];
        $instance->id = folder_add_instance( $instance, null );

        $section->id = course_add_cm_to_section( $params[ 'courseid' ], $cmid, $params[ 'sectionnum' ], $params[ 'beforemod' ] );

        $update = [
            'message'=>'Successful',
            'id' =>$cmid
        ];
        return $update;
    }

    /**
     * Obtains the Parameter which will be returned.
     */
    public static function add_new_course_module_directory_returns() {
        return new external_single_structure(
            array(
                'message' => new external_value( PARAM_TEXT, 'if the execution was successful' ),
                'id' => new external_value( PARAM_TEXT, 'cmid of the new module' )
            )
        );
    }

     /**
     * Defines the necessary parameters to execute the request.
     */
    public static function add_files_to_directory_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value( PARAM_TEXT, 'id of course' ),
                'cmid' => new external_value( PARAM_TEXT, 'id of the module' ),
                'itemid' => new external_value( PARAM_TEXT, 'id of the upload' ),
                'displayname' => new external_value( PARAM_TEXT, 'displayed mod name' ),
                'instanceid' => new external_value( PARAM_TEXT, 'instance id of folder' )
            )
        );
    }

    /**
     * This method implements the logic for the API-Call.
     * IMPORTANT: Still in progress, currently not working.
     * 
     * @param $courseid The course id.
     * @param $cmid The module id.
     * @param $itemid File to update.
     * @param $instanceid The instance id.
     * 
     * @return Message: Successful.
     */
    public static function add_files_to_directory( $courseid, $cmid, $itemid, $instanceid) {
        global $DB, $CFG;
        require_once $CFG->dirroot . '/mod/' . '/folder' . '/lib.php';

        // Parameter validation.
        $params = self::validate_parameters(
            self::add_files_to_directory_parameters(),
            array(
                'courseid' => $courseid,
                'cmid' => $cmid,
                'itemid' => $itemid,
                'instanceid' => $instanceid
            )
        );

        // Ensure the current user has required permission in this course.
        $context = context_course::instance( $params[ 'courseid' ] );
        self::validate_context( $context );

        //Required permissions
        require_capability( 'mod/folder:addinstance', $context );

    
        $cmid        = $params[ 'cmid' ];
        $draftitemid = $params[ 'itemid' ];
        
        file_save_draft_area_files($draftitemid, $params[ 'instanceid' ], 'mod_folder', 'content', $draftitemid, array('subdirs'=>true));



        $update = [
            'message'=>'Successful',
        ];
        return $update;
    }

    /**
     * Obtains the Parameter which will be returned.
     */
    public static function add_files_to_directory_returns() {
        return new external_single_structure(
            array(
                'message' => new external_value( PARAM_TEXT, 'if the execution was successful' ),
                )
        );
    }
}