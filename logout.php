<?php
session_start();
session_destroy();
header("Location: login.html?form=login&success=0");
exit();
?>