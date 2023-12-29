<?php

namespace App\Http\Controllers;

use App\Http\Constants\PaginateConstant;
use App\Http\Requests\Banner\BannerStoreRequest;
use App\Http\Requests\Banner\BannerUpdateRequest;
use App\Repositories\Banner\BannerRepositoryInterface;
use App\Traits\CheckCountSlug;
use App\Traits\SessionTrait;
use Illuminate\Support\Str;
class BannerController extends Controller
{

    use SessionTrait, CheckCountSlug;

    protected BannerRepositoryInterface $bannerRepo;

    public function __construct(BannerRepositoryInterface $bannerRepo)
    {
        $this->bannerRepo = $bannerRepo;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banner = $this->bannerRepo->paginateWithOrder(PaginateConstant::PERPAGE, 'id', 'desc');

        return view('backend.banner.index')->with('banners', $banner);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.banner.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BannerStoreRequest $request)
    {
        $data = $request->all();
        $slug = Str::slug($request->title);
        $count = $this->bannerRepo->getCountBySlug($slug);
        $slug = $this->countSlug($count, $slug);
        $data['slug'] = $slug;

        $status = $this->bannerRepo->create($data);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('banner.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $banner = $this->bannerRepo->findOrFail($id);

        return view('backend.banner.edit')->with('banner', $banner);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BannerUpdateRequest $request, $id)
    {
        $data = $request->all();
        $status = $this->bannerRepo->update($id, $data);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('banner.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $status = $this->bannerRepo->delete($id);

        if($status){
            $this->success();
        } else{
            $this->error();
        }
        return redirect()->route('banner.index');
    }
}
