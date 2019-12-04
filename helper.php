<?php
\think\Console::addDefaultCommands([
    FuDanDa\Casbin\command\Db::class,
]);
\think\Loader::addClassAlias([
    'Casbin' => FuDanDa\Casbin\facade\Casbin::class,
]);
if (!function_exists('scan_dir')) {
    /**
     * 扫描目录.
     *
     * @param  string 目录
     * @param  int 层级
     * @param  int 当前层级
     *
     * @return array
     */
    function scan_dir($dir, $depth = 0, $now = 0)
    {
        $dirs = [];
        if (!is_dir($dir) || ($now >= $depth && $depth != 0)) {
            return false;
        }
        // file_build_path($dir, '*');
        $dirArr = glob(file_build_path($dir, '*'));
        $now++;
        foreach ($dirArr as $item) {
            if (is_dir($item)) {
                $dirs[] = $item;
                $subDir = scan_dir($item, $depth, $now);
                if ($subDir) {
                    $dirs = array_merge($dirs, $subDir);
                }
            }
        }
        return $dirs;
    }
}
if (!function_exists('copy_dir')) {
    /**
     * 复制目录.
     *
     * @param  string  $dir   目录
     * @param  string  $dest  目标目录
     *
     * @return bool
     */
    function copy_dir($dir, $dest = '')
    {
        if (!is_dir($dir)) {
            return false;
        }
        @mkdir($dest, 0777, true);
        $resources = scandir($dir);
        foreach ($resources as $item) {
            if (
                is_dir($dir . DIRECTORY_SEPARATOR . $item) && $item != '.'
                && $item != '..'
            ) {
                copy_dir(
                    $dir . DIRECTORY_SEPARATOR . $item,
                    $dest . DIRECTORY_SEPARATOR . $item
                );
            } elseif (is_file($dir . DIRECTORY_SEPARATOR . $item)) {
                copy(
                    $dir . DIRECTORY_SEPARATOR . $item,
                    $dest . DIRECTORY_SEPARATOR . $item
                );
            }
        }
        return true;
    }
}

if (!function_exists('file_build_path')) {
    /**
     * 构建文件路径
     *
     * @param [string] ...$segments 路径名称
     * @return string
     */
    function file_build_path(...$segments)
    {
        return join(DIRECTORY_SEPARATOR, $segments);
    }
}
if (!function_exists('throw_exception')) {
    /**
     * 抛出异常处理
     *
     * @param string    $msg  异常消息
     * @param integer   $code 异常代码 默认为0
     * @param string    $exception 异常类
     *
     * @throws Exception
     */
    function throw_exception($msg, $code = 0, $exception = '')
    {
        throw new \Exception($msg, $code);
    }
}