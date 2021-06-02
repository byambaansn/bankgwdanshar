<?php

$xml = simplexml_load_string(sfOutputEscaper::unescape($result));
echo json_encode($xml);

?>