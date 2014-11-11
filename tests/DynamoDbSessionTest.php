<?php

namespace pythagor\dynamodbsession\tests;

use pythagor\dynamodbsession\DynamoDbSession;
use Yii;
use Aws\Common\Enum\Region;

class DynamoDbSessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DynamoDbSession
     */
    private static $dynamo;

    private static $idValue = 'edfsfj84';
    private static $dataKey = 'Vitya';
    private static $dataValue = 'Maleev';

    public static function setUpBeforeClass()
    {
        // Start Local Engine
        exec(__DIR__ . '/start.sh ' . __DIR__ . '/DynamoDbLocal');

        // Generating Client
        self::$dynamo = Yii::createObject([
            'class'  => 'pythagor\dynamodbsession\DynamoDbSession',
            'params' => [
                'region'   => Region::EU_CENTRAL_1,
                'key'      => 'BLA-BLA-KEY',
                'secret'   => 'BLA-BLA-SECRET',
                'base_url' => 'http://localhost:8000',
                'profile'  => 'default',
            ],
        ]);

        // Create Temp Table
        self::$dynamo->dynamoDb->createTable([
            'TableName' => self::$dynamo->sessionTable,
            'AttributeDefinitions' => [
                [
                    'AttributeName' => 'id',
                    'AttributeType' => 'S',
                ],
            ],
            'KeySchema' => [
                [
                    'AttributeName' => 'id',
                    'KeyType'       => 'HASH',
                ],
            ],
            'ProvisionedThroughput' => [
                'ReadCapacityUnits' => 2,
                'WriteCapacityUnits' => 2,
            ],
        ]);
    }

    public static function tearDownAfterClass()
    {
        exec('pkill -f DynamoDBLocal');
    }

    public static function getTableCount()
    {
        $tableInfo = self::$dynamo->dynamoDb->describeTable([
            'TableName' => self::$dynamo->sessionTable,
        ]);

        return $tableInfo['Table']['ItemCount'];
    }

    public function testWriteSession()
    {
        $this->assertTrue(0 == self::getTableCount());

        $result = self::$dynamo->writeSession(self::$idValue, serialize([self::$dataKey => self::$dataValue]));

        $this->assertTrue(1 == self::getTableCount());

        $row = self::$dynamo->dynamoDb->getItem([
            'ConsistentRead' => true,
            'TableName' => self::$dynamo->sessionTable,
            'Key' => [
                'id' => ['S' => (string) self::$idValue],
            ],
        ]);

        $expire = $row['Item']['expire']['N'];
        $data = $row['Item']['data']['S'];

        $this->assertTrue(is_numeric($expire));
        $this->assertTrue((int) $expire == $expire);
        $this->assertTrue((int) $expire > time());

        $dataArray = unserialize($data);
        $this->assertTrue(is_array($dataArray));
        $this->assertTrue($dataArray[self::$dataKey] == self::$dataValue);

        $this->assertTrue($result);
    }

    public function testReadSession()
    {
        $result = self::$dynamo->readSession(self::$idValue);

        $this->assertTrue(is_string($result));

        if (!empty($result)) {
            $dataArray = unserialize($result);
            $this->assertTrue(is_array($dataArray));
            $this->assertTrue($dataArray[self::$dataKey] == self::$dataValue);
        }
    }

    public function testGetData()
    {
        $result = self::$dynamo->getData(self::$idValue);

        $expire = $result['expire']['N'];
        $data = $result['data']['S'];

        $this->assertTrue(is_numeric($expire));
        $this->assertTrue((int) $expire == $expire);
        $this->assertTrue((int) $expire > time());

        $dataArray = unserialize($data);
        $this->assertTrue(is_array($dataArray));
        $this->assertTrue($dataArray[self::$dataKey] == self::$dataValue);
    }

    public function testGetExpireTime()
    {
        $method = new \ReflectionMethod(self::$dynamo, 'getExpireTime');
        $method->setAccessible(true);

        $result = $method->invoke(self::$dynamo);

        $this->assertTrue(is_numeric($result));
        $this->assertTrue((int) $result == $result);
        $this->assertTrue((int) $result > time());
    }

    public function testDestroySession()
    {
        $this->assertTrue(1 == self::getTableCount());

        $result = self::$dynamo->destroySession(self::$idValue);

        $this->assertTrue(0 == self::getTableCount());

        $this->assertTrue($result);
    }
}
