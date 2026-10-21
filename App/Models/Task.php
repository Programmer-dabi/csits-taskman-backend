<?php

namespace App\Models;

use App\Database\Database;
use PDO;

class Task
{
    private PDO $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Get all tasks
    public function getAll(): array
    {
        $sql = "SELECT * FROM tasks ORDER BY due_date ASC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    // Get a task by ID
    public function getById(int $id): array|false
    {
        $sql = "SELECT * FROM tasks WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    // Create a task
    public function create(array $data): bool
    {
        $sql = "INSERT INTO tasks (
                    task_name,
                    sub_organization,
                    due_date,
                    status
                ) VALUES (
                    :task_name,
                    :sub_organization,
                    :due_date,
                    :status
                )";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'task_name' => $data['task_name'],
            'sub_organization' => $data['sub_organization'],
            'due_date' => $data['due_date'],
            'status' => $data['status'] ?? 'Pending'
        ]);
    }


    // Delete a task
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM tasks WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    // Filter tasks by status
    public function getByStatus(string $status): array
    {
        $sql = "SELECT * FROM tasks
                WHERE status = :status
                ORDER BY due_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['status' => $status]);

        return $stmt->fetchAll();
    }


        public function markOverdue(): int
    {
        $sql = "UPDATE tasks
                SET status = 'Unaccomplished'
                WHERE due_date < NOW()
                  AND status IN ('Pending', 'Due Tommorrow')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        // Return the number of tasks changed
        return $stmt->rowCount();
    }
    public function updateDueStatuses(): int
{
    $sql = "UPDATE tasks
            SET status = CASE
                WHEN due_date < NOW() THEN 'Unaccomplished'
                WHEN DATE(due_date) = CURDATE() + INTERVAL 1 DAY
                    THEN 'Due Tommorrow'
                ELSE 'Pending'
            END
            WHERE status IN (
                'Pending',
                'Due Tommorrow',
                'Unaccomplished'
            )";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->rowCount();
}

        public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE tasks SET status = :status WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'status' => $status,
            'id' => $id
        ]);
    }
}


?>