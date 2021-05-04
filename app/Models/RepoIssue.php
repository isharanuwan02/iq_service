<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RepoIssue
 * 
 * @property int $id
 * @property int $issue_id
 * @property int $repo_id
 * @property int|null $assignee_id
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class RepoIssue extends Model
{
	protected $table = 'repo_issues';
	public $timestamps = false;

	protected $casts = [
		'issue_id' => 'int',
		'repo_id' => 'int',
		'assignee_id' => 'int'
	];

	protected $fillable = [
		'issue_id',
		'repo_id',
		'assignee_id'
	];
}
