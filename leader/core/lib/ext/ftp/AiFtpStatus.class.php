<?php

/**
 * FTP状态码
 * @tutorial 常量命名依据为带有下划线的方法名称+状态简称
 * @author Oreki
 * @since 2014-1-23
 */

class AiFtpStatus {
	
	/*TODO:
	 * AiFtp::uploadNewFile() 将废弃
	 * @deprecated
	 */
	/**
	 * 上传新文件成功
	 * @deprecated
	 */
	const UPLOAD_NEWFILE_SUCCESS = 1001;
	/**
	 * 上传新文件过程中，指定的本地文件不存在
	 * @deprecated
	 */
	const UPLOAD_NEWFILE_HASNO_LOCALFILE = 1002;
	/**
	 * 文件上传未完成，可能原因是指定的远程目录不存在，或编码设置错误等等
	 * @deprecated
	 */
	const UPLOAD_NEWFILE_UN_FINISH = 1003;
	/**
	 * 上传新文件过程中，指定的远程文件已存在
	 * @deprecated
	 */
	const UPLOAD_NEWFILE_EXIST_REMOTEFILE = 1004;
	
	/*TODO:
	 * AiFtp::listDirsAndFiles()
	 */
	/**
	 * 获取远程目录内列表失败。
	 * 可能原因是目录不存在、编码设置错误，或其它原因
	 */
	const LIST_REMOTE_ENCODE_ERROR_ELSE = 2001;
	
	/*TODO:
	 * AiFtp::uploadFile()
	 */
	/**
	 * 上传新文件过程中，指定的本地文件不存在
	 */
	const UPLOAD_NEW_FILE_HASNO_LOCALFILE = 3001;
	/**
	 * 断点续传文件过程中，指定的本地文件不存在
	 */
	const UPLOAD_HALF_FILE_HASNO_LOCALFILE = 3002;
	/**
	 * 选定了全新上传，但指定的远程文件已存在
	 */
	const UPLOAD_NEW_FILE_EXIST_REMOTEFILE = 3003;
	/**
	 * 选定了续传，但指定的远程文件不存在
	 * （也拦截了因编码问题造成的远程目录不存在的问题）
	 */
	const UPLOAD_HALF_FILE_HASNO_REMOTEFILE = 3004;
	/**
	 * 全新上传：文件上传未完成，可能原因是指定的远程目录不存在，或编码设置错误等等
	 */
	const UPLOAD_NEW_FILE_UN_FINISH = 3005;
	/**
	 * 续传：文件上传未完成，可能原因是指定的远程目录不存在，或编码设置错误等等
	 * 注意：该错几乎不会报出。
	 * 		因为由于编码错误而产生的路径错误问题，
	 * 		已被UPLOAD_HALF_FILE_HASNO_REMOTEFILE首先拦截。
	 * 		只有文件真的未续传完成时，才会报这个错。
	 */
	const UPLOAD_HALF_FILE_UN_FINISH = 3006;
	/**
	 * 文件上传成功
	 */
	const UPLOAD_FILE_SUCCESS = 3007;
	
	/*TODO:
	 * AiFtpStatus::downloadFile()
	 */
	/**
	 * 全新下载文件过程中，指定的远程文件不存在
	 */
	const DOWNLOAD_NEW_FILE_HASNO_REMOTEFILE = 4001;
	/**
	 * 断点下载文件过程中，指定的远程文件不存在
	 */
	const DOWNLOAD_HALF_FILE_HASNO_REMOTEFILE = 4002;
	/**
	 * 选定了全新下载，但指定的本地文件已存在
	 */
	const DOWNLOAD_NEW_FILE_EXIST_LOCALFILE = 4003;
	/**
	 * 选定了断点下载，但指定的本地文件不存在
	 * （也拦截了因编码问题造成的本地目录不存在的问题）
	 */
	const DOWNLOAD_HALF_FILE_HASNO_LOCALFILE = 4004;
	/**
	 * 全新下载：文件下载未完成，可能原因是指定的本地目录不存在，或编码设置错误等等
	 */
	const DOWNLOAD_NEW_FILE_UN_FINISH = 4005;
	/**
	 * 续传：文件下载未完成，可能原因是指定的本地目录不存在，或编码设置错误等等
	 * 注意：该错几乎不会报出。
	 * 		因为由于编码错误而产生的路径错误问题，
	 * 		已被DOWNLOAD_NEW_FILE_EXIST_LOCALFILE首先拦截。
	 * 		只有文件真的未续传完成时，才会报这个错。
	 */
	const DOWNLOAD_HALF_FILE_UN_FINISH = 4006;
	/**
	 * 文件下载成功
	 */
	const DOWNLOAD_FILE_SUCCESS = 4007;
	
	/*TODO:
	 * AiFtp::rename()
	 */
	/**
	 * 远程文件更名：新的名称非法
	 */
	const RENAME_REMOTE_ILLEGAL_NEWNAME = 5001;
	/**
	 * 远程文件更名：指定的远程文件或目录不存在
	 */
	const RENAME_REMOTE_HASNO_REMOTEFILE = 5002;
	/**
	 * 远程文件更名：由于其它原因（断开连接、远程位置不存在、编码问题等等），操作失败
	 */
	const RENAME_REMOTE_ELSE_FAILED = 5003;
	/**
	 * 远程文件更名：成功
	 */
	const RENAME_REMOTE_SUCCESS = 5004;
}