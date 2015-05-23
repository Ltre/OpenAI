<?php


/**
 * 包含公共目录中的扩展库AiMySQL
 */
AiCore::parseXxxFormatFromDirAndAutoIncludeTheir( PUBLIC_DIR_NAME.'/core/'.USER_LIB_DIRS.'mysql/', 'class');
AiCore::parseXxxFormatFromDirAndAutoIncludeTheir( PUBLIC_DIR_NAME.'/core/'.USER_LIB_DIRS.'mysql/', 'entity');
