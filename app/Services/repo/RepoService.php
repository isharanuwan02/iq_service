<?php

/**
 * {@inheritdoc}
 */

namespace App\Services\Repo;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\Auth\ConnectAuthService;
use App\Models\Repo;
use App\Models\RepoCronjob;
use App\Models\RepoCommit;
use App\Models\RepoIssue;
use App\Models\RepoUser;
use App\Models\RepoPull;
use App\Models\RepoMatric;

class RepoService
{

    private $enumSuccess = 0;

    public function __construct()
    {
        $this->enumSuccess = app('config')->get("enum.common.log_status")['SUCCESS'];
    }

    public function viewDevIq($request)
    {

        DB::beginTransaction();
        try {

//            $authApi = new ConnectAuthService();
//            $userData = $authApi->getUserDetails($request);
//
//            if (!permissionLevelCheck('SELLER_ONLY', $userData['role_id'])) {
//                throw new Exception("SELLER_ONLY", getStatusCodes('UNAUTHORIZED'));
//            }
//            $userId = $userData['id'];
            $repoOwnerName = $request->ownername;
            $repoName = $request->reponame;
            $repoFullName = $repoOwnerName . '/' . $repoName;
            $repoData = $this->getRepoGitId($repoFullName);
            if (!$repoData['status']) {
                //add to cron table
                $this->addCronJob($repoName, $repoOwnerName);
                throw new Exception("REPO_NOT_FOUND_CHECK_IN_ANOTHER_TIME", getStatusCodes('EXCEPTION'));
            }

            //get repo users
            $repoUsers = RepoUser::where('org_name', '=', $repoOwnerName)
                    ->select(['git_user_id'])
                    ->get();

            if (sizeof($repoUsers) == 0) {
                throw new Exception("REPO_USERS_CANNOT_FIND", getStatusCodes('EXCEPTION'));
            }

            $commitCount = [];
            $issueCount = [];
            $openedPullCount = [];
            $closedPullCount = [];
            foreach ($repoUsers as $key => $value) {
                //get commits for the given repo
                $repoCommits = RepoCommit::where('repo_id', '=', $repoData['repo_id'])
                        ->where('committer_id', '=', $value['git_user_id'])
                        ->select(['id', 'committer_id', 'repo_id'])
                        ->get();
                $commitCount[$value['git_user_id']] = sizeof($repoCommits);

                $repoIssues = RepoIssue::where('repo_id', '=', $repoData['repo_id'])
                        ->where('assignee_id', '=', $value['git_user_id'])
                        ->select(['id', 'assignee_id', 'repo_id', 'issue_id'])
                        ->get();
                $issueCount[$value['git_user_id']] = sizeof($repoIssues);

                //count pulls
                $repoOpenedPulls = RepoPull::where('repo_id', '=', $repoData['repo_id'])
                        ->where('pull_owner_id', '=', $value['git_user_id'])
                        ->select(['id', 'pull_owner_id', 'repo_id', 'pull_status'])
                        ->where('pull_status', '=', 0)
                        ->get();
                $openedPullCount[$value['git_user_id']] = sizeof($repoOpenedPulls);

                $repoClosedPulls = RepoPull::where('repo_id', '=', $repoData['repo_id'])
                        ->where('pull_owner_id', '=', $value['git_user_id'])
                        ->select(['id', 'pull_owner_id', 'repo_id', 'pull_status'])
                        ->where('pull_status', '=', 0)
                        ->get();
                $closedPullCount[$value['git_user_id']] = sizeof($repoClosedPulls);
            }

//            dd($commitCount, $openedPullCount, $closedPullCount, $issueCount);
            //calculate
            $devIqs = [];
            foreach ($repoUsers as $key => $value) {
                $userId = $value['git_user_id'];
                $weightedSum = (0.5 * $commitCount[$userId]) + (1.5 * $openedPullCount[$userId]) + $closedPullCount[$userId] - ( 0.5 * $issueCount[$userId]);
                $precentage = ($weightedSum / 4) * 100;
                $matricExist = RepoMatric::where('user_id', '=', $userId)->first();
                if (!$matricExist) {
                    $saveMatric = new RepoMatric();
                    $saveMatric->user_id = $userId;
                    $saveMatric->score = $precentage;
                    $saveMatric->save();
                    DB::commit();
                } else {
                    $matricExist->score = $precentage;
                    $metricDate = $matricExist->created_at;
                    $matricExist->created_at = now();
                    $matricExist->save();
                    DB::commit();

                    //check the date and re-run the cron
                    $diff = now()->diffInMinutes($metricDate);
                    if ($diff > 1200) {
                        //add to cron table
                        $this->addCronJob($repoName, $repoOwnerName);
                    }
                }


                $devIqs[] = [$userId => $precentage];
            }


            addToLog('repo issues added', $this->enumSuccess);

            return response()->json([
                        'data' => $devIqs,
                        'message' => 'DEV_IQ'
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception);
            addToLog($exception->getMessage());
            return response()->json(['message' => $exception->getMessage()], $exception->getCode() == 0 ? getStatusCodes('VALIDATION_ERROR') : $exception->getCode());
        }
    }

    public function addCronJob($repoName, $repoOwnerName)
    {
        $cronExist = RepoCronjob::where('repo_owner', '=', $repoOwnerName)
                ->where('repo_name', '=', $repoName)
                ->where('cron_status', '=', 0)
                ->first();
        if (!$cronExist) {
            $cronInst = new RepoCronjob();
            $cronInst->repo_name = $repoName;
            $cronInst->repo_owner = $repoOwnerName;
            $cronInst->cron_status = 0;
            $cronInst->save();
            DB::commit();
        }
    }

    public function getRepoGitId($repoFullName)
    {
        $repoInst = Repo::where('full_name', '=', $repoFullName)->first();
        if (!$repoInst) {
            return ['status' => false];
        }

        return ['status' => true, 'repo_id' => $repoInst->git_id];
    }

}
