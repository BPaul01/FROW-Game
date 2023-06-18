<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{

    private $conn;
    private $secret_Key  = '%aaSWvtJ98os_b<IQ_c$j<_A%bo_[xgct+j$d6LJ}^<pYhf+53k^-R;Xs<l%5dF';
    private $domainName = "https://127.0.0.1";

    public function __construct($db)
    {
        $this->conn = $db->getDb();
    }

    public function processLoginRequest() {
        //TODO: check if the username and password are correct and if so
        if(isset($_POST['username']) && isset($_POST['password'])) {
            $jwt = $this->createJWT($_POST['username']);
        }
        return $jwt['body'] ?? "";
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
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $this->storeUserData($_POST['username'], $hashedPassword);
            $jwt = $this->createJWT($_POST['username']);
        }

        return json_encode([
            "success" => $success,
            "token" => $jwt['body'] ?? "",
            "message" => $message
        ]);
    }

    public function isUsernameTaken($username) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
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
        $stmt = $this->conn->prepare('INSERT INTO users (`username`, `password`) VALUES (?, ?)');
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
    }

    private function createJWT($user) {
        $date   = new DateTimeImmutable();
        $request_data = [
            'iat'  => $date->getTimestamp(),         // ! Issued at: time when the token was generated
            'iss'  => $this->domainName,                   // ! Issuer
            'nbf'  => $date->getTimestamp(),         // ! Not before
            'exp'  => time() + 3600 * 24,                    // ! Expire
            'userName' => $user,                 // User name
        ];

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['content_type_header'] = 'Content-Type: application/json';
        $response['body'] = JWT::encode(
            $request_data,
            $this->secret_Key,
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

    function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }

    public function validateJWT( $jwt ) {
        $secret_Key = $this -> secret_Key;

        try {
            $token = JWT::decode($jwt, new Key($secret_Key, 'HS512'));
        } catch (Exception $e) {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }
        $now = new DateTimeImmutable();
        $domainName = $this -> domainName;

        if ($token->iss !== $domainName ||
            $token->nbf > $now->getTimestamp() ||
            $token->exp < $now->getTimestamp())
        {
            header('HTTP/1.1 401 Unauthorized');
            exit;
        }
    }

}
