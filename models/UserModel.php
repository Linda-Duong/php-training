<?php
require_once(__DIR__ . '/BaseModel.php');

class UserModel extends BaseModel {

    public function findUserById($id) {
        $id = intval($id);
        $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
        if ($stmt = $this->connection->prepare($sql)) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res ? $res->fetch_assoc() : null;
            $stmt->close();
            return $row ? [$row] : [];
        }
        return [];
    }

    public function auth($userNameOrEmail, $password) {
        $md5p = md5($password);
        $sql = "SELECT * FROM users WHERE (email = ? OR name = ?) AND password = ? LIMIT 1";
        if ($stmt = $this->connection->prepare($sql)) {
            $stmt->bind_param('sss', $userNameOrEmail, $userNameOrEmail, $md5p);
            $stmt->execute();
            $res = $stmt->get_result();
            $rows = [];
            if ($res) {
                while ($r = $res->fetch_assoc()) $rows[] = $r;
                $res->free();
            }
            $stmt->close();
            return $rows;
        }
        return [];
    }

    public function createUser($name, $password, $email, $fullname = '', $type = 0) {
        // Trim & basic validation
        $name = trim($name);
        $email = trim($email);
        if ($name === '' || $password === '' || $email === '') {
            throw new Exception("Vui lòng nhập đầy đủ thông tin.");
        }

        // Check existing email
        $sqlCheckEmail = "SELECT id FROM users WHERE email = ? LIMIT 1";
        if ($stmt = $this->connection->prepare($sqlCheckEmail)) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->close();
                throw new Exception("Email đã tồn tại, vui lòng chọn email khác!");
            }
            $stmt->close();
        }

        // Check existing username
        $sqlCheckName = "SELECT id FROM users WHERE name = ? LIMIT 1";
        if ($stmt = $this->connection->prepare($sqlCheckName)) {
            $stmt->bind_param('s', $name);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->close();
                throw new Exception("Username đã tồn tại, vui lòng chọn tên khác!");
            }
            $stmt->close();
        }

        $fullname = trim($fullname);
        $type = intval($type);
        $hash = md5($password);

        $sql = "INSERT INTO users (name, fullname, email, type, password) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $this->connection->prepare($sql)) {
            $stmt->bind_param('sssds', $name, $fullname, $email, $type, $hash);
            // Note: 'd' used for integer in bind_param above is a placeholder; mysqli expects i (integer).
            // To be safe, use i for integer:
            $stmt->close();
            // re-prepare with correct types
            $stmt = $this->connection->prepare("INSERT INTO users (name, fullname, email, type, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssis', $name, $fullname, $email, $type, $hash);
            if ($stmt->execute()) {
                $insertId = $stmt->insert_id;
                $stmt->close();
                return $insertId;
            } else {
                $err = $stmt->error;
                $stmt->close();
                throw new Exception("Tạo user thất bại: " . $err);
            }
        } else {
            throw new Exception("Tạo user thất bại (prepare).");
        }
    }

    public function getUsers($params = []) {
        if (!empty($params['keyword'])) {
            $kw = '%' . $params['keyword'] . '%';
            $sql = "SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC";
            if ($stmt = $this->connection->prepare($sql)) {
                $stmt->bind_param('ss', $kw, $kw);
                $stmt->execute();
                $res = $stmt->get_result();
                $rows = [];
                if ($res) {
                    while ($r = $res->fetch_assoc()) $rows[] = $r;
                    $res->free();
                }
                $stmt->close();
                return $rows;
            }
            return [];
        } else {
            $sql = "SELECT * FROM users ORDER BY id DESC";
            // use select helper
            return $this->select($sql);
        }
    }

    public function deleteUserById($id) {
        $id = intval($id);
        $sql = "DELETE FROM users WHERE id = ?";
        if ($stmt = $this->connection->prepare($sql)) {
            $stmt->bind_param('i', $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        }
        return false;
    }
}
