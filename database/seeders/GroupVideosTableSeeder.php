<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GroupVideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('group_videos')->insert([
            ['group_id'=>1, 'video_id'=>1, 'hide'=>false,
                'annotation_config' => json_encode([
                    'label' => 'New Annotation',
                    'header_name' => "ADD ANNOTATION",
                    'downloadable' => true,
                    'is_show_annotation_button' => true
                ])
            ],
            ['group_id'=>1, 'video_id'=>2, 'hide'=>false,
                'annotation_config' => json_encode([
                    'label' => 'New Annotation',
                    'header_name' => "ADD ANNOTATION",
                    'downloadable' => true,
                    'is_show_annotation_button' => true
                ])
            ],
            ['group_id'=>2, 'video_id'=>3, 'hide'=>false,
                'annotation_config' => json_encode([
                    'label' => 'New Annotation',
                    'header_name' => "ADD ANNOTATION",
                    'downloadable' => true,
                    'is_show_annotation_button' => true
                ])
            ],
            ['group_id'=>2, 'video_id'=>4, 'hide'=>false,
                'annotation_config' => json_encode([
                    'label' => 'New Annotation',
                    'header_name' => "ADD ANNOTATION",
                    'downloadable' => true,
                    'is_show_annotation_button' => true
                ])
            ],
            ['group_id'=>2, 'video_id'=>5, 'hide'=>false,
                'annotation_config' => json_encode([
                    'label' => 'New Annotation',
                    'header_name' => "ADD ANNOTATION",
                    'downloadable' => true,
                    'is_show_annotation_button' => true
                ])
            ],
            ['group_id'=>3, 'video_id'=>6, 'hide'=>false,
                'annotation_config' => json_encode([
                    'label' => 'New Annotation',
                    'header_name' => "ADD ANNOTATION",
                    'downloadable' => true,
                    'is_show_annotation_button' => true
                ])
            ],
            ['group_id'=>4, 'video_id'=>7, 'hide'=>false,
                'annotation_config' => json_encode([
                    'label' => 'New Annotation',
                    'header_name' => "ADD ANNOTATION",
                    'downloadable' => true,
                    'is_show_annotation_button' => true
                ])
            ],
            ['group_id'=>1, 'video_id'=>8, 'hide'=>false,
                'annotation_config' => json_encode([
                    'label' => 'New Annotation',
                    'header_name' => "ADD ANNOTATION",
                    'downloadable' => true,
                    'is_show_annotation_button' => true
                ])
            ],
            ['group_id'=>1, 'video_id'=>9, 'hide'=>false,
                'annotation_config' => json_encode([
                    'label' => 'New Annotation',
                    'header_name' => "ADD ANNOTATION",
                    'downloadable' => true,
                    'is_show_annotation_button' => true
                ])
            ],
            ['group_id'=>1, 'video_id'=>10, 'hide'=>false,
                'annotation_config' => json_encode([
                    'label' => 'New Annotation',
                    'header_name' => "ADD ANNOTATION",
                    'downloadable' => true,
                    'is_show_annotation_button' => true
                ])
            ],
        ]);
    }
}
