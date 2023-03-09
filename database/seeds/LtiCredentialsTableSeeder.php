<?php

use Illuminate\Database\Seeder;
use oval\LtiConsumer;
use oval\LtiCredential;

class LtiCredentialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //-- DB credentials for moodles (LTI consumers)
        //-- These are linked to records in lti2_consumer table, so get the two seeded records..
        $moo = LtiConsumer::where('name', 'Moo')->first();
        $noo = LtiConsumer::where('name', 'Noo')->first(); 

        //-- because LtiCredential object is set up to encrypt/decrypt
        //-- username and password automatically, create object instead of
        //-- entering db record manually...
        $c1 = new LtiCredential;
        $c1->db_type = 'mysql';
        $c1->host = '127.0.0.1';
        $c1->port = 3306;
        $c1->database = 'moodle';
        $c1->username = 'moodle';
        $c1->password = 'moodle';
        $c1->prefix = 'mdl_';
        $c1->consumer_id= $moo->consumer_pk;
        $c1->save();

        
        $c2 = new LtiCredential;
        $c2->db_type = 'mysql';
        $c2->host = '127.0.0.1';
        $c2->port = 3306;
        $c2->database = 'moodle2';
        $c2->username = 'moodle2';
        $c2->password = 'moodle2';
        $c2->prefix = 'mdl_';
        $c2->consumer_id = $noo->consumer_pk;
        $c2->save();
    }
}