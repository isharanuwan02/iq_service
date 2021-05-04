<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RepoUser
 * 
 * @property int $id
 * @property string $org_name
 * @property int $git_user_id
 * @property string $git_user_login
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class RepoUser extends Model
{
	protected $table = 'repo_users';
	public $timestamps = false;

	protected $casts = [
		'git_user_id' => 'int'
	];

	protected $fillable = [
		'org_name',
		'git_user_id',
		'git_user_login'
	];
}
