<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RepoCronjob
 * 
 * @property int $id
 * @property string $repo_owner
 * @property string $repo_name
 * @property int $cron_status
 * @property Carbon $created_at
 *
 * @package App\Models
 */
class RepoCronjob extends Model
{
	protected $table = 'repo_cronjobs';
	public $timestamps = false;

	protected $casts = [
		'cron_status' => 'int'
	];

	protected $fillable = [
		'repo_owner',
		'repo_name',
		'cron_status'
	];
}
