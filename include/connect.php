<?php
if ($sdb = sqlite_open('skillerndb', 0666, $sqliteerror)) {
} else {
  die ($sqliteerror);
}

?>