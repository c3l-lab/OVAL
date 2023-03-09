<?php
/** 
 * This file contains configuration values for OVAL.
 * 
 * Settings for OVAL application is stored here. 
 * Values that depends on server environment or sensitive data are placed in .env file.
 * 
 **/

return [
/** -----------------------------------------------------
 * Application default settings.
 * -----------------------------------------------------
 * group_video_hide : default value for group_video.hide (if set to true, it is not visible to students)
 * group_video_show_analysis : default value for group_video.show_analysis (if set to true, shows text analysis result)
 * 
 */
    "defaults" => [
        "group_video_hide" => 1,
        "group_video_show_analysis" => 1,

    ],

    /**
     * Course wide objects
     * 
     * Objects set to "true" are course-wide. (All groups in same course with same video share the course-wide objects)
     */
    "course_wide" => [
        "comment_instruction" => 1,
        "point" => 1,
        "quiz" => 1,
    ]
];