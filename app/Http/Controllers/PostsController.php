<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use App\Models\Imagem;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class PostsController extends Controller
{
    public function index(Request $request){
       
        $array = ['error' => ''];
        $posts = Post::where('visivel', 1)->paginate(2);

        if($posts){
            
            foreach($posts as $key => $item){
            // $posts[$key]['imagens'] = $item->imagens;
                $posts[$key]['category'] = $item->category;
            }
            $array['posts'] = $posts;
            $array['path'] = url('content/banner/');
        }else{
            $array['error'] = 'Nenhum post foi encontrado';
            return $array;
        }
        
        return $array; 
    }

    public function findOne($id){
        $array = ['error' => ''];

        $post = Post::where('visivel', 1)->find($id);
        if($post){
            $post['category'] = $post->category;
            $post['imagens'] = $post->imagens;
            $array['path'] = url('content/imagens/');
            $array['post'] = $post;
            return $array;
        }else{
            $array['error'] = 'Nenhum post foi encontrado';
            return $array;
        }
    }

    public function create(Request $request){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'banner.*' => 'required|image|mimes:jpeg,png,jpg,svg',
            'description' => 'required',
            'category' => 'required',
            'images.*' =>  'required|image|mimes:jpeg,png,jpg,svg',
        ]);

        if(!$validator->fails()){

            $title = $request->input('title');
            $banner = $request->file('banner');
            $category = $request->input('category');
            $description = $request->input('description');
            $images = $request->file('images.*');

            $titleExists = Post::where('title', $title)->count();

            if($titleExists === 0){
                $photoNameBanner = '';
                //banner
                if($banner){
                    $destBanner = public_path('content/banner');
                    $photoNameBanner = md5(time().rand(0,9999)).'.jpg';
                    $imgBanner = Image::make($banner->getRealPath());
                    $imgBanner->save($destBanner.'/'.$photoNameBanner);
                }
                
                $newPost = new Post();
                $newPost->title = $title;
                $newPost->banner = $photoNameBanner;
                $newPost->description = $description;
                $newPost->category_id = $category;
                $newPost->created_at = date('Y-m-d H:i:s');
                $str = strtolower($title);
                $newPost->slug = preg_replace('/\s+/', '-', $str);

                $newPost->save();

                //images
                if($images){
                    foreach($images as $item){
                        
                        $dest = public_path('content/imagens');
                        $photoName = md5(time().rand(0,9999)).'.jpg';
                
                        $img = Image::make($item->getRealPath());
                        $img->save($dest.'/'.$photoName);

                        $newPostPhoto = new Imagem();
                        $newPostPhoto->post_id = $newPost->id;
                        $newPostPhoto->imagem = $photoName;
                        $newPostPhoto->save();
                    }
                }

            }else{
                $array['error'] = 'Esse t??tulo j?? existe';
                return $array;
            }


        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }

    public function delete($id){
        $array = ['error' => ''];

        $post = Post::find($id);

        if($id){

            //deletar images banco e pasta
            File::delete(public_path("/content/banner/".$post->banner));
            $imgDel = Imagem::where('post_id', $post->id)->get();
            foreach($imgDel as $item){
                File::delete(public_path("/content/images/".$item["imagem"]));
                $item->delete();
            }

            //deletar post banco
            $post->delete();

        }

        return $array;  
    }

    public function deleteImage($id){
        $array = ['error' => ''];

        $imageDel = Imagem::find($id);

        if($imageDel){
            File::delete(public_path("/content/images/".$imageDel->imagem));
            $imageDel->delete();

        }else{
            $array['error'] = 'Imagem n??o existe';
            return $array;
        }

        return $array;

    }

    public function updateImages(Request $request, $id){
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'images.*' =>  'required|image|mimes:jpeg,png,jpg,svg',
        ]);

        if(!$validator->fails()){

            $images = $request->file('images.*');
                $post = Post::find($id);
                //images
                if($images){
                    foreach($images as $item){
                        
                        $dest = public_path('content/images');
                        $photoName = md5(time().rand(0,9999)).'.jpg';
                
                        $img = Image::make($item->getRealPath());
                        $img->save($dest.'/'.$photoName);

                        $newPostPhoto = new Imagem();
                        $newPostPhoto->post_id = $post->id;
                        $newPostPhoto->imagem = $photoName;
                        $newPostPhoto->save();
                    }
                }


        }else{
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;

    }

    public function update(Request $request, $id){
        $array = ['error' => ''];

        $rules = [
            'title' => 'required',
            'banner.*' => 'image|mimes:jpeg,png,jpg,svg',
            'description' => 'required',
            'category' => 'required',

        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $array['error'] = $validator->errors()->first();
            return $array;
        } 


        $title = $request->input('title');
        $description = $request->input('description');
        $category = $request->input('category');
        $banner = $request->file('banner');
        $post = Post::find($id);

            if($title){
                $post->title = $title;
            }

            if($description){
                $post->description = $description;
            }

            if($category){
                $post->category_id = $category;
            }

            if($banner){

                File::delete(public_path("/content/banner/".$post->banner));
                
                $destBanner = public_path('content/banner');
                $photoNameBanner = md5(time().rand(0,9999)).'.jpg';
                $imgBanner = Image::make($banner->getRealPath());
                $imgBanner->save($destBanner.'/'.$photoNameBanner);
                $post->banner = $photoNameBanner;
            }
            
            $post->save();

        return $array;

    }

    public function postImage(Request $request){
        $request->validate([
            'file' => 'image'
        ]);

        $ext = $request->file->extension();
        $imageName = time().'.'.$ext;

        $request->file->move(public_path('content/imagens'), $imageName);

        return [
            'location' => asset('content/imagens/'.$imageName)
        ];
    }

    public function search(Request $request){
        $array = ['error' => ''];

        $q = $request->input('q');
        
        if($q){
            $posts = Post::where('title', 'LIKE', '%'.$q.'%')->get();
            $array['posts']['data'] = $posts;

        }else{
            $array['error'] = 'Digite algo para buscar!';
            return $array;
        }
        $array['path'] = url('content/banner/');
        return $array;

    }


}
