<?php
require_once "Department.php";
require_once "../../db/db.php";

global $mysqli;

function find_all_departments(): array {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM departments");
    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = new Department($row["id"], $row["name"]);
    }
    return $departments;
}

function find_department_by_id(int $id): ?Department {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM departments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        return new Department($row["id"], $row["name"]);
    }
    return null;
}

function create_department_entity(Department $department): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO departments (name) VALUES (?)");
    $name = $department->getName();
    $stmt->bind_param("s", $name);
    return $stmt->execute();
}

function update_department(Department $department): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE departments SET name = ? WHERE id = ?");
    $name = $department->getName();
    $id = $department->getId();
    $stmt->bind_param("si", $name, $id);
    return $stmt->execute();
}

function delete_department_by_id(int $id): bool {
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
