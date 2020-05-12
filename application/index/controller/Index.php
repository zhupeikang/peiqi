<?php

namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {

        return $this->fetch();

    }

    public function getData()
    {
        $begin = $this->request->param('begin');
        $end = $this->request->param('end');
        if (empty($begin) || empty($end)) {
            return json([
                'code' => -1
                , 'msg' => '参数错误'
            ]);
        }
        $subQuery = Db::name('order_detail')
            ->alias('a')
            ->leftJoin('order b', 'b.id=a.order_id')
            ->field('sum(a.quantity) quantity ,a.color_id,a.product_id,a.order_id,b.create_date')
            ->whereTime('create_date', 'between', [$begin . ' 00:00:00', $end . ' 23:59:59'])
            ->group('product_id')
            ->buildSql();
        $data = Db::table($subQuery . ' a')
            ->leftJoin('product b', 'b.id=a.product_id')
            ->leftJoin('color c', 'c.id=a.color_id')
            ->field('b.name product_name,c.name color_name,a.quantity')
            ->select();
        return json([
            'code' => 0
            , 'orderDateEnd' => $end
            , 'orderDateStart' => $begin
            , 'productQuantity' => $data
        ]);
    }
}
