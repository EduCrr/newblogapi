<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function index(Request $request){
        $array = ['error' => ''];
        $categories = Category::all();
        $array['categories'] = $categories;
        
        return $array;
    }

    public function findOne($id){
        $array = ['error' => ''];
        $category = Category::find($id)->posts()->paginate(1);
        if($category){
            $array['posts'] = $category;
            $array['path'] = url('content/banner/');
        }else{
            $array['error'] = 'Não foi encontrada!';
            return $array;
        }
        return $array;
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

    public function update(Request $request, $id){
        $array = ['error' => ''];

        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        } 

        $name = $request->input('name');
        $category = Category::find($id);

        if($name){
            $category->name = $name;
        }
   
        $category->save();
        return $array;
    }
}
