<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attributes; 
use App\Models\Category; 
use App\Models\Product;
class ProductController extends Controller
{
  public function index(Request $request){
    $category =Category::all();
    $products =Product::where('pro_active',0);

    if($name = $request->keyword)
     $products=$products->where('pro_name','like','%'. $name.'%');
    if($request->price) {
      $price = $request->price;
      switch($price)
      {
      case '1':
                 $products->where('pro_price','<',2000000);
                break;
      
      case '2':
                 $products->whereBetween('pro_price',[2000000,5000000]);
                 break;
      case '5':
                 $products->whereBetween('pro_price',[5000000,10000000]);
                 break;
      case '10':
                $products->whereBetween('pro_price',[10000000,50000000]);
                break;
      case '50':
                $products->where('pro_price','>',50000000);
                break;
    }
    }
    if($request->s){
      $request->s ==1 ? $products->orderBy('pro_price','desc') :  $products->orderBy('pro_price','asc') ;
  }
     $products= $products
          ->select('id','pro_name','pro_slug','pro_sale','pro_avatar','pro_price','pro_review_total','pro_review_star')
          ->paginate(10);
         $viewData=[
            'category'      =>$category,
            'title_page'    =>'Kết quả tìm kiếm cho '. $name,
            'products'      =>$products,
            'query'         =>$request->query()
         ];
    return view('frontend.category.index',$viewData);
        }
} 
