<?php
/**
 * TestModel
 *
 * @author camera360_server@camera360.com
 * @copyright Chengdu pinguo Technology Co.,Ltd.
 */

namespace PG\MSF\Server\Models;


use PG\MSF\Server\CoreBase\Model;
use PG\MSF\Server\CoreBase\SwooleException;

class TestModel extends Model
{
    public function timerTest()
    {
        print_r("model timer\n");
    }

    public function contextTest()
    {
        print_r($this->getContext());
        $testTask = $this->loader->task('TestTask', $this);
        $testTask->contextTest();
        $testTask->startTask(null);
    }
    public function test_coroutine()
    {
        $redisCoroutine = $this->redis_pool->coroutineSend('get', 'test');
        $result = yield $redisCoroutine;
        return $result;
    }

    public function test_coroutineII($callback)
    {
        $this->redis_pool->get('test', function ($uid) use ($callback) {
            $this->mysql_pool->dbQueryBuilder->select('*')->from('account')->where('uid', $uid);
            $this->mysql_pool->query(function ($result) use ($callback) {
                call_user_func($callback, $result);
            });
        });
    }

    public function test_exception()
    {
        throw new SwooleException('test');
    }

    public function test_exceptionII()
    {
        $result = yield $this->redis_pool->coroutineSend('get', 'test');
        $result = yield $this->mysql_pool->dbQueryBuilder->select('*')->where('uid', 10303)->coroutineSend();
    }

    public function test_task()
    {
        $testTask = $this->loader->task('TestTask', $this);
        $testTask->test();
        $testTask->startTask(null);
    }

    public function test_pdo()
    {
        $result = yield $this->mysql_pool->dbQueryBuilder->select('*')->from('account')->where('uid',36)->coroutineSend();
        $result = yield $this->mysql_pool->dbQueryBuilder->update('account')->where('uid',36)->set(['status'=>1])->coroutineSend();
        $result = yield $this->mysql_pool->dbQueryBuilder->replace('account')->where('uid',91)->set(['status'=>1])->coroutineSend();
        print_r($result);
    }
}