<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Shop;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    //商家列表接口
    public function index()
    {
        $shops = Shop::all();
        $data=[];
        foreach ($shops as $key=>$shop){
            $data[$key]['id']=$shop->id;
            $data[$key]['shop_name']=$shop->shop_name;
            $data[$key]['shop_img']=$shop->shop_img;
            $data[$key]['shop_rating']=$shop->shop_rating;
            $data[$key]['brand']=$shop->brand;
            $data[$key]['on_time']=$shop->on_time;
            $data[$key]['fengniao']=$shop->fengniao;
            $data[$key]['bao']=$shop->bao;
            $data[$key]['piao']=$shop->piao;
            $data[$key]['zhun']=$shop->zhun;
            $data[$key]['start_send']=$shop->start_send;
            $data[$key]['send_cost']=$shop->send_cost;
            $data[$key]['distance']=mt_rand(0,2000);
            $data[$key]['estimate_time']=mt_rand(20,60);
            $data[$key]['notice']=$shop->notice;
            $data[$key]['discount']=$shop->discount;
        }
        //dd(json_encode($data));
        return json_encode($data);
    }
    //获得指定商家
    public function show(Request $request)
    {
        $id = $request->id;
        $shop = Shop::where('id',$id)->first();
        $data=[];
        $data['id']=$shop->id;
        $data['shop_name']=$shop->shop_name;
        $data['shop_img']=$shop->shop_img;
        $data['shop_rating']=$shop->shop_rating;
        $data['service_code']=mt_rand(1,50)/10;
        $data['foods_code']=mt_rand(1,50)/10;
        $data['high_or_low']=mt_rand(0,1);
        $data['h_l_percent']=mt_rand(100,800)/10;
        $data['brand']=$shop->brand;
        $data['on_time']=$shop->on_time;
        $data['fengniao']=$shop->fengniao;
        $data['bao']=$shop->bao;
        $data['piao']=$shop->piao;
        $data['zhun']=$shop->zhun;
        $data['start_send']=$shop->start_send;
        $data['send_cost']=$shop->send_cost;
        $data['distance']=mt_rand(0,5000);
        $data['estimate_time']=mt_rand(10,70);
        $data['notice']=$shop->notice;
        $data['discount']=$shop->discount;
        //评价
        $evaluate=[
                    ["user_id"=> 12344,
                    "username"=> '美食tian',
                    "user_img"=>null,
                    "time"=> "2017-2-22",
                    "evaluate_code"=> 2,
                    "send_time"=> 44,
                    "evaluate_details"=> "还行吧"],
                    ["user_id"=> 12344,
                    "username"=> '好吃tian',
                    "user_img"=>null,
                    "time"=> "2017-2-22",
                    "evaluate_code"=> 4,
                    "send_time"=> 44,
                    "evaluate_details"=> "还行吧"],
        ];
        $data['evaluate']=$evaluate;
        //店铺商品分类
        $shopcategories = MenuCategory::where('shop_id',$id)->get();
        $cate = [];
        $goods_list = [];
        foreach ($shopcategories as $key=>$shopcategory){
             $cate[$key]['description']=$shopcategory->description;
             $cate[$key]['is_selected']=$shopcategory->is_selected;
             $cate[$key]['name']=$shopcategory->name;
             $cate[$key]['type_accumulation']=$shopcategory->id;
             //得到菜品分类id查询相应菜品
             $shopcategory_id = $shopcategory->id;
             $menus = Menu::where('category_id',$shopcategory_id)->get();
             foreach ($menus as $k=>$menu){
                 $goods_list[$k]['goods_id']=$menu->id;
                 $goods_list[$k]['goods_name']=$menu->goods_name;
                 $goods_list[$k]['rating']=$menu->rating;
                 $goods_list[$k]['goods_price']=$menu->goods_price;
                 $goods_list[$k]['description']=$menu->description;
                 $goods_list[$k]['rating_count']=$menu->rating_count;
                 $goods_list[$k]['rating_count']=$menu->rating_count;
                 $goods_list[$k]['tips']=$menu->tips;
                 $goods_list[$k]['satisfy_count']=$menu->satisfy_count;
                 $goods_list[$k]['satisfy_rate']=$menu->satisfy_rate;
                 $goods_list[$k]['goods_img']=$menu->goods_img;
             }
             //把菜品加入到分类列表里面
             $cate[$key]['goods_list']=$goods_list;
        }
        //降分类加入到数据里面
        $data['commodity'] = $cate;
//        dd(json_encode($data));
        return json_encode($data);
    }
}
