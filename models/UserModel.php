<?php
require_once(__DIR__ . '/BaseModel.php');

class UserModel extends BaseModel {

    public function findUserById($id) {
        $id = intval($id);
        $sql = "SELECT * FROM users WHERE id = {$id} LIMIT 1";
        return $this->select($sql);
    }

    public function auth($userNameOrEmail, $password) {
        $u = $this->connection->real_escape_string($userNameOrEmail);
        $md5p = md5($password);
        $sql = "SELECT * FROM users 
                WHERE (email = '{$u}' OR name = '{$u}') 
                  AND password = '{$md5p}' 
                LIMIT 1";
        return $this->select($sql);
    }

    public function createUser($name, $password, $email, $fullname = '', $type = 0) {
        $n = $this->connection->real_escape_string($name);
        $e = $this->connection->real_escape_string($email);
        $f = $this->connection->real_escape_string($fullname);
        $t = intval($type);

        // check email
        $existing = $this->select("SELECT id FROM users WHERE email = '{$e}' LIMIT 1");
        if (!empty($existing)) {
            throw new Exception("Email đã tồn tại, vui lòng chọn email khác!");
        }

        // check username
        $existing = $this->select("SELECT id FROM users WHERE name = '{$n}' LIMIT 1");
        if (!empty($existing)) {
            throw new Exception("Username đã tồn tại, vui lòng chọn tên khác!");
        }

        $p = md5($password);
        $sql = "INSERT INTO users (name, fullname, email, type, password) 
                VALUES ('{$n}', '{$f}', '{$e}', {$t}, '{$p}')";

        $insertId = $this->insert($sql);
        if ($insertId === false) {
            throw new Exception("Tạo user thất bại");
        }
        return $insertId;
    }

    public function getUsers($params = []) {
        if (!empty($params['keyword'])) {
            $kw = $this->connection->real_escape_string($params['keyword']);
            $sql = "SELECT * FROM users 
                    WHERE name LIKE '%{$kw}%' 
                       OR email LIKE '%{$kw}%' 
                    ORDER BY id DESC";
            return $this->select($sql);
        } else {
            return $this->select("SELECT * FROM users ORDER BY id DESC");
        }
    }

    public function deleteUserById($id) {
        $id = intval($id);
        return $this->delete("DELETE FROM users WHERE id = {$id}");
    }
}
