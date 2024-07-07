<?php
namespace App\Common;

class CompareArrayCommon
{
    public static function compareArrays($oldArray, $newArray)
    {
        $added = [];
        $updated = [];
        $removed = [];
        //create one array with all keys from old arrays
        $oldIndex = [];
        foreach ($oldArray as $item) {
            $oldIndex[$item['id']] = $item;
        }
        //create one array with all keys from new arrays
        $newIndex = [];
        foreach ($newArray as $item) {
            $newIndex[$item['id']] = $item;
        }
        //check for added and updated items
        foreach ($newArray as $newItem) {
            $id = $newItem['id'];
            if (!isset($oldIndex[$id])) {
                $added[] = $newItem;
            } elseif ($oldIndex[$id] !== $newItem) {
                $updated[] = [
                    'old' => $oldIndex[$id],
                    'new' => $newItem
                ];
            }
        }
        //check for removed items
        foreach ($oldArray as $oldItem) {
            $id = $oldItem['id'];
            if (!isset($newIndex[$id])) {
                $removed[] = $oldItem;
            }

        }

        return [
            'added' => $added,
            'updated' => $updated,
            'removed' => $removed
        ];
    }
}