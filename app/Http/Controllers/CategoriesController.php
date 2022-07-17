<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function index(Request $request){
        $categories = Category::all();
        return $categories;
    }

    public function findOne($id){
        $category = Category::find($id);
        $category['posts'] = $category->posts;
        return $category;
    }
   
    public function create(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        $name = $request->input('name');

        if(!$validator->fails()){
            $catExists = Category::where('name', $name)->count();

            if($catExists === 0){
                
                $newCat = new Category();
                $newCat->name = $name;
                $newCat->save();
                $array['success'] = 'Categoria criada com sucesso!';

            }else{
                $array['error'] = 'Categoria já existe!';
                return $array;
            }

        }else{
            $array['error'] = 'Preencha corretamente!';
            return $array;
        }

        return $array;
    }

    public function delete($id){
        $array = ['error' => ''];

        $category = Category::find($id);

        if($id){
            $category->delete();
        }

        return $array;  
    }
}
