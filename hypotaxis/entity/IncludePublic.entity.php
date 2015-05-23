<?php

/**
 * 本脚本用于包含PUBLIC_DIR_NAME目录中的公共实体类，减少代码重复
 * 可以在本目录中定义本开发成员私有的实体，但要注意实体名不能与公共实体类的相同
 */

AiCore::parseXxxFormatFromDirAndAutoIncludeTheir( PUBLIC_DIR_NAME.'/core/'.ENTITY_DIR, 'entity');
