<?php
/**
 * This class defines the functions and konfigurations of the external service.
 * 
 * @author Daniel Schröter
 */

defined('MOODLE_INTERNAL') || die();
$functions = array(
    'local_course_add_new_course_module_url' => array(
        'classname' => 'local_sync_service_external',
        'methodname' => 'add_new_course_module_url',
        'classpath' => 'local/sync_service/externallib.php',
        'description' => 'Add course module URL',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/url:addinstance',
    ),
    'local_course_add_new_course_module_resource' => array(
        'classname' => 'local_sync_service_external',
        'methodname' => 'add_new_course_module_resource',
        'classpath' => 'local/sync_service/externallib.php',
        'description' => 'Add course module Resource',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/resource:addinstance',
    ),
    'local_course_move_module_to_specific_position' => array(
        'classname' => 'local_sync_service_external',
        'methodname' => 'move_module_to_specific_position',
        'classpath' => 'local/sync_service/externallib.php',
        'description' => 'Moves a module to a dedicated position',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'moodle/course:movesections'
    ),
    'local_course_add_new_course_module_directory' => array(
        'classname' => 'local_sync_service_external',
        'methodname' => 'add_new_course_module_directory',
        'classpath' => 'local/sync_service/externallib.php',
        'description' => 'Add course modul folder',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/folder:addinstance'
    ),
    'local_course_add_files_to_directory' => array(
        'classname' => 'local_sync_service_external',
        'methodname' => 'add_files_to_directory',
        'classpath' => 'local/sync_service/externallib.php',
        'description' => 'Add files to folder',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'mod/folder:addinstance'
    )


);

$services = array(
    'Course Sync Extension Service' => array(
        'functions' => array(
            'local_course_add_new_course_module_url',
            'local_course_add_new_course_module_resource',
            'local_course_move_module_to_specific_position',
            'local_course_add_new_course_module_directory',
            'local_course_add_files_to_directory',
            'core_course_get_contents',
            'core_enrol_get_users_courses',
            'core_webservice_get_site_info',
            'core_course_delete_modules'
        ),
        'restrictedusers' => 1,
        'enabled' => 1,
        'shortname' => 'sync_service',
        'downloadfiles' => 1, 
        'uploadfiles'  => 1      
    )
);