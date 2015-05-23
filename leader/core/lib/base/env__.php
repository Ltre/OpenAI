<?php
 //TODO:本文件配置常量和一些系统的惯例，如时区设定


/*
 * 定义项目入口文件位置
 * 默认为/index.php。
 * 如果要定义多个入口，则需要用“|”分隔。
 * 如：'index.php|admin.php|abc/def.php'
 */
define('DEFAULT_ENTER_SCRIPT', 'index.php');


/*
 * 是否支持URL重写
 * 默认为false，不支持。
 * 如果你的服务器支持URL重写，则设置为true，这样可以拦截更多非法的URI。
 * 如果还是不懂什么意思，就不要改。
 */
define('ENABLE_URL_REWRITE', false);


/*
 * 默认URL SHELL 名称，即使在URL串中不指定指令，也会执行该URL SHELL
 * 链接 http://server.com/项目名/?xxx=shell 的效果
 * 		同  http://server.com/项目名
 * URL SHELL 的含义：见ActionUtil::numOfShellArgs()  @  /PUBLIC_DIR_NAME/core/lib/action__.php
 * 注意：默认的URL SHELL所对应的Action及方法必须存在
 */
define('DEFAULT_URL_SHELL', 'Index-index');	//这里以'index'为默认指令，其位于IndexAction中

/*
 * 全局过滤器的类名
 * 默认为：GLOBALFILTER
 * 文件名为GLOBALFILTER.filter.php
 * 固定存储于FILTER_DIR常量指定的目录
 */
define('GLOBAL_FILTER_NAME', 'GLOBALFILTER');

/*
 * 存放Xxxx.entity.php的路径
 * Xxxx.entity是开发者定义的实体类。
 * 其声明方式：class Xxxx {}
 */
define('ENTITY_DIR', PUBLIC_DIR_NAME.'/core/entity/');

/*
 * 存放Xxxx.action.php的路径
 * XxxxAction是开发者定制的功能模块。
 * 模块的声明方式：class XxxxAction extends ActionUtil {}
 * 其内声明的方法可以作为功能模块中的各种操作。
 * 如果想要通过URL[http://server.com/ItemName/?xxx=shell|*]的形式调用其中的方法，则需要配置ActionUtil::numOfShellArgs() @ /PUBLIC_DIR_NAME/core/lib/base/action__.php，详见注释。
 * 如果想在进入模块之前先执行过滤器，则可实现框架内置的 【模块单过滤器Filter】的doFilter方法
 * 	格式如：
 * 			class XxxxAction implements Filter{
 *	 			public function doFilter(){
 * 					//实现系统内置的模块单过滤器
 * 				}
 * 			}
 * 如果需要多个过滤器、过滤方法的过滤器等，就要用到过滤器群组XxxxFilter，见FILTER_DIR常量定义处的注释部分。
 */
define('ACTION_DIR', PUBLIC_DIR_NAME.'/core/action/');

/*
 * 存放Xxxx.filter.php的路径
 * XxxxFilter是XxxxAction对应的过滤器群组，不同于框架内置的【模块单过滤器Filter】。
 * 当系统检索到存在这样的类时，将会与XxxxAction绑定。
 * 注意：在模块外调用模块方法时，将会执行过滤器；模块内调用，将不会触发过滤器执行。
 * 		如果非要在模块内也唤醒过滤器，则调用方式要改为： parent::__call($method, $vars)。其中，$method是方法名，$vars是传入的参数表构成的数组。
 * 在本框架中，过滤器按过滤类型分为两种：Action模块过滤器、Action操作[方法]过滤器；按过滤层次还可分为：单过滤器、过滤器链。
 * 1、如果仅需要————单个Action模块过滤器，就不需要定义高级过滤器 XxxxFilter，仅需实现Filter接口。
 * 
 * 		具体见 常量 ACTION_DIR 定义处的注释部分。
 * 
 * 2、如果需要在进入Action模块之前设置多个过滤器，那么这里就可以用到XxxxFilter了。
 * 	过滤器群组XxxxFilter的定义格式：
 * 		class XxxxFilter {
 * 			public function doFilter_1(){
 * 				//模块过滤器1
 * 			}
 * 			public function doFilter_2(){
 * 				//模块过滤器2
 * 			}
 * 				... ...
 * 		}
 * 	  执行过滤器的顺序将是：doFilter_1(), doFilter_2(), ...
 * 	 如果XxxxAction还实现了Filter接口的doFilter()方法，则会先执行doFilter()，再执行doFilter_1(), doFilter_2(), ...
 *   建议：如果是定义多个模块过滤器，就不要使用框架内置的【模块单过滤器Filter】，以确保代码的可读性。
 * 3、如果Action模块中的操作[方法]也需要过滤器，则可以在XxxxFilter()定义专门过滤Action操作的方法，方法名与Action方法同名
 * 	格式如：
 * 		class XxxxFilter {
 * 			public function Aaaa(){
 * 				//XxxxAction::Aaaa()方法对应的过滤器
 * 			}
 * 		}
 *     如果XxxxAction::Aaaa()方法需要多个过滤器，则可以这样定义：
 * 		class XxxxFilter {
 * 			public function Aaaa_1(){
 * 				//XxxxAction::Aaaa()方法对应的过滤器
 * 			}
 * 			public function Aaaa_2(){
 * 				//XxxxAction::Aaaa()方法对应的过滤器
 * 			}
 * 			public function Aaaa_3(){
 * 				//XxxxAction::Aaaa()方法对应的过滤器
 * 			}
 * 				... ...
 * 		}
 * 		执行过滤器的顺序将是：Aaaa_1(), Aaaa_2(), Aaaa_3(), ...
 * 		如果Aaaa()过滤方法也存在，则会先执行Aaaa()，在执行Aaaa_1(), Aaaa_2(), Aaaa_3(), ...
 * 	      建议：如果是定义多个方法过滤器，就不要再使用与Action方法同名的过滤方法，以确保代码的可读性。
 */
define('FILTER_DIR', PUBLIC_DIR_NAME.'/core/filter/');

/*
 * 用户扩展库文件默认保存位置是PUBLIC_DIR_NAME.'/core/lib/ext'。
 * 格式：路径要以项目根目录为起点，不能以“/”开头，必须以“/”结尾。
 * 如果要配置多个保存位置，就需要用“|”隔开。如：PUBLIC_DIR_NAME.'/core/lib/ext/|core/lib/abc/|abc/def/'。
 */
define('USER_LIB_DIRS', PUBLIC_DIR_NAME.'/core/lib/ext/');

/*
 * php的const、define、URL指令等等的文件位置，这些文件名格式为：*.php。
 * 默认值为：PUBLIC_DIR_NAME.'/core/config/'。
 * 格式：路径要以项目根目录为起点，不能以“/”开头，必须以“/”结尾
 * 如果要配置多个保存位置，就需要用“|”隔开。如：PUBLIC_DIR_NAME.'/core/config/|core/config/myconfig/'。
 */
define('USER_CONFIG_DIRS', PUBLIC_DIR_NAME.'/core/config/');

/*
 * XxxxAction默认模板目录，用于与输出Action方法同名的模板文件。
 * 默认保存于/PUBLIC_DIR_NAME/core/tpl/default/。
 * 模板文件保存形式：先在PUBLIC_DIR_NAME/core/tpl/default/新建名为Action名称的文件夹，再在这个文件夹中新建与Action方法同名的模板文件，后缀名限定为.php。
 * 输出方式：必须由Action的tpl()方法输出。链接将无法直接访问这类文件。
 * 参数支持：由XxxxAction的基类ActionUtil::getTplArgs() 提供带索引数组形式的参数，以供模板文件内脚本使用。
 */
define('ACTION_TEMPLATE_DIR', PUBLIC_DIR_NAME.'/core/tpl/default/');


/*
 * 普通模板目录路径（其它模板目录路径），是可输出页面所在的路径，用于存放.php为后缀名的模板文件。
 * 通过包含这里的页面，就可以实现页面输出，这么做可以隐藏页面的真实路径。
 * 默认路径是PUBLIC_DIR_NAME/core/tpl/ 。
 * 赋值格式：要以项目根目录为起点，不能以“/”开头，必须以“/”结尾。
 * 链接访问方法：http://server.com/ItemName/?xxx=模板文件参数
 * 可以要再细分目录存放模板文件，如果想通过链接访问到再深一层的文件，则需要用“-”代替“/”。
 * 如：http://server.com/ItemName/?xxx=a-b-c 将默认访问到PUBLIC_DIR_NAME/core/tpl/a/b/c.php。
 * 因此模板文件名的命名规范是：1、不含“-”；2、必须以.php为后缀名。
 */
define('OTHER_TEMPLATE_DIR', PUBLIC_DIR_NAME.'/core/tpl/other/');


/*
 * 配置403页面的路径
 * 默认值：PUBLIC_DIR_NAME/core/tpl/403.php
 */
define('PAGE_403', PUBLIC_DIR_NAME.'/core/tpl/403.php');


//================================以上是框架本身必需的常量====================================//

/*
 * 以下是示例常量
 */
define('JQUERY_LIB_PATH', PUBLIC_DIR_NAME.'/res/js/jquery-1.8.3.min.js');	//jQuery.js路径
