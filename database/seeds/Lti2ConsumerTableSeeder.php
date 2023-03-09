<?php

use Illuminate\Database\Seeder;

class Lti2ConsumerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //-- Enter key and secret, then use the same for moodle
        //-- (name can be null)
        // DB::table('lti2_consumer')->insert([
        //     'name' => '',
        //     'consumer_key256' => '',
        //     'secret' => '',
        //     'enabled' => true
        // ]);

        DB::table('lti2_consumer')->insert([
            'name' => 'Moo',
            'consumer_key256' => '7671f502-c8d4-4f35-9cc4-d21e81a2a4ae',
            'secret' => '5t$ZvAh2-zS_M55cDhr675rk',
            'enabled' => true
        ]);
        DB::table('lti2_consumer')->insert([
            'name' => 'Noo',
            'consumer_key256' => '#h;E`!dtplT{yy#60Y%3',
            'secret' => 'tTIGGC}UiaAx-2S[D&p8',
            'enabled' => true
        ]);
    }
}