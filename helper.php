<?php
if (!function_exists('isEmpty')) {
    /**
     * 构建文件路径
     *
     * @param [string] ...$segments
     * @return bool
     */
    function isEmpty($param)
    {
        $result = false;
        !isset($param) || !$param && $result = true;
        return $result;
    }
}