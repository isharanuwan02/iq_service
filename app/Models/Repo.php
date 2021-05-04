<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Repo
 * 
 * @property int $id
 * @property int $git_id
 * @property int $owner_id
 * @property string $full_name
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class Repo extends Model
{
	protected $table = 'repos';
	public $timestamps = false;

	protected $casts = [
		'git_id' => 'int',
		'owner_id' => 'int'
	];

	protected $fillable = [
		'git_id',
		'owner_id',
		'full_name'
	];
}
