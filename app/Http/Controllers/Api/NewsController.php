<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsController extends Controller
{
    /**
     * 说明: 专家专栏列表数据
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author 郭庆
     */
    public function index(Request $request)
    {
        $data = News::orderBy('sort', 'desc')
            ->orderBy('read_num', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate($request->per_page??10);
        return $this->sendResponse($data, '获取专家专栏成功！');
    }

    /**
     * 说明: 新闻详情
     *
     * @param News $news
     * @return \Illuminate\Http\JsonResponse
     * @author 郭庆
     */
    public function show(News $news)
    {
        return $this->sendResponse($news, '获取新闻详情成功');
    }
}
