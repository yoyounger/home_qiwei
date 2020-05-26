<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Models\OrderGoods;
use App\Models\Shop;
use App\SignatureHelper;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class ApiController extends Controller
{
    //商家列表接口
    public function index()
    {
        $shops = Shop::all();
        $data = [];
        foreach ($shops as $key => $shop) {
            $data[$key]['id'] = $shop->id;
            $data[$key]['shop_name'] = $shop->shop_name;
            $data[$key]['shop_img'] = $shop->shop_img;
            $data[$key]['shop_rating'] = $shop->shop_rating;
            $data[$key]['brand'] = $shop->brand;
            $data[$key]['on_time'] = $shop->on_time;
            $data[$key]['fengniao'] = $shop->fengniao;
            $data[$key]['bao'] = $shop->bao;
            $data[$key]['piao'] = $shop->piao;
            $data[$key]['zhun'] = $shop->zhun;
            $data[$key]['start_send'] = $shop->start_send;
            $data[$key]['send_cost'] = $shop->send_cost;
            $data[$key]['distance'] = mt_rand(0, 2000);
            $data[$key]['estimate_time'] = mt_rand(20, 60);
            $data[$key]['notice'] = $shop->notice;
            $data[$key]['discount'] = $shop->discount;
        }
        return json_encode($data);
    }

    //获得指定商家
    public function show(Request $request)
    {
        $id = $request->id;
        $shop = Shop::where('id', $id)->first();
        $data = [];
        $data['id'] = $shop->id;
        $data['shop_name'] = $shop->shop_name;
        $data['shop_img'] = $shop->shop_img;
        $data['shop_rating'] = $shop->shop_rating;
        $data['service_code'] = mt_rand(1, 50) / 10;
        $data['foods_code'] = mt_rand(1, 50) / 10;
        $data['high_or_low'] = mt_rand(0, 1);
        $data['h_l_percent'] = mt_rand(100, 800) / 10;
        $data['brand'] = $shop->brand;
        $data['on_time'] = $shop->on_time;
        $data['fengniao'] = $shop->fengniao;
        $data['bao'] = $shop->bao;
        $data['piao'] = $shop->piao;
        $data['zhun'] = $shop->zhun;
        $data['start_send'] = $shop->start_send;
        $data['send_cost'] = $shop->send_cost;
        $data['distance'] = mt_rand(0, 5000);
        $data['estimate_time'] = mt_rand(10, 70);
        $data['notice'] = $shop->notice;
        $data['discount'] = $shop->discount;
        //评价
        $evaluate = [
            ["user_id" => 12344,
                "username" => '美食tian',
                "user_img" => null,
                "time" => "2017-2-22",
                "evaluate_code" => 2,
                "send_time" => 44,
                "evaluate_details" => "还行吧"],
            ["user_id" => 12344,
                "username" => '好吃tian',
                "user_img" => null,
                "time" => "2017-2-22",
                "evaluate_code" => 4,
                "send_time" => 44,
                "evaluate_details" => "还行吧"],
        ];
        $data['evaluate'] = $evaluate;
        //店铺商品分类
        $shopcategories = MenuCategory::where('shop_id', $id)->get();
        $cate = [];
        foreach ($shopcategories as $key => $shopcategory) {
            $cate[$key]['description'] = $shopcategory->description;
            $cate[$key]['is_selected'] = $shopcategory->is_selected;
            $cate[$key]['name'] = $shopcategory->name;
            $cate[$key]['type_accumulation'] = $shopcategory->id;
            //得到菜品分类id查询相应菜品
            $shopcategory_id = $shopcategory->id;
            $menus = Menu::where('category_id', $shopcategory_id)->get();
            $goods_list = [];
            foreach ($menus as $k => $menu) {
                $goods_list[$k]['goods_id'] = $menu->id;
                $goods_list[$k]['goods_name'] = $menu->goods_name;
                $goods_list[$k]['rating'] = $menu->rating;
                $goods_list[$k]['goods_price'] = $menu->goods_price;
                $goods_list[$k]['description'] = $menu->description;
                $goods_list[$k]['rating_count'] = $menu->rating_count;
                $goods_list[$k]['rating_count'] = $menu->rating_count;
                $goods_list[$k]['tips'] = $menu->tips;
                $goods_list[$k]['satisfy_count'] = $menu->satisfy_count;
                $goods_list[$k]['satisfy_rate'] = $menu->satisfy_rate;
                $goods_list[$k]['goods_img'] = $menu->goods_img;
            }
            //把菜品加入到分类列表里面
            $cate[$key]['goods_list'] = $goods_list;
        }
        //分类加入到数据里面
        $data['commodity'] = $cate;
        return $data;
    }

    //验证码验证
    public function sms()
    {
        $tel = request()->tel;
        $params = array();

        // *** 需用户填写部分 ***

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "";
        $accessKeySecret = "";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $tel;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "王奇";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_140515037";
        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $num = mt_rand(1000, 9999);
        $params['TemplateParam'] = Array(
            "code" => $num,
            //"product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );
        Redis::set($tel, $num);
        Redis::expire($tel, 600);
//        return $content;
        return [
            "status" => "true",
            "message" => "短信发送成功"
        ];
    }

    //用户注册
    public function regist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:customers',
            'tel' => 'required|unique:customers',
            'password' => 'required|min:6',
        ], [
            'username.required' => '用户名不能为空!',
            'username.unique' => '用户名已存在!',
            'tel.required' => '电话号码不能为空!',
            'tel.unique' => '电话号码已存在!',
            'password.required' => '密码不能为空!',
            'password.min' => '密码至少6位!',
        ]);
        if ($validator->fails()) {
            return [
                "status" => "false",
                "message" => $validator->errors()->first(),
            ];
        }
        $code = Redis::get($request->tel);
        if ($code != $request->sms) {
            return [
                "status" => "false",
                "message" => "注册失败!验证码不正确或超期"
            ];
        }
        Customer::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'tel' => $request->tel,
        ]);
        return [
            "status" => "true",
            "message" => "注册成功!"
        ];
    }

    //用户登录
    public function loginCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
        ], [
            'name.required' => '用户名不能为空!',
            'password.required' => '密码不能为空!',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 'false',
                'message' => $validator->errors()->first()
            ];
        }
        $res = Customer::where('username', $request->name)->first();
        if (!$res || $res->status == 0) {
            return ["status" => "false",
                "message" => "登录失败!用户名错误或被禁用!",
                "user_id" => "",
                "username" => ""];
        }
        if (!Auth::attempt([
            'username' => $request->name,
            'password' => $request->password,
        ])
        ) {
            return ["status" => "false",
                "message" => "登录失败!用户名或密码错误",
                "user_id" => "",
                "username" => ""];
        } else {
            $customer = Customer::where('username', $request->name)->first();
            return [
                "status" => "true",
                "message" => "登录成功!",
                "user_id" => Auth::user()->id,
                "username" => Auth::user()->username
            ];
        }
    }

    //用户修改密码
    public function changePassword(Request $request)
    {
        $oldpassword = $request->oldPassword;
        $newpassword = $request->newPassword;
        if (!Hash::check($oldpassword, auth()->user()->password)) {
            return [
                "status" => 'false',
                'message' => '原密码错误!',
            ];
        } else {
            Customer::where('id', auth()->user()->id)->update([
                'password' => bcrypt($newpassword)
            ]);
            return [
                "status" => 'true',
                'message' => '修改成功!',
            ];
        }
    }

    //用户重置密码
    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tel' => 'required',
            'sms' => 'required',
            'password' => 'required|min:6',
        ], [
            'tel.required' => '电话号码不能为空!',
            'sms.required' => '验证码不能为空!',
            'password.required' => '密码不能为空!',
            'password.min' => '密码不能小于6位!',
        ]);
        if ($validator->fails()) {
            return [
                'status' => 'false',
                'message' => $validator->errors()->first()
            ];
        }
        $code = Redis::get($request->tel);
        if ($code != $request->sms) {
            return [
                'status' => 'false',
                'message' => '验证码错误或者已经过期!'
            ];
        }
        Customer::where('tel', $request->tel)->update([
            'password' => bcrypt($request->password)
        ]);
        return [
            'status' => 'true',
            'message' => '密码修改成功!'
        ];
    }

    //用户地址管理
    public function addressList()
    {
        $data = [];
        $addresses = Address::where('user_id', auth()->user()->id)->get();
        foreach ($addresses as $key => $address) {
            $data[$key]['id'] = $address->id;
            $data[$key]['provence'] = $address->province;
            $data[$key]['city'] = $address->city;
            $data[$key]['area'] = $address->county;
            $data[$key]['detail_address'] = $address->address;
            $data[$key]['name'] = $address->name;
            $data[$key]['tel'] = $address->tel;
        }
        return $data;
    }

    //添加用户地址
    public function addAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:6',
            'tel' => 'required|max:11',
            'provence' => 'required',
            'city' => 'required',
            'area' => 'required',
            'detail_address' => 'required',
        ], [
            'name.required' => '姓名不能为空!',
            'name.max' => '姓名不能超过6位!',
            'tel.required' => '电话号码不能为空!',
            'tel.max' => '电话不能超过11位!',
            'provence.required' => '省份不能为空!',
            'city.required' => '市不能为空!',
            'area.required' => '地区不能为空!',
            'detail_address.required' => '详细地址不能为空!'
        ]);
        if ($validator->fails()) {
            return [
                'status' => 'false',
                'message' => $validator->errors()->first()
            ];
        }
        $res = Address::where('is_default', 1)->get();
        if ($res) {
            $is_default = 0;
        } else {
            $is_default = 1;
        }
        Address::create([
            'user_id' => auth()->user()->id,
            'province' => $request->provence,
            'city' => $request->city,
            'county' => $request->area,
            'address' => $request->detail_address,
            'tel' => $request->tel,
            'name' => $request->name,
            'is_default' => $is_default,
        ]);
        return [
            'status' => 'true',
            'message' => '添加成功!'
        ];
    }

    //修改回显用户地址
    public function address(Request $request)
    {
        $address = Address::where('id', $request->id)->first();
        $data['id'] = $address->id;
        $data['provence'] = $address->province;
        $data['city'] = $address->city;
        $data['area'] = $address->county;
        $data['detail_address'] = $address->address;
        $data['name'] = $address->name;
        $data['tel'] = $address->tel;
        return $data;
    }

    //修改保存用户地址
    public function editAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:6',
            'tel' => 'required|max:11',
            'provence' => 'required',
            'city' => 'required',
            'area' => 'required',
            'detail_address' => 'required',
        ], [
            'name.required' => '姓名不能为空!',
            'name.max' => '姓名不能超过6位!',
            'tel.required' => '电话号码不能为空!',
            'tel.max' => '电话不能超过11位!',
            'provence.required' => '省份不能为空!',
            'city.required' => '市不能为空!',
            'area.required' => '地区不能为空!',
            'detail_address.required' => '详细地址不能为空!'
        ]);
        if ($validator->fails()) {
            return [
                'status' => 'false',
                'message' => $validator->errors()->first()
            ];
        }
        Address::where('id', $request->id)->update([
            'province' => $request->provence,
            'city' => $request->city,
            'county' => $request->area,
            'address' => $request->detail_address,
            'tel' => $request->tel,
            'name' => $request->name,
        ]);
        return [
            'status' => 'true',
            'message' => '修改成功!'
        ];
    }

    //保存购物车
    public function addCart(Request $request)
    {
        $res = Cart::where('user_id', auth()->user()->id)->get();
        if ($res) {
            Cart::where('user_id', auth()->user()->id)->delete();
        }
        $length = count($request->goodsList);
        $data = [];
        for ($i = 0; $i < $length; $i++) {
            $data['goods_id'] = $request->goodsList[$i];
            $data['amount'] = $request->goodsCount[$i];
            Cart::create([
                'goods_id' => $data['goods_id'],
                'user_id' => auth()->user()->id,
                'amount' => $data['amount'],
            ]);
        }
        return [
            'status' => 'true',
            'message' => '添加购物车成功!'
        ];
    }

    //获取购物车信息
    public function cart()
    {
        $carts = Cart::where('user_id', auth()->user()->id)->get();
        $data = [];
        $totalCost = 0;
        foreach ($carts as $key => $cart) {
            $data[$key]['goods_id'] = $cart->goods_id;
            $data[$key]['amount'] = $cart->amount;
            $menu = Menu::where('id', $cart->goods_id)->first();
            $data[$key]['goods_name'] = $menu->goods_name;
            $data[$key]['goods_img'] = $menu->goods_img;
            $data[$key]['goods_price'] = $menu->goods_price;
            $totalCost += $menu->goods_price * $cart->amount;
        }
        $cart = [
            'goods_list' => $data,
            'totalCost' => $totalCost
        ];
        return $cart;
    }

    //生成订单
    public function addOrder(Request $request)
    {
        $address_id = $request->address_id;
        //查找地址
        $address = Address::where('id', $address_id)->first();
        $user_id = auth()->user()->id;
        //根据用户id查找购物车商品
        $carts = Cart::where('user_id', $user_id)->get();
        $shop = Shop::where('id',$carts[0]->menu->shop_id)->first();
        $shopname = $shop->shop_name;
        //查询商家账户发邮件的邮箱
        $user = User::where('shop_id',$carts[0]->menu->shop_id)->first();

        //计算价格
        $total = 0;
        foreach ($carts as $cart) {
            $total += $cart->menu->goods_price * $cart->amount;
        }
        //添加到订单表
        $data = DB::transaction(function () use ($user_id, $address, $total, $carts) {
            $order = Order::create([
                'user_id' => $user_id,
                'shop_id' => $carts[0]->menu->shop_id,
                'sn' => date('YmdHis') . mt_rand(1000, 9999),
                'province' => $address->province,
                'city' => $address->city,
                'county' => $address->county,
                'address' => $address->address,
                'tel' => $address->tel,
                'name' => $address->name,
                'total' => $total,
                'status' => 0,
                'out_trade_no' => uniqid(),
            ]);
            $order_id = $order->id;
            $goods = '';
            //创建订单商品表
            foreach ($carts as $cart) {
                OrderGoods::create([
                    'order_id' => $order_id,
                    'goods_id' => $cart->goods_id,
                    'amount' => $cart->amount,
                    'goods_name' => $cart->menu->goods_name,
                    'goods_img' => $cart->menu->goods_img,
                    'goods_price' => $cart->menu->goods_price,
                ]);
                $goods .= $cart->menu->goods_name.',';
                $amount = $cart->amount;
                $menu = Menu::where('id', $cart->goods_id)->first();
                if (date('d', time()) == 01) {
                    $data = ['month_sales' => 0];
                } else {
                    $num = $menu->month_sales + $amount;
                    $data = ['month_sales' => $num];
                }
                Menu::where('id', $cart->goods_id)->update($data);
            }
            return ['order_id'=>$order_id,'goods'=>$goods];
        });

        $tel = $address->tel;
        $params = array();
//        dd($tel);
        // *** 需用户填写部分 ***

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "";
        $accessKeySecret = "";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $tel;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "王奇";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_141200242";
        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array(
            "name" =>$address->name,
            'shop'=>$shopname,
            'menus'=>$data['goods']
            //"product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        // fixme 选填: 启用https
        // ,true
        );
        Mail::raw('您有新的外卖订单,请注意商户端查收!!',function ($message) use($user){
            $message->subject('外卖订单通知提醒');
            $message->to($user->email);
        });
        return [
            'status' => 'true',
            'message' => '生成订单成功!',
            'order_id' => $data['order_id'],
        ];
    }

    //指定订单
    public function order(Request $request)
    {
        $order = Order::where('id', $request->id)->first();
        $order_goods = OrderGoods::where('order_id', $request->id)->get();
        if ($order->status == 0) {
            $order_status = '待支付';
        } elseif ($order->status == -1) {
            $order_status = '已取消';
        } elseif ($order->status == 1) {
            $order_status = '待发货';
        } elseif ($order->status == 2) {
            $order_status = '待确认';
        } else {
            $order_status = '完成';
        }
        $data['id'] = $order->id;
        $data['order_code'] = $order->sn;
        $data['order_birth_time'] = substr($order->created_at, 0, 16);
        $data['order_status'] = $order_status;
        $data['shop_id'] = $order->shop_id;
        $data['shop_name'] = $order->shop->shop_name;
        $data['shop_img'] = $order->shop->shop_img;
        foreach ($order_goods as $val) {
            unset($val['id'], $val['order_id'], $val['created_at'], $val['updated_at']);
        }
        $data['goods_list'] = $order_goods;
        $data['order_price'] = $order->total;
        $data['order_address'] = $order->province . $order->city . $order->county . $order->address;
        return $data;
    }

    //订单列表
    public function orderList()
    {
        $orders = Order::where('user_id', auth()->user()->id)->get();
        $data = [];
        foreach ($orders as $key => $order) {
            $order_goods = OrderGoods::where('order_id', $order->id)->get();
            if ($order->status == 0) {
                $order_status = '待支付';
            } elseif ($order->status == -1) {
                $order_status = '已取消';
            } elseif ($order->status == 1) {
                $order_status = '待发货';
            } elseif ($order->status == 2) {
                $order_status = '待确认';
            } else {
                $order_status = '完成';
            }
            $data[$key]['id'] = $order->id;
            $data[$key]['order_code'] = $order->sn;
            $data[$key]['order_birth_time'] = substr($order->created_at, 0, 16);
            $data[$key]['order_status'] = $order_status;
            $data[$key]['shop_id'] = $order->shop_id;
            $data[$key]['shop_name'] = $order->shop->shop_name;
            $data[$key]['shop_img'] = $order->shop->shop_img;
            foreach ($order_goods as $val) {
                unset($val['id'], $val['order_id'], $val['created_at'], $val['updated_at']);
            }
            $data[$key]['goods_list'] = $order_goods;
            $data[$key]['order_price'] = $order->total;
            $data[$key]['order_address'] = $order->province . $order->city . $order->county . $order->address;
        }
        return $data;
    }
}
