<?php

namespace fdd\casbin\command;

use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use fdd\casbin\Utils;
use think\facade\Console;

class Publish extends \think\console\Command
{
    public function configure()
    {
        $this->setName('fddcasbin:publish')
            ->addArgument('name', Argument::OPTIONAL, "your module name")
            ->setDescription('move migration file');
    }
    public function execute(Input $input, Output $output)
    {
        $this->createMigrate($output);
        $this->createConfig($output);
        $result = Console::call('migrate:run');
        $output->writeln($result->fetch());
    }
    //复制数据库迁移文件
    public function createMigrate($output)
    {
        $filePath = file_build_path($this->app->getRootPath(), '..', 'database', 'migrations');
        $baseFilePath = file_build_path(__DIR__, '..', '..', 'database', 'migrations');
        Utils::handle($output, __FUNCTION__, $filePath, $baseFilePath, true, 'copy_dir');
    }
    //复制权限配置文件
    public function createConfig($output)
    {
        $filePath2 = file_build_path($this->app->getRootPath(), 'config', 'casbin.rbac.conf');
        $baseFilePath2 = file_build_path(__DIR__, '..', '..', 'config', 'casbin.rbac.conf');
        Utils::handle($output, __FUNCTION__, $baseFilePath2, $filePath2, true, 'copy');
    }
}
