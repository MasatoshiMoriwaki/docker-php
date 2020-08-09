<?php

define('SITE_BASE_URL', 'http://localhost:8080');

define('URL_PARAM_TYPE_NUM', '[');
define('URL_PARAM_TYPE_STR', ']');

define('MSG_TYPE_INFO',  '10');
define('MSG_TYPE_ERROR', '20');

define('ERR_TYPE_FILE_UPLOAD', 'setFileUploadErrMessage()');
define('ERR_TYPE_DATA_COMMIT', 'setDataCommitErrMessage()');

define('JUNKISSAS_PER_PAGE', 5);

define('IMAGE_KEY_TYPE_JUNKISSA', 1);
define('IMAGE_KEY_TYPE_USER', 2);

define('JUNKISSA_IMAGE_MIN_WIDTH', 800);
define('JUNKISSA_IMAGE_MIN_HIGHT', 600);
define('USER_IMAGE_MIN_WIDTH', 400);
define('USER_IMAGE_MIN_HIGHT', 400);

define('JUNKISSA_IMAGE_MAX_SEQ', 6);
define('IMAGE_FILE_PATH_JUNKISSA', '/images/junkissa/');
define('IMAGE_FILE_PATH_USER', '/images/user/');

define('EMAIL_VERIFY_EXPIRATION_MINITES', 60);