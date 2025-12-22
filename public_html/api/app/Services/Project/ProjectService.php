<?php
namespace App\Services\Project;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Repositories\Interfaces\ProjectRepositoryInterface;

class ProjectService
{

    public function __construct(protected ProjectRepositoryInterface $projectRepository) { }

    public function all($request, $userId)
    {
        return $this->projectRepository->all($request, $userId);
    }


    public function create(StoreProjectRequest $request): mixed
    {
        if (!$request->validated()) {
            return false;
        }
        return $this->projectRepository->create($request);
    }

    public function update(UpdateProjectRequest $request, $project)
    {
        if (!$request->validated()) {
            return false;
        }

        return $this->projectRepository->update($request, $project);
    }
}
