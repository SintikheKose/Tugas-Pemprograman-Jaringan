<?php
namespace Src\Controllers;

class UserController {
    public function index() {
        $data = [
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'Sintikhe Kose']
        ];
        echo json_encode(['success' => true, 'data' => $data]);
    }

    public function show($id) {
        $data = ['id' => (int)$id, 'name' => "User $id"];
        echo json_encode(['success' => true, 'data' => $data]);
    }
}
