<?php
session_start();
session_unset();
session_destroy();
header("Location:shopsmart_v_1.0.0.0.1/index.html");
exit();
?>