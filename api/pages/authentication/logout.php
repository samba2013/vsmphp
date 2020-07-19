<?php

namespace vsm\api\pages\authentication;

require_once "../../lib/webconfig.php";


unset($_SESSION['user']);
session_destroy();
session_unset();

header("Location: /");
exit(0);
