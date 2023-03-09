<?php

namespace oval;

use Illuminate\Database\Eloquent\Model;

/**
 * Model class for table 'group_members'
 */
class GroupMember extends Model
{
	protected $table = 'group_members';
	protected $fillable = ['user_id', 'group_id'];
}
