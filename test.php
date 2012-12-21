<?php
// open in read-only mode
$db = dbase_open('5961.dbf', 0);
if ($db) {
  // read some data ..
  dbase_close($db);
}
?>