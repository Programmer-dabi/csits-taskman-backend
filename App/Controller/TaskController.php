<?php

namespace App\Controller;

use App\Models\Task;
use Flight;

class TaskController
{
    private Task $taskModel;

    public function __construct()
    {
        $this->taskModel = new Task();
    }

    // Send a JSON response
    private function respond(array $data, int $statusCode = 200): void
    {
        Flight::json($data, $statusCode);
    }

    // GET /tasks
    // Retrieve all tasks
    public function index(): void
    {
        // Update overdue statuses first
        $this->taskModel->markOverdue();
        $this->taskModel->updateDueStatuses();

        // Retrieve the updated task list
        $tasks = $this->taskModel->getAll();

        $this->respond([
            'success' => true,
            'data' => $tasks
        ]);
    }

    // GET /tasks/@id
    // Retrieve a single task
    public function show(int $id): void
    {
        $task = $this->taskModel->getById($id);

        if (!$task) {
            $this->respond([
                'success' => false,
                'message' => 'Task not found.'
            ], 404);

            return;
        }

        $this->respond([
            'success' => true,
            'data' => $task
        ]);
    }

    // POST /tasks
    // Create a task
    public function store(): void
    {
        $data = Flight::request()->data->getData();

        $taskName = trim($data['task_name'] ?? '');
        $organization = trim($data['sub_organization'] ?? '');
        $dueDate = $data['due_date'] ?? '';
        $status = $data['status'] ?? 'Pending';

        if (
            $taskName === '' ||
            $organization === '' ||
            $dueDate === ''
        ) {
            $this->respond([
                'success' => false,
                'message' => 'Task name, organization, and due date are required.'
            ], 422);

            return;
        }

        if (!in_array($status, ['Pending', 'In Progress', 'Completed'], true)) {
            $this->respond([
                'success' => false,
                'message' => 'Invalid task status.'
            ], 422);

            return;
        }

        $timestamp = strtotime($dueDate);

        if ($timestamp === false) {
            $this->respond([
                'success' => false,
                'message' => 'Invalid due date.'
            ], 422);

            return;
        }

        $data = [
            'task_name' => $taskName,
            'sub_organization' => $organization,
            'due_date' => date('Y-m-d H:i:s', $timestamp),
            'status' => $status
        ];

        $created = $this->taskModel->create($data);

        if (!$created) {
            $this->respond([
                'success' => false,
                'message' => 'Failed to create task.'
            ], 500);

            return;
        }

        $this->respond([
            'success' => true,
            'message' => 'Task created successfully.'
        ], 201);
    }

    // PUT /tasks/@id
    // Update a task
   public function update(int $id): void
{
    $existingTask = $this->taskModel->getById($id);

    if (!$existingTask) {
        $this->respond([
            'success' => false,
            'message' => 'Task not found.'
        ], 404);
        return;
    }

    $data = Flight::request()->data->getData();
    $status = $data['status'] ?? '';

    $allowedStatuses = [
        'Pending',
        'Due Tommorrow',
        'Accomplished',
        'Unaccomplished'
    ];

    if (!in_array($status, $allowedStatuses, true)) {
        $this->respond([
            'success' => false,
            'message' => 'Invalid task status.'
        ], 422);
        return;
    }

    $updated = $this->taskModel->updateStatus($id, $status);
    
    $this->respond([
        'success' => $updated,
        'message' => $updated
            ? 'Status updated successfully.'
            : 'Failed to update status.'
    ], $updated ? 200 : 500);
}

    // DELETE /tasks/@id
    // Delete a task
    public function destroy(int $id): void
    {
        $existingTask = $this->taskModel->getById($id);

        if (!$existingTask) {
            $this->respond([
                'success' => false,
                'message' => 'Task not found.'
            ], 404);

            return;
        }

        $deleted = $this->taskModel->delete($id);

        $this->respond([
            'success' => $deleted,
            'message' => $deleted
                ? 'Task deleted successfully.'
                : 'Failed to delete task.'
        ], $deleted ? 200 : 500);
    }

    // GET /tasks/search?keyword=website
    // Search by task name or organization
    public function search(): void
    {
        $keyword = trim(Flight::request()->query['keyword'] ?? '');

        $tasks = $this->taskModel->search($keyword);

        $this->respond([
            'success' => true,
            'data' => $tasks
        ]);
    }

    // GET /tasks/status/@status
    // Filter tasks by status
    public function filterByStatus(string $status): void
    {
        $this->taskModel->markOverdue();
        $this->taskModel->updateDueStatuses();
        if (!in_array($status, ['Pending', 'Due Tommorrow', 'Accomplished', 'Unaccomplished','All'], true)) {
            $this->respond([
                'success' => false,
                'message' => 'Invalid task status.'
            ], 422);

            return;
        }
        if($status === 'All'){
            $this->index();
            return;
        }

        $tasks = $this->taskModel->getByStatus($status);

        $this->respond([
            'success' => true,
            'data' => $tasks
        ]);
    }

    // GET /tasks/overdue
    // Retrieve overdue, incomplete tasks
    // public function overdue(): void
    // {
    //     $tasks = $this->taskModel->getOverdue();

    //     $this->respond([
    //         'success' => true,
    //         'data' => $tasks
    //     ]);
    // }
}