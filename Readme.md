yii2-dynamodbsession
====================

The **yii2-dynamodbsession** extension is a Yii2 component for store sessions in the Amazon AWS DynamoDB storage.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

## Configuration

Add **session** component into your components config.

### Example

```php
'components' => [
    // ...
    'session' => [
        'class' => 'pythagor\dynamodbsession\DynamoDbSession',
        'sessionTable' => 'YourTableName',
        'idColumn' => 'id',
        'dataColumn' => 'data',
        'expireColumn' => 'expire',
        'params' => [
            'key' => 'YOUR_AWS_ACCESS_KEY_ID',
            'secret' => 'YOUR_AWS_SECRET_ACCESS_KEY',
            'region' => 'us-west-2',
        ],
    ],
],
```
## License

**yii2-dynamodbsession** is released under the BSD 3-Clause License.
