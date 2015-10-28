<?php
class users extends sqlInt {
  public $_user;
  public $_config;

  public function __construct($config) {
    $this->newConnection(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATABASE);
    $this->_config = $config;
  }

  public function userInfo() {
    return $this->_user;
  }

  // Password Generation functions

  public function hashPassword($password) {
    $options = [
      'cost' => 11,
    ];

    $resulting = password_hash($password, PASSWORD_DEFAULT, $options);

    if ($resulting == FALSE) {
      return FALSE;
    } else {
      return $resulting;
    }
  }


  // Account checking / retrieval functions

  public function checkPassword($username, $password) {


    $this->query_userExists($username);
    if (!$this->getRows()) {
      $error = array (
        'isError' => TRUE,
        'error' => 'UserNonExistent'
      );
      return $error;
    }

    $options = [
      'column' => 'username',
      'value'  => $username
    ];
    $this->query_getUserData($options);

    $userData = $this->getRows();
    if (password_verify($password, $userData[0]["password"])) {
      return true;
    } else {
      return false;
    }

  }

  public function createLoginToken($username, $validFor = 5184000) {

    $token = hash('sha512', uniqid(rand(), true));

    $options = [
      'username' => $username,
      'validUntil' => (time() + $validFor), // Two months until the token expires
      'userAgent' => $_SERVER['HTTP_USER_AGENT'],
      'token' => $token
    ];

    $this->query_insertLoginToken($options);
    return $options;
  }


  public function checkLoginToken($token) {

    $options = [
      'token' => $token
    ];

    $result = $this->query_retrieveLoginToken($options);
    if ($result[0]['is_valid'] == 1) {
      return $result[0]['username'];
    } else {
      return false;
    }

  }

  public function listLoginTokens($username) {

    $options = [
      'username' => $username,
      'token' => '*'
    ];

    $this->query_retrieveLoginToken($options);
    return($this->getRows());

  }

  public function invalidateLoginToken($username, $token) {
    // Make $token = * to invalidate ALL tokens
    $options = [
      'username' => $username,
      'token' => $token
    ];

    $this->query_retrieveLoginToken($options);
    $this->query_invalidateLoginToken($options);
    return true;

  }


 public function generateAPIkey($username) {
   $rand = openssl_random_pseudo_bytes(2048);
   $apikey = $rand;
   $i = 0;
   while ($i <= 300) {
      $apikey = hash('whirlpool', $apikey);
      $apikey = hash('whirlpool', $apikey);
      $apikey = hash('whirlpool', $apikey);
      $apikey = hash('sha512', $apikey);
      $i++;
   }
   $apikey = hash('ripemd256', $apikey);
   $options = [
     'username' => $username,
     'api_key' => $apikey
   ];
   $this->query_updateAPIkey($options);

 }

 public function getAPIKey($username) {
   $options = [
     'username' => $username
   ];

   $api_key = $this->query_getAPIKey($options)[0]['api_key'];
   return $api_key;
 }

  // User Creation / Deletion Functions
  public function createUser($username, $password, $role) {

    $password = $this->hashPassword($password);
    $role_id = $this->query_getRoleId(['role_name' => $role])[0]['role_id'];
    $options = [
      'username' => $username,
      'password' => $password,
      'role' => $role_id
    ];
    $this->query_userExists($options['username']);
    if (!$this->getRows()) {
      $this->query_insertUser($options);

      $options = [
        'user_id' => $this->query_getUserID($options)[0]['id'],
        'role_id' => $role_id
      ];
      $this->query_insertRole($options);
      $this->generateAPIkey($username);
      return true;
    } else {
        $error = array (
          'isError' => TRUE,
          'error' => 'UserExists'
        );
        return $error;
    }
  }

  public function deleteUser($username) {
    $options = [
      'username' => $username
    ];
    $this->query_userExists($options['username']);

    if ($this->getRows()) {
      $user_id = $this->query_getUserID($options)[0]['id'];
      $options = [
        'user_id' => $user_id
      ];
      $this->query_deleteUser($options);
      $this->query_deleteUserRoles($options);
      return true;
    } else {
        $error = array (
          'isError' => TRUE,
          'error' => 'UserNonExistent'
        );
        return $error;
    }
  }

  // Account management Functions

  public function changeRole($username, $role) {


    $options = [
      'username' => $username,
      'role_name' => $role
    ];
    $user_id = $this->query_getUserID($options)[0]['id'];
    $role_id = $this->query_getRoleID($options)[0]['role_id'];

    $options = NULL;
    $options = [
      'username' => $user_id,
      'role' => $role_id
    ];

    $response = $this->query_updateRole($options);

  }

  public function changePassword($username, $password) {
    $this->generateAPIKey($username);
    $password = $this->hashPassword($password);

    $options = [
      'username' => $username,
      'password' => $password
    ];
    $this->query_changePassword($options);
  }

  public function login($username, $remember = false, $addredirect = true) {

        // If remember me is true, generate a token and store as a cookie
        if ($remember == true) {
          $loginTokens = $this->createLoginToken($username);
          setCookie("remember", $loginTokens['token'], $loginTokens['validUntil'], '/');
        }

        $apikey = $this->getAPIKey($username);
        $_SESSION['API_KEY'] = $apikey;
        $_SESSION['is_logged_in'] = TRUE;
        $_SESSION['username'] = $username;
        if ($addredirect == true) {
          header('Location: ' . $this->_config['root_dir_url'] . '/index.php');
        }
  }

  public function logout($message = NULL) {
    if (isset($_SESSION['username']) && isset($_COOKIE['remember'])) {
      $this->invalidateLoginToken($_SESSION['username'], $_COOKIE['remember']);
    }
    setcookie("remember", "", time()-3600, '/');
    session_destroy();
    if (isset($message)) {
      header('Location: ' . $this->_config['root_dir_url'] . '/pages/login.php?error=' . $message);
    } else {
      header('Location: ' . $this->_config['root_dir_url'] . '/pages/login.php');
    }
  }

  public function autoLogin() {

    if (isset($_COOKIE['remember'])) {
      $checkToken = $this->checkLoginToken($_COOKIE['remember']);
      if (!$checkToken) {
        return false;
      } else {
        $this->login($checkToken);
      }
    }

  }

  public function isLoggedIn() {
    if (isset($_SESSION['is_logged_in'])) {
      return true;
    } else {
      return false;
    }
  }

  public function checkLoginStatus() {
    if (!$this->isLoggedIn()) {
      header ('Location: pages/login.php');
    } else {
      $this->retrieveUserInfo($_SESSION['username']);
      if (!$this->userHasPermission('can_login')) {
        $this->logOut();
      }

    }
  }

  public function getUserInfo() {
    $info = [
      'username' => $_SESSION['username'],
      'rank' => 'Super Admin'
    ];
    return $info;
  }

  // User permission functions

  public function retrieveUserInfo($username, $addToSession = TRUE) {

    $userdata = [
      'username' => $username,
    ];

    // First: Get ID from 'users' table about said user
    $options = [
      'username' => $username
    ];
    $userdata['id'] = $this->query_getUserID($options)[0]['id'];


    // Query the users Role in the user_roles table
    $options = [
      'id' => $userdata['id']
    ];

    $userdata['role_id'] = $this->query_getRoleByID($options)[0]['role_id'];

    // Get the human readable name of the role
    $options = [
      'role_id' => $userdata['role_id']
    ];
    $userdata['human_role_name'] = $this->query_getRoleName($options)[0]['human_role_name'];

    // Get permissions for this role
    $options = [
      'role_id' => $userdata['role_id']
    ];
    $userdata['permissions'] = [];
    $permissionsDB = $this->query_getPermissionsForRole($options);
    foreach ($permissionsDB as &$value) {
      $userdata['permissions'][$value['perm_name']] = [
        'perm_id' => $value['perm_id'],
        'role_id' => $value['role_id']
      ];
    }

    if ($addToSession == TRUE) {
      $this->_user = $userdata;
      $_SESSION['userdata'] = $userdata;
    }

    return $userdata;
  }

  public function userHasPermission($permission_name) {
    if (isset($_SESSION['userdata']['permissions'][$permission_name])) {
      return true;
    } else {
      return false;
    }
  }

  public function getAllUserInfo() {
    $usernamesRaw = $this->query_listUsers();

    foreach($usernamesRaw as &$value) {
      $users[$value['username']] = $this->retrieveUserInfo($value['username'], FALSE);
    }
    return $users;

  }

}


?>
