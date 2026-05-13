<?php
require_once('../database/user_auth.php');
UserAuth::logout();
header('Location: index.php');
exit;