<?php
/**
 * 用于包含整个项目常用的文件。
 * 任何文件只要在其同目录下含有文件__path.php,并且已包含之，
 * 那么在包含这个文件之后，本文件所包含的文件就能被包含了。
 * 一般情况下，不要修改该文件，更不要颠倒包含文件的顺序。除非你要颠覆这个框架的目录结构！
 */
require_once 'path__.php';	//提供路径纠错支持（最先包含）
require_once PUBLIC_DIR_NAME.'/core/lib/base/core__.php';	//框架核心支持
require_once 'lib/base/env__.php';	//环境配置（用成员自己的）
require_once PUBLIC_DIR_NAME.'/core/lib/base/url__.php';	//URL解析支持
require_once PUBLIC_DIR_NAME.'/core/lib/base/filter__.php';	//过滤器支持，为“指令————Action调度”中间提供一道关卡
require_once PUBLIC_DIR_NAME.'/core/lib/base/action__.php';	//调度器支持

/*
 * 自动包含开发者指定的常量配置目录
 * 开发者应该把所需的库文件或目录放置到USER_CONST_DIRS常量所指定的 目录中。
 */
AiCore::includePhpWithEveryLayer( USER_CONFIG_DIRS );

/*
 * 自动包含其它库
 * 开发者应该把所需的库文件或目录放置到USER_LIB_DIRS常量所指定的 目录中。
 * 指定的目录内所有层次目录的*.php都会被包含。
 */
AiCore::includePhpWithEveryLayer( USER_LIB_DIRS );

/*
 * 自动包含各种自定义实体类（Entity）
 */
AiCore::parseXxxFormatFromDirAndAutoIncludeTheir(ENTITY_DIR, 'entity');

/*
 * 自动包含各种自定义Filter
 */
// ActionUtil::parseXxxFilterFromFilterDirAndAutoIncludeTheir();
AiCore::parseXxxFormatFromDirAndAutoIncludeTheir(FILTER_DIR, 'filter');

/*
 * 自动包含各种自定义Action。
 * 执行后，将等同于：
 * require_once PUBLIC_DIR_NAME.'/core/action/IndexAction.class.php';	//默认Action
 * require_once PUBLIC_DIR_NAME.'/core/action/TestAction.class.php';	//示例Action
 * ... ...
 */
// ActionUtil::parseXxxActionFromActionDirAndAutoIncludeTheir();
AiCore::parseXxxFormatFromDirAndAutoIncludeTheir(ACTION_DIR, 'action');

require_once PUBLIC_DIR_NAME.'/core/lib/base/init__.php';	//以上所有文件包含完毕后，最后包含初始化文件，以便执行处理业务前的准备。
