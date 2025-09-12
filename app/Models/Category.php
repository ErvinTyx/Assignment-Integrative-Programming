<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public static function treeOptions($parentId = null, $prefix = '', $departmentId = null): array
    {
        $query = self::query();

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $categories = $query->where('parent_id', $parentId)
            ->orderBy('name')
            ->get();

        $options = [];

        foreach ($categories as $category) {
            $options[$category->id] = $prefix . $category->name;
            $options += self::treeOptions($category->id, $prefix . '— ', $departmentId);
        }

        return $options;
    }


    public function getFullPathAttribute(): string
    {
    $names = [];
    $category = $this;

    while ($category) {
        array_unshift($names, $category->name);
        $category = $category->parent;
    }

    return implode(' > ', $names);
}

}

