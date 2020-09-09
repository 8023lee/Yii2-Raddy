<?php

namespace benbanfa\raddy\repository;

use yii\db\ActiveRecord;

class Repository implements RepositoryInterface
{
    public function supports($object): bool
    {
        return $object instanceof ActiveRecord;
    }

    public function save($object): bool
    {
        $result = $object->save(false);

        return $result;
    }

    public function delete($object): bool
    {
        return $object->delete();
    }
}
