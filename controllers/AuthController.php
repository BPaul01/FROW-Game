<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

include_once($_SERVER["DOCUMENT_ROOT"] . "/entities/UserEntity.php");

class AuthController
{
    private $conn;
    private static $secret_Key  = '%aaSWvtJ98os_b<IQ_c$j<_A%bo_[xgct+j$d6LJ}^<pYhf+53k^-R;Xs<l%5dF';
    private static $domainName = "https://127.0.0.1";

    public function __construct($db)
    {
        $this->conn = $db->getDb();
    }

    public function processLoginRequest() {
        if(isset($_POST['username']) && isset($_POST['password']))
        {
            $user = trim(htmlspecialchars($_POST['username']));
            $password = trim(htmlspecialchars($_POST['password']));
            $hashedPassword = crypt($password, '$5$rounds=5000$' . static::$secret_Key . '$');

            $stmt = $this->conn->prepare("SELECT * FROM users WHERE `name` = ? AND `password` = ?");
            $stmt->bind_param("ss", $user, $hashedPassword);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $jwt = $this->createJWT($row['name'], $row['id'], $row['is_admin']);
                return $jwt['body'];
            }
        }

        header('HTTP/1.1 401 Unauthorized');
        exit();
    }

    public function signUpRequest()
    {
        $success = true;
        $message = "";

        $user = trim(htmlspecialchars($_POST['username']));
        $password = trim(htmlspecialchars($_POST['password']));
        $confirmPassword = trim(htmlspecialchars($_POST['confirmPassword']));

        if (empty($user) || empty($password) || empty($confirmPassword)) {
            $message = 'All fields are required.';
            $success = false;
        }

        if ($password != $confirmPassword) {
            $message = 'Password should math Confirm Password.';
            $success = false;
        }

        if ($this->isUsernameTaken($_POST['username'])) {
            $message = 'Username is already taken.';
            $success = false;
        }

        if($success)
        {
            $hashedPassword = crypt($_POST['password'], '$5$rounds=5000$' . static::$secret_Key . '$');
            $user_id = $this->storeUserData($_POST['username'], $hashedPassword);
            $jwt = $this->createJWT($_POST['username'], $user_id, 0);
        }

        return json_encode([
            "success" => $success,
            "token" => $jwt['body'] ?? "",
            "message" => $message
        ]);
    }

    public function isUsernameTaken($username) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM users WHERE `name` = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($row = $result->fetch_assoc()) {
            return $row['count'] > 0;
        }

        return false;
    }

    public function storeUserData($username, $password)
    {
        $stmt = $this->conn->prepare('INSERT INTO users (`name`, `password`) VALUES (?, ?)');
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();

        return $stmt->insert_id;
    }

    private function createJWT($userName, $userId, $isAdmin) {
        $date   = new DateTimeImmutable();
        $request_data = [
            'iat'  => $date->getTimestamp(),         // ! Issued at: time when the token was generated
            'iss'  => static::$domainName,                   // ! Issuer
            'nbf'  => $date->getTimestamp(),         // ! Not before
            'exp'  => time() + 3600 * 24,                    // ! Expire
            'user_name' => $userName,               // User name
            'user_id' => $userId,                   // User id
            'isAdmin' => $isAdmin,                   // User is admin
        ];

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['content_type_header'] = 'Content-Type: application/json';
        $response['body'] = JWT::encode(
            $request_data,
            static::$secret_Key,
            'HS512'
        );

        return $response;
    }

    function checkJWTExistance () {
        // Check JWT
        if (! preg_match('/Bearer\s(\S+)/', $this->getAuthorizationHeader(), $matches)) {
            header('HTTP/1.0 400 Bad Request');
            echo 'Token not found in request';
            exit;
        }
        return $matches[1];
    }

    public static function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHENTICATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHENTICATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public static function validateJWT($jwt) {
        $secret_Key = static::$secret_Key;

        try {
            $tokenData = JWT::decode($jwt, new Key($secret_Key, 'HS512'));
        } catch (Exception $e) {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }
        $now = new DateTimeImmutable();
        $domainName = static::$domainName;

        if ($tokenData->iss !== $domainName ||
            $tokenData->nbf > $now->getTimestamp() ||
            $tokenData->exp < $now->getTimestamp())
        {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }

        return $tokenData;
    }

    public static function getUserFomToken($jwt)
    {
        $tokenData = static::validateJWT($jwt);
        return new UserEntity($tokenData->user_id, $tokenData->user_name, $tokenData->isAdmin);
    }

    public static function parseBearerToken($authorizationHeader)
    {
        if (preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches))
        {
            return $matches[1];
        }
        return null;
    }


}
