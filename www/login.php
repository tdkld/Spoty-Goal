<?php
if (isset($_GET['code'])) {
    $tokens = exchange_temp_code($_GET['code']);
    if (isset($tokens['access_token']) && isset($tokens['refresh_token'])) {
        try {
            $db = connect_to_db();
            $token_id = save_token($db, $tokens['access_token'], $tokens['refresh_token'], $tokens['expires_in']);
            $username = fetch_spotify_user($tokens['access_token']);
            $user_id = save_user($db, $token_id, $username);
            authenticate($db, $user_id);
            header('Location: /');
            exit();
        } catch (Exception $e) {
            die('Error: ' . $e);
        }
    } else {
        print_r("Error exchanging temporary code to access token: \n");
        print_r($tokens);
    }
} elseif (isset($_GET['error'])) {
    echo "Error signing in using Spotify: ". $_GET['error'];
} else {
    $auth_endpoint = "https://accounts.spotify.com/authorize";
    $params = array(
        'client_id' => getenv('SPOTIFY_CLIENT_ID'),
        'response_type' => 'code',
        'redirect_uri' => getenv('REDIRECT_URI'),
        'show_dialog' => true
    );
    $temp_code_url = $auth_endpoint . '?' . http_build_query($params);
    header("Location: $temp_code_url"); 
    exit();
}

function exchange_temp_code($temp_code)
{
    $params = array(
        'grant_type' => 'authorization_code',
        'code' => $temp_code,
        'redirect_uri' => getenv('REDIRECT_URI')
    );
    $credentials = getenv('SPOTIFY_CLIENT_ID') . ':' . getenv('SPOTIFY_CLIENT_SECRET');

    $request = curl_init("https://accounts.spotify.com/api/token");
    curl_setopt_array($request, array(
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . base64_encode($credentials),
            'Content-Type: application/x-www-form-urlencoded'
        ),
        CURLOPT_POSTFIELDS => http_build_query($params)
    ));

    $response = curl_exec($request);
    if(!$response) {
        die(curl_error($request));
    }

    $tokens = json_decode($response, true);
    curl_close($request);

    return $tokens;
}

function fetch_spotify_user($token)
{
    $request = curl_init("https://api.spotify.com/v1/me");
    curl_setopt_array($request, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json'
        )
    ));

    $response = curl_exec($request);
    if(!$response) {
        throw new Exception('Error fetching Spotify user data: ' . curl_error($request));
    }

    $user = json_decode($response, true);
    curl_close($request);

    if (isset($user['display_name'])) {
        return $user['display_name'];
    } elseif (isset($user['id'])) {
        return $user['id'];
    } else {
        throw new Exception('Wrong user type: ' .$user);
    }
}

function connect_to_db()
{
    $dbname = getenv('DATABASE_NAME');
    $dbhost = getenv('DATABASE_HOST');
    $dbport = intval(getenv('DATABASE_PORT'));
    $dsn = "pgsql:dbname=".$dbname.";host=".$dbhost.";port=".$dbport.";";
    $user = getenv('DATABASE_USER');
    $password = getenv('DATABASE_PASSWORD');
    return new PDO($dsn, $user, $password);
}

function retrieve_token(PDO $db, $access_token)
{
    $query = 'SELECT * FROM tokens WHERE access_token=?';
    $statement = $db->prepare($query);
    $statement->execute(array($access_token));
    return $statement->fetch(PDO::FETCH_ASSOC);
}

function create_token(PDO $db, $access_token, $refresh_token, int $expires_in)
{
    $expire_date = time() + $expires_in;
    $query = 'INSERT INTO tokens (access_token, refresh_token, expire_date) VALUES (?, ?, to_timestamp(?))';
    $statement = $db->prepare($query);
    $statement->execute(array($access_token, $refresh_token, $expire_date));   
}

function save_token(PDO $db, $access_token, $refresh_token, $expires_in)
{
    $token = retrieve_token($db, $access_token);
    if ($token == NULL) {
        create_token($db, $access_token, $refresh_token, $expires_in);
        $token = retrieve_token($db, $access_token);
    }
    return $token['token_id'];
}

function retrieve_user(PDO $db, $token_id)
{
    $query = 'SELECT * FROM users WHERE token_id=?';
    $statement = $db->prepare($query);
    $statement->execute(array($token_id));
    return $statement->fetch(PDO::FETCH_ASSOC);
}

function create_user(PDO $db, $token_id, $username)
{
    $query = 'INSERT INTO users (token_id, username) VALUES (?, ?)';
    $statement = $db->prepare($query);
    $statement->execute(array($token_id, $username));
}

function save_user(PDO $db, $token_id, $username)
{
    $user = retrieve_user($db, $token_id);
    if ($user == NULL) {
        create_user($db, $token_id, $username);
        $user = retrieve_user($db, $token_id);
    }
    return $user['user_id'];
}

function create_session(PDO $db, $session_id, $user_id)
{
    $query = 'INSERT INTO sessions (session_id, user_id) VALUES (?, ?)';
    $statement = $db->prepare($query);
    $statement->execute(array($session_id, $user_id));
}

function authenticate(PDO $db, $user_id)
{
    session_start();
    $session_id = generate_random_string(32);
    create_session($db, $session_id, $user_id);
    $_SESSION['AUTH_SESSION_ID'] = $session_id;
}

function generate_random_string($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, $characters_length - 1)];
    }
    return $random_string;
}
?>
