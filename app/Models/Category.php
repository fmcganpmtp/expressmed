<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id','name','image','description' ,'status'
    ];

    public function subcategory()
    {
        return $this->hasMany('App\Models\Category', 'parent_id')->where('status', 'active');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Category', 'parent_id');
    }

    public function getParentsNames()
    {
        $parents = collect([]);

        if($this->parent) {
            $parent = $this->parent;
            while(!is_null($parent)) {
                $parents->push($parent);
                $parent = $parent->parent;
            }
            return $parents;
        } else {
            return $this->name;
        }
    }

    //--Get all child categories --
    public function getCategories($parent_id)
    {
        $categories = Category::whereIn('parent_id', $parent_id)->get();
        $categories = $this->addRelation($categories);

        return $categories;
    }

    public function selectChild($id)
    {
        $categories = Category::where('parent_id', $id)->get();

        $categories = $this->addRelation($categories);

        return $categories;
    }

    protected function addRelation($categories)
    {
        $categories->map(function ($item, $key) {
            $sub = $this->selectChild($item->id);

            return $item = $this->array_add($item, 'child_categories', $sub);
        });

        return collect($categories);
    }

    protected function array_add($array, $key, $value)
    {
        return Arr::add($array, $key, $value);
    }


}
