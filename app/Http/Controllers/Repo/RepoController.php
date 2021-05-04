<?php

namespace App\Http\Controllers\Repo;

use App\Http\Controllers\Controller;
use App\Services\Repo\RepoService;
use Illuminate\Http\Request;

class RepoController extends Controller
{

    private $repoService;

    public function __construct()
    {
        $this->repoService = new RepoService();
    }

    /**
     * {@inheritdoc}
     */
    public function viewDevIq(Request $request)
    {
        return $this->repoService->viewDevIq($request);
    }

}
