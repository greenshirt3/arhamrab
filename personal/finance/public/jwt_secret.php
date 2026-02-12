<?php
// One-time generator; delete after use
header('Content-Type: text/plain');
echo bin2hex(random_bytes(32));
