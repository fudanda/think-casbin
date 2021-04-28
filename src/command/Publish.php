<?php

namespace fdd\casbin\command;

use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use FuDanDa\Casbin\Utils;
use think\Console;

class Db extends \think\console\Command
{
    public function configure()
    {
        $this->setName('db:run')
            ->addArgument('name', Argument::OPTIONAL, "your module name")
            ->setDescription('move migration file');
    }
    public function execute(Input $input, Output $output)
    {
        $this->createMigrate($output);
        $result = Console::call('migrate:run');
        $output->writeln($result->fetch());
    }
    //复制数据库迁移文件
    public function createMigrate($output)
    {
        $filePath = file_build_path(env('app_path'), '..', 'database', 'migrations');
        $baseFilePath = file_build_path(__DIR__, '..', '..', 'database', 'migrations');
        Utils::handle($output, __FUNCTION__, $filePath, $baseFilePath, true, 'copy_dir');
    }
}
