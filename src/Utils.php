<?php

namespace FuDanDa\Casbin;

class Utils
{

    /**
     * 文件复制公共方法
     *
     * @param mixed  $output
     * @param string $type
     * @param string $filePath
     * @param string $baseFilePath
     * @param string $copyType
     * @return void
     */
    public static function handle($output, $type, $filePath, $baseFilePath, $checkFile = true, $copyType = 'copy')
    {
        try {
            $config = [
                'createConfig' => [
                    'Config is exist',
                    'Create Config error',
                    'Create Config success:' . $filePath,
                ],
                'createMigrate' => [
                    'database migrate is exist',
                    'Create database migrate error',
                    'Create database migrate success:' . $filePath,
                ],
                'createResources' => [
                    'Resources is exist',
                    'Create Resources error',
                    'Create Resources success:' . $filePath,
                ],
                'createCommonModel' => [
                    'Common Model is exist',
                    'Create Common Model error',
                    'Create Common Model success:' . $filePath,
                ],
                'createRoute' => [
                    'Router is exist',
                    'Create Router error',
                    'Create Router success:' . $filePath,
                ],
                'createWebpackmix' => [
                    'Webpackmix is exist',
                    'Create Webpackmix error',
                    'Create Webpackmix success:' . $filePath,
                ],
                'createBabelrc' => [
                    'Babelrc is exist',
                    'Create Babelrc error',
                    'Create Babelrc success:' . $filePath,
                ],

            ];
            //判断是否有该方法
            !array_key_exists($type, $config) && throw_exception($type . '方法不存在');
            //判断文件是否已存在
            $exist = ($copyType == 'copy') ? is_file($filePath) : is_dir($filePath);
            $exist && throw_exception($config[$type][0]);
            //复制文件
            $copy_res = true;
            $checkFile && $copy_res = $copyType($baseFilePath, $filePath);
            //判断是否复制成功
            !$copy_res && throw_exception($config[$type][1]);
            //返回成功信息
            $output->writeln($config[$type][2]);
        } catch (\exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}