<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RepoPull
 * 
 * @property int $id
 * @property int $pull_id
 * @property int $repo_id
 * @property int $pull_owner_id
 * @property bool $pull_status
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class RepoPull extends Model
{
	protected $table = 'repo_pulls';
	public $timestamps = false;

	protected $casts = [
		'pull_id' => 'int',
		'repo_id' => 'int',
		'pull_owner_id' => 'int',
		'pull_status' => 'bool'
	];

	protected $fillable = [
		'pull_id',
		'repo_id',
		'pull_owner_id',
		'pull_status'
	];
}
