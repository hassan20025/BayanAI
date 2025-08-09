<?php
require_once "DepartmentRepository.php";
require_once "../../utils/utils.php";
header('Content-Type: application/json');

function get_all_departments() {
    return find_all_departments();
}

function get_department_by_id($id) {
    $dept = find_department_by_id($id);
    if (!$dept) {
        respond(404, "error", ["message" => "Department not found."]);
    }
    return $dept;
}

function create_department($name) {
    if (!$name) {
        respond(400, "error", "Name is required");
    }

    $department = new Department(null, $name);
    $created = create_department_entity($department);

    if (!$created) {
        respond(500, "error", "Failed to create department.");
    }

    respond(201, "success", $department);
}

function update_department_wrapper($id, $data) {
    $dept = get_department_by_id($id);
    if (!$dept) {
        return false;
    }

    if (isset($data['name'])) {
        $dept->setName($data['name']);
    }

    return update_department($dept);
}

function delete_department($id) {
    delete_department_by_id($id);
    respond(200, "success", ["message" => "Department deleted successfully"]);
}
