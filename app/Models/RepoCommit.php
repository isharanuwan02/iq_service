<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RepoCommit
 * 
 * @property int $id
 * @property int $repo_id
 * @property int $committer_id
 * @property Carbon $commited_date
 * @property string $commit_sha
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class RepoCommit extends Model
{
	protected $table = 'repo_commits';
	public $timestamps = false;

	protected $casts = [
		'repo_id' => 'int',
		'committer_id' => 'int'
	];

	protected $dates = [
		'commited_date'
	];

	protected $fillable = [
		'repo_id',
		'committer_id',
		'commited_date',
		'commit_sha'
	];
}
