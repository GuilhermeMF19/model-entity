<?php

namespace app\database\model;

use app\database\Connection;
use app\database\entity\UserEntity;
use PDO;

class Post extends Model
{
    protected string $table = 'posts';

}
