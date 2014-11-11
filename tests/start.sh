#!/bin/bash
PATHTOLIB=$1
nohup java -Djava.library.path=$PATHTOLIB/DynamoDBLocal_lib -jar $PATHTOLIB/DynamoDBLocal.jar -inMemory -port 8000 > /dev/null 2>&1 &
