<?php

fwrite(STDOUT, "Hello, World!" . PHP_EOL . "test output" . PHP_EOL);
fwrite(STDERR, "Hello, World!" . PHP_EOL . "test error" . PHP_EOL);
exit(2);
