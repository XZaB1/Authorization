<?php
// заголовки
header("Access-Control-Allow-Origin: http://authentication-jwt/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// файлы необходимые для соединения с БД
include_once 'config/database.php';
include_once 'objects/user.php';

// получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// создание объекта 'User'
$user = new User($db);

// получаем данные
$data = json_decode(file_get_contents("php://input"));

// устанавливаем значения
$user-> email = $data->email;

$email_exists = $user->emailExists();


// подключение файлов jwt
include_once 'config/core.php';
include_once 'libs/php-jwt-6.0.0/src/BeforeValidException.php';
include_once 'libs/php-jwt-6.0.0/src/ExpiredException.php';
include_once 'libs/php-jwt-6.0.0/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-6.0.0/src/JWT.php';
use \Firebase\JWT\JWT;



// существует ли электронная почта и соответствует ли пароль тому, что находится в базе данных
if ( $email_exists && password_verify($data->password, $user->password) ) {

    $token = array(
        "iss" => $iss,
        "aud" => $aud,
        "iat" => $iat,
        "nbf" => $nbf,
        "data" => array(
            "id" => $user->id,
            "firstname" => $user->firstname,
            "lastname" => $user->lastname,
            "email" => $user->email
        )
    );

    // код ответа
    http_response_code(200);

    // создание jwt
    $jwt = JWT::encode($token, $key, 'HS256');
    echo json_encode(
        array(
            "message" => "Успешный вход в систему.",
            "jwt" => $jwt
        )
    );
            if("remember_me" == "1")
    {
     $token = JWTAuth::attempt($credentials, ['exp' => Carbon\Carbon::now()->addDays(7)->timestamp]);
     // и ищем их в БД в таблице пользователей и принимаем решение -- авторизован пользователь или нет
    }

}

// Если электронная почта не существует или пароль не совпадает,
// сообщим пользователю, что он не может войти в систему
else {

    // код ответа
    http_response_code(401);

    // сказать пользователю что войти не удалось
    echo json_encode(array("message" => "Ошибка входа."));
};

?>
